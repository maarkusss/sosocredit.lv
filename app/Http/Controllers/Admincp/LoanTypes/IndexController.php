<?php

namespace App\Http\Controllers\Admincp\LoanTypes;

use App\Http\Controllers\Admincp\AdminController;
use App\Models\LoanType;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;

class IndexController extends AdminController
{
    public function index(): Renderable
    {
        return view('admincp.loantypes.index')
            ->with([
                'loantypes' => $this->getAllLoanTypes(),
                'meta' => $this->metaTitle('Loan types'),
            ]);
    }

    public function getAllLoanTypes(): Collection
    {
        return LoanType::query()
            ->where('lang', app()->getLocale())
            ->orderBy('order', 'ASC')
            ->get();
    }

    public function update(): RedirectResponse
    {
        $arr = request()->loantypes['order'];
        asort($arr);

        $i = 0;
        foreach ($arr as $id => $value) {
            $i++;
            LoanType::query()
                ->where('id', $id)
                ->update([
                    'order' => $i,
                ]);
        }

        foreach (request()->loantypes['active'] as $id => $value) {
            LoanType::query()
                ->where('id', $id)
                ->update([
                    'active' => $value
                ]);
        }

        $this->logAdminAction('Updated all loantypes info on Loantypes main page');

        return redirect()
            ->back()
            ->with('success', 'Loantypes updated!');
    }
}
