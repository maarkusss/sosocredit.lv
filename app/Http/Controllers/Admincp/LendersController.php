<?php

namespace App\Http\Controllers\Admincp;

use App\Models\Application;
use App\Models\Lender;
use App\Models\Lender\LenderCategory;
use App\Models\Lender\LenderData;
use App\Models\Lender\LenderSorting;
use App\Models\Lender\LenderSortingEpc;
use App\Models\LenderCampaign;
use App\Models\ProviderLinkVisit;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use stdClass;

class LendersController extends AdminController
{
    public function index(): Renderable
    {
        $lenders_array = [];
        if (request()->sorting) {
            foreach (LenderSortingEpc::query()
                         ->where('sorting_id', request()->sorting)
                         ->orderBy('epc', 'DESC')
                         ->orderBy('clicks', 'DESC')
                         ->get() as $sorting) {
                $sorting->lender->epc = $sorting->epc;
                $sorting->lender->clicks = $sorting->clicks;
                $sorting->lender->earnings = $sorting->earnings;
                $sorting->lender->position = $sorting->position;

                $lenders_array[] = $sorting->lender;
            }
        } else {
            foreach (Lender::query()
                         ->orderBy('guaranteed_epc', 'DESC')
                         ->orderBy('position')
                         ->orderBy('epc', 'DESC')
                         ->get() as $lender) {
                $lenders_array[] = $lender;
            }
        }

        $lenders = $lenders_array;

        $sortings = LenderSorting::query()
            ->orderBy('clicks', 'DESC')
            ->get();

        return view('admincp.lenders.index')
            ->with([
                'lenders' => $lenders,
                'sortings' => $sortings,
                'meta' => $this->metaTitle('Lenders'),
            ]);
    }

    public function updateAll(): RedirectResponse
    {
        $arr = request()->position;
        asort($arr);

        $i = 0;
        foreach ($arr as $key => $value) {
            $i++;
            $this->updateLenderPosition($key, $i);
        }

        foreach (request()->frequency as $key => $value) {
            $this->updateLenderFrequency($key, $value);
        }

        foreach (request()->daily_epc as $key => $value) {
            $this->updateLenderDailyEpc($key, $value);
        }

        foreach (request()->daily_epc_before as $key => $value) {
            $this->updateLenderDailyEpcBefore($key, $value);
        }

        foreach (request()->guaranteed_epc as $key => $value) {
            $this->updateLenderGuaranteedEpc($key, $value);
        }

        foreach (request()->active as $key => $value) {
            $this->updateLenderActive($key, $value);
        }

        $this->logAdminAction('Created all lenders!');

        return redirect()
            ->back()
            ->with('success', 'Lenders updated!');
    }

    public function updateLenderPosition($id, $value): void
    {
        $lender = Lender::findOrFail($id);
        $lender->update([
            'position' => $value
        ]);
    }

    public function updateLenderFrequency($id, $value): void
    {
        $lender = Lender::findOrFail($id);
        $lender->update([
            'frequency' => $value
        ]);
    }

    public function updateLenderDailyEpc($id, $value): void
    {
        $lender = Lender::findOrFail($id);
        $lender->update([
            'daily_epc' => $value
        ]);
    }

    public function updateLenderDailyEpcBefore($id, $value): void
    {
        $lender = Lender::findOrFail($id);
        $lender->update([
            'daily_epc_before' => $value
        ]);
    }

    public function updateLenderGuaranteedEpc($id, $value): void
    {
        $lender = Lender::findOrFail($id);
        $lender->update([
            'guaranteed_epc' => $value
        ]);
    }

    public function updateLenderActive($id, $value): void
    {
        $lender = Lender::findOrFail($id);
        $lender->update([
            'active' => $value
        ]);
    }

    public function create(): Renderable
    {
        return view('admincp.lenders.add')->with([
            'categories' => $this->getLenderCategories(),
            'languages' => $this->getLanguages(),
            'meta' => $this->metaTitle('Add a lender'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required',
            'affLink' => 'required',
            'firstLoan' => 'required',
            'maxAmount' => 'required',
            'minTerm' => 'required',
            'maxTerm' => 'required',
            'minYears' => 'required',
            'maxYears' => 'required',
            'receivingTime' => 'required',
            'is_provider' => 'nullable|in:0,1',
            'is_broker' => 'nullable|in:0,1',
            'slug' => 'required_with:is_provider|nullable|string',
            'abbreviation' => 'required_with:is_provider|nullable|string',
            'offer_expiration' => 'nullable|integer',
            'rejected_statuses.Error' => 'nullable|in:0,1',
            'rejected_statuses.Rejected' => 'nullable|in:0,1',
            'rejected_statuses.Expired' => 'nullable|in:0,1',
            'rejected_statuses.Cancelled' => 'nullable|in:0,1',
        ]);

        if ($request->has('rejected_statuses')) {
            $data['rejected_statuses'] = json_encode($request->rejected_statuses);
        }

        $path = Storage::putFile('public/logos', $request->file('image'));

        $path = explode('/', $path);
        $path = '/storage/' . $path[1] . '/' . $path[2];

        $lender = new Lender();

        $lender->name = $data['name'];
        $lender->abbreviation = $data['abbreviation'] ?? null;
        $lender->slug = $data['slug'] ?? null;
        $lender->offer_expiration = $data['offer_expiration'] ?? null;
        $lender->image = $path;
        $lender->affiliate_link = $data['affLink'];
        $lender->first_loan = $data['firstLoan'];
        $lender->max_amount = $data['maxAmount'];
        $lender->min_term = $data['minTerm'];
        $lender->max_term = $data['maxTerm'];
        $lender->min_years = $data['minYears'];
        $lender->max_years = $data['maxYears'];
        $lender->receiving_time = $data['receivingTime'];
        $lender->active = $request->active ?? 0;
        $lender->is_broker = $request->is_broker ?? 0;
        $lender->is_provider = $request->is_provider ?? 0;
        $lender->rejected_statuses = $data['rejected_statuses'] ?? null;
        $lender->zero_percent = $request->zero_percent ?? 0;

        $lender->save();

        if ($request->category) {
            foreach ($request->category as $key => $value) {
                if ($value == 1) {
                    LenderCategory::create([
                        'lender_id' => $lender->id,
                        'loan_type_id' => $key,
                    ]);
                }
            }
        }

        $this->logAdminAction('Created lender "' . $data['name'] . '"');

        return redirect(route('admincp.lenders.index'))
            ->with(['success', 'Lender has been added!']);
    }

    public function edit($id): Renderable
    {
        return view('admincp.lenders.edit')
            ->with([
                'campaigns' => LenderCampaign::orderBy('name', 'ASC')->get(),
                'lender' => Lender::findOrFail($id),
                'lender_data' => $this->getCurrentLenderData($id),
                'categories' => $this->getLenderCategories(),
                'languages' => $this->getLanguages(),
                'meta' => $this->metaTitle('Edit a lender'),
            ]);
    }

    public function update($id, Request $request)
    {
        $lender = Lender::findOrFail($id);

        if ($request->input('action') === 'save') {
            LenderCategory::query()
                ->where('lender_id', $id)
                ->delete();

            $data = $request->validate([
                'name' => 'required',
                'affLink' => 'required',
                'firstLoan' => 'required',
                'maxAmount' => 'required',
                'minTerm' => 'required',
                'maxTerm' => 'required',
                'minYears' => 'required',
                'maxYears' => 'required',
                'receivingTime' => 'required',
                'benefits' => 'array|max:4',
                'benefits.*' => 'string|max:50|nullable',
                'is_broker' => 'nullable|in:0,1',
                // provider specific fields
                'is_provider' => 'nullable|in:0,1',
                'slug' => 'required_with:is_provider|nullable|string',
                'abbreviation' => 'required_with:is_provider|nullable|string',
                'offer_expiration' => 'nullable|integer',
                'rejected_statuses.Error' => 'nullable|in:0,1',
                'rejected_statuses.Rejected' => 'nullable|in:0,1',
                'rejected_statuses.Refused' => 'nullable|in:0,1',
                'rejected_statuses.Expired' => 'nullable|in:0,1',
                'rejected_statuses.Cancelled' => 'nullable|in:0,1',
            ]);

            if ($request->image) {
                $path = Storage::putFile('public/logos', $request->file('image'));

                $path = explode('/', $path);
                $path = '/storage/' . $path[1] . '/' . $path[2];

                $lender->update([
                    'image' => $path,
                ]);
            }

            $lender->update([
                'name' => $data['name'],
                'abbreviation' =>  $data['abbreviation'] ?? null,
                'slug' => $data['slug'] ?? null,
                'offer_expiration' => $data['offer_expiration'] ?? null,
                'affiliate_link' => $data['affLink'],
                'first_loan' => $data['firstLoan'],
                'max_amount' => $data['maxAmount'],
                'min_term' => $data['minTerm'],
                'max_term' => $data['maxTerm'],
                'min_years' => $data['minYears'],
                'max_years' => $data['maxYears'],
                'receiving_time' => $data['receivingTime'],
                'active' => $request->active ?? 0,
                'is_broker' => $request->is_broker ?? 0,
                'is_provider' => $request->is_provider ?? 0,
                'rejected_statuses' => $data['rejected_statuses'] ?? null,
                'zero_percent' => $request->zero_percent ?? 0,
            ]);


            $benefits = $request->input('benefits') == [null] ? null : $request->input('benefits');

            LenderData::updateOrCreate([
                'lender_id' => $id,
                'lang' => app()->getLocale(),
            ], [
                'slogan' => $request->input('slogan'),
                'benefits' => $benefits,
                'company_name' => $request->input('companysName'),
                'address' => $request->input('address'),
                'phone' => $request->input('phone'),
                'email' => $request->input('email'),
                'max_apr' => $request->input('maxAPR'),
                'apr_example' => $request->input('aprExample'),
                'title' => $request->input('lendersTitle'),
                'description' => $request->input('lendersDescription'),
            ]);

            if ($request->category) {
                foreach ($request->category as $key => $value) {
                    if ($value == 1) {
                        LenderCategory::create([
                            'lender_id' => (int)$lender->id,
                            'loan_type_id' => $key,
                        ]);
                    }
                }
            }

            $this->logAdminAction('Updated lender "' . $data['name'] . '"');

            return redirect()
                ->back()
                ->with('success', 'Lender updated!');
        }

        // this is weird but the delete button leads to update controller
        if ($request->input('action') === 'delete') {
            $lender = Lender::findOrFail($id);
            $this->destroy($lender);
            return redirect()
                ->route('admincp.lenders.index')
                ->with([
                    'success' => 'Lender deleted successfully',
                ]);
        }

        return abort(404);
    }

    public function updateCampaignId(int $lender_id, Request $request): RedirectResponse
    {
        if (!empty($request->campaign_id)) {
            Lender::query()
                ->where('id', $lender_id)
                ->update([
                    'campaign_id' => (int) $request->campaign_id,
                ]);
        }

        return redirect()->back();
    }

    public function getCurrentLenderData(int $id)
    {
        return LenderData::query()
            ->where('lender_id', $id)
            ->where('lang', app()->getLocale())
            ->first();
    }

    public function destroy(Lender $lender): RedirectResponse
    {
        if ($lender && $lender->id) {
            $this->logAdminAction('Deleted lender ' . $lender->name);

            $lender->delete();

            return redirect()
                ->route('admincp.lenders.index')
                ->with([
                    'success' => 'Lender deleted successfully',
                ]);
        }

        return redirect()->back();
    }

    public function linkVisits(): Renderable
    {
        $now = now();

        $lenders = Lender::where('is_provider', 1)
            ->withCount([
                'providerLinkVisits as total_visits',

                'providerLinkVisits as today_visits' => fn ($q) =>
                    $q->whereDate('created_at', $now),

                'providerLinkVisits as yesterday_visits' => fn ($q) =>
                    $q->whereDate('created_at', $now->copy()->subDay()),

                'providerLinkVisits as this_month_visits' => fn ($q) =>
                    $q->whereMonth('created_at', $now->month)
                      ->whereYear('created_at', $now->year),

                'providerLinkVisits as last_month_visits' => fn ($q) =>
                    $q->whereMonth('created_at', $now->copy()->subMonth()->month)
                      ->whereYear('created_at', $now->copy()->subMonth()->year),
            ])
            ->orderByDesc('total_visits')
            ->get();

        $sources = ProviderLinkVisit::selectRaw('lender_id, source, COUNT(*) as total')
            ->where('created_at', '>=', $now->copy()->subMonths(2))
            ->groupBy('lender_id', 'source')
            ->get()
            ->groupBy('lender_id');

        $lastVisits = ProviderLinkVisit::selectRaw('lender_id, MAX(created_at) as last_visit')
            ->groupBy('lender_id')
            ->pluck('last_visit', 'lender_id');

        $applications = Application::whereNotNull('exports_summary')
            ->where('created_at', '>=', $now->copy()->subDays(7))
            ->get();

        $approvalRates = [];
        foreach ($lenders as $lender) {
            $approvalRates[$lender->id] =
                $lender->getApprovalRate($applications)
                ?? (object)[
                    'apr' => 0,
                    'approved' => 0,
                    'total' => 0,
                ];
        }

        $totalVisits = ProviderLinkVisit::count();

        $todayVisits = ProviderLinkVisit::whereDate('created_at', $now)->count();
        $yesterdayVisits = ProviderLinkVisit::whereDate('created_at', $now->copy()->subDay())->count();

        $thisMonthVisits = ProviderLinkVisit::whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();

        $lastMonthVisits = ProviderLinkVisit::whereMonth('created_at', $now->copy()->subMonth()->month)
            ->whereYear('created_at', $now->copy()->subMonth()->year)
            ->count();

        return view('admincp.lenders.providers.index', compact(
            'lenders',
            'sources',
            'lastVisits',
            'approvalRates',
            'totalVisits',
            'todayVisits',
            'yesterdayVisits',
            'thisMonthVisits',
            'lastMonthVisits'
        ))->with('meta', $this->metaTitle('Provider Link Visits'));
    }
}
