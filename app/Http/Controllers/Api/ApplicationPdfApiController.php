<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Services\ApplicationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApplicationPdfApiController extends Controller
{
    public function __construct(protected ApplicationService $application_service)
    {
    }

    public function index(string $application_token, string $pdf_hash, Request $request)
    {
        $application = $this->application_service->getByToken($application_token);

        if (empty($application->id) || !hash_equals($pdf_hash, $application->pdf_hash)) {
            abort(400);
        }

        if (empty($application->pdf)) {
            abort(404);
        }

        if (!empty($application->pdf_downloaded_at)) {
            abort(403, 'The link has expired');
        }

        $application->pdf_downloaded_at = now();
        $application->pdf_downloaded_from_ip = $request->ip();
        $application->save();

        return Storage::download('/' . Application::$STORAGE_PATH . $application->pdf);
    }
}
