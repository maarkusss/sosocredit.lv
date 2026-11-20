<div :class="{ 'hidden': rightsOfUsageModalClosed }"
     class="fixed inset-0 z-50 flex items-center justify-center hidden w-screen h-full max-h-full pt-24 pb-8">
    <div id="rightsOfUsageModal"
         class="z-20 flex flex-col items-start justify-start w-full h-auto max-w-xl max-h-full px-6 py-3 mx-2 bg-white rounded-lg md:mx-0">
        <div class="flex flex-row justify-between items-start w-full mt-3">
            <h3 class="mb-2 sm:mb-4 text-xl sm:text-2xl font-semibold leading-none text-left text-gray-800">
                {{ __('modals.rightsofusage_line1') }}
            </h3>

            <button @click="rightsOfUsageModalClosed = true"
                    class="flex items-center justify-center">
                <svg class="w-6 h-6 text-gray-500 fill-current hover:text-gray-600">
                    <path d="M13.41 12l4.3-4.29a1.004 1.004 0 0 0-1.42-1.42L12 10.59l-4.29-4.3a1.004 1.004 0 1 0-1.42 1.42l4.3 4.29-4.3 4.29a.999.999 0 0 0 0 1.42 1 1 0 0 0 1.42 0l4.29-4.3 4.29 4.3a1.001 1.001 0 0 0 1.639-.325 1 1 0 0 0-.22-1.095L13.41 12z" />
                </svg>
            </button>
        </div>
        <div class="max-h-full overflow-y-auto">
            <p class="mb-2 text-base font-normal leading-tight text-gray-600">
                {{ __('modals.rightsofusage_line2') }}
            </p>
            <p class="mb-2 text-base font-normal leading-tight text-gray-600">
                {{ __('modals.rightsofusage_line3') }}
            </p>
            <p class="mb-2 text-base font-normal leading-tight text-gray-600">
                {{ __('modals.rightsofusage_line4') }}
            </p>
            <p class="mb-2 text-base font-normal leading-tight text-gray-600">
                {{ __('modals.rightsofusage_line5') }}
            </p>
            <p class="mb-2 text-base font-normal leading-tight text-gray-600">
                {{ __('modals.rightsofusage_line6') }}
            </p>
            <p class="mb-2 text-base font-normal leading-tight text-gray-600">
                {{ __('modals.rightsofusage_line7') }}
            </p>
            <p class="mb-2 text-base font-normal leading-tight text-gray-600">
                {{ __('modals.rightsofusage_line8') }}
            </p>
            <p class="mb-2 text-base font-normal leading-tight text-gray-600">
                {{ __('modals.rightsofusage_line9') }}
            </p>
        </div>
        <div class="flex items-center justify-center w-full pt-4 pb-0">
            <span class="landing-form__button cursor-pointer"
                  @click="rightsOfUsageModalClosed = true; rightsOfUsageChecked = true;">
                @lang("Agree")
            </span>
        </div>
    </div>
    <div @click="rightsOfUsageModalClosed = true"
         class="absolute inset-0 z-10 block w-full h-full bg-black opacity-25"></div>
</div>
