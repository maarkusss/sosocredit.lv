<?php

use App\Http\Controllers\ApplicationFormController;
use App\Http\Controllers\Consumers\ConsumerDashboardController;
use App\Http\Controllers\Consumers\ConsumerSessionController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:consumers')
    ->group(function () {
        Route::get('/consumers/login', [ConsumerSessionController::class, 'step1'])->name('consumers.login');
        Route::post('/consumers/login', [ConsumerSessionController::class, 'postStep1']);
        Route::get('/consumers/login/step2', [ConsumerSessionController::class, 'step2'])->name('consumers.login.step2');
        Route::post('/consumers/login/step2', [ConsumerSessionController::class, 'postStep2']);
    });

Route::middleware('auth:consumers')
    ->group(function () {
        Route::get('/consumers/dashboard', [ConsumerDashboardController::class, 'dashboard'])->name('consumers.dashboard');
        Route::post('/consumers/logout', [ConsumerSessionController::class, 'destroy'])->name('consumers.logout');
        Route::get('/consumers/applications/{token}', [ConsumerDashboardController::class, 'applicationShow'])->name('consumers.applications.show');
        Route::match(['GET', 'POST'],'/consumers/applications/{token}/{provider}/{source?}', [ConsumerDashboardController::class, 'applicationOpenLink'])->name('consumers.applications.open-link');
        Route::get('/o/{token}/{provider}/{source?}', [ConsumerDashboardController::class, 'applicationOpenLink'])->name('consumers.applications.open-short-link');
        Route::get('/continue/{token}', [ApplicationFormController::class, 'continue'])->name('applications.continue');
    });
