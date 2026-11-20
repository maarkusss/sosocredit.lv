<section class="bg-white section_narrower" id="how-to-unify-loans">
    <div class="section__content">
        <div class="section__content__heading-wrapper">
            <h1 class="section__content__heading">
                {{ $section->json['block3_title'] }}
            </h1>
        </div>
        {{-- STEPS --}}
        <div class="flex flex-wrap">
            <div class="flex flex-col items-center justify-start flex-shrink-0 w-full md:mt-8 sm:w-1/4">
                <div class="flex items-center justify-center w-32 mr-4 h-28 md:h-32">
                    <div>
                        <div class="pr-3 text-sm font-medium leading-tight pb-14">01</div>
                    </div>
                    <img src="{{ asset('images/website.svg') }}" alt="{{ __('alts.website') }}" width="80" height="80">
                    <div>
                        <img class="hidden lg:block md:absolute md:pl-16" src="{{ asset('images/line.svg') }}" width="161" height="2"
                            alt="{{ __('alts.line_horizontal') }}">
                    </div>
                </div>
                <div class="relative px-1 lg:px-4">
                    <h2 class="section__content__step__subtitle">
                        @lang('applicant_form.fill_enquiry')</h2>
                    <p class="section__content__step__desc">@lang('applicant_form.fill_enquiry_desc')</p>
                </div>
                <img class="block pt-4 sm:hidden" src="{{ asset('images/line_vertical.svg') }}" width="2" height="46"
                    alt="{{ __('alts.line_vertical') }}">
            </div>

            <div class="flex flex-col items-center justify-start flex-shrink-0 w-full md:mt-8 sm:w-1/4">
                <div class="flex items-center justify-center w-32 mr-4 h-28 md:h-32">
                    <div>
                        <div class="pr-3 text-sm font-medium leading-tight pb-14">02</div>
                    </div>
                    <img src="{{ asset('images/to-do-list.svg') }}" alt="{{ __('alts.to_do_list') }}" width="80" height="80">
                    <div>
                        <img class="hidden lg:block md:absolute md:pl-16" src="{{ asset('images/line.svg') }}" width="161" height="2"
                            alt="{{ __('alts.line_horizontal') }}">
                    </div>
                </div>
                <div class="relative px-1 lg:px-4">
                    <h2 class="mx-auto section__content__step__subtitle">
                        @lang('applicant_form.get_offer')</h2>
                    <p class="section__content__step__desc">@lang('applicant_form.get_offer_desc')</p>
                </div>
                <img class="block pt-4 sm:hidden" src="{{ asset('images/line_vertical.svg') }}" width="2" height="46"
                    alt="{{ __('alts.line_vertical') }}">
            </div>
            <div class="flex flex-col items-center justify-start flex-shrink-0 w-full md:mt-8 sm:mt-0 sm:w-1/4">
                <div class="flex items-center justify-center w-32 mr-4 h-28 md:h-32">
                    <div>
                        <div class="pr-3 text-sm font-medium leading-tight pb-14">03</div>
                    </div>
                    <img src="{{ asset('images/contract.svg') }}" alt="{{ __('alts.contract') }}" width="80" height="80">
                    <div>
                        <img class="hidden lg:block md:absolute md:pl-16" src="{{ asset('images/line.svg') }}" width="161" height="2"
                            alt="{{ __('alts.line_horizontal') }}">
                    </div>
                </div>
                <div class="relative px-1 lg:px-4">
                    <h2 class="mx-auto section__content__step__subtitle">
                        @lang('applicant_form.sign_contract')</h2>
                    <p class="section__content__step__desc">@lang('applicant_form.sign_contract_desc')</p>
                </div>
                <img class="block pt-4 sm:hidden" src="{{ asset('images/line_vertical.svg') }}" width="2" height="46"
                    alt="{{ __('alts.line_vertical') }}">
            </div>
            <div class="flex flex-col items-center justify-start flex-shrink-0 w-full md:mt-8 sm:mt-0 sm:w-1/4">
                <div class="flex items-center justify-center w-32 mr-4 h-28 md:h-32">
                    <div>
                        <div class="pr-3 text-sm font-medium leading-tight pb-14">04</div>
                    </div>
                    <img src="{{ asset('images/wallet.svg') }}" alt="{{ __('alts.wallet') }}" width="80" height="80">
                </div>
                <div class="relative px-1 lg:px-4">
                    <h2 class="mx-auto section__content__step__subtitle">
                        @lang('applicant_form.get_money')</h2>
                    <p class="section__content__step__desc">@lang('applicant_form.get_money_desc')</p>
                </div>
            </div>
        </div>
    </div>
</section>
