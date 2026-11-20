<?php

namespace App\ValueObjects;

use Illuminate\Support\Facades\Log;

class LenderConfig
{
    public $id;
    public $name;
    public $abbreviation;
    public $export_service;
    public $partner_ref;
    public $type;
    public $country;
    public $active;
    public $approved_filter;
    public $registered_filter;
    public $additional_required_fields;
    public $links;
    public $tiers;
    public $export_action;
    public $requirements;
    public $commission_percent;
    public $auto_create_commission;
    public $cooldown_reject;
    public $offer_paths;
    protected $termInDays = ['banknote', 'vizia', 'banknotelt'];

    public function __construct($object = null)
    {
        if (!empty($object)) {
            foreach ($object as $key => $value) {
                $this->{$key} = $value;
            }
        }
    }

    public function getApprovedExport(array $exports): ?array
    {
        foreach ($exports as $export) {
            if ($export['decision'] == $this->approved_filter['decision']
                && $export['action'] == $this->approved_filter['action']
            ) {
                return $export;
            }
        }

        return null;
    }
}
