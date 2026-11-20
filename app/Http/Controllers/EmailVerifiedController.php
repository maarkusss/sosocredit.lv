<?php

namespace App\Http\Controllers;

use App\Traits\WorksWithMeta;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerifiedController extends Controller
{
    use WorksWithMeta;

    /**
     * View presented to Consumer after he clicked the confirmation link in his "confirm email" letter
     *
     * @param Request $request
     * @return Renderable|RedirectResponse
     */
    public function index(Request $request): Renderable|RedirectResponse
    {
        if ($request->get('verified')) {
            return view('email_verified', [
                'meta' => [
                    'title' => __('main.email_address_verified') . ' | ' . $this->seoSiteName(),
                ],
            ]);
        }

        return redirect()
            ->route('home.index');
    }
}
