<?php

namespace App\Models\News;

use App\Models\Image;
use App\Models\Section;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image as InterventionImage;
use Illuminate\Http\Request;

/**
 * @property $id
 * @property $category_id
 * @property $author_id
 * @property $lang
 * @property $url
 * @property $image
 * @property $image_alt_text
 * @property $title
 * @property $tiny_description
 * @property $text
 * @property $active
 * @property $order
 * @property $meta_title
 * @property $meta_description
 * @property $source
 * @property $created_at
 * @property $updated_at
 * @property $deleted_at
 */
class News extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $table = 'news';

    public function category(): HasOne
    {
        return $this->hasOne(NewsCategory::class, 'id', 'category_id');
    }

    public function uploadImage(Request $request): void
    {
        if (!$request->image_changed) {
            \Log::info('Image not changed', $request->all());
            return;
        }

        if (!$request->file('image')) {
            if ($this->image) {
                Storage::disk('public_folder')->delete($this->image);

                $this->image = null;
                $this->save();
            }

            return;
        }

        $imageName = explode('.', $request->file('image')->getClientOriginalName());

        $image = InterventionImage::make($request->file('image'))
            ->encode('jpg', 80);

        $imageFullName = $imageName[0] . '.jpg';
        $fullPath = 'images/news/' . $this->id.'_'.$imageFullName;

        if (Image::where('path', $fullPath)->exists()) {
            Log::error('Image name already exists!', [
                'fullPath' => $fullPath,
                'news_id' => $this->id,
            ]);

            return;
        }

        Storage::disk('public_folder')->put($fullPath, $image);

        $this->image = $fullPath;
        $this->save();
    }

    public function canonical_url(): string
    {
        $section = Section::where('template', 'news')
            ->where('lang', $this->lang)
            ->first();

        return route('section.deep', [
            'name' => $section->route_name,
            'deep_name' => $this->url,
        ]);
    }
}
