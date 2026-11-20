@php
    use App\Models\Section;

    /** @var Section $section */
    /** @var array $applicant_form_types */
    /** @var int $step */

@endphp

@extends('layouts.public')

@section('head')
    {{--    @if ($step === 3) --}}
    {{--        <script src="https://cdn.by.wonderpush.com/sdk/1.1/wonderpush-loader.min.js" async></script> --}}
    {{--        <script> --}}
    {{--            window.WonderPush = window.WonderPush || []; --}}
    {{--            WonderPush.push(["init", { --}}
    {{--                webKey: "6edc40a02794fa4dc3b96a0d50ae3704638280508a6c617008547f8eb511b6e1", --}}
    {{--            }]); --}}
    {{--        </script> --}}
    {{--    @endif --}}

    @if ($step === 3 && config('app.address_autocomplete_enabled'))
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA9uNpPIOny_Do4BmlHOtXdk0XhKpferPg&libraries=places" async
            defer></script>
    @endif
@endsection

@section('content')

    @if (!empty($step))
        @if ($step === 5)
            @include('includes.applicant-form.step5-waiting')
            {{--            @include('includes.applicant-form.step5') --}}
        @elseif ($step === 4)
            @include('includes.applicant-form.step4')
        @elseif ($step === 3)
            @include('includes.applicant-form.step3')
        @elseif ($step === 2)
            @include('includes.applicant-form.step2')
        @else
            @include('includes.applicant-form.step1')
        @endif
    @endif

@endsection

@section('javascript')
    @include('includes.modals.applicant_form_privacypolicymodal')
    @include('includes.modals.applicant_form_recieveoffersmodal')
    @include('includes.modals.applicant_form_edit_modal')

    <script>
        const applicantForm = document.getElementById('applicant_form'),
            submitButton = document.getElementById("submit_button");

        window.onunload = function() {};

        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                if (applicantForm && submitButton) {
                    let loadingSpinner = applicantForm.querySelector(".loading-spinner");

                    if (submitButton) {
                        submitButton.disabled = false;
                    }

                    if (loadingSpinner) {
                        loadingSpinner.classList.add("hidden");
                    }
                }
            }
        });

        if (applicantForm && submitButton) {
            loadingSpinner = applicantForm.querySelector(".loading-spinner");
            applicantForm.addEventListener('submit', (e) => {
                if (submitButton) {
                    submitButton.disabled = true;
                }

                if (loadingSpinner) {
                    loadingSpinner.classList.remove("hidden");
                }

                return true;
            });
        }
    </script>

    @if (!empty($step) && $step === 3)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const personalCodeInput = document.getElementById('personal_code');

                const daySelect = document.querySelector('[name="dob_day"]');
                const monthSelect = document.querySelector('[name="dob_month"]');
                const yearSelect = document.querySelector('[name="dob_year"]');
                const ageInput = document.getElementById('age');

                function setAge(day, month, year) {
                    ageInput.value =
                        `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                }

                function clearDob() {
                    daySelect.value = '';
                    monthSelect.value = '';
                    yearSelect.value = '';
                    ageInput.value = '';
                }

                /**
                 * Old Latvian personal code parser (DOB encoded)
                 * Returns null for new format (32xxxx-xxxxx)
                 */
                function parseLatvianPersonalCode(code) {
                    if (!code) return null;

                    const normalized = code.replace('-', '');

                    // New format – DOB not encoded
                    if (normalized.startsWith('32')) {
                        return null;
                    }

                    if (!/^\d{11}$/.test(normalized)) {
                        return null;
                    }

                    const dd = parseInt(normalized.substring(0, 2), 10);
                    const mm = parseInt(normalized.substring(2, 4), 10);
                    const yy = parseInt(normalized.substring(4, 6), 10);
                    const centuryCode = normalized.substring(6, 7);

                    let century;
                    switch (centuryCode) {
                        case '0':
                            century = 1800;
                            break;
                        case '1':
                            century = 1900;
                            break;
                        case '2':
                            century = 2000;
                            break;
                        default:
                            return null;
                    }

                    const year = century + yy;

                    if (mm < 1 || mm > 12 || dd < 1 || dd > 31) {
                        return null;
                    }

                    return {
                        day: dd,
                        month: mm,
                        year
                    };
                }

                /**
                 * Fill DOB from personal code (ONLY for old format)
                 */
                function fillDobFromPersonalCode() {
                    const result = parseLatvianPersonalCode(personalCodeInput.value);

                    // If new personal code → DO NOT clear manual DOB
                    if (!result) {
                        return;
                    }

                    daySelect.value = result.day;
                    monthSelect.value = result.month;
                    yearSelect.value = result.year;

                    setAge(result.day, result.month, result.year);
                }

                /**
                 * Fill age when DOB is selected manually
                 */
                function updateAgeFromSelects() {
                    if (daySelect.value && monthSelect.value && yearSelect.value) {
                        setAge(daySelect.value, monthSelect.value, yearSelect.value);
                    }
                }

                // Events
                personalCodeInput.addEventListener('blur', fillDobFromPersonalCode);

                daySelect.addEventListener('change', updateAgeFromSelects);
                monthSelect.addEventListener('change', updateAgeFromSelects);
                yearSelect.addEventListener('change', updateAgeFromSelects);

                // Initial load (edit / back)
                if (personalCodeInput.value) {
                    fillDobFromPersonalCode();
                }

                updateAgeFromSelects();
            });
        </script>
    @endif

    @if (!empty($step) && $step === 4)
        <script src="/js/selector.min.js"></script>
        <script>
            function openFileBrowser() {
                const file_handler = document.getElementById("attachPdfFileHandler");
                file_handler.click();
            }

            function submitPdfFile(e) {
                const filename_container = document.getElementById("attachPdfFileName");
                if (e && e.name) {
                    filename_container.innerText = e.name;
                }
            }

            let pdf_upload_input = document.getElementById("attachPdfFileHandler");
            if (pdf_upload_input) {
                pdf_upload_input.onchange = function() {
                    const input = this.files[0],
                        filename_container = document.getElementById("attachPdfFileName");
                    if (input && input.name) {
                        filename_container.innerText = input.name;
                    }
                };
            }

            const institutionModalContent = document.getElementById("institution-modal-content");
            if (institutionModalContent) {
                let institutionList = [{
                        "id": "CITADELE_PARXLV22",
                        "name": "Citadele",
                        "bic": "PARXLV22",
                        "transaction_total_days": "730",
                        "countries": [
                            "LV"
                        ],
                        "logo": "{{ asset('/images/logos/svg/citadele.svg') }}"
                    },
                    {
                        "id": "LUMINOR_RIKOLV2X",
                        "name": "Luminor",
                        "bic": "RIKOLV2X",
                        "transaction_total_days": "365",
                        "countries": [
                            "LV"
                        ],
                        "logo": "{{ asset('/images/logos/svg/luminor.svg') }}"
                    },
                    {
                        "id": "SEB_UNLALV2X",
                        "name": "SEB",
                        "bic": "UNLALV2X",
                        "transaction_total_days": "730",
                        "countries": [
                            "LV"
                        ],
                        "logo": "{{ asset('/images/logos/svg/seb.svg') }}"
                    },
                    {
                        "id": "SWEDBANK_HABALV22",
                        "name": "Swedbank",
                        "bic": "HABALV22",
                        "transaction_total_days": "730",
                        "countries": [
                            "LV"
                        ],
                        "logo": "{{ asset('/images/logos/svg/swedbank.svg') }}"
                    },
                    {
                        "id": "REVOLUT_REVOLT21",
                        "name": "Revolut",
                        "bic": "REVOLT21XXX",
                        "transaction_total_days": "730",
                        "countries": [
                            "LV"
                        ],
                        "logo": "{{ asset('/images/logos/svg/revolut.svg') }}"
                    },
                    {
                        "id": "WISE_EEA_TRWIGB22",
                        "name": "Wise",
                        "bic": "TRWIGB22XXX",
                        "transaction_total_days": "730",
                        "countries": [
                            "LV"
                        ],
                        "logo": "{{ asset('/images/logos/svg/wise.svg') }}"
                    },
                    {
                        "id": "INDEXO_IDXOLV22",
                        "name": "INDEXO",
                        "bic": "IDXOLV22",
                        "transaction_total_days": "730",
                        "countries": [
                            "LV"
                        ],
                        "logo": "{{ asset('/images/logos/svg/indexo.svg') }}"
                    },
                    {{-- { --}}
                    {{--    "id": "PAYSERA_EVIULT21XXX", --}}
                    {{--    "name": "Paysera", --}}
                    {{--    "bic": "EVIULT21XXX", --}}
                    {{--    "transaction_total_days": "90", --}}
                    {{--    "countries": [ --}}
                    {{--        "LV" --}}
                    {{--    ], --}}
                    {{--    "logo": "{{ asset('/images/logos/svg/paysera.svg') }}" --}}
                    {{-- }, --}} {
                        "id": "BLUEORANGE_CBBRLV22",
                        "name": "BluOr",
                        "bic": "CBBRLV22",
                        "transaction_total_days": "90",
                        "countries": [
                            "LV"
                        ],
                        "logo": "{{ asset('/images/logos/svg/bluor.svg') }}"
                    },

                ];

                @if (!app()->isProduction())
                    institutionList.push({
                        "id": "SANDBOXFINANCE_SFIN0000",
                        "name": "Test",
                        "bic": "SANDBOXFINANCE_SFIN0000",
                        "transaction_total_days": "730",
                        "countries": [
                            "LV"
                        ],
                        "logo": "{{ asset('/images/logos/png/test.jpg') }}"
                    })
                @endif

                const config = {
                    redirectUrl: "{{ config('app.url') . '/lv/paterina-kredits' }}",
                    logoUrl: "{{ config('app.url') . '/logo.svg' }}",
                    text: '',
                    countryFilter: false,
                    styles: {
                        // Primary
                        // fontFamily: 'https://fonts.googleapis.com/css2?family=Roboto&display=swap',
                        fontSize: '16',
                        // backgroundColor: '#F2F2F2',
                        textColor: '#1B2021',
                        headingColor: '#222',
                        linkColor: '#3F52E5',
                        // Modal
                        modalTextColor: '#1B2021',
                        modalBackgroundColor: '#fff',
                    }
                };

                institutionSelector(institutionList, 'institution-modal-content', config);
            }

            const institutionListOptions = Array.from(document.querySelectorAll('.ob-list-institution > a')),
                institutionIdInput = document.getElementById('institution_id');

            if (institutionListOptions) {
                institutionListOptions.forEach((institution) => {
                    institution.addEventListener('click', (e) => {
                        e.preventDefault()
                        const institutionId = institution.getAttribute('data-institution');

                        if (institutionIdInput) {
                            institutionIdInput.value = institutionId;
                        }

                        if (applicantForm) {
                            applicantForm.submit();
                        }

                        if (submitButton) {
                            submitButton.disabled = true;
                        }
                    });
                });
            }
        </script>
    @endif
@endsection
