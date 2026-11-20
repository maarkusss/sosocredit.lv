<?php

namespace App\Http\Controllers\Api\OffersApi;

use App\Http\Controllers\Controller;
use App\Models\Lender\LenderVisit;
use App\Models\Lender\LenderVisitSorting;
use App\Models\Lender;
use Illuminate\Http\JsonResponse;

class SessionController extends Controller
{
    public function index(): JsonResponse
    {
        $lenders = Lender::get();
        $lenders_array = [];

        $date[0] = request()->date_from;
        $date[1] = request()->date_to;

        $i = 1;
        foreach ($lenders as $lender) {
            $lenders_array[$i]['name'] = $lender->name;

            if (request()->date_from && request()->date_to) {
                $total_sessions = LenderVisit::where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($date[0])))->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($date[1])))->where('bot', 0)->count();

                if (request()->position) {
                    $sessions = LenderVisitSorting::where('lender_id', $lender->id)
                        ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($date[0])))
                        ->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($date[1])))
                        ->where('position', request()->position)
                        ->where('bot', 0)
                        ->get();

                    $lenders_array[$i]['sessions'] = count($sessions);
                } else {
                    $sessions = LenderVisitSorting::where('lender_id', $lender->id)
                        ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($date[0])))
                        ->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($date[1])))
                        ->where('bot', 0)
                        ->get();

                    $lenders_array[$i]['sessions'] = count($sessions);
                }

                $lenders_array[$i]['total_sessions'] = $total_sessions;
            } else {
                $total_sessions = LenderVisit::where('created_at', '>=', date('Y-m-d 00:00:00', strtotime('-30 days')))->where('created_at', '<=', date('Y-m-d 23:59:59'))->where('bot', 0)->count();

                if (request()->position) {
                    $sessions = LenderVisitSorting::where('lender_id', $lender->id)
                        ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime('-30 days')))
                        ->where('created_at', '<=', date('Y-m-d 23:59:59'))
                        ->where('position', request()->position)
                        ->where('bot', 0)
                        ->get();

                    $lenders_array[$i]['sessions'] = count($sessions);
                } else {
                    $sessions = LenderVisitSorting::where('lender_id', $lender->id)
                        ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime('-30 days')))
                        ->where('created_at', '<=', date('Y-m-d 23:59:59'))
                        ->where('bot', 0)
                        ->get();

                    $lenders_array[$i]['sessions'] = count($sessions);
                }

                $lenders_array[$i]['total_sessions'] = $total_sessions;
            }

            $i++;
        }

        return response()
            ->json($lenders_array);
    }
}
