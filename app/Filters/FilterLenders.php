<?php

namespace App\Filters;

use App\Entity\LendersDTO;
use App\Entity\UtmDataDTO;
use App\Filters\RuleSets\ExcludeLenders;
use App\Filters\RuleSets\ExcludeLendersFromFirstOffer;
use App\Filters\RuleSets\RuleSet;
use App\Filters\RuleSets\ApplyFrequency;
use App\Filters\RuleSets\WhitelistOnlyLendersForSecondOffer;
use App\Models\Lender\LenderFilter;
use Illuminate\Support\Facades\App;

class FilterLenders
{
    protected array $filters = [];
    protected ?array $target_filter = [];

    /**
     * @var RuleSet[]
     */
    protected array $rule_sets = [];

    public function handle(LendersDTO $lenders_dto): LendersDTO
    {
        $this->filters = LenderFilter::getCachedFilters();
        if (empty($this->filters) || empty($lenders_dto->lenders)) {
            return $lenders_dto;
        }

        $this->fetchTargetListing($lenders_dto->utm_data);

        $this->setRuleSets();

        return $this->filterLenders($lenders_dto);
    }

    protected function fetchTargetListing(?UtmDataDTO $utm_data): void
    {
        $utm_source = request()->utm_source ?: request()->cookie('utm_source');
        $utm_campaign = request()->utm_campaign ?: request()->cookie('utm_campaign');
        $token = request()->token;

        if (!empty($utm_data->utm_source) || !empty($utm_data->utm_campaign)) {
            $utm_source = $utm_data->utm_source;
            $utm_campaign = $utm_data->utm_campaign;
        }

        $utm_source_filter = collect($this->filters)
            ->where('utm_source', $utm_source)
            ->whereNull('utm_campaign')
            ->whereNull('token')
            ->first();

        $full_query_filter = collect($this->filters)
            ->where('utm_source', $utm_source)
            ->when($utm_campaign, function ($collection) use ($utm_campaign) {
                return $collection->where(function ($filter) use ($utm_campaign) {
                    return $filter['utm_campaign'] === $utm_campaign;
                });
            })
            ->when($token, function ($collection) use ($token) {
                return $collection->where(function ($filter) use ($token) {
                    return $filter['token'] === $token;
                });
            }, function ($collection) {
                return $collection->where(function ($filter) {
                    return empty($filter['token']);
                });
            })
            ->first();

        $this->target_filter = $full_query_filter;

        if (empty($this->target_filter)) {
            $this->target_filter = $utm_source_filter;
        }
    }

    protected function filterLenders(LendersDTO $lenders_dto): LendersDTO
    {
        if (!empty($this->target_filter) && !empty($this->target_filter['id'])) {
            LenderFilter::query()
                ->where('id', $this->target_filter['id'] ?? 0)
                ->increment('hits');
        }

        $rules = !empty($this->target_filter)
            ? $this->target_filter['rules'] ?? []
            : [];

        foreach ($this->rule_sets as $rule_set) {
            $lenders_dto->lenders = $rule_set->apply($rules, $lenders_dto->lenders);
        }

        return $lenders_dto;
    }

    protected function setRuleSets(): void
    {
        // All filters
        if (!empty($this->target_filter)) {
            $this->rule_sets[] = App::make(ExcludeLenders::class);
            $this->rule_sets[] = App::make(ApplyFrequency::class);
        }

        // First Offer
        if (!empty(request()->token) && request()->token === 'first-offer') {
            $this->rule_sets[] = App::make(ExcludeLendersFromFirstOffer::class);
        }

        // Second Offer
        if (!empty(request()->token) && request()->token === 'second-offer') {
            $this->rule_sets[] = App::make(WhitelistOnlyLendersForSecondOffer::class);
        }
    }
}
