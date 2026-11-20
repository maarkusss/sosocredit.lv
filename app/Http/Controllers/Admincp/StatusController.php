<?php

namespace App\Http\Controllers\Admincp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ApplicationStatus;
use App\Models\Lender;

class StatusController extends Controller
{
    public function index()
    {
        $statuses = ApplicationStatus::select('id', 'name', 'multiplier')
            ->orderBy('name')
            ->get();

        $providers = Lender::where('is_provider', 1)
            ->select('id', 'name', 'base_priority', 'image')
            ->orderBy('base_priority', 'desc')
            ->get();

        $page_title = 'Provider & Status Settings';

        return view('admincp.statuses.index', [
            'statuses' => $statuses,
            'providers' => $providers,
            'page_title' => $page_title
        ]);
    }

    public function update(Request $request)
    {
        if ($request->has('providers')) {
            foreach ($request->providers as $providerData) {
                Lender::where('id', $providerData['id'])
                    ->update(['base_priority' => $providerData['base_priority']]);
            }
        }

        if ($request->has('statuses')) {
            foreach ($request->statuses as $statusData) {
                ApplicationStatus::where('id', $statusData['id'])
                    ->update(['multiplier' => $statusData['multiplier']]);
            }
        }

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }
}
