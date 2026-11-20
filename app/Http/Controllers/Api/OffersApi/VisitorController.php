<?php

namespace App\Http\Controllers\Api\OffersApi;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use Illuminate\Http\JsonResponse;

class VisitorController extends Controller
{
    public function index(): JsonResponse
    {
        $visitor_count = Visitor::query()
            ->where('updated_at', '>=', date("Y-m-d H:i:s", strtotime("-5 minutes")))
            ->where('user_agent', 'not like', '%bot%')
            ->where('user_agent', 'not like', '%crawler%')
            ->count();

        return response()
            ->json([
                'message' => $visitor_count
            ]);
    }
}
