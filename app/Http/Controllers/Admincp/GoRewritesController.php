<?php

namespace App\Http\Controllers\Admincp;

use App\Entity\GoLink;
use App\Http\Requests\Admincp\SaveGoRewriteRequest;
use App\Http\Requests\Admincp\UpdateAllGoRewritesRequest;
use App\Models\GoRewrite;
use App\Models\Lender;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;

class GoRewritesController extends AdminController
{
    public function __construct(protected GoLink $go_link)
    {
    }

    public function index(): Renderable
    {
        $per_page = 20;

        return view('admincp.go_rewrites.index')
            ->with([
                'go_rewrites' => GoRewrite::paginate($per_page),
                'meta' => $this->metaTitle('Go Rewrites'),
            ]);
    }

    public function updateAll(UpdateAllGoRewritesRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $active_array = $data['active'];

        foreach ($active_array as $go_rewrite_id => $active) {
            GoRewrite::query()
                ->where('id', $go_rewrite_id)
                ->update([
                    'active' => (int)$active,
                ]);
        }

        $this->logAdminAction('Updated all Go Rewrites!');

        $this->refreshCache();

        return redirect()
            ->back()
            ->with('success', 'Go Rewrites updated!');
    }

    public function create(): Renderable
    {
        return view('admincp.go_rewrites.edit')
            ->with([
                'meta' => $this->metaTitle('Add a Go Rewrite'),
                'page_title' => 'Add a Go Rewrite',
                'go_rewrite' => new GoRewrite(),
            ]);
    }

    public function store(SaveGoRewriteRequest $request): RedirectResponse
    {
        $go_rewrite = new GoRewrite();
        $go_rewrite->fill($request->validated());

        $go_rewrite->params_from = [];
        if ($request->input('params_from')) {
            $go_rewrite->params_from = json_decode($request->input('params_from'), true);
        }

        $go_rewrite->params_to = [];
        if ($request->input('params_to')) {
            $go_rewrite->params_to = json_decode($request->input('params_to'), true);
        }

        $go_rewrite->save();

        $this->logAdminAction('Created Go Rewrite "' . $go_rewrite->id . '"');

        $this->refreshCache();

        return redirect(route('admincp.go-rewrites.index'))
            ->with('success', 'Go Rewrite has been created!');
    }

    public function edit(GoRewrite $go_rewrite): Renderable
    {
        return view('admincp.go_rewrites.edit')
            ->with([
                'go_rewrite' => $go_rewrite,
                'meta' => $this->metaTitle('Edit a Go Rewrite'),
            ]);
    }

    public function previewAllActive(int $position = 0)
    {
        $lenders = Lender::active()
            ->get()
            ->map(function ($lender) use (&$position) {
                $lender->go_link = $this->go_link->build(lender: $lender, position: ++$position);

                return $lender;
            });

        $go_rewrites = GoRewrite::getCachedRewrites();

        return view('admincp.go_rewrites.preview', [
            'go_link' => $this->go_link,
            'go_rewrites' => $go_rewrites,
            'lenders' => $lenders,
            'meta' => $this->metaTitle('Go Rewrites Preview'),
            'page_title' => 'Previewing All Active Rewrites',
            'show_custom_header' => true,
            'hide_footer' => true,
        ]);
    }

    public function preview(GoRewrite $go_rewrite, int $position = 0)
    {
        $lenders = Lender::active()
            ->get()
            ->map(function ($lender) use ($go_rewrite, &$position) {
                $lender->go_link = $this->go_link->build(lender: $lender, position: ++$position, go_rewrite: $go_rewrite);

                return $lender;
            });

        return view('admincp.go_rewrites.preview', [
            'go_link' => $this->go_link,
            'go_rewrite' => $go_rewrite,
            'lenders' => $lenders,
            'meta' => $this->metaTitle('Go Rewrite Preview'),
            'page_title' => 'Previewing "' . $go_rewrite->name . '" Rewrite',
            'show_custom_header' => true,
            'hide_footer' => true,
        ]);
    }

    public function update(GoRewrite $go_rewrite, SaveGoRewriteRequest $request): RedirectResponse
    {
        $should_refresh_cache = false;
        $go_rewrite->fill($request->validated());
        if (!$request->has('active')) {
            $go_rewrite->active = 0;
        }

        $go_rewrite->params_from = [];
        if ($request->input('params_from')) {
            $go_rewrite->params_from = json_decode($request->input('params_from'), true);
        }

        $go_rewrite->params_to = [];
        if ($request->input('params_to')) {
            $go_rewrite->params_to = json_decode($request->input('params_to'), true);
        }

        if ($go_rewrite->isDirty('active')) {
            $should_refresh_cache = true;
        }

        $go_rewrite->save();

        $this->logAdminAction('Updated Go Rewrite "' . $go_rewrite->id . '"');

        if ($should_refresh_cache === true) {
            $this->refreshCache();
        }

        return redirect()
            ->back()
            ->with('success', 'Go Rewrite updated successfully');
    }

    public function destroy(GoRewrite $go_rewrite): RedirectResponse
    {
        $this->logAdminAction('Deleted Go Rewrite ' . $go_rewrite->id);

        $go_rewrite->delete();

        return redirect()
            ->route('admincp.go-rewrites.index')
            ->with([
                'success' => 'Go Rewrite deleted successfully',
            ]);
    }

    protected function refreshCache(): void
    {
        Cache::forget(GoRewrite::CACHE_KEY);
    }
}
