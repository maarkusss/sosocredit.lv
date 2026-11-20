@extends('layouts.public', ['meta_robots' => 'noindex, follow'])

@section('content')
    <main>
        <section class="flex justify-center w-full px-3 bg-loan-normal" id="slogan-top" style="height: 1px;"></section>
        <section class="py-5 section sm:py-8">
            <div class="section__content">
                <div class="flex flex-col items-center justify-center">
                    <div class="flex flex-col flex-shrink-0 w-full max-w-screen-md min-h-96">
                        <div class="w-full p-6 bg-white rounded shadow-xl sm:p-8">
                            <div class="mx-auto">
                                @include('admincp.includes.infobox')
                                <x-auth-session-status class="mb-4" :status="session('status')" />

                                <!-- Validation Errors -->
                                <x-auth-validation-errors class="mb-4" :errors="$errors" />

                                <div class="applicant-form">
                                    <form method="POST" action="{{ route('consumers.login.step2') }}">
                                        @csrf

                                        <div
                                            class="flex flex-col items-center justify-center h-full px-4 border rounded bg-loan-normal bg-opacity-10 border-loan-dark">

                                            <div
                                                class="w-20 h-20 flex items-center justify-center my-5 bg-[#C6EECB] rounded-full">
                                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#297935" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                                    <polyline points="22,6 12,13 2,6"></polyline>
                                                </svg>
                                            </div>

                                            {{-- Email verification section --}}
                                            <div class="grid grid-cols-1 gap-4 my-4" id="email_verification_container">
                                                <span class="block text-xl font-medium text-center text-gray-800">
                                                    @lang('Uz Jūsu e-pastu')
                                                    <span class="font-semibold text-loan-normal" id="email_verification_email">
                                                        {{ $email ?? session('temp_email') }}
                                                    </span>
                                                    @lang('nosūtīts verifikācijas kods. Ievadiet to, lai apstiprinātu un turpinātu.')
                                                </span>

                                                <label class="block mt-4 mb-4">
                                                    <input
                                                        class="w-full px-4 py-3 text-center text-2xl tracking-widest font-bold text-black bg-white border border-gray-300 rounded focus:border-loan-dark focus:ring-loan-dark"
                                                        id="email_verification_input" name="verification_code"
                                                        type="text" inputmode="numeric" autocomplete="one-time-code"
                                                        pattern="[0-9]*" minlength="6" maxlength="6"
                                                        placeholder="••••••" required>
                                                </label>

                                                <div x-data="{
                                                    countdown: 60,
                                                    timer: null,
                                                    init() { this.startCountdown(); },
                                                    startCountdown() {
                                                        this.countdown = 60;
                                                        if (this.timer) clearInterval(this.timer);
                                                        this.timer = setInterval(() => {
                                                            if (this.countdown > 0) {
                                                                this.countdown--;
                                                            } else {
                                                                clearInterval(this.timer);
                                                            }
                                                        }, 1000);
                                                    },
                                                    resendCode() {
                                                        if (this.countdown > 0) return;
                                                        this.startCountdown();
                                                        fetch('{{ route('consumers.login.resend-code') }}', {
                                                            method: 'POST',
                                                            headers: {
                                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                                'Accept': 'application/json',
                                                                'Content-Type': 'application/json'
                                                            }
                                                        });
                                                    }
                                                }">
                                                    <div class="flex flex-col items-center justify-center gap-2">
                                                        <button type="submit"
                                                            class="w-full px-6 py-3 font-semibold text-white rounded bg-loan-normal hover:bg-loan-dark transition-colors">
                                                            @lang('Apstiprināt un ienākt')
                                                        </button>

                                                        <button type="button" @click="resendCode()"
                                                            :disabled="countdown > 0"
                                                            class="mt-2 text-sm text-gray-600 hover:text-loan-dark disabled:opacity-50 disabled:cursor-not-allowed">
                                                            <span x-show="countdown > 0">@lang('Nosūtīt kodu atkārtoti pēc') (<span x-text="countdown"></span>s)</span>
                                                            <span x-show="countdown === 0" class="underline font-medium text-loan-normal">@lang('Nosūtīt kodu atkārtoti')</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="hidden md:block h-60"></div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
