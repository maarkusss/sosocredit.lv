<?php

namespace App\Services;

use App\Models\Setting;
use App\Services\GoodAff\GoodAffDataService;
use App\ValueObjects\LenderConfig;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class LenderConfigService
{
    public static function getAll(): Collection
    {
        return collect(self::getFromAPI())
            ->map(function ($config) {
                return new LenderConfig($config);
            })->keyBy('abbreviation');
    }

    public static function getByName(string $name): LenderConfig
    {
        return collect(self::getFromAPI())
            ->map(function($config) use ($name) {
                return new LenderConfig($config);
            })
            ->where('name', $name)
            ->first();
    }

    public static function getByAbbreviation(string $abbreviation): LenderConfig
    {
        return collect(self::getFromAPI())
            ->map(function($config) use ($abbreviation) {
                return new LenderConfig($config);
            })
            ->where('abbreviation', $abbreviation)
            ->first();
    }

    public static function listWhereGcRequisitionIsRequired(): array
    {
        $providers = self::getAll();

        return $providers->where('requirements.gc_req_id.requirement', true)
            ->pluck('name')
            ->toArray();
    }

    public static function requiresAdditionalFieldUpdate(
        LenderConfig $lenderConfig,
        array $applicationRemoteResourceFromAPI
    ): bool
    {
        $fields = $lenderConfig->additional_required_fields ?? [];

        if (empty($fields)) {
            return false;
        }

        // Abort if any field config is broken
        foreach ($fields as $fieldName => $field) {
            if (! self::verifyAdditionalField($field)) {
                Log::warning("[LenderConfigService]: Additional field config '$fieldName' is invalid");
                return false;
            }
        }

        $enabledFields = array_filter($fields, fn (array $field) => $field['enabled']);

        if (empty($enabledFields)) {
            return false; // all fields are disabled
        }

        $countryData = $applicationRemoteResourceFromAPI['data']['application_data']['data_country'] ?? [];

        if (empty($countryData)) {
            return true; // additional data not collected at all
        }

        // Every enabled field must exist as a column in data_{country}
        $missingColumns = array_diff_key($enabledFields, $countryData);

        if (! empty($missingColumns)) {
            Log::warning('[LenderConfigService]: Additional fields not found in data_{country} table: '
                . implode(', ', array_keys($missingColumns)));
            return false;
        }

        // Update needed if any enabled, required field has no value yet
        foreach ($enabledFields as $fieldName => $field) {
            if ($field['required'] && is_null($countryData[$fieldName])) {
                return true;
            }
        }

        return false;
    }

    public static function verifyAdditionalField(array $field): bool
    {
        if ( ! isset(
            $field['required'],
            $field['enabled'],
            $field['type'],
            $field['translations']['label'],
            $field['translations']['placeholder'],
        )) {
            return false;
        }

        $availableLocales = Setting::query()
            ->where('name', 'lang')
            ->pluck('value')
            ->toArray();

        foreach ($availableLocales as $locale) {
            if (! isset(
                $field['translations']['label'][$locale],
                $field['translations']['placeholder'][$locale],
            )) {
                return false;
            }
        }

        return true;
    }

    protected static function getFromAPI(): array
    {
        $data = new GoodAffDataService();
        return $data->getLenderConfigs();
    }
}
