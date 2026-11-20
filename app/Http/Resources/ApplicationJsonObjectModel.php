<?php

namespace App\Http\Resources;

use App\Models\Application;
use App\Models\Section;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationJsonObjectModel extends JsonResource
{
    public function toArray($request): array
    {
        $section = Section::find($this->section_id);
    
        $previous = Application::where('consumer_id', $this->consumer_id)
            ->where('id', '!=', $this->id)
            ->where('completion_percentage', '>=', 70)
            ->latest()
            ->first();
    
        $isBusiness = in_array(
            $section?->common_name,
            ['kredits-uznemumam'],
            true
        );
    
        $isMortgage = in_array(
            $section?->common_name,
            ['kredits-pret-kilu'],
            true
        );
    
        $isCarMortgage = in_array(
            $section?->common_name,
            ['kredits-pret-auto-kilu'],
            true
        );
    
        return [
            'id' => $this->id,
            'credit_type' => $section?->common_name,
            'credit_type_name' => $section?->name ?? '',
            'credit_amount' => (float) $this->amount,
            'credit_term' => (int) $this->term_months,
            'contact_phone_number' => (string) $this->phone,
            'contact_email' => $this->email,
            'created_at' => $this->created_at?->format('d.m.Y'),
            'updated_at' => $this->updated_at?->format('d.m.Y'),
            'status' => $this->additional['status'] ?? 'pending',
            'lgp_form_fields' => $this->phone
                ? $this->buildFormFields(
                    $previous,
                    $isBusiness,
                    $isMortgage,
                    $isCarMortgage
                )
                : [],
            'is_gocardless_service_available' => !$isBusiness,
            'is_bank_account_statement_upload_enabled' => false,
            'offers' => $this->additional['offers'] ?? [],
        ];
    }

    private function buildFormFields(
        ?Application $previous = null,
        bool $isBusiness = false,
        bool $isMortgage = false,
        bool $isCarMortgage = false
    ): array {
        $val = fn(string $field) =>
            $this->{$field} ?? $previous?->{$field} ?? null;
    
        if ($isBusiness) {
            return [
                $this->inputText(
                    'first_name',
                    __('First name'),
                    $val('first_name'),
                    true
                ),
                $this->inputText(
                    'last_name',
                    __('Last name'),
                    $val('last_name'),
                    true
                ),
                $this->inputText(
                    'personal_code',
                    __('Personal code'),
                    $val('personal_code'),
                    true,
                    12,
                    '123456-12345'
                ),
                $this->inputText(
                    'company_name',
                    __('Company Name'),
                    $val('company_name'),
                    true
                ),
                $this->inputText(
                    'company_reg_nr',
                    __('Company Reg. Nr.'),
                    $val('company_reg_nr'),
                    false
                ),
            ];
        }
    
        $fields = [
            $this->inputText(
                'first_name',
                __('First name'),
                $val('first_name'),
                true
            ),
            $this->inputText(
                'last_name',
                __('Last name'),
                $val('last_name'),
                true
            ),
            $this->inputText(
                'personal_code',
                __('Personal code'),
                $val('personal_code'),
                true,
                12,
                '123456-12345'
            ),
            $this->dateField(
                'age',
                __('Date of birth'),
                $val('age')
            ),
            $this->selectField(
                'employment_status',
                __('Employment status'),
                Application::getEmploymentStatuses(),
                $val('employment_status'),
                false
            ),
            $this->selectField(
                'education',
                __('Education level'),
                Application::getEducationLevels(),
                $val('education'),
                false
            ),
            $this->inputNumber(
                'salary',
                __('Netto monthly income'),
                $val('salary'),
                true,
                0,
                99999,
                '€'
            ),
            $this->inputNumber(
                'monthly_debit',
                __('Monthly debit'),
                $val('monthly_debit'),
                true,
                0,
                99999,
                '€'
            ),
        ];
    
        if ($isMortgage) {
            $fields[] = $this->inputText(
                'cadastral_number',
                __('Cadastral Number'),
                $val('cadastral_number'),
                true
            );
        } else {
            $fields[] = $this->selectField(
                'has_real_property',
                __('Real property'),
                [
                    '0' => __('No'),
                    '1' => __('Yes'),
                ],
                $this->booleanValue('has_real_property', $previous) ?? '0',
                true,
                __('Real property tip')
            );
        
            $fields[] = $this->inputText(
                'cadastral_number',
                __('Cadastral Number'),
                $val('cadastral_number'),
                true,
                null,
                null,
                null,
                [
                    'operator' => 'and',
                    'conditions' => [
                        [
                            'field' => 'has_real_property',
                            'values' => ['1'],
                        ],
                    ],
                ]
            );
        }
    
        if ($isCarMortgage) {
            $fields[] = $this->inputText(
                'car_number',
                __('Car Number'),
                $val('car_number'),
                true
            );
    
        } else {
            $fields[] = $this->selectField(
                'has_car',
                __('Has car'),
                [
                    '0' => __('No'),
                    '1' => __('Yes'),
                ],
                $this->booleanValue('has_car', $previous) ?? '0',
                true
            );
        
            $fields[] = $this->inputText(
                'car_number',
                __('Car Number'),
                $val('car_number'),
                true,
                null,
                null,
                null,
                [
                    'operator' => 'and',
                    'conditions' => [
                        [
                            'field' => 'has_car',
                            'values' => ['1'],
                        ],
                    ],
                ]
            );
        }
    
        return array_merge($fields, [
            $this->inputText(
                'address_street',
                __('Address'),
                $val('address_street'),
                true,
                63
            ),
            $this->inputText(
                'address_city',
                __('City'),
                $val('address_city'),
                true,
                31
            ),
            $this->inputText(
                'address_zipcode',
                __('Zipcode') . ' (LV-****)',
                $val('address_zipcode'),
                true,
                7,
                'LV-'
            ),
            $this->selectField(
                'marital_status',
                __('Marital status'),
                Application::getMaritalStatuses(),
                $val('marital_status'),
                false
            ),
            $this->inputNumber(
                'dependants',
                __('Dependants'),
                $val('dependants'),
                true,
                0,
                99
            ),
            $this->selectField(
                'is_political_person',
                __('applicant_form.is_political_person'),
                [
                    '0' => __('No'),
                    '1' => __('Yes'),
                ],
                $this->booleanValue('is_political_person', $previous),
                true,
                __('applicant_form.is_political_person_desc')
            ),
            $this->inputText(
                'disbursement_iban',
                __('IBAN to receive money to'),
                $val('disbursement_iban'),
                false,
                45
            ),
        ]);
    }

    private function inputText(
        string $id,
        string $title,
        mixed $value,
        bool $mandatory,
        ?int $maxLength = null,
        ?string $placeholder = null,
        ?string $tooltip = null,
        ?array $dependsOn = null
    ): array {
        $field = [
            'id' => $id,
            'title' => $title,
            'type' => 'input_text',
            'input_keyboard_type' => 'default',
            'mandatory' => $mandatory,
            'placeholder' => $placeholder,
            'tooltip_text' => $tooltip,
            'depends_on' => $dependsOn,
            'current_value' => $value !== null ? (string) $value : null,
        ];
    
        if ($maxLength !== null) {
            $field['max_length'] = $maxLength;
        }
    
        return $field;
    }

    private function inputNumber(string $id, string $title, mixed $value, bool $mandatory, ?int $min = null, ?int $max = null, ?string $placeholder = null): array
    {
        $field = [
            'id' => $id,
            'title' => $title,
            'type' => 'input_number',
            'input_keyboard_type' => 'number',
            'mandatory' => $mandatory,
            'placeholder' => $placeholder,
            'tooltip_text' => null,
            'current_value' => $value !== null ? (string) $value : null,
        ];

        if ($min !== null) $field['number_min'] = $min;
        if ($max !== null) $field['number_max'] = $max;

        return $field;
    }

    private function dateField(string $id, string $title, mixed $value): array
    {
        return [
            'id' => $id,
            'title' => $title,
            'type' => 'date',
            'mandatory' => true,
            'placeholder' => null,
            'tooltip_text' => null,
            'current_value' => $value !== null
                ? \Carbon\Carbon::parse($value)->format('d.m.Y')
                : null,
        ];
    }

    private function selectField(string $id, string $title, array $options, mixed $selectedVal, bool $mandatory, ?string $tooltip = null): array
    {
        $mappedOptions = array_map(
            fn($val, $label) => ['val' => (string) $val, 'title' => __($label)],
            array_keys($options),
            array_values($options)
        );

        $selectedOption = null;
        if ($selectedVal !== null && isset($options[$selectedVal])) {
            $selectedOption = ['val' => (string) $selectedVal, 'title' => __($options[$selectedVal])];
        }

        return [
            'id' => $id,
            'title' => $title,
            'type' => 'select',
            'mandatory' => $mandatory,
            'placeholder' => null,
            'tooltip_text' => $tooltip,
            'options' => $mappedOptions,
            'selected_value' => $selectedOption ?? null,
            'current_value' => $selectedVal !== null ? (string) $selectedVal : null,
        ];
    }

    private function booleanValue(
        string $field,
        ?Application $previous = null
    ): ?string {
        if ($this->{$field} !== null) {
            return (string) $this->{$field};
        }
    
        if ($previous?->{$field} !== null) {
            return (string) $previous->{$field};
        }
    
        return null;
    }
}
