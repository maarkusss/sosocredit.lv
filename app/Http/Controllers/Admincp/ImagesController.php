<?php

namespace App\Http\Controllers\Admincp;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Traits\WorksWithMeta;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image as InterventionImage;

class ImagesController extends Controller
{
    use WorksWithMeta;

    public function index(): Renderable
    {
        $images = Image::query()->orderBy('created_at', 'DESC')->paginate(24);

        return view('admincp.images.index')->with([
            'images' => $images,
            'meta' => $this->metaTitle('Images'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $imageName = explode('.', $request->file('image')->getClientOriginalName());

        $image = InterventionImage::make($request->file('image'))
            ->encode('jpg', 80);

        $image_name = $imageName[0] . '.jpg';
        $full_path = 'images/other/' . $image_name;

        if (Image::where('path', $full_path)->exists()) {
            return redirect()->route('admincp.images.index')->with([
                'error' => 'Image name already exists!',
            ]);
        }

        Storage::disk('public_folder')->put($full_path, $image);

        Image::create([
            'path' => $full_path,
        ]);

        return redirect()->route('admincp.images.index')->with([
            'success' => 'Image uploaded successfully',
        ]);
    }

    public function destroy(Image $image): RedirectResponse
    {
        Storage::disk('public_folder')
            ->delete($image->path);

        $image->delete();

        return redirect()->route('admincp.images.index')->with([
            'success' => 'Image deleted successfully',
        ]);
    }
}
