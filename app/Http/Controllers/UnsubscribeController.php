<?php

namespace App\Http\Controllers;

use App\Contracts\HoiFmServiceInterface;
use App\Http\Requests\UnsubscribeEmailRequest;
use App\Http\Requests\UnsubscribePhoneRequest;
use App\Models\Application;
use App\Models\Consumer;
use App\Services\RcService;
use App\Traits\WorksWithMeta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UnsubscribeController extends Controller
{
    use WorksWithMeta;

    public function __construct(protected HoiFmServiceInterface $hoi_fm, protected RcService $rc_service)
    {
    }

      /**
     * Return email unsubscribe view.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        return view('unsubscribe.index', [
            'meta' => [
                'title' => __('main.unsubscribe_news') . ' | ' . $this->seoSiteName(),
                'description' => __('main.unsubscribe_news_description'),
            ],
            'applicant_form_types' => Application::getApplicationTypes(app()->getLocale()),
        ]);
    }

    /**
     * Return email unsubscribe view.
     *
     * @param Request $request
     * @return View
     */
    public function indexEmail(Request $request): View
    {
        $email_value = $request->has('email') && filter_var($request->input('email'), FILTER_VALIDATE_EMAIL)
            ? strtolower(trim($request->input('email')))
            : '';

        return view('unsubscribe.email', [
            'meta' => [
                'title' => __('main.unsubscribe_news_email') . ' | ' . $this->seoSiteName(),
                'description' => __('main.unsubscribe_news_email_description'),
            ],
            'email_value' => $email_value,
            'applicant_form_types' => Application::getApplicationTypes(app()->getLocale()),
        ]);
    }

    /**
     * Return phone unsubscribe view.
     *
     * @return View
     */
    public function indexPhone(): View
    {
        return view('unsubscribe.phone', [
            'meta' => [
                'title' => __('main.unsubscribe_news_phone') . ' | ' . $this->seoSiteName(),
                'description' => __('main.unsubscribe_news_phone_description'),
            ],
            'applicant_form_types' => Application::getApplicationTypes(app()->getLocale()),
        ]);
    }

    public function indexRc(): View
    {
        return view('unsubscribe.rc', [
            'meta' => [
                'title' => __('main.unsubscribe_calls') . ' | ' . $this->seoSiteName(),
                'description' => __('main.unsubscribe_calls_description'),
            ],
            'applicant_form_types' => Application::getApplicationTypes(app()->getLocale()),
        ]);
    }

    /**
     * Unsubscribe the consumer from Hoi e-mail project.
     *
     * @param  UnsubscribeEmailRequest $request
     * @return RedirectResponse
     */
    public function postEmail(UnsubscribeEmailRequest $request): RedirectResponse
    {
        $consumer = Consumer::query()
            ->where('email', $request->email)
            ->first();

        if (!empty(config('app.unsubscribe_not_found_consumers')) && empty($consumer->id)) {
            $this->hoi_fm->unsubscribeEmail($request->email);
            // $this->hoi_fm->unsubscribeEmailFromProjectPrefix($request->email, config('services.hoi_fm.email_project_prefix'));

            return redirect()->back()->with([
                'success' => __('main.unsubscribed_successfully'),
            ]);
        }

        /** @var Consumer $consumer */
        if (!empty($consumer->id)) {
            $this->hoi_fm->unsubscribeEmail($consumer->email);

            $consumer->update([
                'subscribed' => 0,
            ]);

            return redirect()->back()->with([
                'success' => __('main.unsubscribed_successfully'),
            ]);
        }

        return redirect()->back()->with([
            'error' => __('main.unsubscription_failed'),
        ]);
    }

    /**
     * Unsubscribe the consumer from Hoi SMS project.
     *
     * @param  UnsubscribePhoneRequest $request
     * @return RedirectResponse
     */
    public function postPhone(UnsubscribePhoneRequest $request): RedirectResponse
    {
        $consumer = Consumer::query()
            ->where('phone', $request->phone)
            ->first();

        if (!empty(config('app.unsubscribe_not_found_consumers')) && empty($consumer->id)) {
            //            $this->hoi_fm->unsubscribePhoneNumber($request->phone);
            $this->hoi_fm->unsubscribePhoneNumberFromProjectPrefix($request->phone, config('services.hoi_fm.sms_project_prefix'));

            return redirect()->back()->with([
                'success' => __('main.unsubscribed_successfully'),
            ]);
        }

        /** @var Consumer $consumer */
        if (!empty($consumer->id)) {
            $this->hoi_fm->unsubscribePhoneNumber($consumer->phone);

            $consumer->update([
                'subscribed' => 0,
            ]);

            return redirect()->back()->with([
                'success' => __('main.unsubscribed_successfully'),
            ]);
        }

        return redirect()->back()->with([
            'error' => __('main.unsubscription_failed'),
        ]);
    }

    /**
     * Unsubscribe phone from RC Project
     *
     * @param  Request $request
     * @return RedirectResponse
     */
    public function postRc(UnsubscribePhoneRequest $request): RedirectResponse
    {
        $consumer = Consumer::query()->where('phone', $request->phone)->first();

        if (!empty(config('app.unsubscribe_not_found_consumers')) && empty($consumer->id)) {
            $this->rc_service->unsubscribe($request->phone);

            return redirect()->back()->with([
                'success' => __('main.unsubscribed_successfully'),
            ]);
        }

        /** @var Consumer $consumer */
        if (!empty($consumer->id)) {
            $this->rc_service->unsubscribe($request->phone);

            $consumer->update([
                'subscribed' => 0,
            ]);

            return redirect()->back()->with([
                'success' => __('main.unsubscribed_successfully'),
            ]);
        }

        return redirect()->back()->with([
            'error' => __('main.unsubscription_failed'),
        ]);
    }
}
