<?php

namespace App\Http\Controllers\Admincp\LoanTypes;

use App\Http\Controllers\Admincp\AdminController;
use App\Models\Lender\LenderCategory;
use App\Models\LoanType;
use App\Models\LoanType\LoanTypeHistory;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EditController extends AdminController
{
    public function index(int $loantype_id): Renderable
    {
        return view('admincp.loantypes.edit')->with([
            'loantype' => $this->getLoanTypeById($loantype_id),
            'history' => $this->getLoanTypeHistory($loantype_id),
            'meta' => $this->metaTitle('Edit a loan type'),
        ]);
    }

    public function getLoanTypeById($id)
    {
        if (request()->history) {
            return LoanTypeHistory::query()
                ->where('loan_type_id', $id)
                ->where('id', request()->history)
                ->firstOrFail();
        }

        return LoanType::findOrFail($id);
    }

    public function update($id, Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required',
            'route_name' => 'required',
            'title' => 'required',
        ]);

        LoanTypeHistory::create([
            'user_id' => auth()->id(),
            'loan_type_id' => $id,
            'lang' => app()->getLocale(),
            'route_name' => $data['route_name'],
            'name' => $data['name'],
            'title' => $request->title,
            'description' => $request->description,
            'keywords' => $request->keywords,
            'text' => $request->text,
        ]);

        LoanType::where('id', $id)->update([
            'active' => $request->active ?? 0,
            'lang' => app()->getLocale(),
            'name' => $data['name'],
            'route_name' => $data['route_name'],
            'title' => $request->title,
            'description' => $request->description,
            'keywords' => $request->keywords,
            'text' => $request->text,
        ]);

        $this->logAdminAction('Updated loantype "' . $data['name'] . '"');

        return redirect(route('admincp.loantypes.index'))
            ->with('success', 'Loan type has been updated!');
    }

    public function destroy($id): RedirectResponse
    {
        LoanType::query()
            ->where('id', $id)
            ->delete();

        LenderCategory::query()
            ->where('loan_type_id', $id)
            ->delete();

        $this->logAdminAction('Deleted loantype "' . $id . '"');

        return redirect(route('admincp.loantypes.index'))
            ->with('success', 'Loantype has been deleted!');
    }

    public function getLoanTypeHistory($loantype_id)
    {
        return LoanTypeHistory::query()
            ->where('loan_type_id', $loantype_id)
            ->orderBy('created_at', 'DESC')
            ->get();
    }
}
