<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FormType;
use Illuminate\Http\JsonResponse;

class FormTypeController extends Controller
{
    public function index(): JsonResponse
    {
        $form_types = FormType::query()
            ->select(['id', 'name'])
            ->orderBy('id', 'ASC')
            ->get();

        return new JsonResponse([
            'data' => [
                'form_types' => $form_types,
            ],
        ]);
    }
}
