<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;
use GuzzleHttp\Client;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($request->wantsJson() && $exception instanceof ValidationException) {
            $transformer = App::make(LaravelErrorsDecorator::class);
            /** @var LaravelErrorsDecorator $transformer */
            $errors = $transformer->transform($exception->errors());

            if (!empty($errors)) {
                return response()->json([
                    'errors' => $errors,
                ], $exception->status);
            }
        }

        return parent::render($request, $exception);
    }

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */

    public function report(Throwable $exception)
    {
        if ($this->shouldReport($exception) && (app()->isProduction())) {
            $this->exportToMonitoring($exception);
        }

        parent::report($exception);
    }

    protected function exportToMonitoring(Throwable $e)
    {
        try {
            $client = new Client([
                'verify' => false,
                'connect_timeout' => 3,
                'timeout' => 5,
            ]);

            $client->post('https://www.monitoring.goodday.group/api/errors/cp', [
                'headers' => [
                    'apikey' => config('services.goodaff.api_key'),
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'error' => $e->getMessage(),
                    'status_code' => $e->getCode() > 200 && $e->getCode() < 600 ? $e->getCode() : 500,
                    'site' => config('app.name'),
                    'site_url' => config('app.url'),
                ]
            ]);
        } catch (Throwable $monitoring_exception) {
        }
    }
}
