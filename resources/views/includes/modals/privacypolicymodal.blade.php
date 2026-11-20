@php
use App\Models\Section;

$privacy_policy_title = '';
$privacy_policy_text = '';
$privacy_policy = Section::active()
    ->where('lang', app()->getLocale())
    ->where('template', Section::TEMPLATE_PRIVACY_POLICY)
    ->select([
        'id',
        'title',
        'text',
    ])->first();

if (!empty($privacy_policy->id)) {
    $privacy_policy_title = $privacy_policy->title;
    $privacy_policy_text = $privacy_policy->text;
}

@endphp

@if (!empty($mode) && $mode === 'vuejs')
    <div class="fixed inset-0 z-50 flex items-center justify-center w-screen h-full max-h-full pt-24 pb-8">
@else
    <div :class="{ 'hidden': privacyPolicyModalClosed }"
         class="fixed inset-0 z-50 flex items-center justify-center hidden w-screen h-full max-h-full pt-24 pb-8">
@endif

    <div id="rightsOfUsageModal"
         class="z-20 flex flex-col items-start justify-start w-full h-auto max-w-xl max-h-full p-4 sm:p-6 mx-2 bg-white rounded-lg md:mx-0">
        <div class="flex items-center justify-end w-full mb-4">
            @if (!empty($mode) && $mode === 'vuejs')
                <button @click="toggleModalPrivacyPolicy()"
                        class="flex items-center justify-center">
            @else
                <button @click="privacyPolicyModalClosed = true"
                        class="flex items-center justify-center">
            @endif
                <svg class="w-6 h-6 text-gray-500 fill-current hover:text-gray-600">
                    <path d="M13.41 12l4.3-4.29a1.004 1.004 0 0 0-1.42-1.42L12 10.59l-4.29-4.3a1.004 1.004 0 1 0-1.42 1.42l4.3 4.29-4.3 4.29a.999.999 0 0 0 0 1.42 1 1 0 0 0 1.42 0l4.29-4.3 4.29 4.3a1.001 1.001 0 0 0 1.639-.325 1 1 0 0 0-.22-1.095L13.41 12z" />
                </svg>
            </button>
        </div>
        <div class="max-h-full overflow-y-auto pr-2">
            <h3 class="mb-2 sm:mb-4 text-xl sm:text-2xl font-semibold leading-none text-left text-gray-800">
                {{ $privacy_policy_title }}
            </h3>
            {!! $privacy_policy_text !!}
        </div>
        <div class="flex items-center justify-center w-full pt-4 pb-0">
            <span class="landing-form__button cursor-pointer"
                  @click="privacyPolicyModalClosed = true; privacyPolicyChecked = true;">
                @lang("Agree")
            </span>
        </div>
    </div>
    <div @click="privacyPolicyModalClosed = true"
         class="absolute inset-0 z-10 block w-full h-full bg-black opacity-25"></div>
</div>
