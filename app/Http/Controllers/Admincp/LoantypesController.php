<?php

namespace App\Http\Controllers\Admincp;

use App\Models\ConnectedUrl;
use App\Models\Lender\LenderCategory;
use App\Models\LoanType;
use App\Models\LoanType\LoanTypeHistory;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LoantypesController extends AdminController
{
    public function index(): Renderable
    {
        $loantypes = LoanType::query()
            ->where('lang', app()->getLocale())
            ->orderBy('order', 'ASC')
            ->get();

        return view('admincp.loantypes.index')
            ->with([
                'loantypes' => $loantypes,
                'meta' => $this->metaTitle('Loan types'),
            ]);
    }

    public function updateAll(): RedirectResponse
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
            ->with('success', 'Loantypes updated');
    }

    public function create(): Renderable
    {
        return view('admincp.loantypes.edit')
            ->with([
                'history' => new Collection(),
                'loantype' => new Loantype(),
                'meta' => $this->metaTitle('Add a loan type'),
                'page_title' => 'Add a loan type',
            ]);
    }

    /**
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'route_name' => 'required',
            'title' => 'required',
        ]);

        $loantype = new LoanType();
        $loantype->lang = app()->getLocale();
        $loantype->order = $this->getLastOrderId();
        $loantype->name = $data['name'];
        $loantype->route_name = $data['route_name'];
        $loantype->title = $request->title;
        $loantype->description = $request->description;
        $loantype->keywords = $request->keywords;
        $loantype->text = $request->text;
        $loantype->active = $request->input('active') ? 1 : 0;
        $loantype->h1 = $request->input('h1') ?? null;
        $loantype->h1_description = $request->input('h1_description') ?? null;
        $loantype->save();

        LoanTypeHistory::create([
            'user_id' => Auth()->id(),
            'loan_type_id' => $loantype->id,
            'lang' => app()->getLocale(),
            'name' => $data['name'],
            'route_name' => $data['route_name'],
            'title' => $request->title,
            'description' => $request->description,
            'keywords' => $request->keywords,
            'text' => $request->text,
        ]);

        $this->logAdminAction('Created loantype "' . $data['name'] . '"');

        return redirect(route('admincp.loantypes.index'))
            ->with([
                'success' =>  'Loan type added successfully',
            ]);
    }

    /**
     * @return Renderable|RedirectResponse
     */
    public function edit(int $loantype)
    {
        if (request()->history) {
            $loantype = LoanTypeHistory::query()
                ->where('loan_type_id', $loantype)
                ->where('id', request()->history)
                ->firstOrFail();
        } else {
            $loantype = LoanType::findOrFail($loantype);
        }

        /** @var LoanType $loantype */

        if ($loantype->lang != app()->getLocale()) {
            return redirect()->route('admincp.loantypes.index');
        }

        $related_urls = ConnectedUrl::getRelatedUrlsForRoute($loantype->canonical_url());

        $history = LoanTypeHistory::query()
            ->where('loan_type_id', $loantype)
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('admincp.loantypes.edit')->with([
            'loantype' => $loantype,
            'history' => $history,
            'meta' => $this->metaTitle('Edit a loan type'),
            'related_urls' => $related_urls,
        ]);
    }

    public function update(int $loantype, Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required',
            'route_name' => 'required',
            'title' => 'required',
        ]);

        LoanTypeHistory::create([
            'user_id' => auth()->id(),
            'loan_type_id' => $loantype,
            'lang' => app()->getLocale(),
            'route_name' => $data['route_name'],
            'name' => $data['name'],
            'title' => $request->title,
            'description' => $request->description,
            'keywords' => $request->keywords,
            'text' => $request->text,
            'h1' => $request->h1 ?? null,
            'h1_description' => $request->h1_description ?? null,
        ]);

        LoanType::query()
            ->where('id', $loantype)
            ->update([
                'active' => $request->active ?? 0,
                'lang' => app()->getLocale(),
                'name' => $data['name'],
                'route_name' => $data['route_name'],
                'title' => $request->title,
                'description' => $request->description,
                'keywords' => $request->keywords,
                'text' => $request->text,
                'h1' => $request->h1 ?? null,
                'h1_description' => $request->h1_description ?? null,
            ]);

        $this->logAdminAction('Updated loantype "' . $data['name'] . '"');

        return redirect()
            ->back()
            ->with([
                'success' => 'Loan type updated successfully'
            ]);
    }

    public function destroy(LoanType $loantype): RedirectResponse
    {
        if ($loantype && $loantype->id) {
            $this->logAdminAction('Deleted loantype ' . $loantype->name);

            LenderCategory::query()
                ->where('loan_type_id', $loantype->id)
                ->delete();

            $loantype->delete();

            return redirect()
                ->route('admincp.loantypes.index')
                ->with([
                    'success' => 'Loantype deleted successfully',
                ]);
        }

        return redirect()->back();
    }

    protected function getLastOrderId(): int
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
