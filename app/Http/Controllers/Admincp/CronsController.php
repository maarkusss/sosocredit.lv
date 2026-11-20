<?php

namespace App\Http\Controllers\Admincp;

use App\Models\Cron;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CronsController extends AdminController
{
    public function index(): Renderable
    {
        $per_page = 20;

        return view('admincp.crons.index')
            ->with([
                'crons' => Cron::paginate($per_page),
                'meta' => $this->metaTitle('Crons'),
            ]);
    }

    public function edit(Cron $cron): Renderable
    {
        return view('admincp.crons.edit')
            ->with([
                'cron' => $cron,
                'meta' => $this->metaTitle('Edit cron'),
            ]);
    }

    public function update(Cron $cron, Request $request): RedirectResponse
    {
        $cron->fill($request->toArray());
        $cron->save();

        return redirect()->back()
            ->with([
                'success' => 'Cron updated successfully',
            ]);
    }
}
