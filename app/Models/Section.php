<?php

namespace App\Models;

use App\Contracts\Seoable;
use Database\Factories\SectionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Services\ApplicationService;
use stdClass;

/**
 * @property $id
 * @property $active
 * @property $order
 * @property $parent_section_id
 * @property $main_page
 * @property $contact_us
 * @property $lang
 * @property $title
 * @property $description
 * @property $keywords
 * @property $route_name
 * @property $common_name
 * @property $name
 * @property $redirect_link
 * @property $source
 * @property $text
 * @property $template
 * @property $in_header_menu
 * @property $in_footer_menu
 * @property $h1
 * @property $h1_description
 * @property $created_at
 * @property $updated_at
 *
 * @method active()
 * @method contentPages()
 */
class Section extends Model implements Seoable
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'sections';

    protected $casts = [
        'json' => 'array',
    ];

    public const TEMPLATE_HOME = 'home';
    public const TEMPLATE_FAQS = 'faqs';
    public const TEMPLATE_REVIEWS = 'reviews';
    public const TEMPLATE_PRIVACY_POLICY = 'privacy_policy';
    public const TEMPLATE_MARKETING_CONSENT = 'marketing_consent';
    public const TEMPLATE_APPLICANT_FORM_STEP = 'applicant_form_step';
    public const TEMPLATE_APPLICANT_FORM_LANDING_PAGE = 'applicant_form_landing_page';

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', 1);
    }

    public function scopeContentPages(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('template')
                ->orWhere('template', '!=', Section::TEMPLATE_HOME);
        });
    }

    protected static function newFactory(): Factory
    {
        return SectionFactory::new();
    }

    /**
     * Return the parent of a section.
     *
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'parent_section_id');
    }

    /**
     * Return the children of a section
     *
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(Section::class, 'parent_section_id')
            ->orderBy('order', 'ASC');
    }

    public function isHome(): bool
    {
        return !empty($this->main_page);
    }

    public function h1_tag(): string
    {
        if (!empty($this->h1)) {
            return $this->h1;
        }

        if (!empty($this->title)) {
            return $this->title;
        }

        return $this->name ?? '';
    }

    public function canonical_url(): string
    {
        if (empty($this->route_name)) {
            return '';
        }

        if (!empty($this->parent_section_id)) {
            return route('section.deep', [
                'name' => $this->parent->route_name,
                'deep_name' => $this->route_name,
            ]);
        }

        return route('section', ['name' => $this->route_name]);
    }

    public function getAmounts(?ApplicationService $application_service): array
    {
        $amounts = [];

        $from = $this->json['slider_min_amount'] ?? 500;
        $till = $this->json['slider_max_amount'] ?? 25000;
        $step = $this->json['slider_step_amount'] ?? 100;
        $selected = $this->json['slider_start_amount'] ?? 50;

        if ($application_service) {
            $application = $application_service->getFromSession();

            if (!empty($application->id)) {
                $selected = $application->amount;
            }
        }

        $currency_symbol = config('app.currency_symbol');

        for ($i = $from; $i <= $till; $i += $step) {
            $amount = new stdClass();
            $amount->key = $i;
            $amount->value = $i . ' ' . $currency_symbol;
            if ($i == $selected) {
                $amount->selected = true;
            }

            $amounts[] = $amount;
            $amounts['step'] = $step;
        }

        return $amounts;
    }

    public function getTermMonths(?ApplicationService $applicationService): array
    {
        $term_months = [];

        $from = $this->json['slider_min_term'] ?? 3;
        $till = $this->json['slider_max_term'] ?? 96;
        $step = 1;
        $selected = $this->json['slider_start_term'] ?? 16;

        if ($applicationService) {
            $application = $applicationService->getFromSession();
            if (!empty($application->id)) {
                $selected = $application->term_months;
            }
        }

        for ($i = $from; $i <= $till; $i += $step) {
            $term_month = new stdClass();
            $term_month->key = $i;
            $term_month->value = $i . ' ' . __('main.month');
            if ($i == $selected) {
                $term_month->selected = true;
            }

            $term_months[] = $term_month;
        }

        return $term_months;
    }

    public function getHeroBgName(): string
    {
        switch ($this->route_name) {
            case 'kredits':
            case 'kredit':
            case 'paterina-kredits':
            case 'potrebitelskij-kredit':
                $name = 'paterina-kredits';
                break;
            case 'kreditu-apvienosana':
            case 'obyedinenie-kreditov':
                $name = 'kreditu-apvienosana';
                break;
            case 'parkreditacija':
            case 'perekreditovanie':
                $name = 'parkreditacija';
                break;
            case 'auto-kredits':
            case 'avtokredit':
                $name = 'auto-kredits';
                break;
            case 'kredits-remontam':
            case 'kredit-na-remont':
                $name = 'kredits-remontam';
                break;
            case 'atrais-kredits':
            case 'bistrij-kredit':
                $name = 'atrais-kredits';
                break;
            case 'aizdevums':
            case 'zaim':
                $name = 'aizdevums';
                break;
            case 'kreditlinija':
            case 'kreditnaja-linija':
                $name = 'kreditlinija';
                break;
            case 'kredits-pret-kilu':
            case 'kredit-pod-zalog':
                $name = 'kredits-pret-kilu';
                break;
            case 'kredits-pret-auto-kilu':
            case 'kredit-pod-zalog-avto':
                $name = 'kredits-pret-auto-kilu';
                break;
            case 'kredits-uznemumam':
            case 'kredit-dlya-biznesa':
                $name = 'kredits-uznemumam';
                break;
            default:
                $name = 'paterina-kredits';
        }

        return $name;
    }
}
