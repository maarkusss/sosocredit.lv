<?php

namespace App\Http\Controllers;

use App\Entity\LandingFormVersion;
use App\Libraries\News\NewsData;
use App\Models\Application;
use App\Models\Faq;
use App\Models\GoogleReview;
use App\Models\GoogleReviewsStat;
use App\Models\Lender;
use App\Models\Lender\LenderCategory;
use App\Models\Lender\LenderSorting;
use App\Models\Lender\LenderSortingEpc;
use App\Models\LoanType;
use App\Models\Review;
use App\Models\Section;
use App\Models\Setting;
use App\Services\ApplicationService;
use App\Traits\WorksWithHeaderMenu;
use App\Traits\WorksWithMeta;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class SectionController extends Controller
{
    use WorksWithMeta;
    use WorksWithHeaderMenu;

    public function __construct(protected ApplicationService $application_service)
    {
    }

    /**
     * Homepage action
     *
     * @return Renderable|RedirectResponse
     */
    public function home()
    {
        $homepage = Section::active()
            ->where('lang', app()->getLocale())
            ->where('main_page', 1)
            ->firstOrFail();

        /** @var Section $homepage */
        if ($homepage->redirect_link) {
            return redirect($homepage->redirect_link);
        }

        $section_template = $homepage->template ?: 'section';
        $meta = $this->metaFromSection($homepage);
        //        $this->createStep4AndStep5PagesForApplicantTypes();
        $faqs = $this->getFaqs(8);

        $locale = app()->getLocale();

        $allReviews = GoogleReview::whereNotNull('text')
            ->where('text', '!=', '')
            ->orderByDesc('review_time')
            ->get();

        $filtered = $allReviews->filter(function ($review) use ($locale) {
            if ($locale === 'lv') {
                return in_array($review->lang, ['lv', 'en']);
            }

            return $review->lang === $locale;
        });

        $google_reviews = $filtered->take(10);
        $google_stats = GoogleReviewsStat::find(1);

        return view('sections.' . $section_template)->with([
            'amounts' => $homepage->getAmounts($this->application_service),
            'applicant_form_types' => Application::getApplicationTypes(app()->getLocale()),
            'header_menu' => $this->getHeaderMenu(),
            'homepage' => $homepage,
            'section' => $homepage,
            'google_stats' => $google_stats,
            'google_reviews' => $google_reviews,
            'landing_form_version' => LandingFormVersion::retrieve(),
            'meta' => $meta,
            'reviews' => $this->getReviews($homepage),
            'term_months' => $homepage->getTermMonths($this->application_service),
            'qr_code' => Setting::retrieve('qr_code'),
            'qr_popup' => Setting::retrieve('qr_popup'),
            'show_important_info' => true,
            'faqs' => $faqs,
        ]);
    }

    public function createStep4AndStep5PagesForApplicantTypes()
    {
        $landing_pages = Section::query()
            ->where('template', Section::TEMPLATE_APPLICANT_FORM_LANDING_PAGE)
            ->where('lang', app()->getLocale())
            ->orderBy('id', 'ASC')
            ->get();

        foreach ($landing_pages as $section) {
            for ($i = 4; $i <= 5; $i++) {
                if (!Section::where('parent_section_id', $section->id)->where('route_name', 'step' . $i)->exists()) {
                    Section::create([
                        'parent_section_id' => $section->id,
                        'main_page' => 0,
                        'contact_us' => 0,
                        'lang' => $section->lang,
                        'title' => $section->name,
                        'description' => null,
                        'keywords' => null,
                        'route_name' => 'step' . $i,
                        'name' => $section->name,
                        'template' => Section::TEMPLATE_APPLICANT_FORM_STEP,
                        'order' => $i * 20,
                    ]);
                }
            }
        }
    }

    /**
     * Standard section action
     *
     * @param string $name
     * @return Renderable|RedirectResponse
     */
    public function section($name)
    {
        $locale = app()->getLocale();

        $section = Section::active()
            ->contentPages()
            ->where('route_name', $name)
            ->where('lang', $locale)
            ->first();

        if ($section && $section->template === 'news') {
            $newsData = (new NewsData)->newsListData();
            $loanModalData = $this->getLoanModalData();

            return view('sections.news')->with([
                'applicant_form_types' => Application::getApplicationTypes(app()->getLocale()) ?? [],
                'header_menu' => $this->getHeaderMenu(),
                'meta' => $this->metaFromSection($section),
                'section' => $section,
                'news' => $newsData['news'],
                'categories' => $newsData['categories'],
                'selected_category_id' => $newsData['selected_category_id'],
                'loan_modal_data' => $loanModalData,
            ]);
        }

        $loantype = LoanType::active()
            ->where('route_name', $name)
            ->where('lang', $locale)
            ->first();


        if (!$loantype && !$section) {
            abort(404);
        }

        $utm_campaign = request()->utm_campaign ? request()->utm_campaign : request()->cookie('utm_campaign');

        if ($loantype && $loantype->id) {
            $lenders = [];
            $sorting_epc = [];
            $sorting = LenderSorting::query()
                ->where('campaign_name', $utm_campaign)
                ->first();

            if ($sorting) {
                $sorting_epc = LenderSortingEpc::query()
                    ->where('sorting_id', $sorting->id)
                    ->get();
            }

            if ($utm_campaign && count($sorting_epc) !== 0) {
                foreach ($sorting_epc as $sorting) {
                    $lender = Lender::query()
                        ->where('id', $sorting->lender_id)
                        ->first();

                    if (empty($lender->id)) {
                        continue;
                    }

                    $categories = LenderCategory::query()
                        ->where('loan_type_id', $loantype->id)
                        ->where('lender_id', $lender->id)
                        ->exists();

                    if ($lender->active && $categories) {
                        if ($lender->frequency) {
                            if (rand(1, $lender->frequency) == $lender->frequency) {
                                $lenders[$sorting->position] = $lender;
                            }
                        } else {
                            $lenders[$sorting->position] = $lender;
                        }
                    }
                }
            } else {
                $categories = LenderCategory::query()
                    ->where('loan_type_id', $loantype->id)
                    ->get();

                foreach ($categories as $category) {
                    $lender = Lender::active()
                        ->where('id', $category->lender_id)
                        ->first();

                    if ($lender) {
                        if ($lender->position && $lender->active) {
                            if ($lender->frequency) {
                                if (rand(1, $lender->frequency) == $lender->frequency) {
                                    $lenders[$lender->position] = $lender;
                                }
                            } else {
                                $lenders[$lender->position] = $lender;
                            }
                        }
                    }
                }
            }

            if ($lenders) {
                ksort($lenders);
            }

            $offer_controller = App::make(OfferController::class);
            /** @var OfferController $offer_controller */
            return $offer_controller->loantype($loantype, $lenders);
        }

        $section_template = $section->template ?: 'section';

        if ($section_template === 'faqs') {
            $faqs = $this->getFaqs();
        }

        if ($section_template === 'reviews') {

            $google_stats = GoogleReviewsStat::find(1);
            $locale = app()->getLocale();
            $sort = request()->get('sort', 'newest');

            $baseQuery = GoogleReview::query()
                ->whereNotNull('text')
                ->where('text', '!=', '');

            if ($locale === 'lv') {
                $baseQuery->whereIn('lang', ['lv', 'en']);
            } else {
                $baseQuery->where('lang', $locale);
            }

            // Statistikai — pirms sort
            $allReviewsForStats = (clone $baseQuery)->get();
            $totalCount = $allReviewsForStats->count();
            $ratingStats = $allReviewsForStats->groupBy('rating')->map->count();
            $structuredReviews = $allReviewsForStats->take(10);

            // Paginēšanai — ar sort
            match ($sort) {
                'oldest' => $baseQuery->orderBy('review_time', 'asc'),
                'rating_desc' => $baseQuery->orderBy('rating', 'desc'),
                'rating_asc' => $baseQuery->orderBy('rating', 'asc'),
                default => $baseQuery->orderBy('review_time', 'desc'),
            };

            $all_reviews = $baseQuery->paginate(10)->withQueryString();
        }

        $application = $section_template === Section::TEMPLATE_APPLICANT_FORM_LANDING_PAGE
            ? $this->application_service->getFromSession()
            : null;

        $is_applicant_page = !empty($section_template) && Str::startsWith($section_template, 'applicant');

        $applicant_form_types = Application::getApplicationTypes(app()->getLocale()) ?? [];

        if($section->template === Section::TEMPLATE_APPLICANT_FORM_LANDING_PAGE || $section->template === Section::TEMPLATE_HOME) {
            $show_important_info = true;
            $faqs = $this->getFaqs(8);

            $locale = app()->getLocale();

            $allReviews = GoogleReview::whereNotNull('text')
                ->where('text', '!=', '')
                ->orderByDesc('review_time')
                ->get();

            $filtered = $allReviews->filter(function ($review) use ($locale) {
                if ($locale === 'lv') {
                    return in_array($review->lang, ['lv', 'en']);
                }

                return $review->lang === $locale;
            });

            $google_reviews = $filtered->take(10);
            $google_stats = GoogleReviewsStat::find(1);
        }

        return view('sections.' . $section_template)->with([
            'application' => $application,
            'applicant_form_types' => $applicant_form_types,
            'header_menu' => $this->getHeaderMenu(),
            'is_applicant_page' => $is_applicant_page,
            'meta' => $this->metaFromSection($section),
            'reviews' => $this->getReviews($section),
            'section' => $section,
            'amounts' => $section->getAmounts($this->application_service),
            'term_months' => $section->getTermMonths($this->application_service),
            'faqs' => $faqs ?? [],
            'google_stats' => $google_stats ?? [],
            'google_reviews' => $google_reviews ?? [],
            'all_reviews' => $all_reviews ?? collect(),
            'totalCount' => $totalCount ?? 0,
            'ratingStats' => $ratingStats ?? collect(),
            'structuredReviews' => $structuredReviews ?? collect(),
            'show_important_info' => $show_important_info ?? false,
        ]);
    }

    /**
     * Deep section action, allows two-level nesting like /parent/child
     *
     * @param $name
     * @param $deep_name
     * @return Renderable|RedirectResponse
     */
    public function deepSection($name, $deep_name): Renderable|RedirectResponse
    {
        $current_locale = app()->getLocale();

        $section = Section::active()
            ->where('route_name', $name)
            ->firstOrFail();

        if ($section && $section->template === 'news') {
            $newsItemData = (new NewsData)->newsItemData($deep_name);
            $loanModalData = $this->getLoanModalData();

            if (empty($newsItemData)) {
                return redirect(route('section', ['name' => $name]));
            }

            return view('sections.news-item-page')->with([
                'applicant_form_types' => Application::getApplicationTypes(app()->getLocale()) ?? [],
                'header_menu' => $this->getHeaderMenu(),
                'meta' => $newsItemData['meta'],
                'section' => $section,
                'post' => $newsItemData['post'],
                'latest_news' => $newsItemData['latest_news'],
                'extra_source' => $newsItemData['post']['source'],
                'loan_modal_data' => $loanModalData,
            ]);
        }

        if ($section->lang != $current_locale)
        { // if locale deviates from locale of section bound to application
            $common_name = $section->common_name;
            $section = Section::active()
                ->where('common_name', $common_name)
                ->where('lang', $current_locale)
                ->firstOrFail();
        }

        $subsection = Section::active()
            ->where('parent_section_id', $section->id)
            ->where('route_name', $deep_name)
            ->where('lang', $current_locale)
            ->first();

        if (!$subsection) {
            abort(404);
        }

        /** @var Section $subsection */

        $subsection_template = $subsection->template ?: 'section';

        if ($subsection_template === Section::TEMPLATE_APPLICANT_FORM_STEP) {
            $application_form_controller = App::make(ApplicationFormController::class);
            /** @var ApplicationFormController $application_form_controller */
            $application = $this->application_service->getFromSession() ?: new Application();

            return $application_form_controller->show($application, request(), $subsection);
        }

        return view('sections.' . $subsection_template)->with([
            'country' => app()->getLocale(),
            'header_menu' => $this->getHeaderMenu(),
            'meta' => $this->metaFromSection($subsection),
            'section' => $subsection,
            'amounts' => $subsection->getAmounts($this->application_service),
            'term_months' => $subsection->getTermMonths($this->application_service),
        ]);
    }

    protected function getFaqs(int $limit = 100): Collection
    {
        return Faq::active()
            ->where('lang', app()->getLocale())
            ->orderBy('order', 'ASC')
            ->take($limit)
            ->get();
    }

    protected function getReviews(Section $section, int $limit = 6): Collection
    {
        return Review::query()
            ->where('lang', app()->getLocale())
            ->where('section_id', $section->id)
            ->orderBy('id', 'ASC')
            ->take($limit)
            ->get();
    }

    protected function getLoanModalData(): Collection
    {
        return Section::query()
            ->where('active', 1)
            ->where('template', Section::TEMPLATE_APPLICANT_FORM_LANDING_PAGE)
            ->where('lang', app()->getLocale())
            ->orderBy('order')
            ->get();
    }
}
