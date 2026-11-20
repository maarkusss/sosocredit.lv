<?php

namespace App\Http\Controllers\Consumers;

use App\Events\ConsumerEmailVerified;
use App\Http\Controllers\Controller;
use App\Http\Requests\Consumers\ConsumerEmailVerificationRequest;
use App\Models\Consumer;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
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
            return redirect($this->redirectTo());
        }

        if ($consumer->markEmailAsVerified()) {
            event(new ConsumerEmailVerified($consumer));
        }

        return redirect($this->redirectTo());
    }

    public function redirectTo(): string
    {
        return app()->getLocale() . '/verified?verified=true';
    }
}
