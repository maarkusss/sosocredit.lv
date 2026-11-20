<?php

namespace App\Http\Controllers\Admincp;

use App\Models\Click;
use App\Models\Commission;
use App\Models\Consumer;
use App\Models\GoRewrite;
use App\Models\Lender;
use App\Models\LenderCampaign;
use App\Models\Lender\LenderFilter;
use App\Models\LoanType;
use App\Models\Visitor;
use Carbon\Carbon;
use Goodday\Postbacks\Models\Postback;
use Illuminate\Contracts\Support\Renderable;

class DashboardController extends AdminController
{
    public function index(): Renderable
    {
        $pctClass = function (float $pct): string {
            return match (true) {
                $pct < 50   => 'text-red-500',
                $pct < 75   => 'text-orange-600',   // 50–74.9
                $pct <= 90  => 'text-green-500',    // 75–90
                default     => 'text-green-700',  // > 90
            };
        };
    
        // ALL TIME
        $allTotal = Consumer::count();
        $allConfirmed = Consumer::where('confirmed', 1)->count();
        $allPct = $allTotal > 0 ? round(($allConfirmed / $allTotal) * 100, 1) : 0;
    
        // LAST MONTH
        $lmFrom = now()->subMonth()->startOfMonth();
        $lmTo   = now()->subMonth()->endOfMonth();
    
        $lastMonthTotal = Consumer::whereBetween('created_at', [$lmFrom, $lmTo])->count();
        $lastMonthConfirmed = Consumer::where('confirmed', 1)->whereBetween('created_at', [$lmFrom, $lmTo])->count();
        $lastMonthPct = $lastMonthTotal > 0 ? round(($lastMonthConfirmed / $lastMonthTotal) * 100, 1) : 0;
    
        // THIS MONTH
        $tmFrom = now()->startOfMonth();
        $tmTo   = now()->endOfMonth();
    
        $thisMonthTotal = Consumer::whereBetween('created_at', [$tmFrom, $tmTo])->count();
        $thisMonthConfirmed = Consumer::where('confirmed', 1)->whereBetween('created_at', [$tmFrom, $tmTo])->count();
        $thisMonthPct = $thisMonthTotal > 0 ? round(($thisMonthConfirmed / $thisMonthTotal) * 100, 1) : 0;

        // DAILY (today)
        $dayFrom = now()->startOfDay();
        $dayTo   = now()->endOfDay();

        $dailyTotal = Consumer::whereBetween('created_at', [$dayFrom, $dayTo])->count();
        $dailyConfirmed = Consumer::where('confirmed', 1)->whereBetween('created_at', [$dayFrom, $dayTo])->count();
        $dailyPct = $dailyTotal > 0 ? round(($dailyConfirmed / $dailyTotal) * 100, 1) : 0;

        // YESTERDAY
        $yFrom = now()->subDay()->startOfDay();
        $yTo   = now()->subDay()->endOfDay();

        $yesterdayTotal = Consumer::whereBetween('created_at', [$yFrom, $yTo])->count();
        $yesterdayConfirmed = Consumer::where('confirmed', 1)->whereBetween('created_at', [$yFrom, $yTo])->count();
        $yesterdayPct = $yesterdayTotal > 0 ? round(($yesterdayConfirmed / $yesterdayTotal) * 100, 1) : 0;

        // WEEKLY (last 7 days incl today)
        $weekFrom = now()->subDays(6)->startOfDay();
        $weekTo   = now()->endOfDay();

        $weeklyTotal = Consumer::whereBetween('created_at', [$weekFrom, $weekTo])->count();
        $weeklyConfirmed = Consumer::where('confirmed', 1)->whereBetween('created_at', [$weekFrom, $weekTo])->count();
        $weeklyPct = $weeklyTotal > 0 ? round(($weeklyConfirmed / $weeklyTotal) * 100, 1) : 0;
    
        return view('admincp.dashboard.index')->with([
            'data' => $this->getStatistics(),
            'chart_data' => $this->getChartData(),
            'meta' => $this->metaTitle('Dashboard'),
            'consumers_stats' => [
                'all' => [
                    'confirmed' => $allConfirmed,
                    'total' => $allTotal,
                    'pct' => $allPct,
                    'pct_text_class' => $pctClass($allPct),
                ],
                'last_month' => [
                    'confirmed' => $lastMonthConfirmed,
                    'total' => $lastMonthTotal,
                    'pct' => $lastMonthPct,
                    'pct_text_class' => $pctClass($lastMonthPct),
                ],
                'this_month' => [
                    'confirmed' => $thisMonthConfirmed,
                    'total' => $thisMonthTotal,
                    'pct' => $thisMonthPct,
                    'pct_text_class' => $pctClass($thisMonthPct),
                ],
                'daily' => [
                    'confirmed' => $dailyConfirmed,
                    'total' => $dailyTotal,
                    'pct' => $dailyPct,
                    'pct_text_class' => $pctClass($dailyPct),
                ],
                'yesterday' => [
                    'confirmed' => $yesterdayConfirmed,
                    'total'     => $yesterdayTotal,
                    'pct'       => $yesterdayPct,
                    'pct_text_class' => $pctClass($yesterdayPct),
                ],
                'weekly' => [
                    'confirmed' => $weeklyConfirmed,
                    'total' => $weeklyTotal,
                    'pct' => $weeklyPct,
                    'pct_text_class' => $pctClass($weeklyPct),
                ],
            ],
        ]);
    }

    public function getChartData(): array
    {
        $chart = [];
        $days = 8;
        $report_date = Carbon::today()->subDays($days);

        for ($i = 0; $i < $days; $i++) {
            $date = $report_date->addDay()->format('Y-m-d');

            $chart['clicks'][$date] = Click::where('created_at', '>=', $date . ' 00:00:00')->where('created_at', '<=', $date . ' 23:59:59')->count();

            $chart['commissions'][$date] = Commission::where('commission_date', '>=', $date . ' 00:00:00')->where('commission_date', '<=', $date . ' 23:59:59')->approved()->sum('commission');

            $chart['visitors'][$date] = Visitor::where('created_at', '>=', $date . ' 00:00:00')->where('created_at', '<=', $date . ' 23:59:59')->count();
        }

        return $chart;
    }

    public function getStatistics(): array
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $array['clicks']['today'] = Click::where('created_at', '>=', $today->format('Y-m-d 00:00:00'))->where('created_at', '<=', $today->format('Y-m-d 23:59:59'))->count();
        $array['clicks']['yesterday'] = Click::where('created_at', '>=', $yesterday->format('Y-m-d 00:00:00'))->where('created_at', '<=', $yesterday->format('Y-m-d 23:59:59'))->count();

        $array['commissions']['today'] = Commission::where('commission_date', '>=', $today->format('Y-m-d 00:00:00'))->where('commission_date', '<=', $today->format('Y-m-d 23:59:59'))->approved()->sum('commission');
        $array['commissions']['yesterday'] = Commission::where('commission_date', '>=', $yesterday->format('Y-m-d 00:00:00'))->where('commission_date', '<=', $yesterday->format('Y-m-d 23:59:59'))->approved()->sum('commission');

        $array['visitors']['today'] = Visitor::where('created_at', '>=', $today->format('Y-m-d 00:00:00'))->where('created_at', '<=', $today->format('Y-m-d 23:59:59'))->count();
        $array['visitors']['yesterday'] = Visitor::where('created_at', '>=', $yesterday->format('Y-m-d 00:00:00'))->where('created_at', '<=', $yesterday->format('Y-m-d 23:59:59'))->count();

        $array['clicks']['percentage'] = $this->calcPercentage($array, 'clicks');
        $array['commissions']['percentage'] = $this->calcPercentage($array, 'commissions');
        $array['visitors']['percentage'] = $this->calcPercentage($array, 'visitors');

        $array['clicks']['color'] = $this->calcColorStyling($array, 'clicks');
        $array['commissions']['color'] = $this->calcColorStyling($array, 'commissions');
        $array['visitors']['color'] = $this->calcColorStyling($array, 'visitors');

        if (auth()->user()->can('view lenders')) {
            $array['lenders']['total'] = Lender::query()->count();
            $array['lenders']['active'] = Lender::active()->count();
            $array['lenders']['link'] = route('admincp.lenders.index');

            $array['lendercampaigns']['total'] = LenderCampaign::query()->count();
            $array['lendercampaigns']['active'] = LenderCampaign::active()->count();
            $array['lendercampaigns']['link'] = route('admincp.lenders.campaigns.index');

            $array['lenderfilters']['total'] = LenderFilter::query()->count();
            $array['lenderfilters']['active'] = LenderFilter::active()->count();
            $array['lenderfilters']['link'] = route('admincp.lenders.filters.index');
        }

        if (auth()->user()->can('view go-rewrites')) {
            $array['go_rewrites']['total'] = GoRewrite::query()->count();
            $array['go_rewrites']['active'] = GoRewrite::active()->count();
            $array['go_rewrites']['link'] = route('admincp.go-rewrites.index');
        }

        if (auth()->user()->can('view loantypes')) {
            $array['loantypes']['total'] = LoanType::query()->count();
            $array['loantypes']['active'] = LoanType::active()->count();
            $array['loantypes']['link'] = route('admincp.loantypes.index');
        }

        if (config('postbacks.enabled') && auth()->user()->can('viewInternals')) {
            $array['postbacks']['total'] = Postback::query()->count();
            $array['postbacks']['active'] = Postback::active()->count();
            $array['postbacks']['link'] = route('admincp.postbacks.index');
        }

        return $array;
    }

    protected function calcPercentage(array $array, string $type): string
    {
        if (!in_array($type, ['clicks', 'commissions', 'visitors'])) {
            throw new \InvalidArgumentException('Cannot calculate percentage for the passed type: ' . $type);
        }

        if ($array[$type]['yesterday'] == 0) {
            return number_format(($array[$type]['today'] - $array[$type]['yesterday']) * 100, 2);
        }

        return number_format(($array[$type]['today'] - $array[$type]['yesterday']) / $array[$type]['yesterday'] * 100, 2);
    }

    protected function calcColorStyling(array $array, string $type): string
    {
        if (!in_array($type, ['clicks', 'commissions', 'visitors'])) {
            throw new \InvalidArgumentException('Cannot calculate styling for the passed type: ' . $type);
        }

        if ($array[$type]['today'] < $array[$type]['yesterday']) {
            return 'text-red-500';
        } elseif ($array[$type]['today'] == $array[$type]['yesterday']) {
            return 'text-gray-500';
        }

        return 'text-green-500';
    }
}
