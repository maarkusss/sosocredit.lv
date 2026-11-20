<?php

namespace App\Http\Controllers\Admincp\Lenders;

use App\Http\Controllers\Admincp\AdminController;
use App\Models\Lender\LenderSorting;
use App\Models\Lender\LenderSortingEpc;
use App\Models\Lender;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;

class IndexController extends AdminController
{
    public function index(): Renderable
    {
        return view('admincp.lenders.index')
            ->with([
                'lenders' => $this->getAllLenders(),
                'sortings' => $this->getAllSortings(),
                'meta' => $this->metaTitle('Lenders'),
            ]);
    }

    public function getAllLenders(): array
    {
        $lenders_array = [];

        if (request()->sorting) {
            foreach (LenderSortingEpc::query()
                         ->where('sorting_id', request()->sorting)
                         ->orderBy('epc', 'DESC')
                         ->orderBy('clicks', 'DESC')
                         ->get() as $sorting) {
                $sorting->lender->epc = $sorting->epc;
                $sorting->lender->clicks = $sorting->clicks;
                $sorting->lender->earnings = $sorting->earnings;
                $sorting->lender->position = $sorting->position;

                $lenders_array[] = $sorting->lender;
            }
        } else {
            foreach (Lender::query()
                         ->orderBy('guaranteed_epc', 'DESC')
                         ->orderBy('position')
                         ->orderBy('epc', 'DESC')
                         ->get() as $lender) {
                $lenders_array[] = $lender;
            }
        }

        return $lenders_array;
    }

    public function getAllSortings(): Collection
    {
        return LenderSorting::query()
            ->orderBy('clicks', 'DESC')
            ->get();
    }

    public function update(): RedirectResponse
    {
        $arr = request()->position;
        asort($arr);

        $i = 0;
        foreach ($arr as $key => $value) {
            $i++;
            $this->updateLenderPosition($key, $i);
        }

        foreach (request()->frequency as $key => $value) {
            $this->updateLenderFrequency($key, $value);
        }

        foreach (request()->daily_epc as $key => $value) {
            $this->updateLenderDailyEpc($key, $value);
        }

        foreach (request()->daily_epc_before as $key => $value) {
            $this->updateLenderDailyEpcBefore($key, $value);
        }

        foreach (request()->guaranteed_epc as $key => $value) {
            $this->updateLenderGuaranteedEpc($key, $value);
        }

        foreach (request()->active as $key => $value) {
            $this->updateLenderActive($key, $value);
        }

        $this->logAdminAction('Created all lenders!');

        return redirect()
            ->back()
            ->with('success', 'Lenders updated!');
    }

    public function updateLenderPosition($id, $value): void
    {
        $lender = Lender::findOrFail($id);
        $lender->update([
            'position' => $value
        ]);
    }

    public function updateLenderFrequency($id, $value): void
    {
        $lender = Lender::findOrFail($id);
        $lender->update([
            'frequency' => $value
        ]);
    }

    public function updateLenderDailyEpc($id, $value): void
    {
        $lender = Lender::findOrFail($id);
        $lender->update([
            'daily_epc' => $value
        ]);
    }

    public function updateLenderDailyEpcBefore($id, $value): void
    {
        $lender = Lender::findOrFail($id);
        $lender->update([
            'daily_epc_before' => $value
        ]);
    }

    public function updateLenderGuaranteedEpc($id, $value): void
    {
        $lender = Lender::findOrFail($id);
        $lender->update([
            'guaranteed_epc' => $value
        ]);
    }

    public function updateLenderActive($id, $value): void
    {
        $lender = Lender::findOrFail($id);
        $lender->update([
            'active' => $value
        ]);
    }
}
