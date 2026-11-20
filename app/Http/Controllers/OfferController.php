<?php

namespace App\Http\Controllers;

use App\Models\Lender;
use App\Models\Lender\LenderSorting;
use App\Models\Lender\LenderSortingEpc;
use App\Models\Lender\LenderVisit;
use App\Models\Lender\LenderVisitSorting;
use App\Models\LoanType;
use App\Traits\WorksWithMeta;
use Illuminate\Contracts\Support\Renderable;

class OfferController extends Controller
{
    use WorksWithMeta;

    public const HIDE_FOOTER = true;

    public function index(): Renderable
    {
        $lenders = Lender::filter($this->getAllLenders(99));

        $meta_title = __('main.special_offers') . ' ' . date('d-m-Y');
        $meta_description = __('main.fill_enquiry_wait_and_get_money');

        return view('offer')
            ->with([
                'lenders' => $lenders,
                'meta' => [
                    'title' => $meta_title,
                    'description' => $meta_description,
                ],
                'hide_footer' => static::HIDE_FOOTER,
            ]);
    }

    public function loantype(LoanType $loantype, array $lenders): Renderable
    {
        $lenders = Lender::filter($lenders);

        return view('offer')
            ->with([
                'loantype' => $loantype,
                'lenders' => $lenders,
                'meta' => $this->metaFromSeoable($loantype),
                'hide_footer' => static::HIDE_FOOTER,
            ]);
    }

    protected function getAllLenders(int $limit): array
    {
        $bot = is_bot();
        $visit = $this->createVisit($bot);

        $lenders_array = [];
        $utm_campaign = request()->utm_campaign ?: request()->cookie('utm_campaign');

        $sorting = LenderSorting::query()
            ->where('campaign_name', $utm_campaign)
            ->first();

        $sorting_epc = [];
        if ($sorting) {
            $sorting_epc = LenderSortingEpc::query()
                ->where('sorting_id', $sorting->id)
                ->get();
        }

        $lenders = [];

        if ($utm_campaign && count($sorting_epc) !== 0) {
            foreach ($sorting_epc as $sorting) {
                $lender = Lender::query()
                    ->with(['data'])
                    ->where('id', $sorting->lender_id)
                    ->first();

                if ($lender && $lender->active) {
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
            $lenders_model = Lender::active()
                ->with(['data'])
                ->orderBy('position', 'ASC')
                ->get();

            foreach ($lenders_model as $lender) {
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

        if ($lenders) {
            ksort($lenders);
        }

        if ($limit) {
            $lenders = array_slice($lenders, 0, $limit, true);
        }

        $i = 1;

        foreach ($lenders as $lender) {
            $this->createVisitSorting($visit, $lender->id, $i, $bot);

            $lenders_array[$i++] = $lender;
        }

        return $lenders_array;
    }

    protected function createVisit($bot): int
    {
        $visit = new LenderVisit();
        $visit->visitor_id = request()->hasCookie('visitor_id') ? request()->cookie('visitor_id') : null;
        $visit->utm_campaign = request()->cookie('utm_campaign');
        $visit->bot = $bot;
        $visit->save();

        return $visit->id;
    }

    protected function createVisitSorting($visit_id, $lender_id, $position, $bot): void
    {
        LenderVisitSorting::create([
            'visit_id' => $visit_id,
            'lender_id' => $lender_id,
            'position' => $position,
            'bot' => $bot,
        ]);
    }
}
