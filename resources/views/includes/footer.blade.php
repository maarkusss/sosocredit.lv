@php
    use App\Models\Block;
    use App\Models\Section;

    /** @var bool $show_important_info */
    /** @var array $applicant_form_types */
    /** @var Section $section */

    $whatsapp_url = config('app.whatsapp.msgs_url');
    $whatsapp_url =
        App::getLocale() == 'ru'
            ? $whatsapp_url . config('app.whatsapp.ru')
            : $whatsapp_url . config('app.whatsapp.lv');

@endphp

<footer class="px-6 py-8 bg-white md:py-16 md:px-14">

    <div class="grid grid-cols-1 lg:[grid-template-columns:1fr_3fr] max-w-screen-xl mx-auto">
        <div class="flex flex-col items-start justify-between order-2 lg:order-1">
            <div class="flex-col hidden gap-4 md:flex">
                <x-application-logo is_applicant_page="{{ !empty($is_applicant_page) ? 1 : 0 }}"></x-application-logo>
                <span class="block md:my-1 text-xs font-medium leading-none text-[#7C8381]">@lang('applicant_form.license_nr')</span>
                <div class="hidden md:block">
                    <div class="flex mb-4">
                        <a href="https://www.facebook.com/sosocredit.lv" target="_blank"
                            aria-label="Visit our Facebook page">
                            <svg class="w-10 h-10 md:w-8 md:h-8" viewBox="0 0 32 32" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M6.93004 3C4.19901 3 2 5.19901 2 7.93003V25.0699C2 27.801 4.19901 30 6.93004 30H16.2197V19.4447H13.4286V15.6444H16.2197V12.3977C16.2197 9.84686 17.8689 7.50478 21.6678 7.50478C23.206 7.50478 24.3434 7.65244 24.3434 7.65244L24.2539 11.2013C24.2539 11.2013 23.0939 11.1903 21.8281 11.1903C20.4582 11.1903 20.2385 11.8215 20.2385 12.8694V15.6445H24.3628L24.183 19.4447H20.2385V30H24.07C26.801 30 29 27.801 29 25.07V7.93006C29 5.19904 26.801 3.00003 24.07 3.00003H6.93001L6.93004 3Z"
                                    fill="#297935" />
                            </svg>
                        </a>
                        <a href="https://www.instagram.com/sosocredit.lv" target="_blank"
                            aria-label="Visit our Instagram page">
                            <svg class="w-10 h-10 md:w-8 md:h-8" viewBox="0 0 32 32" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M15.4999 13.5387C14.9142 13.5387 14.3416 13.7124 13.8547 14.0378C13.3677 14.3632 12.9881 14.8256 12.764 15.3667C12.5399 15.9079 12.4812 16.5033 12.5955 17.0777C12.7097 17.6521 12.9918 18.1798 13.4059 18.5939C13.8201 19.0081 14.3477 19.2901 14.9221 19.4044C15.4966 19.5186 16.092 19.46 16.6331 19.2359C17.1742 19.0117 17.6367 18.6322 17.9621 18.1452C18.2875 17.6582 18.4612 17.0857 18.4612 16.5C18.4603 15.7149 18.1481 14.9621 17.5929 14.4069C17.0377 13.8518 16.285 13.5395 15.4999 13.5387Z"
                                    fill="#297935" />
                                <path
                                    d="M23.2059 3H7.79194C6.25652 3.00227 4.78464 3.61322 3.69893 4.69893C2.61322 5.78464 2.00227 7.25652 2 8.79194V24.2059C2.00182 25.7416 2.61256 27.214 3.6983 28.3002C4.78404 29.3863 6.25618 29.9976 7.79194 30H23.2059C24.742 29.9981 26.2146 29.387 27.3008 28.3008C28.387 27.2146 28.9981 25.742 29 24.2059V8.79194C28.9976 7.25618 28.3863 5.78404 27.3002 4.6983C26.214 3.61256 24.7416 3.00182 23.2059 3ZM15.5 22.4792C14.3174 22.4792 13.1614 22.1285 12.1781 21.4715C11.1949 20.8145 10.4285 19.8807 9.97595 18.7881C9.52339 17.6956 9.40499 16.4934 9.63569 15.3335C9.8664 14.1737 10.4359 13.1083 11.2721 12.2721C12.1083 11.4359 13.1737 10.8664 14.3335 10.6357C15.4934 10.405 16.6956 10.5234 17.7881 10.9759C18.8807 11.4285 19.8145 12.1949 20.4715 13.1781C21.1285 14.1614 21.4792 15.3174 21.4792 16.5C21.4773 18.0852 20.8467 19.6049 19.7258 20.7258C18.6049 21.8467 17.0852 22.4773 15.5 22.4792ZM23.0513 11.0869C22.63 11.0867 22.2182 10.9616 21.868 10.7274C21.5178 10.4932 21.2449 10.1604 21.0837 9.77117C20.9226 9.3819 20.8805 8.9536 20.9628 8.5404C21.045 8.12721 21.2479 7.74767 21.5458 7.44977C21.8437 7.15186 22.2233 6.94896 22.6365 6.86672C23.0496 6.78447 23.478 6.82657 23.8672 6.9877C24.2565 7.14883 24.5893 7.42174 24.8235 7.77195C25.0577 8.12216 25.1828 8.53394 25.183 8.95524C25.1825 9.52045 24.9577 10.0624 24.5581 10.462C24.1584 10.8617 23.6165 11.0864 23.0513 11.0869Z"
                                    fill="#297935" />
                            </svg>
                        </a>
                        <a href="https://www.tiktok.com/@sosocredit.lv" target="_blank"
                            aria-label="Visit our TikTok page">
                            <svg class="w-10 h-10 md:w-8 md:h-8" viewBox="0 0 32 32" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M6.90909 3C5.60712 3 4.35847 3.51721 3.43784 4.43784C2.51721 5.35847 2 6.60712 2 7.90909V25.0909C2 26.3929 2.51721 27.6415 3.43784 28.5622C4.35847 29.4828 5.60712 30 6.90909 30H24.0909C25.3929 30 26.6415 29.4828 27.5622 28.5622C28.4828 27.6415 29 26.3929 29 25.0909V7.90909C29 6.60712 28.4828 5.35847 27.5622 4.43784C26.6415 3.51721 25.3929 3 24.0909 3H6.90909ZM15.9173 7.29545H18.8468C18.8468 9.25909 20.506 11.4805 23.0318 11.4805V14.4087C21.2927 14.2885 19.8065 14.0037 18.8468 13.1532V20.6838C18.8468 22.7763 17.1924 25.7045 13.4087 25.7045C8.78682 25.7045 7.96945 21.5195 7.96945 20.2653C7.96945 19.011 8.70582 14.826 13.826 14.826V17.7555C12.487 17.472 10.8977 18.5913 10.8977 20.2653C10.8977 22.3578 12.1532 22.7763 13.4087 22.7763C14.6642 22.7763 15.9185 21.5208 15.9185 20.2653L15.9173 7.29545Z"
                                    fill="#297935" />
                            </svg>
                        </a>
                        <a href="https://www.youtube.com/@sosocredit_lv" target="_blank"
                            aria-label="Visit our Youtube channel">
                            <svg class="w-10 h-10 md:w-8 md:h-8" viewBox="0 0 32 32" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M18.7869 16.8522L13.9739 19.4348C13.8565 19.4348 13.7391 19.5522 13.7391 19.5522C13.6217 19.5522 13.5043 19.5522 13.5043 19.4348C13.3869 19.3174 13.2695 19.2 13.2695 18.9652V13.6826C13.2695 13.4478 13.3869 13.3304 13.5043 13.213C13.6217 13.0957 13.8565 13.0957 14.0913 13.213L18.9043 15.913C19.1391 16.0304 19.2565 16.1478 19.2565 16.3826C19.0217 16.5 18.9043 16.7348 18.7869 16.8522Z"
                                    fill="#297935" />
                                <path
                                    d="M24.5391 3H6.46087C3.99565 3 2 4.99565 2 7.46087V25.5391C2 28.0043 3.99565 30 6.46087 30H24.5391C27.0043 30 29 28.0043 29 25.5391V7.46087C29 4.99565 27.0043 3 24.5391 3ZM25.0087 17.2043C25.0087 18.7304 24.7739 20.2565 24.7739 20.2565C24.7739 20.3739 24.5391 21.7826 23.9522 22.487C23.2478 23.3087 22.3087 23.4261 21.9565 23.4261H21.8391C19.3739 23.6609 15.5 23.6609 15.5 23.6609C15.2652 23.6609 10.8043 23.6609 9.39565 23.4261C9.27826 23.4261 9.27826 23.4261 9.16087 23.4261C8.6913 23.3087 7.75217 23.3087 7.04783 22.487C6.34348 21.7826 6.1087 20.4913 6.1087 20.3739C6.1087 20.2565 5.87391 18.8478 5.87391 17.2043V15.7957C5.9913 14.2696 6.1087 12.7435 6.1087 12.7435C6.1087 12.6261 6.34348 11.2174 6.93043 10.513C7.63478 9.6913 8.57391 9.57391 8.92609 9.57391H9.04348C11.6261 9.33913 15.5 9.33913 15.5 9.33913C15.5 9.33913 19.3739 9.33913 21.8391 9.57391H21.9565C22.4261 9.57391 23.2478 9.6913 23.9522 10.513C24.6565 11.2174 24.8913 12.5087 24.8913 12.6261C24.8913 12.7435 25.1261 14.1522 25.1261 15.6783V17.2043H25.0087Z"
                                    fill="#297935" />
                                <path d="M14.2087 18.0261L17.2609 16.2652L14.2087 14.6217V18.0261Z" fill="#297935" />
                            </svg>
                        </a>
                    </div>
                    <x-application-copyright-line is_applicant_page="{{ !empty($is_applicant_page) ? 1 : 0 }}"
                        include_developer="1"></x-application-copyright-line>
                </div>
            </div>
            <div class="md:hidden">
                <div class="flex mb-4">
                    <a href="https://www.facebook.com/sosocredit.lv" target="_blank"
                        aria-label="Visit our Facebook page">
                        <svg class="w-10 h-10 md:w-8 md:h-8" viewBox="0 0 32 32" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M6.93004 3C4.19901 3 2 5.19901 2 7.93003V25.0699C2 27.801 4.19901 30 6.93004 30H16.2197V19.4447H13.4286V15.6444H16.2197V12.3977C16.2197 9.84686 17.8689 7.50478 21.6678 7.50478C23.206 7.50478 24.3434 7.65244 24.3434 7.65244L24.2539 11.2013C24.2539 11.2013 23.0939 11.1903 21.8281 11.1903C20.4582 11.1903 20.2385 11.8215 20.2385 12.8694V15.6445H24.3628L24.183 19.4447H20.2385V30H24.07C26.801 30 29 27.801 29 25.07V7.93006C29 5.19904 26.801 3.00003 24.07 3.00003H6.93001L6.93004 3Z"
                                fill="#297935" />
                        </svg>
                    </a>
                    <a href="https://www.instagram.com/sosocredit.lv" target="_blank"
                        aria-label="Visit our Instagram page">
                        <svg class="w-10 h-10 md:w-8 md:h-8" viewBox="0 0 32 32" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M15.4999 13.5387C14.9142 13.5387 14.3416 13.7124 13.8547 14.0378C13.3677 14.3632 12.9881 14.8256 12.764 15.3667C12.5399 15.9079 12.4812 16.5033 12.5955 17.0777C12.7097 17.6521 12.9918 18.1798 13.4059 18.5939C13.8201 19.0081 14.3477 19.2901 14.9221 19.4044C15.4966 19.5186 16.092 19.46 16.6331 19.2359C17.1742 19.0117 17.6367 18.6322 17.9621 18.1452C18.2875 17.6582 18.4612 17.0857 18.4612 16.5C18.4603 15.7149 18.1481 14.9621 17.5929 14.4069C17.0377 13.8518 16.285 13.5395 15.4999 13.5387Z"
                                fill="#297935" />
                            <path
                                d="M23.2059 3H7.79194C6.25652 3.00227 4.78464 3.61322 3.69893 4.69893C2.61322 5.78464 2.00227 7.25652 2 8.79194V24.2059C2.00182 25.7416 2.61256 27.214 3.6983 28.3002C4.78404 29.3863 6.25618 29.9976 7.79194 30H23.2059C24.742 29.9981 26.2146 29.387 27.3008 28.3008C28.387 27.2146 28.9981 25.742 29 24.2059V8.79194C28.9976 7.25618 28.3863 5.78404 27.3002 4.6983C26.214 3.61256 24.7416 3.00182 23.2059 3ZM15.5 22.4792C14.3174 22.4792 13.1614 22.1285 12.1781 21.4715C11.1949 20.8145 10.4285 19.8807 9.97595 18.7881C9.52339 17.6956 9.40499 16.4934 9.63569 15.3335C9.8664 14.1737 10.4359 13.1083 11.2721 12.2721C12.1083 11.4359 13.1737 10.8664 14.3335 10.6357C15.4934 10.405 16.6956 10.5234 17.7881 10.9759C18.8807 11.4285 19.8145 12.1949 20.4715 13.1781C21.1285 14.1614 21.4792 15.3174 21.4792 16.5C21.4773 18.0852 20.8467 19.6049 19.7258 20.7258C18.6049 21.8467 17.0852 22.4773 15.5 22.4792ZM23.0513 11.0869C22.63 11.0867 22.2182 10.9616 21.868 10.7274C21.5178 10.4932 21.2449 10.1604 21.0837 9.77117C20.9226 9.3819 20.8805 8.9536 20.9628 8.5404C21.045 8.12721 21.2479 7.74767 21.5458 7.44977C21.8437 7.15186 22.2233 6.94896 22.6365 6.86672C23.0496 6.78447 23.478 6.82657 23.8672 6.9877C24.2565 7.14883 24.5893 7.42174 24.8235 7.77195C25.0577 8.12216 25.1828 8.53394 25.183 8.95524C25.1825 9.52045 24.9577 10.0624 24.5581 10.462C24.1584 10.8617 23.6165 11.0864 23.0513 11.0869Z"
                                fill="#297935" />
                        </svg>
                    </a>
                    <a href="https://www.tiktok.com/@sosocredit.lv" target="_blank" aria-label="Visit our TikTok page">
                        <svg class="w-10 h-10 md:w-8 md:h-8" viewBox="0 0 32 32" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M6.90909 3C5.60712 3 4.35847 3.51721 3.43784 4.43784C2.51721 5.35847 2 6.60712 2 7.90909V25.0909C2 26.3929 2.51721 27.6415 3.43784 28.5622C4.35847 29.4828 5.60712 30 6.90909 30H24.0909C25.3929 30 26.6415 29.4828 27.5622 28.5622C28.4828 27.6415 29 26.3929 29 25.0909V7.90909C29 6.60712 28.4828 5.35847 27.5622 4.43784C26.6415 3.51721 25.3929 3 24.0909 3H6.90909ZM15.9173 7.29545H18.8468C18.8468 9.25909 20.506 11.4805 23.0318 11.4805V14.4087C21.2927 14.2885 19.8065 14.0037 18.8468 13.1532V20.6838C18.8468 22.7763 17.1924 25.7045 13.4087 25.7045C8.78682 25.7045 7.96945 21.5195 7.96945 20.2653C7.96945 19.011 8.70582 14.826 13.826 14.826V17.7555C12.487 17.472 10.8977 18.5913 10.8977 20.2653C10.8977 22.3578 12.1532 22.7763 13.4087 22.7763C14.6642 22.7763 15.9185 21.5208 15.9185 20.2653L15.9173 7.29545Z"
                                fill="#297935" />
                        </svg>
                    </a>
                    <a href="https://www.youtube.com/@sosocredit_lv" target="_blank"
                        aria-label="Visit our Youtube channel">
                        <svg class="w-10 h-10 md:w-8 md:h-8" viewBox="0 0 32 32" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M18.7869 16.8522L13.9739 19.4348C13.8565 19.4348 13.7391 19.5522 13.7391 19.5522C13.6217 19.5522 13.5043 19.5522 13.5043 19.4348C13.3869 19.3174 13.2695 19.2 13.2695 18.9652V13.6826C13.2695 13.4478 13.3869 13.3304 13.5043 13.213C13.6217 13.0957 13.8565 13.0957 14.0913 13.213L18.9043 15.913C19.1391 16.0304 19.2565 16.1478 19.2565 16.3826C19.0217 16.5 18.9043 16.7348 18.7869 16.8522Z"
                                fill="#297935" />
                            <path
                                d="M24.5391 3H6.46087C3.99565 3 2 4.99565 2 7.46087V25.5391C2 28.0043 3.99565 30 6.46087 30H24.5391C27.0043 30 29 28.0043 29 25.5391V7.46087C29 4.99565 27.0043 3 24.5391 3ZM25.0087 17.2043C25.0087 18.7304 24.7739 20.2565 24.7739 20.2565C24.7739 20.3739 24.5391 21.7826 23.9522 22.487C23.2478 23.3087 22.3087 23.4261 21.9565 23.4261H21.8391C19.3739 23.6609 15.5 23.6609 15.5 23.6609C15.2652 23.6609 10.8043 23.6609 9.39565 23.4261C9.27826 23.4261 9.27826 23.4261 9.16087 23.4261C8.6913 23.3087 7.75217 23.3087 7.04783 22.487C6.34348 21.7826 6.1087 20.4913 6.1087 20.3739C6.1087 20.2565 5.87391 18.8478 5.87391 17.2043V15.7957C5.9913 14.2696 6.1087 12.7435 6.1087 12.7435C6.1087 12.6261 6.34348 11.2174 6.93043 10.513C7.63478 9.6913 8.57391 9.57391 8.92609 9.57391H9.04348C11.6261 9.33913 15.5 9.33913 15.5 9.33913C15.5 9.33913 19.3739 9.33913 21.8391 9.57391H21.9565C22.4261 9.57391 23.2478 9.6913 23.9522 10.513C24.6565 11.2174 24.8913 12.5087 24.8913 12.6261C24.8913 12.7435 25.1261 14.1522 25.1261 15.6783V17.2043H25.0087Z"
                                fill="#297935" />
                            <path d="M14.2087 18.0261L17.2609 16.2652L14.2087 14.6217V18.0261Z" fill="#297935" />
                        </svg>
                    </a>
                </div>
                <x-application-copyright-line is_applicant_page="{{ !empty($is_applicant_page) ? 1 : 0 }}"
                    include_developer="1"></x-application-copyright-line>
                <div class="block mt-2 text-xs font-medium leading-none text-[#7C8381]">@lang('applicant_form.license_nr')</div>
            </div>
        </div>
        <div
            class="md:grid order-1 lg:order-2 gap-4 mb-4 md:mb-8 lg:grid-cols-[auto_auto_1fr_3fr] md:grid-cols-2 md:gap-8 lg:gap-0">
            @if (!empty($footer_loan_types))
                <div class="pt-4 pb-0 lg:px-4 lg:py-0" x-data="{ open: false }">
                    <div class="mb-4 text-left sm:mb-8">
                        <div
                            class="block mb-8 md:mb-4 text-base font-bold text-neutral-900 md:text-[#3FAF4F] leading-tight">
                            <div class="flex items-center justify-between">
                                <span>@lang('main.loans')</span>
                                <button class="hidden md:hidden" @click="open = !open">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg" :class="{ 'rotate-45': open }"
                                        class="transition-transform duration-200">
                                        <path
                                            d="M19 13h-6v6a1 1 0 0 1-2 0v-6H5a1 1 0 0 1 0-2h6V5a1 1 0 0 1 2 0v6h6a1 1 0 0 1 0 2z"
                                            fill="#081B1A" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        @if (!empty($footer_loan_types))
                            <ul class="-my-1" id="footer_loan_types">
                                @foreach ($footer_loan_types as $applicant_form_type)
                                    <li class="flex justify-start pb-2">
                                        <a class="text-neutral-900 text-sm font-medium leading-tight @if (!empty($is_applicant_page)) hover:text-loan-dark @else link_primary @endif"
                                            href="{{ $applicant_form_type['url'] }}">
                                            {{ $applicant_form_type['name'] }}
                                        </a>
                                    </li>
                                @endforeach
                                <li class="flex justify-start pb-2">
                                    <a class="text-neutral-900 text-sm font-medium leading-tight @if (!empty($is_applicant_page)) hover:text-loan-dark @else link_primary @endif"
                                        href="{{ app()->getLocale() == 'ru' ? 'https://www.sosocredit.lv/ru/kredit-dlja-hozjaistvujushchih-subjektov' : 'https://www.sosocredit.lv/lv/kredits-saimnieciskas-darbibas-veicejiem' }}">
                                        {{ __('Kredīts uzņēmumam') }}
                                    </a>
                                </li>
                            </ul>
                        @endif
                    </div>
                </div>
            @endif

            <div class="pt-4 pb-0 lg:px-4 lg:py-0" x-data="{ open: false }">
                <div class="block mb-8 md:mb-4 text-base font-bold text-neutral-900 md:text-[#3FAF4F] leading-tight">
                    <div class="flex items-center justify-between">
                        <span>@lang('main.information')</span>
                        <button class="hidden md:hidden" @click="open = !open">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg" :class="{ 'rotate-45': open }"
                                class="transition-transform duration-200">
                                <path
                                    d="M19 13h-6v6a1 1 0 0 1-2 0v-6H5a1 1 0 0 1 0-2h6V5a1 1 0 0 1 2 0v6h6a1 1 0 0 1 0 2z"
                                    fill="#081B1A" />
                            </svg>
                        </button>
                    </div>
                </div>

                <ul class="mb-6 -my-1" id="footer_information">
                    @foreach (Section::active()->where('lang', app()->getLocale())->where('parent_section_id', null)->where('main_page', 0)->where(function ($q) {
            $q->whereNull('template');
            $q->orWhere('template', Section::TEMPLATE_PRIVACY_POLICY);
            $q->orWhere('template', Section::TEMPLATE_FAQS);
            $q->orWhere('template', 'news');
        })->select(['name', 'route_name', 'common_name'])->orderBy('order', 'ASC')->get() as $section)
                        @if (!in_array($section->common_name, ['offer-expired', 'lottery']))
                            <li class="pb-2">
                                <a href="{{ route('section', ['name' => $section->route_name]) }}"
                                    class="text-sm font-medium leading-tight text-neutral-900 block md:max-w-[225px] max-w-full
                                    @if (!empty($is_applicant_page)) hover:text-loan-dark @else link_primary @endif">
                                    {{ $section->name }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                    <li class="pb-2">
                        <a class="text-sm font-medium leading-tight text-neutral-900 block md:max-w-[225px] max-w-full link_primary"
                            href="{{ route('unsubscribe') }}">
                            @lang('main.unsubscribe_from_marketing')
                        </a>
                    </li>
                </ul>
            </div>
            <div class="pt-4 pb-0 lg:px-4 lg:py-0" x-data="{ open: false }">
                <div class="mb-4 text-left sm:mb-8">
                    <div
                        class="block mb-8 md:mb-4 text-base font-bold text-neutral-900 md:text-[#3FAF4F] leading-tight">
                        <div class="flex items-center justify-between">
                            <span>@lang('main.contacts')</span>
                            <button class="hidden md:hidden" @click="open = !open">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg" :class="{ 'rotate-45': open }"
                                    class="transition-transform duration-200">
                                    <path
                                        d="M19 13h-6v6a1 1 0 0 1-2 0v-6H5a1 1 0 0 1 0-2h6V5a1 1 0 0 1 2 0v6h6a1 1 0 0 1 0 2z"
                                        fill="#081B1A" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="flex flex-col gap-1 mb-4 text-sm font-medium leading-tight text-neutral-900">
                        <div class="flex items-start gap-4 mt-1">
                            <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M20.2347 14.4405C18.8878 14.4405 17.5652 14.2298 16.312 13.8157C15.6979 13.6062 14.9429 13.7984 14.5681 14.1833L12.0945 16.0507C9.22571 14.5193 7.45861 12.7528 5.94818 9.90556L7.76059 7.49633C8.23147 7.02608 8.40037 6.33915 8.19802 5.69462C7.78208 4.43478 7.57079 3.11289 7.57079 1.76539C7.57084 0.791943 6.7789 0 5.80552 0H1.76532C0.791941 0 0 0.791943 0 1.76533C0 12.9229 9.07715 22 20.2347 22C21.2081 22 22 21.2081 22 20.2347V16.2058C21.9999 15.2324 21.208 14.4405 20.2347 14.4405Z"
                                    fill="#3FAF4F" />
                            </svg>
                            <div class="flex flex-col">
                                <span class="block font-semibold md:mb-1">@lang('Phone')</span>
                                <a href="tel:{{ str_replace(' ', '', config('app.broker_phone')) }}"
                                    class="block md:mb-1">
                                    {{ config('app.broker_phone') }}
                                </a>
                                <a href="{{ $whatsapp_url }}" target="_blank" rel="noopener noreferrer"
                                    class="block md:mb-1 text-[#297935] font-medium hover:underline" aria-label="WhatsApp">
                                    @lang('applicant_form.available_in_whatsapp')
                                </a>
                            </div>
                        </div>
                        <br />
                        <div class="flex items-start gap-4 mt-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M2.90151 21.1719H21.8948C22.9439 21.1719 23.7964 20.3194 23.7964 19.2703V9.77563L14.3543 16.074C13.1632 16.8644 11.6332 16.8644 10.442 16.074L1 9.77563V19.2703C1 20.3194 1.85242 21.1719 2.90151 21.1719Z"
                                    fill="#3FAF4F" />
                                <path
                                    d="M21.8948 5H2.90151C1.85242 5 1 5.85242 1 6.90151V7.82677L11.3454 14.7225C11.9866 15.1524 12.8134 15.1524 13.4546 14.7225L23.8 7.82677V6.90518C23.7964 5.85605 22.9439 5 21.8948 5Z"
                                    fill="#3FAF4F" />
                            </svg>
                            <div class="flex flex-col">
                                <span class="block font-semibold md:mb-1">@lang('E-mail')</span>
                                <span class="block md:mb-1">{{ config('mail.contact_email') }}</span>
                                <span class="block md:mb-1">{{ config('app.broker_email') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4 pb-0 lg:px-4 lg:py-0" x-data="{ open: false }">
                <div class="mb-4 text-left sm:mb-8">
                    <div
                        class="block mb-8 md:mb-4 text-base font-bold text-neutral-900 md:text-[#3FAF4F] leading-tight">
                        <div class="flex items-center justify-between">
                            <span>@lang('main.working_time')</span>
                            <button class="hidden md:hidden" @click="open = !open">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg" :class="{ 'rotate-45': open }"
                                    class="transition-transform duration-200">
                                    <path
                                        d="M19 13h-6v6a1 1 0 0 1-2 0v-6H5a1 1 0 0 1 0-2h6V5a1 1 0 0 1 2 0v6h6a1 1 0 0 1 0 2z"
                                        fill="#081B1A" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="flex flex-col gap-1 mb-4 text-sm font-medium leading-tight text-neutral-900">
                        <div class="flex items-start gap-4 mt-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_2136_13265)">
                                    <path
                                        d="M23.9999 16.2421C23.9999 13.2639 22.2913 10.6059 19.7324 9.31299C19.653 15.0233 15.0233 19.653 9.31299 19.7324C10.6059 22.2913 13.2639 23.9999 16.2421 23.9999C17.6384 23.9999 18.9963 23.628 20.1895 22.9214L23.966 23.966L22.9214 20.1895C23.628 18.9963 23.9999 17.6384 23.9999 16.2421Z"
                                        fill="#3FAF4F" />
                                    <path
                                        d="M18.3281 9.16406C18.3281 4.1109 14.2172 0 9.16406 0C4.1109 0 0 4.1109 0 9.16406C0 10.8109 0.438354 12.4144 1.27075 13.8221L0.0336914 18.2943L4.50604 17.0574C5.91376 17.8898 7.51721 18.3281 9.16406 18.3281C14.2172 18.3281 18.3281 14.2172 18.3281 9.16406ZM7.75781 7.03125H6.35156C6.35156 5.48035 7.61316 4.21875 9.16406 4.21875C10.715 4.21875 11.9766 5.48035 11.9766 7.03125C11.9766 7.81842 11.6433 8.57501 11.062 9.10675L9.86719 10.2003V11.2969H8.46094V9.58099L10.1125 8.06927C10.4077 7.79919 10.5703 7.4306 10.5703 7.03125C10.5703 6.2558 9.93951 5.625 9.16406 5.625C8.38861 5.625 7.75781 6.2558 7.75781 7.03125ZM8.46094 12.7031H9.86719V14.1094H8.46094V12.7031Z"
                                        fill="#3FAF4F" />
                                </g>
                                <defs>
                                    <clipPath id="clip0_2136_13265">
                                        <rect width="24" height="24" fill="white" />
                                    </clipPath>
                                </defs>
                            </svg>
                            <div class="flex flex-col">
                                <span class="block font-semibold md:mb-1">@lang('applicant_form.customer_service')</span>
                                <span class="block md:mb-1">@lang('applicant_form.monday_till_friday'): 8:30-19:00</span>
                                <span class="block md:mb-1">@lang('applicant_form.saturday'): 9:00-18:00</span>
                                <span class="block md:mb-1">@lang('applicant_form.sunday'): @lang('applicant_form.free')</span>
                            </div>
                        </div>
                        <br />

                        <div class="flex items-start gap-4 mt-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_2144_13344)">
                                    <path
                                        d="M12 4.125C10.4425 4.125 8.91993 4.58686 7.62489 5.45218C6.32985 6.31749 5.32049 7.5474 4.72445 8.98637C4.12841 10.4253 3.97246 12.0087 4.27632 13.5363C4.58018 15.0639 5.3302 16.4671 6.43154 17.5685C7.53288 18.6698 8.93607 19.4198 10.4637 19.7237C11.9913 20.0275 13.5747 19.8716 15.0136 19.2756C16.4526 18.6795 17.6825 17.6702 18.5478 16.3751C19.4131 15.0801 19.875 13.5575 19.875 12C19.8727 9.91212 19.0423 7.91041 17.566 6.43405C16.0896 4.9577 14.0879 4.12728 12 4.125ZM15.585 14.715C15.5152 14.8034 15.4263 14.875 15.3251 14.9243C15.2238 14.9737 15.1127 14.9995 15 15C14.8304 15.0014 14.6657 14.9429 14.5349 14.835L11.5349 12.585C11.4466 12.5151 11.3751 12.4262 11.3258 12.325C11.2765 12.2237 11.2506 12.1126 11.25 12V6.75C11.25 6.55109 11.329 6.36032 11.4697 6.21967C11.6103 6.07902 11.8011 6 12 6C12.1989 6 12.3897 6.07902 12.5303 6.21967C12.671 6.36032 12.75 6.55109 12.75 6.75V11.64L15.4651 13.665C15.5432 13.7252 15.6083 13.8004 15.6568 13.8862C15.7052 13.972 15.736 14.0667 15.7472 14.1646C15.7583 14.2626 15.7497 14.3617 15.7219 14.4563C15.694 14.5508 15.6475 14.6388 15.585 14.715Z"
                                        fill="#3FAF4F" />
                                    <path
                                        d="M12 0.75C9.77497 0.75 7.59989 1.4098 5.74984 2.64597C3.89979 3.88213 2.45785 5.63914 1.60636 7.69481C0.754875 9.75048 0.532087 12.0125 0.966171 14.1948C1.40025 16.3771 2.47171 18.3816 4.04505 19.955C5.61839 21.5283 7.62295 22.5998 9.80524 23.0338C11.9875 23.4679 14.2495 23.2451 16.3052 22.3936C18.3609 21.5422 20.1179 20.1002 21.354 18.2502C22.5902 16.4001 23.25 14.225 23.25 12C23.2464 9.01743 22.0599 6.15805 19.951 4.04906C17.842 1.94006 14.9826 0.753628 12 0.75ZM12 21.425C10.1359 21.425 8.31369 20.8722 6.76376 19.8366C5.21383 18.801 4.00581 17.329 3.29245 15.6068C2.5791 13.8846 2.39245 11.9895 2.75612 10.1613C3.11978 8.33301 4.01742 6.65364 5.33553 5.33553C6.65364 4.01742 8.33301 3.11978 10.1613 2.75611C11.9895 2.39245 13.8846 2.57909 15.6068 3.29245C17.329 4.0058 18.801 5.21383 19.8366 6.76376C20.8722 8.31369 21.425 10.1359 21.425 12C21.4226 14.4989 20.4288 16.8948 18.6618 18.6618C16.8948 20.4288 14.4989 21.4226 12 21.425Z"
                                        fill="#3FAF4F" />
                                </g>
                                <defs>
                                    <clipPath id="clip0_2144_13344">
                                        <rect width="24" height="24" fill="white" />
                                    </clipPath>
                                </defs>
                            </svg>
                            <div class="flex flex-col">
                                <span class="block font-semibold md:mb-1">@lang('applicant_form.applying_for_a_loan_24/7')</span>
                                <span class="block md:mb-1">@lang('applicant_form.you_can_apply_for_a_loan_24/7')</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if ($show_important_info || !empty($footer_loan_types))
        <section class="max-w-screen-xl pt-8 mx-auto" id="important-info">
            <p class="text-xs text-sm font-medium text-[#7C8381]">
                {{ Block::getValue('home_footer') }}
            </p>
        </section>
    @endif
</footer>
