<div id="formEditModalElement" :class="{ 'hidden': formEditModalClosed }"
    class="fixed inset-0 z-50 flex items-center justify-center hidden w-screen h-screen pt-12 pb-8"
    style="backdrop-filter: blur(3px);" x-data="{
        formEditModal: {
            typeId: {{ $section->parent_section_id ?? 0 }},
            minTermMonths: {{ $section->parent->json['slider_min_term'] ?? 0 }},
            maxTermMonths: {{ $section->parent->json['slider_max_term'] ?? 0 }},
            selectedTermMonths: {{ $application['term_months'] ?? 0 }},
            minAmount: {{ $section->parent->json['slider_min_amount'] ?? 0 }},
            maxAmount: {{ $section->parent->json['slider_max_amount'] ?? 0 }},
            selectedAmount: {{ $application['amount'] ?? 0 }},
            amountStep: {{ $section->parent->json['slider_step_amount'] ?? 0 }}
        },
    }">
    <div class="relative z-50 flex flex-col max-w-2xl max-h-full mx-2 bg-white rounded-lg"
        style="box-shadow: 16px 16px 20px 0 rgba(0, 0, 0, 0.25);">
        <a @click="formEditModalClosed = true"
            class="items-center justify-center absolute p-2.5 bg-white rounded-full hidden md:flex cursor-pointer"
            style="top: -25px; right: -40px; box-shadow: 10px 10px 20px 0 rgba(0, 0, 0, 0.25);">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                <path
                    d="M1.78922 0.294524L6 4.50513L10.189 0.316341C10.2815 0.217858 10.393 0.139075 10.5167 0.0847163C10.6404 0.0303576 10.7738 0.00154281 10.9089 0C11.1983 0 11.4757 0.114926 11.6803 0.319497C11.8849 0.524067 11.9998 0.801524 11.9998 1.09083C12.0024 1.22457 11.9776 1.35742 11.9269 1.48122C11.8763 1.60502 11.8008 1.71717 11.7053 1.81078L7.46177 5.99957L11.7053 10.2429C11.8851 10.4188 11.9905 10.657 11.9998 10.9083C11.9998 11.1976 11.8849 11.4751 11.6803 11.6796C11.4757 11.8842 11.1983 11.9991 10.9089 11.9991C10.7699 12.0049 10.6312 11.9817 10.5016 11.931C10.3721 11.8803 10.2544 11.8032 10.1562 11.7046L6 7.49401L1.80012 11.6937C1.70795 11.7889 1.59784 11.8649 1.47614 11.9173C1.35444 11.9698 1.22356 11.9976 1.09105 11.9991C0.801736 11.9991 0.524267 11.8842 0.319688 11.6796C0.115109 11.4751 0.000177493 11.1976 0.000177493 10.9083C-0.00236588 10.7746 0.0224482 10.6417 0.0730971 10.5179C0.123746 10.3941 0.199161 10.282 0.294714 10.1884L4.53822 5.99957L0.294714 1.75624C0.114921 1.58035 0.00949316 1.34217 0.000177493 1.09083C0.000177493 0.801524 0.115109 0.524067 0.319688 0.319497C0.524267 0.114926 0.801736 0 1.09105 0C1.35286 0.00327249 1.60377 0.109083 1.78922 0.294524Z"
                    fill="#081B1A" />
            </svg>
        </a>

        <div class="w-full h-full p-5 overflow-y-auto md:p-8">
            <div class="relative flex items-center justify-between w-full mb-4 md:mb-6">
                <h3 class="text-xl font-semibold leading-none text-left text-gray-800">
                    @lang('Change information in a loan application')
                </h3>
                <a @click="formEditModalClosed = true" class="absolute cursor-pointer md:hidden"
                    style="right: -4px; top: -4px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12"
                        fill="none">
                        <path
                            d="M1.78922 0.294524L6 4.50513L10.189 0.316341C10.2815 0.217858 10.393 0.139075 10.5167 0.0847163C10.6404 0.0303576 10.7738 0.00154281 10.9089 0C11.1983 0 11.4757 0.114926 11.6803 0.319497C11.8849 0.524067 11.9998 0.801524 11.9998 1.09083C12.0024 1.22457 11.9776 1.35742 11.9269 1.48122C11.8763 1.60502 11.8008 1.71717 11.7053 1.81078L7.46177 5.99957L11.7053 10.2429C11.8851 10.4188 11.9905 10.657 11.9998 10.9083C11.9998 11.1976 11.8849 11.4751 11.6803 11.6796C11.4757 11.8842 11.1983 11.9991 10.9089 11.9991C10.7699 12.0049 10.6312 11.9817 10.5016 11.931C10.3721 11.8803 10.2544 11.8032 10.1562 11.7046L6 7.49401L1.80012 11.6937C1.70795 11.7889 1.59784 11.8649 1.47614 11.9173C1.35444 11.9698 1.22356 11.9976 1.09105 11.9991C0.801736 11.9991 0.524267 11.8842 0.319688 11.6796C0.115109 11.4751 0.000177493 11.1976 0.000177493 10.9083C-0.00236588 10.7746 0.0224482 10.6417 0.0730971 10.5179C0.123746 10.3941 0.199161 10.282 0.294714 10.1884L4.53822 5.99957L0.294714 1.75624C0.114921 1.58035 0.00949316 1.34217 0.000177493 1.09083C0.000177493 0.801524 0.115109 0.524067 0.319688 0.319497C0.524267 0.114926 0.801736 0 1.09105 0C1.35286 0.00327249 1.60377 0.109083 1.78922 0.294524Z"
                            fill="#081B1A" />
                    </svg>
                </a>
            </div>
            <form id="editLoanForm" method="POST" action="{{ route('application.update-type') }}">
                @csrf
                @method('POST')

                <!-- Form fields to submit -->
                <input type="hidden" name="loantype" :value="formEditModal.typeId">
                <input type="hidden" name="term_months" id="term_months" :value="formEditModal.selectedTermMonths">
                <input type="hidden" name="step" value="{{ $step }}">

                <!-- Loan types -->
                <div class="flex flex-wrap mb-4 md:mb-6">
                    @foreach ($applicant_form_types as $key => $applicant_form_type)
                        <div class="flex flex-col py-2 px-4 rounded-full border border-[#3FAF4F] text-[#081B1A] mr-2 mb-2 cursor-pointer hover:bg-[#3faf4f]/20 text-sm"
                            :class="{ 'bg-[#3faf4f]/20': formEditModal.typeId === {{ $applicant_form_type['id'] }} }"
                            @click="formEditTypeChanged({{ $applicant_form_type['id'] }})">
                            {{ $applicant_form_type['name'] }}
                        </div>
                    @endforeach
                </div>

                <!-- Loan amount -->
                <div class="applicant-form__input-wrap">
                    <!-- Label and Current Value -->
                    <div class="flex items-center justify-between mb-3">
                        <div class="text-base font-semibold text-gray-800">@lang('main.sum_required')</div>
                        <div class="relative flex items-center text-base font-medium text-gray-800">
                            <!-- Check if currency_as_prefix is true, show currency symbol before input -->
                            @if (config('app.currency_as_prefix') == true)
                                <span class="absolute left-3 text-[#7C8381]">{{ config('app.currency_symbol') }}</span>
                            @endif

                            <!-- Visible formatted display input -->
                            <input type="text" id="amount_display"
                                :value="formatCurrency(formEditModal.selectedAmount)"
                                class="block p-1.5 px-3 font-bold text-gray-800 border focus:ring-0 focus:border-primary-normal border-[#CDD7CF] rounded-lg"
                                @if (!empty($disable_term_months)) disabled @endif>

                            <!-- Hidden real value -->
                            <input type="hidden" name="amount" id="amount" :value="formEditModal.selectedAmount">

                            <!-- Check if currency_as_prefix is false, show currency symbol after input -->
                            @if (config('app.currency_as_prefix') == false)
                                <span
                                    class="absolute right-3 text-[#7C8381]">{{ config('app.currency_symbol') }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Slider Container -->
                    <div class="mb-2">
                        <div id="applicantAmountSlider" :data-start="formEditModal.selectedAmount"
                            :data-min="formEditModal.minAmount" :data-max="formEditModal.maxAmount"
                            :data-step="formEditModal.amountStep"></div>
                    </div>

                    <!-- Min/Max Labels -->
                    <div class="flex items-center justify-between">
                        <div class="text-xs font-semibold text-zinc-500">
                            <span x-text="formatCurrency(formEditModal.minAmount)"></span>
                            {{ config('app.currency_symbol') }}
                        </div>
                        <div class="text-[13px] font-semibold leading-none text-zinc-500">
                            <span x-text="formatCurrency(formEditModal.maxAmount)"></span>
                            {{ config('app.currency_symbol') }}
                        </div>
                    </div>
                </div>

                <!-- Loan term -->
                <div class="mt-8 applicant-form__input-wrap">
                    <!-- Label and Current Value -->
                    <div class="flex items-center justify-between mb-3">
                        <div class="text-base font-semibold text-gray-800">
                            @lang('main.loan_term')
                        </div>
                        <div class="relative flex items-center text-base font-medium text-gray-800">
                            <input name="term_months" id="term_months_input" :value="formEditModal.selectedTermMonths"
                                class="block p-1.5 px-3 font-bold text-gray-800 border focus:ring-0 focus:border-primary-normal border-[#CDD7CF] rounded-lg"
                                @if (!empty($disable_term_months)) disabled @endif>
                            <span class="absolute right-3 text-[#7C8381]">
                                @lang('main.month')
                            </span>
                        </div>
                    </div>

                    <!-- Slider Container -->
                    <div class="mb-2">
                        <div id="applicantTermMonthsSlider" :data-start="formEditModal.selectedTermMonths"
                            :data-min="formEditModal.minTermMonths" :data-max="formEditModal.maxTermMonths"
                            :data-step="1"></div>
                    </div>

                    <!-- Min/Max Labels -->
                    <div class="flex items-center justify-between">
                        <div class="text-xs font-semibold text-zinc-500">
                            <span x-text="formEditModal.minTermMonths"></span> @lang('months')
                        </div>
                        <div class="text-xs font-semibold text-zinc-500">
                            <span x-text="formEditModal.maxTermMonths"></span> @lang('months')
                        </div>
                    </div>
                </div>

                <!-- Monthly payment -->
                @include('includes.applicant-form.monthly-payment')

                <!-- Submit button -->
                <div class="flex w-full pt-4 pb-0">
                    <button class="cursor-pointer applicant-form__button" type="submit" form="editLoanForm">
                        @lang('Confirm')
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal background opacity -->
    <div @click="formEditModalClosed = true" class="absolute inset-0 z-10 block w-full h-full bg-black opacity-40">
    </div>
</div>

<script>
    const formEditModalTypes = @json($applicant_form_types);
    const step = {{ $step }};

    window.formEditTypeChanged = function(typeId) {
        const editFormModal = document.getElementById('formEditModalElement');

        const bodyData = Alpine.$data(editFormModal);

        bodyData.formEditModal.typeId = typeId;

        formEditModalTypes.forEach((type) => {
            if (type.id === typeId) {
                bodyData.formEditModal.minAmount = type.amounts[0].key;
                bodyData.formEditModal.maxAmount = type.amounts[Object.keys(type.amounts).length - 2].key;
                bodyData.formEditModal.amountStep = type.amounts.step;
                bodyData.formEditModal.minTermMonths = type.term_months[0].key
                bodyData.formEditModal.maxTermMonths = type.term_months[Object.keys(type.term_months)
                    .length - 1].key;
            }
        });

        setTimeout(() => {
            window.initAmountSlider();
            window.initTermSlider();
        });
    }

    document.addEventListener('alpine:init', () => {
        // Wait until Alpine is ready
        const editFormModalData = document.getElementById('formEditModalElement');
        const editForm = document.getElementById('editLoanForm');

        // Wait until Alpine has initialized this element
        Alpine.nextTick(() => {
            const component = Alpine.$data(editFormModalData); // access x-data state

            // Watch the reactive variable
            Alpine.effect(() => {
                const isClosed = component.formEditModalClosed;

                // Remove any old hidden inputs we might have added previously, hide on show as well as on hide
                editForm.querySelectorAll('.copied-hidden-field').forEach(el => el.remove());

                if (!isClosed) {
                    if (step === 3 || step === 1) {
                        const mainForm = document.getElementById('applicant_form');

                        // Needed, to return step view with old unsaved field values
                        copyMainFormFieldsToEditFormModal(mainForm, editForm);
                    }
                }
            });
        });
    });

    function copyMainFormFieldsToEditFormModal(mainForm, childForm) {
        if (!mainForm || !childForm) {
            console.error('Form not found.');
            return;
        }

        // Select all input, select, and textarea elements
        const fields = mainForm.querySelectorAll('input:not([type="hidden"]), select, textarea');

        fields.forEach(field => {
            const name = field.name;

            if (!name) return; // skip unnamed inputs

            let value = field.value;

            // NOT USED FOR NOW, Special handling for checkboxes and radios
            if (field.type === 'checkbox' || field.type === 'radio') {
                if (!field.checked) return; // skip unchecked
                value = field.value || 'on';
            }

            // NOT USED FOR NOW, Special handling for <select multiple>
            if (field.tagName.toLowerCase() === 'select' && field.multiple) {
                Array.from(field.selectedOptions).forEach(opt => {
                    const hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = name;
                    hidden.value = opt.value;
                    hidden.classList.add('copied-hidden-field');
                    childForm.appendChild(hidden);
                });
                return;
            }

            // Create hidden input in the child form
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = name;
            hidden.value = value;
            hidden.classList.add('copied-hidden-field');
            childForm.appendChild(hidden);
        });
    }
</script>
