<?php

namespace App\Http\Controllers\Admincp;

use App\Http\Requests\Admincp\SaveLenderFilterRequest;
use App\Models\Lender;
use App\Models\Lender\LenderFilter;
use App\Models\LenderCampaign;
use App\Traits\WorksWithMeta;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LenderFiltersController extends AdminController
{
    use WorksWithMeta;

    public function index(Request $request): Renderable
    {
        $filters = LenderFilter::query();

        if ($utm_source = $request->utm_source) {
            $filters = $filters->where('utm_source', $utm_source);
        }

        if ($utm_campaign = $request->utm_campaign) {
            $filters = $filters->where('utm_campaign', 'like', $utm_campaign . '%');
        }

        if ($token = $request->token) {
            $filters = $filters->where('token', 'like', $token . '%');
        }

        $filters = $filters->orderBy('id', 'ASC')
            ->paginate(100);

        return view('admincp.lenders.filters.index', [
            'filters' => $filters,
            'meta' => $this->metaTitle('Lender Filters'),
        ]);
    }

    public function create(): Renderable
    {
        LenderCampaign::linkToLendersTable();

        return view('admincp.lenders.filters.edit', [
            'filter' => new LenderFilter(),
            'lenders' => Lender::linkedToCampaign()->orderBy('name', 'ASC')->get(),
            'meta' => $this->metaTitle('Add Filter'),
            'page_title' => 'Add a Lender Filter',
        ]);
    }

    public function store(SaveLenderFilterRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['active'] = $validated['active'] ?? 0;
        $validated['rules'] = $validated['rules'] ?? [];
        $validated['token'] = $validated['token'] ?? null;

        if (!empty($validated['rules']['set_campaigns_frequencies'])) {
            $validated['rules']['set_campaigns_frequencies'] = collect($validated['rules']['set_campaigns_frequencies'])
                ->reject(function ($value) {
                    return empty($value);
                });
        }

        $filter = new LenderFilter();
        $filter->fill($validated);
        $filter->save();

        $this->logAdminAction('Created lender filter "' . $filter->utm_source . ' ' . $filter->utm_campaign . '"');

        LenderFilter::flushCache();

        return redirect(route('admincp.lenders.filters.index'))
            ->with([
                'success' => 'Lender filter has been added!'
            ]);
    }

    public function edit(LenderFilter $filter): Renderable
    {
        return view('admincp.lenders.filters.edit', [
            'filter' => $filter,
            'lenders' => Lender::linkedToCampaign()->orderBy('name', 'ASC')->get(),
            'meta' => $this->metaTitle('Edit Filter'),
        ]);
    }

    public function update(LenderFilter $filter, SaveLenderFilterRequest $request)
    {
        $validated = $request->validated();
        $validated['active'] = $validated['active'] ?? 0;
        $validated['rules'] = $validated['rules'] ?? [];
        $validated['token'] = $validated['token'] ?? null;

        if (!empty($validated['rules']['set_campaigns_frequencies'])) {
            $validated['rules']['set_campaigns_frequencies'] = collect($validated['rules']['set_campaigns_frequencies'])
                ->reject(function ($value) {
                    return empty($value);
                });
        }

        $filter->update($validated);

        $this->logAdminAction('Updated lender filter "' . $filter->utm_source . ':' . $filter->utm_campaign . '"');

        LenderFilter::flushCache();

        return redirect()
            ->back()
            ->with('success', 'Lender Filter updated!');
    }

    public function destroy(LenderFilter $filter): RedirectResponse
    {
        if ($filter && $filter->id) {
            $this->logAdminAction('Deleted lender filter ' . $filter->utm_source . ' ' . $filter->utm_campaign);

            $filter->delete();

            return redirect()
                ->route('admincp.lenders.filters.index')
                ->with([
                    'success' => 'Lender filter deleted successfully',
                ]);
        }

        return redirect()->back();
    }
}
