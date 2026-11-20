<?php

use App\Http\Controllers\Api\ApiApplicationController;
use App\Http\Controllers\Api\AppApplicationController;
use App\Http\Controllers\Api\AppFaqController;
use App\Http\Controllers\Api\FormTypeController;
use App\Http\Controllers\Api\OffersApi\LenderController;
use App\Http\Controllers\Api\OffersApi\OfferController;
use App\Http\Controllers\Api\OffersApi\SessionController;
use App\Http\Controllers\Api\OffersApi\VisitorController;
use App\Http\Controllers\Api\WebmasterApplication\WebmasterApplicationController;
use App\Http\Controllers\Consumers\VerifyEmailController;
use App\Http\Controllers\ConsumerVerificationRequestController;
use App\Http\Controllers\Api\ApplicationPdfApiController;
use App\Http\Controllers\Api\AppPrivacyController;
use App\Http\Controllers\Api\AppUserController;
use App\Http\Controllers\LandingFormController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'can_access_application_api',
], function () {
    // New app routes
    Route::post('/notification-click', [AppUserController::class, 'postNotificationClick']);
    Route::get('/privacy-policy', [AppPrivacyController::class, 'index']);
    Route::get('/faq', [AppFaqController::class, 'index']);
    Route::get('/calculator-by-credit-types', [AppApplicationController::class, 'appCalculator']);
    Route::post('/user/authentication', [AppUserController::class, 'authentication'])->middleware('throttle:30,1');
    Route::post('/user/authentication/complete', [AppUserController::class, 'authenticationComplete'])->middleware('throttle:20,1');
    Route::get('/user/authentication-type', [AppUserController::class, 'getAuthenticationType']);
    Route::post('/applications/create', [AppApplicationController::class, 'appCreateApplication']);
    Route::post('/applications/{application}/contact-info', [AppApplicationController::class, 'appContactInfo']);
    Route::post('/applications/{application}/contact-info/verification', [AppApplicationController::class, 'appContactInfoVerification']);

    Route::post('/device-country-select', [AppUserController::class, 'postDevice']);

    Route::middleware(['auth:sanctum', 'ability:refresh-token'])->group(function () {
        Route::post('/user/refresh-access-token', [AppUserController::class, 'appRefreshToken']);
    });

    Route::middleware(['auth:sanctum', 'ability:general-access'])->group(function () {
        Route::get('/user', [AppUserController::class, 'getUser']);
        Route::post('/user', [AppUserController::class, 'postUser']);
        Route::delete('/user', [AppUserController::class, 'deleteUser']);

        Route::get('/applications', [AppApplicationController::class, 'appGetApplications']);
        Route::get('/applications/application-by-token/{token?}', [AppApplicationController::class, 'appTokenApplication']);
        Route::get('/applications/by-token/{token}', [AppApplicationController::class, 'appTokenApplication']);
        Route::get('/applications/{application}', [AppApplicationController::class, 'appGetUnfinishedApplication']);
        Route::post('/applications/{application}', [AppApplicationController::class, 'appUpdateApplication']);
        Route::get('/applications/{application}/go-cardless', [AppApplicationController::class, 'appGoCardless']);
        Route::post('/applications/{application}/init-review', [AppApplicationController::class, 'appInitReview']);
        Route::get('/applications/{application}/open-link/{provider}', [AppApplicationController::class, 'appOpenLink'])->name('offers-api.applications.open-link');
        
        Route::get('/app/applications/{application}/status', [AppApplicationController::class, 'appGetApplicationStatus']);
    });

    // Old app routes
    // Route::apiResource('/offers', OfferController::class)->only(['index']);
    // Route::get('/user', [UserController::class, 'getUser']);
    // Route::post('/user', [UserController::class, 'postUser']);
    // Route::get('/user/sms-verification-codes', [UserController::class, 'getSmsVerificationCodes']);
    // Route::post('/user/verification/phone', [UserController::class, 'userVerificationPhone']);
});

Route::group([
    'middleware' => 'can_access_offers_api',
], function () {
    // The below 3 routes are required for the WEB and cannot be changed
    Route::apiResource('/lenders', LenderController::class)->only(['update']);
    Route::get('/sessions', [SessionController::class, 'index']);
    Route::get('/visitors', [VisitorController::class, 'index']);

    Route::get('/form-types', [FormTypeController::class, 'index']);
    Route::post('/webmaster/application/create', [WebmasterApplicationController::class, 'store'])
        ->middleware(['throttle:60,1'])
        ->name('api.application.create');
});

Route::post('/landing-form', [LandingFormController::class, 'post']);
Route::post('/landing-form/verification/sms', [LandingFormController::class, 'verifySMSVerificationCode']);
Route::post('/landing-form/verification/sms/send-again', [LandingFormController::class, 'sendVerificationCodeSMSAgain']);

// Consumer Verify email (Laravel Breeze-powered)
Route::get('/consumer-verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('consumer-verify-email');

Route::get('/applications/{application_token}/pdf/{pdf_hash}', [ApplicationPdfApiController::class, 'index'])->name('api.applications.pdf-link');
Route::get('/application/{application_token}/{hash}/status', [ApiApplicationController::class, 'getStatus'])->name('api.application.status');
Route::get('/application/{id}/token', [ApiApplicationController::class, 'getToken'])
    ->middleware(['throttle:60,1'])
    ->name('api.application.token');

Route::get('/verification/email/{id}/{hash}', [ConsumerVerificationRequestController::class, 'email'])
    ->middleware(['signed','throttle:10,1'])
    ->name('consumer-verification-request-email');

Route::get('/verification/phone/{id}/{hash}', [ConsumerVerificationRequestController::class, 'phone'])
    ->middleware(['throttle:10,1'])
    ->name('consumer-verification-request-phone');
