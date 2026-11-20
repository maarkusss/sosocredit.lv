<?php

namespace App\Http\Controllers\Consumers;

use App\Http\Controllers\Controller;
use App\Mail\SendEmailVerificationCodeMail;
use App\Models\Application;
use App\Models\Consumer;
use App\Models\Consumer\ConsumerEmailVerificationCode;
use App\Services\ConsumerService;
use App\Traits\WorksWithMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ConsumerSessionController extends Controller
{
    use WorksWithMeta;

    public function __construct(protected ConsumerService $consumer_service)
    {
    }

    public function step1()
    {
        request()->session()->forget(['consumer_id_temp', 'temp_email']);

        return view('consumers.login', [
            'meta' => $this->metaTitle(__('Login')),
            'applicant_form_types' => Application::getApplicationTypes(app()->getLocale()),
        ]);
    }

    public function postStep1(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:4',
            'agree_to_terms' => 'required',
        ]);

        $email = strtolower(trim($request->email));
        $consumer = Consumer::where('email', $email)->first();

        if (!$consumer) {
            return redirect()->back()->withInput()->withErrors([
                'email' => __('Consumer with this email does not exist. Please fill out an application first.'),
            ]);
        }

        // Ja patērētājam parole jau ir uzstādīta, pārbaudām to
        if (!empty($consumer->password) && !Hash::check($request->password, $consumer->password)) {
            return redirect()->back()->withInput()->withErrors([
                'password' => __('Incorrect password!'),
            ]);
        }

        // Ja patērētājam parole vēl nav saglabāta, uzstādām ievadīto paroli
        if (empty($consumer->password)) {
            $consumer->password = Hash::make($request->password);
            $consumer->save();
        }

        // Izveidojam un nosūtām 6-ciparu kodu uz e-pastu
        $verificationCode = ConsumerEmailVerificationCode::generateForEmail($email);

        try {
            Mail::to($email)->send(new SendEmailVerificationCodeMail($verificationCode->code));
        } catch (\Throwable $e) {
            Log::error('[ConsumerSessionController] Failed to send email verification code: ' . $e->getMessage());
        }

        $request->session()->put([
            'consumer_id_temp' => $consumer->id,
            'temp_email' => $email,
            'agree_to_terms' => $request->agree_to_terms,
            'subscribed' => $request->subscribed,
        ]);

        return redirect()->route('consumers.login.step2');
    }

    public function step2(Request $request)
    {
        if (empty($request->session()->get('consumer_id_temp'))) {
            return redirect(route('consumers.login'))->with([
                'error' => __('Please enter your email and password to log in.'),
            ]);
        }

        return view('consumers.login-step2', [
            'meta' => $this->metaTitle(__('Login') . ' - #2'),
            'applicant_form_types' => Application::getApplicationTypes(app()->getLocale()),
            'email' => $request->session()->get('temp_email'),
        ]);
    }

    public function postStep2(Request $request)
    {
        if (empty($request->session()->get('consumer_id_temp'))) {
            return redirect(route('consumers.login'))->with([
                'error' => __('Session expired. Please log in again.'),
            ]);
        }

        $request->validate([
            'verification_code' => 'required|string',
        ]);

        $consumer = Consumer::find((int) $request->session()->get('consumer_id_temp'));

        if (empty($consumer?->id)) {
            return redirect(route('consumers.login'))->with([
                'error' => __('Consumer not found.'),
            ]);
        }

        $verified = ConsumerEmailVerificationCode::verify($consumer->email, $request->verification_code);

        if (!$verified) {
            return redirect()->back()->with([
                'error' => __('Verification code invalid or expired!'),
            ]);
        }

        // Atzīmējam e-pastu kā apstiprinātu
        $consumer->markEmailAsVerified();

        Auth::guard('consumers')->login($consumer);

        $consumer->createConsent(
            terms: (bool) $request->session()->get('agree_to_terms'),
            marketing: (bool) $request->session()->get('subscribed'),
            language: app()->getLocale(),
            ip: $request->ip(),
            user_agent: $request->userAgent()
        );

        $request->session()->forget(['consumer_id_temp', 'temp_email', 'agree_to_terms', 'subscribed']);

        return redirect()->intended(route('consumers.dashboard'));
    }

    public function resendCode(Request $request)
    {
        $consumerId = $request->session()->get('consumer_id_temp');
        if (!$consumerId) {
            return response()->json(['success' => false, 'message' => 'Session expired'], 400);
        }

        $consumer = Consumer::find($consumerId);
        if (!$consumer) {
            return response()->json(['success' => false, 'message' => 'Consumer not found'], 404);
        }

        $verificationCode = ConsumerEmailVerificationCode::generateForEmail($consumer->email);

        try {
            Mail::to($consumer->email)->send(new SendEmailVerificationCodeMail($verificationCode->code));
        } catch (\Throwable $e) {
            Log::error('[ConsumerSessionController] Resend email failed: ' . $e->getMessage());
        }

        return response()->json(['success' => true]);
    }

    public function destroy(Request $request)
    {
        Auth::guard('consumers')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/' . app()->getLocale());
    }
}
