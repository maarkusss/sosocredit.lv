<?php

namespace App\Http\Controllers\Admincp;

use App\Models\Review;
use App\Models\Section;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReviewsController extends AdminController
{
    public function index(): Renderable
    {
        $per_page = 20;

        $reviews = Review::query()
            ->where('lang', app()->getLocale());

        if (!empty(request()->section_id)) {
            $reviews = $reviews->where('section_id', (int) request()->section_id);
        }

        $reviews = $reviews->paginate($per_page);

        return view('admincp.reviews.index')
            ->with([
                'reviews' => $reviews,
                'meta' => $this->metaTitle('Reviews'),
            ]);
    }

    public function create(): Renderable
    {
        return view('admincp.reviews.edit')
            ->with([
                'meta' => $this->metaTitle('Add a Review'),
                'page_title' => 'Add a Review',
                'review' => new Review(),
                'reviews_images' => $this->getReviewsImages(),
                'sections' => $this->getReviewsSections(),
            ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'image' => 'required|string|max:500',
            'name' => 'required|string|max:191',
            'text' => 'required|string',
            'section_id' => 'required|integer|exists:sections,id',
        ]);

        $review = new Review();
        $review->lang = app()->getLocale();
        $review->fill($validated);
        $review->save();

        $this->logAdminAction('Created review "' . $validated['name'] . '"');

        return redirect()->route('admincp.reviews.index')
            ->with([
                'success' => 'Review has been created!',
            ]);
    }

    /**
     * @return Renderable|RedirectResponse
     */
    public function edit(Review $review)
    {
        if ($review->lang != app()->getLocale()) {
            return redirect()->route('admincp.reviews.index');
        }

        return view('admincp.reviews.edit')->with([
            'meta' => $this->metaTitle('Edit a Review'),
            'review' => $review,
            'reviews_images' => $this->getReviewsImages(),
            'sections' => $this->getReviewsSections(),
        ]);
    }

    public function update(Review $review, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'image' => 'required|string|max:500',
            'name' => 'required|string|max:191',
            'text' => 'required|string',
            'section_id' => 'required|integer|exists:sections,id',
        ]);

        $review->update([
            'lang' => app()->getLocale(),
            'image' => $validated['image'],
            'name' => $validated['name'],
            'text' => $validated['text'],
            'section_id' => (int) $validated['section_id'],
        ]);

        $this->logAdminAction('Edited review "' . $validated['name'] . '"');

        return redirect()
            ->back()
            ->with('success', 'Review updated!');
    }

    public function destroy($id): RedirectResponse
    {
        Review::query()
            ->where('id', $id)
            ->delete();

        $this->logAdminAction('Deleted review "' . $id . '"');

        return redirect(route('admincp.reviews.index'))
            ->with('success', 'Review has been deleted!');
    }

    protected function getReviewsImages(): array
    {
        $reviews_images = Storage::disk('public_folder')->files('images/reviews');
        $reviews_images = collect($reviews_images)
            ->map(fn ($review_image) => str_starts_with($review_image, '/') ?: '/' . $review_image)
            ->toArray();

        return $reviews_images;
    }

    protected function getReviewsSections(): array
    {
        return Section::query()
            ->where('lang', app()->getLocale())
            ->whereIn('template', [Section::TEMPLATE_HOME, Section::TEMPLATE_APPLICANT_FORM_LANDING_PAGE])
            ->pluck('name', 'id')
            ->toArray();
    }
}
