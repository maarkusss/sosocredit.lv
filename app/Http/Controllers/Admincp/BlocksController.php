<?php

namespace App\Http\Controllers\Admincp;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Traits\WorksWithMeta;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BlocksController extends Controller
{
    use WorksWithMeta;

    public function index(Request $request): Renderable
    {
        $blocks = Block::query()
            ->where('lang', app()->getLocale());

        if ($key = $request->key) {
            $blocks = $blocks->where('key', 'like', $key . '%');
        }

        if ($tag = $request->tag) {
            $blocks = $blocks->where('tag', 'like', '%' . $tag . '%');
        }

        $blocks = $blocks->orderBy('key', 'ASC')
            ->paginate(24);

        return view('admincp.blocks.index', [
            'blocks' => $blocks,
            'meta' => $this->metaTitle('Blocks'),
        ]);
    }

    public function create()
    {
        return view('admincp.blocks.edit', [
            'block' => new Block(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (Block::query()->where('lang', $request->lang)->where('key', $request->key)->exists()) {
            return redirect()
                ->back()
                ->withInput($request->toArray())
                ->with([
                    'error' => 'Block with key "' . $request->key . '" exists',
                ]);
        }

        $block = new Block();
        $block->fill($request->only('lang', 'key', 'value', 'tag'));
        $block->save();

        return redirect()
            ->back()
            ->with([
                'success' => 'Block has been updated',
            ]);
    }

    public function edit(Block $block)
    {
        return view('admincp.blocks.edit', [
            'block' => $block,
        ]);
    }

    public function update(Block $block, Request $request): RedirectResponse
    {
        $block->fill($request->only('value', 'tag'));
        $block->save();

        return redirect()
            ->back()
            ->with([
                'success' => 'Block has been updated',
            ]);
    }

    public function destroy(Block $block): RedirectResponse
    {
        $block->delete();

        return redirect()->route('admincp.blocks.index')->with([
            'success' => 'Block deleted successfully',
        ]);
    }
}
