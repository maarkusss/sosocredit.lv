<?php

namespace App\Http\Controllers;

use App\Events\ConsumerVisitedOfferLink;
use App\Models\Consumer;
use App\Services\ConsumerService;
use App\Services\OfferPage\OfferPageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;

class ConsumerTokenRedirectController extends Controller
{
    public function __construct(protected ConsumerService $consumer_service, protected OfferPageService $offer_page_service)
    {
    }

    /**
     * Redirect a consumer based on hoi token.
     *
     * @param  string|null $hoi_token
     * @return RedirectResponse
     */
    public function index(string $hoi_token = null): RedirectResponse
    {
        if (!$hoi_token) {
            $hoi_token = request()->cookie('hoi_token') ?? '';
        }

        $consumer = $this->consumer_service->getByHoiToken($hoi_token);

        if ($consumer) {
            if (!empty($consumer->lang) && in_array($consumer->lang, available_locales())) {
                App::setLocale($consumer->lang);
            }

            if (!$this->isAutoRedirect()) {
                $consumer = $this->consumer_service->rotateHoiToken($consumer);

                event(new ConsumerVisitedOfferLink($consumer));
            }

            $link_params = $this->extendLinkParamsIfRequired($consumer);

            $link = $this->offer_page_service->buildLink($consumer, $link_params);

            $expiry_time_in_minutes = 60 * 24 * 30;

            return redirect($link)
                ->withCookie(cookie('offer_redirect', true, $expiry_time_in_minutes));
        }

        return redirect('/' . default_locale() . '/offer');
    }

    protected function isAutoRedirect(): bool
    {
        return request()->has('is_button');
    }

    protected function extendLinkParamsIfRequired(Consumer $consumer): array
    {
        $link_params = [];

        if ($this->isAutoRedirect()) {
            if (config('app.verify_email') && !$consumer->hasVerifiedEmail() && $consumer->isSubscribedToEmailsAndSms()) {
                $link_params['verification_email_sent'] = 'true';
                $html = __('main.to_your_email_address');
                if ($consumer->email && filter_var($consumer->email, FILTER_VALIDATE_EMAIL)) {
                    $html .= ' <span class="font-semibold text_primary">' . $consumer->email . '</span> ';
                }
                $html .= __('main.sent_email_confirmation_link_confirm_to_continue');
                session()->flash('verification_email_sent_html', $html);
            } elseif (config('app.verify_phone') && !$consumer->hasVerifiedPhone() && $consumer->isSubscribedToEmailsAndSms()) {
                $link_params['verification_sms_sent'] = 'true';
                $html = __('main.to_your_phone_number');
                if ($consumer->phone && is_numeric($consumer->phone)) {
                    $html .= ' <span class="font-semibold text_primary">' . $consumer->email . '</span> ';
                }
                $html .= __('main.sent_message_with_verification_code_input_to_continue');
                session()->flash('verification_sms_sent_html', $html);
            }
        }

        return $link_params;
    }
}
