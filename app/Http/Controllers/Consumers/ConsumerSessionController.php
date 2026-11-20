<?php

namespace App\Http\Controllers\Consumers;

use App\Events\ConsumerRequiresSmsVerificationCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\GetConsumerRequest;
use App\Models\Application;
use App\Models\Consumer;
use App\Models\Consumer\ConsumerSMSVerificationCode;
use App\Services\ConsumerService;
use App\Traits\WorksWithMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConsumerSessionController extends Controller
{
    use WorksWithMeta;

    public function __construct(protected ConsumerService $consumer_service)
    {
    }

    public function step1()
    {
        request()->session()->forget('consumer_id_temp');

        return view('consumers.login', [
            'meta' => $this->metaTitle(__('Login')),
            'applicant_form_types' => Application::getApplicationTypes(app()->getLocale()),
        ]);
    }

    public function postStep1(GetConsumerRequest $request)
    {
        $consumer = $this->consumer_service->getFromRequest($request);

        if (!empty($consumer->id)) {
            event(new ConsumerRequiresSmsVerificationCode($consumer, resend: false));

            $request->session()->put([
                'consumer_id_temp' => $consumer->id,
                'phone' => $consumer->phone,
                'agree_to_terms' => $request->agree_to_terms,
                'subscribed' => $request->subscribed,
            ]);

            $consumer->subscribed = $request->subscribed ?? 0;
            $consumer->save();
        }else{

        }

        return redirect()->route('consumers.login.step2');
    }

    public function step2(Request $request)
    {
        if (empty($request->session()->get('consumer_id_temp'))) {
            return redirect(route('consumers.login'))->with([
                'error' => __('The phone number is incorrect or was not found. Please ensure the phone number is entered correctly. If the problem persists, return to the home page and fill out a new application.'),
            ]);
        }

        return view('consumers.login-step2', [
            'meta' => $this->metaTitle(__('Login') . ' - #2'),
            'applicant_form_types' => Application::getApplicationTypes(app()->getLocale()),
        ]);
    }

    public function postStep2(Request $request)
    {
        if (empty($request->session()->get('consumer_id_temp'))) {
            return redirect(route('consumers.login'))->with([
                'error' => __('The phone number is incorrect or was not found. Please ensure the phone number is entered correctly. If the problem persists, return to the home page and fill out a new application.'),
            ]);
        }

        $consumer = Consumer::find((int) $request->session()->get('consumer_id_temp'));

        if (empty($consumer?->id)) {
            return redirect(route('consumers.login'))->with([
                'error' => __('The phone number is incorrect or was not found. Please ensure the phone number is entered correctly. If the problem persists, return to the home page and fill out a new application.'),
            ]);
        }

        $sms_verification_code = ConsumerSMSVerificationCode::getFromRequest(
            phone: $consumer->phone,
            verification_code: $request->verification_code
        );

        if (empty($sms_verification_code?->id)) {
            return redirect()->back()->with([
                'error' => __('Verification code invalid!'),
            ]);
        }

        Auth::guard('consumers')->login($consumer);

        $consumer?->createConsent(
            terms: (bool)$request->session()->get('agree_to_terms'),
            marketing: (bool)$request->session()->get('subscribe'),
            language: app()->getLocale(),
            ip: $request->ip(),
            user_agent: $request->userAgent()
        );

        $request->session()->forget(['consumer_id_temp', 'agreed_to_terms', 'subscribed']);

        return redirect()->intended(route('consumers.dashboard'));
    }


    public function destroy(Request $request)
    {
        Auth::guard('consumers')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/' . app()->getLocale());
    }
}
