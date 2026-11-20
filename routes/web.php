<?php

use App\Http\Controllers\Admincp\SettingsController;
use App\Http\Controllers\ApplicationHashRedirectController;
use App\Http\Controllers\ConsumerVerificationRequestController;
use App\Http\Controllers\ConsumerTokenRedirectController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\EmailVerifiedController;
use App\Http\Controllers\GoController;
use App\Http\Controllers\HoiController;
use App\Http\Controllers\LandingFormController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\UnsubscribeController;
use App\Http\Controllers\ApplicationFormController;
use App\Http\Controllers\AppLinkController;
use App\Http\Controllers\CookieConsentController;
use App\Http\Controllers\DeeplinkController;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

// Deeplink GoCardless Completed
Route::get('/deeplink/go-cardless-completed', [DeeplinkController::class, 'goCardlessCompleted'])->name('deeplink.gocardless.completed');

// GoCardless Application Redirect
Route::get('/applications/{application}/go-cardless/{institution_id}', [\App\Http\Controllers\Api\AppApplicationController::class, 'appGoCardlessRedirect'])->name('applications.gocardless.redirect');

// Hoi token redirect to offers
Route::get('/u/{hoi_token?}', [ConsumerTokenRedirectController::class, 'index'])->middleware('add_robots_tag');

Route::get('/a/{hash?}', [ApplicationHashRedirectController::class, 'index'])->middleware('add_robots_tag');

Route::get('/v/{sms_verification_hash}', [ConsumerVerificationRequestController::class, 'smsVerificationHash'])
    ->name('consumer-sms-verification')
    ->middleware('add_robots_tag');

Route::group([
    'middleware' => 'has_apikey',
], function () {
    Route::post('/hoi/send-email', [HoiController::class, 'sendEmail']);
});

// Go page fallback
Route::get('/go/{lender_id}/{lender_position}', [GoController::class, 'index'])->whereNumber('lender_id')->whereNumber('lender_position')->name('go-fallback');

// Localised routes
Route::group([
    'prefix' => Request::segment(1),
    'middleware' => [
        'dev_behind_goodwall',
        'is_setup_complete',
        'detect_locale',
        'log_utm_campaign',
        'log_visitor',
    ]
], function () {
    Route::get('/', [SectionController::class, 'home'])->name('home.index');
    Route::post('/', [LandingFormController::class, 'post'])->name('landing.form.post');
    //    Route::post('/', [ApplicationFormController::class, 'store']); // step 0

    Route::post('/send-pdf', [DocumentController::class, 'uploadAndSendViaEmail'])->name('send-pdf');
    Route::get('/documents/download/{uuid}/{token}', [DocumentController::class, 'downloadPage'])
        ->middleware(['throttle:15,1'])
        ->name('documents.download');
    Route::post('/documents/download/{uuid}/{token}', [DocumentController::class, 'confirmDownload']);

    // Go page
    Route::get('/go/{lender_id}/{lender_position}', [GoController::class, 'index'])->whereNumber('lender_id')->whereNumber('lender_position')->name('go');

    // Offer page (listing lenders)
    Route::get('/offer', [OfferController::class, 'index'])->name('offer');

    // Unsubscribe routes
    Route::get('/unsubscribe', [UnsubscribeController::class, 'index'])->name('unsubscribe');

    // Unsubscribe Email
    Route::get('/unsubscribe/email', [UnsubscribeController::class, 'indexEmail'])->name('unsubscribe.email');
    Route::post('/unsubscribe/email', [UnsubscribeController::class, 'postEmail'])->middleware('throttle:unsubscribe');

    // Unsubscribe Phone (SMS)
    Route::get('/unsubscribe/phone', [UnsubscribeController::class, 'indexPhone'])->name('unsubscribe.phone');
    Route::post('/unsubscribe/phone', [UnsubscribeController::class, 'postPhone'])->middleware('throttle:unsubscribe');

    // Unsubscribe Rc (Robocall)
    Route::get('/unsubscribe/rc', [UnsubscribeController::class, 'indexRc'])->name('unsubscribe.rc');
    Route::post('/unsubscribe/rc', [UnsubscribeController::class, 'postRc'])->middleware('throttle:unsubscribe');

    // Email Verified
    Route::get('/verified', [EmailVerifiedController::class, 'index']);

    // App-Link QR code route
    Route::get('/app-link', [AppLinkController::class, 'index']);

    // Goodwall Protected routes
    Route::group([
        'middleware' => 'behind_goodwall',
    ], function () {
        // Auth (Laravel Breeze)
        require __DIR__ . '/auth.php';

        // Admincp
        require __DIR__ . '/web_admincp.php';
    });

    // Auth - Consumers
    require __DIR__ . '/auth_consumers.php';

    // Contact us POST
    Route::post('/contact-us', [ContactUsController::class, 'post'])->name('contactus.post');

    // Application Form
    Route::post('/{name}', [ApplicationFormController::class, 'store']); // step 0
    Route::post('/{name}/step1', [ApplicationFormController::class, 'submitStep1'])->middleware(['throttle:15,1']);
    Route::post('/{name}/step2', [ApplicationFormController::class, 'submitStep2'])->middleware(['throttle:10,1']); // phone verification
    Route::post('/{name}/step3', [ApplicationFormController::class, 'submitStep3']);
    Route::post('/{name}/step4', [ApplicationFormController::class, 'submitStep4']); // pdf or GoCardless
    Route::post('/application/update-type', [ApplicationFormController::class, 'updateType'])->name('application.update-type');
    Route::get('/application/return-update-gocardless/{token}', [ApplicationFormController::class, 'returnToSubmitGoCardless'])->name('application.return-update-gocardless');

    // Sections
    Route::get('/{name}', [SectionController::class, 'section'])->name('section');
    Route::get('/{name}/{deep_name}', [SectionController::class, 'deepSection'])->name('section.deep');
});

// Hoi endpoint for sending emails
Route::get('/hoi', [HoiController::class, 'downloadAndSendEmail']);

Route::post('/cookie-consent', [CookieConsentController::class, 'store'])
    ->middleware('web');

// Endpoints for SEO and SEO-like files
Route::get('/manifest.json', [SeoController::class, 'manifestJson']);
Route::get('/robots.txt', [SeoController::class, 'robotsTxt']);

// Setup
Route::group([
    'prefix' => 'setup',
    'middleware' => [
        'is_setup_accessible',
    ]
], function () {
    Route::get('/', [SettingsController::class, 'index'])->name('setup.index');
    Route::post('/', [SettingsController::class, 'update']);
});
