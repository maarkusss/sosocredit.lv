<?php

namespace App\Http\Controllers\Api\WebmasterApplication;

use App\Http\Controllers\Controller;
use App\Services\ApplicationService;
use App\Services\ConsumerService;
use Exception;
use Illuminate\Http\Request;
use App\Services\Webmaster\WebmasterApplicationService;
use Illuminate\Support\Facades\Log;

class WebmasterApplicationController extends Controller
{

    public function __construct(protected ConsumerService $consumer_service)
    {}

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * Request must always come from api.data, must be validated on api.data end.
     */
    public function store(Request $request)
    {

        try {
            $application = WebmasterApplicationService::create($request);

            $application->fillConsumerId();
            if (empty($application->consumer_id) && !empty($application->phone)) {
                $consumer = $this->consumer_service->createFromApplication($application);
                $application->consumer_id = $consumer->id;
                $application->save();
            }

            $application = WebmasterApplicationService::ensureUtmSourceGoodaff($application, $request);
            $application = ApplicationService::fillExportsSummaryWithActiveProviders($application);

            return response()->json([
                'status' => 'success',
                'application_id' => $application->id,
            ]);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ]);
        }

    }
}
