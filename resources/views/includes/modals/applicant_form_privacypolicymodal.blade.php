@php
    use App\Models\Section;

    $privacy_policy_title = '';
    $privacy_policy_text = '';
    $privacy_policy = Section::active()
        ->where('lang', app()->getLocale())
        ->where('template', Section::TEMPLATE_PRIVACY_POLICY)
        ->select(['id', 'title', 'text'])
        ->first();

    if (!empty($privacy_policy->id)) {
        $privacy_policy_title = $privacy_policy->title;
        $privacy_policy_text = $privacy_policy->text;
    }

@endphp

<div :class="{ 'hidden': privacyPolicyModalClosed }"
    class="fixed inset-0 z-50 flex items-center justify-center hidden w-screen h-full max-h-full pt-24 pb-8">
    <div id="rightsOfUsageModal"
        class="z-20 flex flex-col items-start justify-start min-w-[200px] md:w-full h-auto max-w-xl max-h-full px-3 sm:px-4 lg:px-6 py-2 sm:py-3 mx-2 bg-white rounded-lg md:mx-0">
        <div class="flex flex-row items-start justify-between w-full mt-3">
            <h3 class="mb-2 text-xl font-semibold leading-none text-left text-gray-800 sm:mb-4 sm:text-2xl">
                {{ $privacy_policy_title }}
            </h3>

            <button @click="privacyPolicyModalClosed = true" class="flex items-center justify-center">
                <svg class="w-6 h-6 text-gray-500 fill-current hover:text-gray-600">
                    <path
                        d="M13.41 12l4.3-4.29a1.004 1.004 0 0 0-1.42-1.42L12 10.59l-4.29-4.3a1.004 1.004 0 1 0-1.42 1.42l4.3 4.29-4.3 4.29a.999.999 0 0 0 0 1.42 1 1 0 0 0 1.42 0l4.29-4.3 4.29 4.3a1.001 1.001 0 0 0 1.639-.325 1 1 0 0 0-.22-1.095L13.41 12z" />
                </svg>
            </button>
        </div>
        <div
            class="w-full max-h-full pr-2 overflow-x-hidden overflow-y-auto text-sm prose-h2:mt-4 prose-h3:mt-4 prose-h1:mt-4 prose-h4:mt-4 prose-p:pt-2 prose-h2:font-bold prose-p:font-normal prose-h2:text-base sm:text-base">
            {!! $privacy_policy_text !!}
        </div>
        <div class="flex items-center justify-center w-full pt-4 pb-0">
            <span class="cursor-pointer applicant-form__button"
                @click="privacyPolicyModalClosed = true; privacyPolicyChecked = true;">
                @lang('Informed')
            </span>
        </div>
    </div>
    <div @click="privacyPolicyModalClosed = true" class="absolute inset-0 z-10 block w-full h-full bg-black opacity-25">
    </div>
</div>
