<?php

namespace App\Http\Controllers\Admincp;

use App\Models\ConnectedUrl;
use App\Traits\WorksWithMeta;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ConnectedUrlsController extends AdminController
{
    use WorksWithMeta;

    public function index(): Renderable
    {
        $connected_urls = ConnectedUrl::query()
            ->where('lang', app()->getLocale())
            ->orderBy('url', 'ASC')
            ->get();

        return view('admincp.connected_urls.index', [
            'connected_urls' => $connected_urls,
            'meta' => $this->metaTitle('Connected URLs'),
        ]);
    }

    public function create(): Renderable
    {
        $related_urls = [];
        foreach (inactive_locales() as $locale) {
            $related_urls[$locale] = '';
        }

        $connected_url = new ConnectedUrl();
        $connected_url->lang = app()->getLocale();

        return view('admincp.connected_urls.edit', [
            'connected_url' => $connected_url,
            'meta' => $this->metaTitle('Add Connected URL'),
            'page_title' => 'Create Connected URL',
            'related_urls' => $related_urls,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $locale = app()->getLocale();
        $related_urls = $request->input('related_urls');
        $url = $request->input('url');

        if (is_array($related_urls)) {
            $all_urls = $related_urls;
            $all_urls[$locale] = $url;

            ConnectedUrl::query()
                ->updateOrCreate([
                    'url' => $url,
                ], [
                    'lang' => app()->getLocale(),
                    'related_urls' => $related_urls,
                ]);

            foreach ($related_urls as $locale => $related_url) {
                $recombined_related_urls = $all_urls;
                unset($recombined_related_urls[$locale]);

                ConnectedUrl::query()
                    ->updateOrCreate([
                        'url' => $related_url,
                    ], [
                        'lang' => $locale,
                        'related_urls' => $recombined_related_urls,
                    ]);
            }
        }

        return redirect()
            ->back()
            ->with([
                'success' => 'Connected URL saved successfully',
            ]);
    }

    /**
     * @return Renderable|RedirectResponse
     */
    public function edit(ConnectedUrl $connected_url)
    {
        $related_urls = $connected_url->related_urls;

        if ($connected_url->lang != app()->getLocale()) {
            return redirect()->route('admincp.connected-urls.index');
        }

        return view('admincp.connected_urls.edit', [
            'connected_url' => $connected_url,
            'meta' => $this->metaTitle('Edit Connected URL'),
            'related_urls' => $related_urls,
        ]);
    }

    public function update(ConnectedUrl $connected_url, Request $request): RedirectResponse
    {
        $lang = $connected_url->lang;
        $related_urls = $request->input('related_urls');
        $url = $request->input('url');

        $connected_url->update([
            'lang' => $lang,
            'related_urls' => $related_urls,
        ]);

        return redirect()->back();
    }

    public function destroy(ConnectedUrl $connected_url): RedirectResponse
    {
        if ($connected_url && $connected_url->id) {
            $this->logAdminAction('Deleted Connected URLs ' . $connected_url->url);

            $connected_url->delete();

            return redirect()
                ->route('admincp.connected-urls.index')
                ->with([
                    'success' => 'Connected URL deleted successfully',
                ]);
        }

        return redirect()->back();
    }
}
