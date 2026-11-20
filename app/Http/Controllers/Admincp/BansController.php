<?php

namespace App\Http\Controllers\Admincp;

use App\Models\Ban;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BansController extends AdminController
{
    public function index(): Renderable
    {
        $per_page = 20;

        return view('admincp.bans.index')
            ->with([
                'bans' => Ban::paginate($per_page),
                'meta' => $this->metaTitle('Bans'),
            ]);
    }

    public function create(): Renderable
    {
        return view('admincp.bans.edit')
            ->with([
                'meta' => $this->metaTitle('Add a ban'),
                'page_title' => 'Add a ban',
                'ban' => new Ban(),
            ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ip' => 'required',
            'reason' => 'required',
        ]);

        Ban::create([
            'ip' => $data['ip'],
            'reason' => $data['reason'],
        ]);

        $this->logAdminAction('Added ban "' . $data['ip'] . '"');

        return redirect(route('admincp.bans.index'))
            ->with('success', 'Ban has been added');
    }

    public function edit(Ban $ban): Renderable
    {
        return view('admincp.bans.edit')
            ->with([
                'ban' => $ban,
                'meta' => $this->metaTitle('Edit a ban'),
            ]);
    }

    public function update(Ban $ban, Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ip' => 'required',
            'reason' => 'required',
        ]);

        $ban->update([
            'ip' => $data['ip'],
            'reason' => $data['reason'],
        ]);

        $this->logAdminAction('Updated ban "' . $ban->ip . '"');

        return redirect()
            ->back()
            ->with('success', 'Ban updated successfully');
    }

    public function destroy(Ban $ban): RedirectResponse
    {
        if ($ban && $ban->id) {
            $this->logAdminAction('Deleted ban ' . $ban->ip);

            $ban->delete();

            return redirect()
                ->route('admincp.bans.index')
                ->with([
                    'success' => 'Ban deleted successfully',
                ]);
        }

        return redirect()->back();
    }
}
