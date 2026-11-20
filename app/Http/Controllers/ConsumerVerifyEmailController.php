<?php

namespace App\Http\Controllers;

use App\Events\ConsumerEmailVerified;
use App\Http\Requests\Consumers\ConsumerEmailVerificationRequest;
use App\Models\Consumer;
use App\Services\OfferPage\OfferPageService;
use Illuminate\Http\RedirectResponse;

class ConsumerVerifyEmailController extends Controller
{
    public function __construct(protected OfferPageService $offer_page_service)
    {
    }

    /**
     * Mark the consumer's email address as verified.
     *
     * @param ConsumerEmailVerificationRequest $request
     * @return RedirectResponse
     */
    public function __invoke(ConsumerEmailVerificationRequest $request): RedirectResponse
    {
        $consumer = Consumer::find(request()->route('id'));
        /** @var Consumer $consumer */

        if ($consumer->hasVerifiedEmail()) {
            return $this->redirectConsumer($consumer);
        }

        if ($consumer = $consumer->markEmailAsVerified()) {
            event(new ConsumerEmailVerified($consumer));
        }

        return $this->redirectConsumer($consumer);
    }

    protected function redirectConsumer(Consumer $consumer): RedirectResponse
    {
        $link = $this->offer_page_service->buildLink($consumer, [
            'verified' => 'true',
        ]);

        $expiry_time_in_minutes = 60 * 24 * 30;

        return redirect($link)
            ->withCookie(cookie('offer_redirect', true, $expiry_time_in_minutes));

        //        $email_verified_route = app()->getLocale() . '/verified?verified=true';
        //
        //        return redirect($email_verified_route);
    }
}
