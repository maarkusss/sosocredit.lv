<?php

namespace App\Models;

use App\Entity\LendersDTO;
use App\Models\Lender\BelongsToCampaign;
use App\Models\Lender\LenderCategory;
use App\Models\Lender\LenderData;
use App\Models\Lender\LenderVisitSorting;
use Database\Factories\LenderFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use stdClass;

/**
 * @property $id
 * @property $name
 * @property $abbreviation
 * @property $slug
 * @property $offer_expiration
 * @property $image
 * @property $affiliate_link
 * @property $position
 * @property $frequency
 * @property $daily_epc
 * @property $daily_epc_before
 * @property $guaranteed_epc
 * @property $earnings
 * @property $clicks
 * @property $epc
 * @property $first_loan
 * @property $max_amount
 * @property $min_term
 * @property $max_term
 * @property $min_years
 * @property $max_years
 * @property $receiving_time
 * @property $active
 * @property $is_provider
 * @property $base_priority
 * @property $is_broker
 * @property $rejected_statuses
 * @property $zero_percent
 * @property $zero_percent_value
 * @property $campaign_id
 *
 * @property $receiving_time_localized
 *
 * @method active()
 * @method providers()
 * @method linkedToCampaign()
 */
class Lender extends Model
{
    use HasFactory;
    use BelongsToCampaign;

    protected $guarded = [];
    protected $table = 'lenders';

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', 1);
    }

    public function scopeProviders(Builder $query): Builder
    {
        return $query->where('is_provider', 1);
    }

    public function scopeBroker(Builder $query): Builder
    {
        return $query->where('is_broker', 1);
    }

    public function scopeLinkedToCampaign(Builder $query): Builder
    {
        return $query->where('campaign_id', '>', 0);
    }

    public function categories()
    {
        return $this->hasMany(LenderCategory::class, 'lender_id');
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class, 'lender_id');
    }

    public function clicksFromTable()
    {
        return $this->hasMany(Click::class, 'lender_id');
    }

    public function data()
    {
        return $this->hasOne(LenderData::class, 'lender_id')->where('lang', app()->getLocale());
    }

    public function dataAPI()
    {
        return $this->hasOne(LenderData::class, 'lender_id');
    }

    public function sessions()
    {
        return $this->hasMany(LenderVisitSorting::class, 'lender_id');
    }

    public function providerLinkVisits(): HasMany
    {
        return  $this->hasMany(ProviderLinkVisit::class, 'lender_id');
    }

    protected static function newFactory(): Factory
    {
        return LenderFactory::new();
    }

    public static function filter($lenders, $utm_data = null)
    {
        $filters = config('app.lenders_listing_filters') ?? [];
        if (empty($filters)) {
            return $lenders;
        }

        return app(Pipeline::class)
            ->send(
                new LendersDTO(
                    lenders: $lenders,
                    utm_data: $utm_data
                )
            )
            ->through($filters)
            ->thenReturn()
            ->lenders;
    }

    public function getReceivingTimeLocalizedAttribute(): string
    {
        $receiving_time = $this->receiving_time;

        if (empty($receiving_time) || !is_numeric($receiving_time)) {
            return "";
        }

        if ($receiving_time < 60) {
            $receiving_time_localized = round($receiving_time) . ' ' . __('minutes');
        } elseif ($receiving_time == 60) {
            $receiving_time_localized = round($receiving_time / 60) . ' ' . __('hour');
        } elseif ($receiving_time < 120) {
            $receiving_time_localized = round($receiving_time) . ' ' . __('minutes');
        } elseif ($receiving_time == 120) {
            $receiving_time_localized = round($receiving_time / 60) . ' ' . __('hours');
        } elseif ($receiving_time < 1440 && $receiving_time % 60 === 0) {
            $receiving_time_localized = round($receiving_time / 60) . ' ' . __('hours');
        } elseif ($receiving_time == 1440) {
            $receiving_time_localized = round($receiving_time / 1440) . ' ' . __('day');
        } else {
            $receiving_time_localized = round($receiving_time / 1440) . ' ' . __('days');
        }

        return $receiving_time_localized;
    }

    public function getApprovalRate($applications): stdClass
    {
        $apr = new stdClass();
        $relevantApplications = $this->getRelevantApplications($applications);
        $totalPingCount = $relevantApplications->count();
        $approvedCount = $relevantApplications->filter(function($application) {
            return in_array($application->exports_summary[$this->slug], ['Approved', 'Done', 'Expired']);
        })->count();

        $apr->total = $totalPingCount;
        $apr->approved = $approvedCount;
        $apr->apr = $totalPingCount > 0 ? round(($approvedCount / $totalPingCount) * 100) : 0;

        return $apr;
    }
    protected function getRelevantApplications($applications): Collection
    {
        return $applications->filter(function($application) {
            $summary = $application->getExportsSummary();
            return isset($summary->{$this->slug}) && $summary->{$this->slug} !== 'Cannot';
        });
    }
    public function isRejected(string $status): bool
    {
        if (empty($this->rejected_statuses)) {
            return false;
        }

        $rejectedStatuses = json_decode($this->rejected_statuses, true);

        return isset($rejectedStatuses[$status]) && $rejectedStatuses[$status] === '1';
    }

    public static function getCurrentCategoryProviders(Application $application): Collection
    {
        $loanType = Section::active()
            ->where('template', Section::TEMPLATE_APPLICANT_FORM_LANDING_PAGE)
            ->where(function ($q) use ($application) {
                $q->where('name', $application->loantype)->orWhere('common_name', $application->loantype);
            })
            ->orderBy('order', 'ASC')
            ->first();

        $sectionProviders = LenderCategory::where('loan_type_id', $loanType->id)
            ->distinct()
            ->pluck('lender_id');

        if ($sectionProviders->isNotEmpty()) {
            return self::providers()
                ->whereIn('id', $sectionProviders)
                ->get();
        }

        $sectionedProviders = LenderCategory::distinct()->pluck('lender_id')->toArray();

        return self::providers()->whereNotIn('id', $sectionedProviders)->get();
    }
}
