<?php

namespace App\Http\Controllers\Admincp;

use App\Models\Redirect;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RedirectsController extends AdminController
{
    public function index(): Renderable
    {
        $per_page = 20;

        return view('admincp.redirects.index', [
            'redirects' => Redirect::query()
                ->orderBy('from', 'ASC')
                ->paginate($per_page),
            'meta' => $this->metaTitle('Redirects'),
        ]);
    }

    public function create(Request $request): Renderable
    {
        return view('admincp.redirects.edit', [
            'redirect' => new Redirect(),
            'request' => $request,
            'meta' => $this->metaTitle('Add Redirect'),
            'page_title' => 'Add Redirect',
            'codes' => Redirect::$CODES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validate($request, [
            'from' => 'required|string',
            'to' => 'required|string',
            'code' => 'required|string',
        ]);

        $redirect = new Redirect();
        $redirect->fill($validated);
        $redirect->save();

        return redirect()
            ->route('admincp.redirects.edit', $redirect->id)
            ->with([
                'success' => 'Redirect successfully added',
            ]);
    }

    public function edit(Redirect $redirect): Renderable
    {
        return view('admincp.redirects.edit', [
            'redirect' => $redirect,
            'meta' => $this->metaTitle('Edit Redirect'),
            'codes' => Redirect::$CODES,
        ]);
    }

    public function update(Request $request, Redirect $redirect): RedirectResponse
    {
        $redirect->fill($request->only([
            'from',
            'to',
            'code'
        ]));
        $redirect->save();

        $this->logAdminAction('Edited redirect ' . $redirect->from);

        return redirect()
            ->back()
            ->with([
                'success' => 'Redirect updated successfully',
            ]);
    }

    public function destroy(Redirect $redirect): RedirectResponse
    {
        if ($redirect && $redirect->id) {
            $this->logAdminAction('Deleted redirect ' . $redirect->from);

            $redirect->delete();

            return redirect()
                ->route('admincp.redirects.index')
                ->with([
                    'success' => 'Redirect from ' . $redirect->from . ' deleted successfully',
                ]);
        }

        return redirect()->back();
    }
}
