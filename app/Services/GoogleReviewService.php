<?php

namespace App\Services;

use App\Models\GoogleReview;
use App\Models\GoogleReviewsStat;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class GoogleReviewService
{
    public function import()
    {
        $response = Http::get(
            'https://maps.googleapis.com/maps/api/place/details/json',
            [
                'place_id' => config('services.google.place_id'),
                'fields'   => 'rating,user_ratings_total,reviews',
                'key'      => config('services.google.api_key'),
                'reviews_no_translations' => true,
                'reviews_sort' => 'newest',
            ]
        );

        $data = $response->json('result');

        GoogleReviewsStat::updateOrCreate(
            ['id' => 1],
            [
                'overall_rating' => $data['rating'] ?? null,
                'ratings_total'  => $data['user_ratings_total'] ?? null,
            ]
        );

        $reviews = $response->json('result.reviews', []);

        foreach ($reviews as $review) {
            GoogleReview::updateOrCreate(
                [
                    'google_review_id' => $review['time'],
                ],
                [
                    'author_name'        => $review['author_name'] ?? null,
                    'author_url'         => $review['author_url'] ?? null,
                    'profile_photo_url'  => $review['profile_photo_url'] ?? null,
                    'rating'             => $review['rating'],
                    'text'               => $review['text'] ?? null,
                    'lang'               => $review['language'] ?? null,
                    'review_time'        => Carbon::createFromTimestamp($review['time']),
                ]
            );
        }
    }
}
