<?php

namespace App\Http\Controllers\Admincp;

use App\Models\Application;
use App\Services\ApplicationService;
use App\Services\GoodAff\GoodAffDataService;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class ApplicationsController extends AdminController
{
    public function __construct(protected ApplicationService $application_service)
    {
        override_locale_if_provided();
    }

    public function index(Request $request): Renderable
    {
        $PER_PAGE = 50;

        $applications = Application::query()
            ->withCount('gocardless_sessions');

        if ($request->input('search')) {
            $applications = $applications->where(function ($q) use ($request) {
                $q->where('email', 'like', $request->input('search') . '%')
                  ->orWhere('phone', 'like', $request->input('search') . '%');
            });
        }

        if ($id = $request->input('id')) {
            $applications = $applications->where('id', $id);
        }

        if ($fingerprint = $request->input('fingerprint')) {
            $applications = $applications->where('fingerprint', $fingerprint);
        }

        if ($lang = $request->input('lang')) {
            $applications = $applications->where('lang', $lang);
        }

        $applications = $applications->orderBy('id', 'DESC')
            ->paginate($PER_PAGE);

        return view('admincp.applications.index', [
            'applications' => $applications,
            'meta' => $this->metaTitle('Applications'),
        ]);
    }

    public function show(Application $application): Renderable
    {
        $fields = [
            'consumer_id',
            'token',
            'webmaster_token',
            'fingerprint',
            'lang',
            'email',
            'phone',
            'phone_verified_at',
            'address_street',
            'address_city',
            'address_zipcode',
            'amount',
            'term_months',
            'loantype',
            'employment_status',
            'education',
            'marital_status',
            'disbursement_iban',
            'salary',
            'monthly_debit',
            'age',
            'credit_history',
            'company_name',
            'company_reg_nr',
            'company_contact_person',
            'cadastral_number',
            'dependants',
            'first_name',
            'last_name',
            'personal_code',
            'is_political_person',
            'has_real_property',
            'has_bank_attachment',
            'coupon_code',
            'agree_to_terms',
            'subscribed',
            'pdf',
            'referrer',
            'user_agent',
            'ip',
            'utm_source',
            'utm_medium',
            'utm_campaign',
            'utm_content',
            'goodaff_click_id',
            'gclid',
            'first_offer_hash',
            'second_offer_hash',
            'will_send_docs_via_email',
            'completion_percentage',
            'pdf_downloaded_from_ip',
            'pdf_downloaded_at',
            'last_exported_at',
            'exports_summary',
            'created_at',
            'updated_at',
        ];

        return view('admincp.applications.show')
            ->with([
                'fields' => $fields,
                'application' => $application,
                'meta' => $this->metaTitle('View application - ' . $application->id),
            ]);
    }

    public function viewPdf(string $application_token)
    {
        $application = $this->application_service->getByToken($application_token);

        if (empty($application->id)) {
            abort(400);
        }

        if (empty($application->pdf)) {
            abort(404);
        }

        return Storage::download('/' . Application::$STORAGE_PATH . $application->pdf);
    }

    public function exportToData(Application $application): RedirectResponse
    {
        $data = App::make(GoodAffDataService::class);
        /** @var GoodAffDataService $data */

        $data->postApplication($application);

        return redirect()->back()
            ->with('success', 'Application ' . $application->id . ' has been exported to Data');
    }
}
