<?php

namespace App\Http\Controllers\Admincp;

use App\Models\Click;
use App\Models\Commission;
use App\Models\Lender;
use App\Models\Visitor;
use App\Services\GoodAff\GoodAffStatus;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;

class StatisticsController extends AdminController
{
    public function index(Request $request): Renderable
    {
        return view('admincp.statistics.index')->with([
            'chart_data' => $this->getChartData($request),
            'lenders' => $this->getLendersStatistics($request),
            'meta' => $this->metaTitle('Statistics'),
        ]);
    }

    public function getChartData(Request $request, int $days_from = 30): array
    {
        $chart = [];
        $from = Carbon::today()->subDays($days_from)->format('Y-m-d');
        $to = Carbon::today()->format('Y-m-d');

        if ($request->has('datePicker')) {
            $datePicker = explode(' to ', $request->input('datePicker'));
            if (count($datePicker) === 1) {
                $datePicker[1] = $datePicker[0];
            }
            $from = $datePicker[0];
            $to = $datePicker[1];
        }

        $carbon_period = CarbonPeriod::create($from, $to);
        /** @var Carbon $period_date */
        foreach ($carbon_period as $period_date) {
            $date = $period_date->format('Y-m-d');

            $chart['visitors'][$date] = Visitor::query()
                    ->where('created_at', '>=', $date . ' 00:00:00')
                    ->where('created_at', '<=', $date . ' 23:59:59')
                    ->count();

            $chart['clicks'][$date] = Click::query()
                    ->where('created_at', '>=', $date . ' 00:00:00')
                    ->where('created_at', '<=', $date . ' 23:59:59')
                    ->count();

            $chart['commissions'][$date] = Commission::approved()
                    ->where('commission_date', '>=', $date . ' 00:00:00')
                    ->where('commission_date', '<=', $date . ' 23:59:59')
                    ->sum('commission');
        }

        return $chart;
    }

    public function getLendersStatistics(Request $request, int $days_from = 30): array
    {
        $array = [];
        $from = Carbon::today()->subDays($days_from)->format('Y-m-d');
        $to = Carbon::today()->format('Y-m-d');

        if ($request->has('datePicker')) {
            $datePicker = explode(' to ', $request->input('datePicker'));
            if (count($datePicker) === 1) {
                $datePicker[1] = $datePicker[0];
            }
            $from = Carbon::createFromFormat('d.m.Y', $datePicker[0])->format('Y-m-d');
            $to = Carbon::createFromFormat('d.m.Y', $datePicker[1])->format('Y-m-d');
        }

        $ordered_lenders = Lender::query()
            ->with([
                'commissions',
            ])
            ->orderBy('name', 'ASC')
            ->get();

        foreach ($ordered_lenders as $lender) {
            $clicks = Click::query()
                ->where('lender_id', $lender->id)
                ->where('created_at', '>=', $from . ' 00:00:00')
                ->where('created_at', '<=', $to . ' 23:59:59')
                ->count();

            $commissions = Commission::approved()
                ->where('lender_id', $lender->id)
                ->where('commission_date', '>=', $from . ' 00:00:00')
                ->where('commission_date', '<=', $to . ' 23:59:59')
                ->sum('commission');

            $epc = $clicks == 0 ? 0 : $commissions / $clicks;

            $array[$lender->id]['name'] = $lender->name;
            $array[$lender->id]['confirmed'] = $lender->commissions
                ->where('status', GoodAffStatus::APPROVED)
                ->where('commission_date', '>=', $from . ' 00:00:00')
                ->where('commission_date', '<=', $to . ' 23:59:59')
                ->where('commission', '>', 0)
                ->sum('commission');

            $array[$lender->id]['waiting'] = $lender->commissions
                ->where('status', GoodAffStatus::PENDING)
                ->where('commission_date', '>=', $from . ' 00:00:00')
                ->where('commission_date', '<=', $to . ' 23:59:59')
                ->where('commission', '>', 0)
                ->sum('commission');

            $array[$lender->id]['clicks'] = $clicks;
            $array[$lender->id]['commissions'] = $commissions;
            $array[$lender->id]['epc'] = $epc;
            $array[$lender->id]['style'] = $this->calcEpcStyling($epc);
        }

        return $array;
    }

    protected function calcEpcStyling($epc): string
    {
        if ($epc == 0) {
            $style = "color:grey";
        } elseif ($epc < 0.15) {
            $style = "color:red;font-weight:bold;";
        } elseif ($epc < 0.25) {
            $style = "color:red;";
        } elseif ($epc < 0.35) {
            $style = "color:orange;";
        } elseif ($epc < 0.5) {
            $style = "color:green;";
        } else {
            $style = "color:green;font-weight:bold;";
        }

        return $style;
    }
}
