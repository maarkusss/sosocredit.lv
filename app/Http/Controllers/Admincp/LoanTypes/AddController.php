<?php

namespace App\Http\Controllers\Admincp\LoanTypes;

use App\Http\Controllers\Admincp\AdminController;
use App\Models\LoanType;
use App\Models\LoanType\LoanTypeHistory;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;

class AddController extends AdminController
{
    public function index(): Renderable
    {
        return view('admincp.loantypes.add')
            ->with([
                'meta' => $this->metaTitle('Add a loan type'),
            ]);
    }

    public function create(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'route_name' => 'required',
            'title' => 'required',
        ]);

        $loantypes = new LoanType();
        $loantypes->lang = App()->getLocale();
        $loantypes->order = $this->getLastOrderId();
        $loantypes->name = $data['name'];
        $loantypes->route_name = $data['route_name'];
        $loantypes->title = $request->title;
        $loantypes->description = $request->description;
        $loantypes->keywords = $request->keywords;
        $loantypes->text = $request->text;
        $loantypes->active = $request->input('active') ? 1 : 0;
        $loantypes->save();

        LoanTypeHistory::create([
            'user_id' => Auth()->id(),
            'loan_type_id' => $loantypes->id,
            'lang' => App()->getLocale(),
            'name' => $data['name'],
            'route_name' => $data['route_name'],
            'title' => $request->title,
            'description' => $request->description,
            'keywords' => $request->keywords,
            'text' => $request->text,
        ]);

        $this->logAdminAction('Created loantype "' . $data['name'] . '"');

        return redirect(route('admincp.loantypes.index'))
            ->with('success', 'Loan type has been added!');
    }

    public function getLastOrderId(): int
    {
        $loantypes = LoanType::query()
            ->where('lang', app()->getLocale())
            ->orderBy('order', 'DESC')
            ->first();

        if ($loantypes) {
            return $loantypes->order + 1;
        }

        return 1;
    }
}
