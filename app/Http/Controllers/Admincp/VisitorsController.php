<?php

namespace App\Http\Controllers\Admincp;

use App\Models\Click;
use App\Models\Visitor;
use Illuminate\Contracts\Support\Renderable;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;

class VisitorsController extends AdminController
{
    public function index(Request $request): Renderable
    {
        return view('admincp.visitors.index')
            ->with([
                'visitors' => $this->getAllVisitors($request),
                'agent' => new Agent(),
                'meta' => $this->metaTitle('Visitors'),
            ]);
    }

    public function show(Visitor $visitor): Renderable
    {
        $visitor->fillCountry();

        return view('admincp.visitors.show')
            ->with([
                'visitor' => $visitor,
                'clicks' => $this->getClicksOfVisitor($visitor),
                'agent' => new Agent(),
                'meta' => $this->metaTitle('View visitor'),
            ]);
    }

    public function getAllVisitors(Request $request)
    {
        $per_page = 20;

        if ($request->input('search')) {
            $per_page = 50;
            $records = Visitor::query()
                ->where('id', $request->input('search'))
                ->orderBy('id', 'DESC')
                ->paginate($per_page);

            if (count($records) == 0) {
                $records = Visitor::query()
                    ->where('ip', $request->input('search'))
                    ->orderBy('id', 'DESC')
                    ->paginate($per_page);
            }
        } else {
            $records = Visitor::query()
                ->orderBy('id', 'DESC')
                ->paginate($per_page);
        }

        return $records;
    }

    public function getClicksOfVisitor(Visitor $visitor)
    {
        return Click::query()
            ->where('visitor_id', $visitor->id)
            ->orderBy('id', 'DESC')
            ->paginate(50);
    }
}
