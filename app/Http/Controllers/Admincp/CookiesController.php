<?php

namespace App\Http\Controllers\Admincp;

use App\Models\Application;
use App\Models\Click;
use App\Models\Commission;
use App\Models\Consumer;
use App\Models\CookieConsent;
use App\Models\Lender;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;

class CookiesController extends AdminController
{
    public function index(): Renderable
    {
        $per_page = 20;

        $cookies = CookieConsent::orderByDesc('id')->paginate($per_page);

        $visitorIds = $cookies->pluck('visitor_id')->filter()->unique();

        // Visitor data for table columns
        $visitors = Visitor::whereIn('id', $visitorIds)
            ->get()
            ->keyBy('id');

        // Consumer lookup by visitor_id
        $consumerIds = Consumer::whereIn('visitor_id', $visitorIds)
            ->pluck('id', 'visitor_id');

        // Visitors that have at least one commission (for the "Commission: Yes/No" badge)
        $withCommission = Commission::whereIn('consumer_id', $consumerIds->values())
            ->distinct()
            ->pluck('consumer_id')
            ->map(fn($consumerId) => $consumerIds->search($consumerId))
            ->filter()
            ->flip();

        // All consumers keyed by visitor_id for commission lookup
        $consumersByVisitor = Consumer::whereIn('visitor_id', $visitorIds)
            ->get()
            ->keyBy('visitor_id');

        $allConsumerIds = $consumersByVisitor->pluck('id');

        // Group commissions by visitor_id for the Commissions table column
        $visitorCommissions = Commission::whereIn('consumer_id', $allConsumerIds)
            ->orderByDesc('id')
            ->get()
            ->groupBy(function ($commission) use ($consumersByVisitor) {
                $consumer = $consumersByVisitor->firstWhere('id', $commission->consumer_id);
                return $consumer?->visitor_id;
            });

        $stats = CookieConsent::selectRaw('
            COUNT(*) as total,
            SUM(analytics = 1)   as analytics_yes,
            SUM(marketing = 1)   as marketing_yes,
            SUM(functional = 1)  as functional_yes
        ')->first();

        $applicationsPerMonth = Application::selectRaw(
                "DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count"
            )
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');
        
        $monthLabels = [];
        $monthCounts = [];
        for ($i = 5; $i >= 0; $i--) {
            $key = now()->subMonths($i)->format('Y-m');
            $label = now()->subMonths($i)->format('M Y');
            $monthLabels[] = $label;
            $monthCounts[] = (int) ($applicationsPerMonth[$key] ?? 0);
        }

        return view('admincp.cookies.index')
            ->with([
                'cookies'            => $cookies,
                'visitors'           => $visitors,
                'withCommission'     => $withCommission,
                'visitorCommissions' => $visitorCommissions,
                'stats'              => $stats,
                'monthLabels' => $monthLabels,
                'monthCounts' => $monthCounts,
                'meta'               => $this->metaTitle('Cookie consent data'),
            ]);
    }

    public function visitor(int $visitorId): JsonResponse
    {
        $consumer = Consumer::where('visitor_id', $visitorId)->first();

        $clicks = Click::where('visitor_id', $visitorId)
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        $commissions = $consumer
            ? Commission::where('consumer_id', $consumer->id)
                ->orderByDesc('id')
                ->limit(20)
                ->get()
            : collect();

        $lenderIds = $clicks->pluck('lender_id')
            ->merge($commissions->pluck('lender_id'))
            ->filter()
            ->unique();

        $lenders = Lender::whereIn('id', $lenderIds)->pluck('name', 'id');

        $fmt = fn($date) => $date ? Carbon::parse($date)->format('d.m.Y H:i') : null;

        return response()->json([
            'consumer'    => $consumer ? [
                'first_name'        => $consumer->first_name,
                'last_name'         => $consumer->last_name,
                'email'             => $consumer->email,
                'phone'             => $consumer->phone,
                'email_verified_at' => $fmt($consumer->email_verified_at),
                'subscribed'        => $consumer->subscribed,
                'created_at'        => $fmt($consumer->created_at),
            ] : null,
            'clicks'      => $clicks->map(fn($c) => [
                'created_at'   => $fmt($c->created_at),
                'utm_source'   => $c->utm_source,
                'utm_campaign' => $c->utm_campaign,
                'lender'       => $lenders[$c->lender_id] ?? ('ID: ' . $c->lender_id),
            ]),
            'commissions' => $commissions->map(fn($c) => [
                'created_at' => $fmt($c->created_at),
                'commission' => $c->commission,
                'status'     => $c->status,
                'lender'     => $lenders[$c->lender_id] ?? ('ID: ' . $c->lender_id),
            ]),
        ]);
    }
}