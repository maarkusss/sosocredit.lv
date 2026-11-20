<?php

namespace App\Libraries\News;

use App\Models\News\News;
use App\Models\News\NewsCategory;
use App\Traits\WorksWithMeta;

class NewsData
{
    use WorksWithMeta;

    public function newsListData(): array
    {
        $locale = app()->getLocale();
        $selectedCategoryId = request()->c;

        $selectedCategory = null;
        if ($selectedCategoryId) {
            $selectedCategory = NewsCategory::where('lang', $locale)
                ->where('active', 1)
                ->find($selectedCategoryId);
        }

        $news = News::where('lang', $locale)
            ->where('active', 1)
            ->with(['category'])
            ->when($selectedCategory, function ($query) use ($selectedCategory) {
                return $query->where('category_id', $selectedCategory->id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $data = [
            'mains_news_items' => [],
            'secondary_news_items' => [],
            'news_list' => [],
        ];

        foreach ($news as $index => $newsItem) {
            /* @var News $newsItem */

            $key = array_keys($data)[$index] ?? 'news_list'; // First 2 news items in specific array, then in main list

            $data[$key][] = $this->formatNewsListItemData($newsItem);
        }


        $categories = NewsCategory::where('lang', $locale)
            ->where('active', 1)
            ->whereHas('news', function ($query) use ($locale) {
                $query->where('active', 1)
                    ->where('lang', $locale);
            })
            ->orderBy('order', 'asc')
            ->get();

        $categoriesData = [];
        foreach ($categories as $category) {
            $categoriesData[] = [
                'id' => $category->id,
                'name' => $category->name,
            ];
        }

        return [
            'news' => $data,
            'categories' => $categoriesData,
            'selected_category_id' => $selectedCategory ? $selectedCategory->id : null,
        ];
    }

    public function newsItemData(mixed $url): array
    {
        /* @var $post News */
        $post = News::where('active', 1)
            ->with(['category'])
            ->where('url', $url)
            ->first();

        if (!$post) {
            return [];
        }

        $latest_news = News::where('lang', app()->getLocale())
            ->where('id', '!=', $post->id)
            ->where('active', 1)
            ->with(['category'])
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        $latestNewsData = [];
        foreach ($latest_news as $newsItem) {
            $latestNewsData[] = $this->formatNewsListItemData($newsItem);
        }

        return [
            'post' => [
                'id' => $post->id,
                'title' => $post->title,
                'tiny_description' => $post->tiny_description,
                'text' => $post->text,
                'category_name' => $post->category ? strtolower($post->category->name) : '',
                'created_at' => $post->created_at->format('d.m.Y'),
                'image' => $post->image ? $post->image : 'images/no_image.png',
                'image_alt_text' => $post->image_alt_text ? $post->image_alt_text : '',
                'source' => $post->source,
            ],
            'meta' => [
                'title' => $post->meta_title ? $this->metaTitle($post->meta_title)['title'] : '',
                'description' => $post->meta_description,
                'keywords' => '',
            ],
            'latest_news' => $latestNewsData,
        ];
    }

    private function formatNewsListItemData(News $newsItem): array
    {
        return [
            'id' => $newsItem->id,
            'title' => $newsItem->title,
            'category_name' => $newsItem->category ? strtolower($newsItem->category->name) : '',
            'created_at' => $newsItem->created_at->format('d.m.Y'),
            'tiny_description' => $newsItem->tiny_description,
            'image' => $newsItem->image ? $newsItem->image : 'images/no_image.png',
            'image_alt_text' => $newsItem->image_alt_text ? $newsItem->image_alt_text : '',
            'url' => $newsItem->url,
        ];
    }
}
