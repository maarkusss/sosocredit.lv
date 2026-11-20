<?php

namespace App\Http\Controllers\Admincp;

use App\Models\ConnectedUrl;
use App\Models\News\News;
use App\Models\News\NewsCategory;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NewsController extends AdminController
{
    public function index(): Renderable
    {
        $per_page = 20;

        $news = News::where('lang', app()->getLocale())
            ->orderBy('id', 'desc')
            ->paginate($per_page);

        return view('admincp.news.index')
            ->with([
                'news' => $news,
                'meta' => $this->metaTitle('News'),
            ]);
    }

    public function updateAll(): RedirectResponse
    {
        foreach (request()->news['active'] as $id => $value) {
            News::where('id', $id)
                ->update([
                    'active' => $value
                ]);
        }

        $this->logAdminAction('Updated all news items info on News main page');

        return redirect()
            ->back()
            ->with('success', 'News updated!');
    }

    public function create(): Renderable
    {
        return view('admincp.news.edit')
            ->with([
                'meta' => $this->metaTitle('Add News'),
                'page_title' => 'Add News',
                'news' => new News,
                'categories' => NewsCategory::allCategories(),
            ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => 'required',
            'category_id' => 'required',
            'url' => 'required|string|min:1',
        ]);

        $news = new News;

        $news->category_id = $data['category_id'];
        $news->author_id = auth()->user()->id;
        $news->lang = app()->getLocale();
        $news->url = $data['url'];
        $news->title = $data['title'];
        $news->tiny_description = $request->tiny_description;
        $news->text = $request->text;
        $news->image_alt_text = $request->image_alt_text;
        $news->active = !empty($request->active) ? 1 : 0;
        $news->meta_title = $request->meta_title;
        $news->meta_description = $request->meta_description;
        $news->source = $request->source;

        $news->save();

        $news->uploadImage($request);

        $this->logAdminAction('Created news item ' . $data['title']);

        return redirect()->route('admincp.news.index')
            ->with([
                'success' => 'News item has been created!',
            ]);
    }

    /**
     * @return Renderable|RedirectResponse
     */
    public function edit(News $news)
    {
        if ($news->lang != app()->getLocale()) {
            return redirect()->route('admincp.news.index');
        }

        $related_urls = ConnectedUrl::getRelatedUrlsForRoute($news->canonical_url());

        return view('admincp.news.edit')->with([
            'news' => $news,
            'meta' => $this->metaTitle('Edit News'),
            'categories' => NewsCategory::allCategories(),
            'related_urls' => $related_urls,
        ]);
    }

    public function update(News $news, Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category_id' => 'required',
            'title' => 'required',
            'url' => 'required|string|min:1',
        ]);

        $news->update([
            'category_id' => $data['category_id'],
            'lang' => app()->getLocale(),
            'url' => $data['url'],
            'title' => $data['title'],
            'tiny_description' => $request->tiny_description,
            'image_alt_text' => $request->image_alt_text,
            'text' => $request->text,
            'active' => !empty($request->active) ? 1 : 0,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'source' => $request->source,
        ]);

        $news->uploadImage($request);

        $this->logAdminAction('Edited news item ' . $data['title']);

        return redirect()
            ->back()
            ->with('success', 'News item updated!');
    }

    public function destroy($id): RedirectResponse
    {
        News::where('id', $id)->delete();

        $this->logAdminAction('Deleted news item ' . $id);

        return redirect(route('admincp.news.index'))
            ->with('success', 'News item has been deleted!');
    }
}
