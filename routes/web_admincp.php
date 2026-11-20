<?php

use App\Http\Controllers\Admincp\AdminsController;
use App\Http\Controllers\Admincp\ApplicationsController;
use App\Http\Controllers\Admincp\BansController;
use App\Http\Controllers\Admincp\BlocksController;
use App\Http\Controllers\Admincp\CronsController;
use App\Http\Controllers\Admincp\ConnectedUrlsController;
use App\Http\Controllers\Admincp\ConsumersController;
use App\Http\Controllers\Admincp\CookiesController;
use App\Http\Controllers\Admincp\DashboardController;
use App\Http\Controllers\Admincp\FaqsController;
use App\Http\Controllers\Admincp\GoRewritesController;
use App\Http\Controllers\Admincp\ImagesController;
use App\Http\Controllers\Admincp\LendersController;
use App\Http\Controllers\Admincp\LenderFiltersController;
use App\Http\Controllers\Admincp\LoantypesController;
use App\Http\Controllers\Admincp\NewsController;
use App\Http\Controllers\Admincp\RedirectsController;
use App\Http\Controllers\Admincp\ReviewsController;
use App\Http\Controllers\Admincp\SectionsController;
use App\Http\Controllers\Admincp\SettingsController;
use App\Http\Controllers\Admincp\StatisticsController;
use App\Http\Controllers\Admincp\StatusController;
use App\Http\Controllers\Admincp\VisitorsController;
use Illuminate\Support\Facades\Route;

// Admincp
Route::group([
    'prefix' => '/admincp',
    'middleware' => [
        'auth',
    ]
], function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('admincp.dashboard');

    Route::get('/cookies', [CookiesController::class, 'index'])->name('admincp.cookies');
    Route::get('/cookies/visitor/{visitorId}', [CookiesController::class, 'visitor'])
    ->name('admincp.cookies.visitor');

    // Admins
    Route::group([
        'prefix' => 'admins',
        'middleware' => ['permission:view admins'],
        'as' => 'admincp.admins.',
    ], function () {
        Route::get('/', [AdminsController::class, 'index'])->name('index');

        Route::group(['middleware' => ['permission:add admins']], function () {
            Route::get('/create', [AdminsController::class, 'create'])->name('create');
            Route::post('/create', [AdminsController::class, 'store'])->name('store');
        });

        Route::group(['middleware' => ['permission:edit admins']], function () {
            Route::get('/{user}', [AdminsController::class, 'edit'])->name('edit');
            Route::post('/{user}', [AdminsController::class, 'update'])->name('update');
        });

        Route::group(['middleware' => ['permission:delete admins']], function () {
            Route::post('/edit/{user}/delete', [AdminsController::class, 'destroy'])->name('destroy');
        });
    });

    // Applications
    Route::group([
        'as' => 'admincp.applications.',
    ], function () {
        Route::get('/applications/{applications_token}/view-pdf', [ApplicationsController::class, 'viewPdf'])->name('view-pdf');
        Route::post('/applications/{application}/export-to-data', [ApplicationsController::class, 'exportToData'])->name('export-to-data');
        Route::resource('/applications', ApplicationsController::class)->only(['index', 'show']);
    });

    // Bans
    Route::group([
        'prefix' => 'bans',
        'middleware' => ['permission:view bans'],
        'as' => 'admincp.bans.',
    ], function () {
        Route::get('/', [BansController::class, 'index'])->name('index');

        Route::group(['middleware' => ['permission:add bans']], function () {
            Route::get('/create', [BansController::class, 'create'])->name('create');
            Route::post('/create', [BansController::class, 'store'])->name('store');
        });

        Route::group(['middleware' => ['permission:edit bans']], function () {
            Route::get('/{ban}', [BansController::class, 'edit'])->name('edit');
            Route::post('/{ban}', [BansController::class, 'update'])->name('update');
        });

        Route::group(['middleware' => ['permission:delete bans']], function () {
            Route::delete('/{ban}', [BansController::class, 'destroy'])->name('destroy');
        });
    });

    // Blocks
    Route::group([
        'as' => 'admincp.',
    ], function () {
        Route::resource('blocks', BlocksController::class)->except(['show']);
    });

    // Crons
    Route::group([
        'prefix' => 'crons',
        'as' => 'admincp.crons.',
    ], function () {
        Route::get('/', [CronsController::class, 'index'])->name('index');

        Route::get('/{cron}', [CronsController::class, 'edit'])->name('edit');
        Route::post('/{cron}', [CronsController::class, 'update'])->name('update');
    });

    // Connected URLs
    Route::group([
        'prefix' => 'connected-urls',
        'as' => 'admincp.connected-urls.',
    ], function () {
        Route::get('/', [ConnectedUrlsController::class, 'index'])->name('index');
        Route::get('/create', [ConnectedUrlsController::class, 'create'])->name('create');
        Route::post('/create', [ConnectedUrlsController::class, 'store'])->name('store');

        Route::get('/{connected_url}', [ConnectedUrlsController::class, 'edit'])->name('edit');
        Route::post('/{connected_url}', [ConnectedUrlsController::class, 'update'])->name('update');

        Route::delete('/{connected_url}', [ConnectedUrlsController::class, 'destroy'])->name('destroy');
    });

    // Consumers
    Route::group([
        'prefix' => 'consumers',
        'middleware' => ['permission:view consumers'],
        'as' => 'admincp.consumers.',
    ], function () {
        Route::get('/', [ConsumersController::class, 'index'])->name('index');
        Route::get('/{consumer}', [ConsumersController::class, 'show'])->name('show');
        Route::put('/{consumer_id}/confirm-phone', [ConsumersController::class, 'confirmPhone'])->name('confirm-phone');
        Route::put('/{consumer_id}/confirm-email', [ConsumersController::class, 'confirmEmail'])->name('confirm-email');

        Route::group(['middleware' => ['permission:delete consumers']], function () {
            Route::post('/{consumer_id}', [ConsumersController::class, 'destroy'])->name('delete');
        });
    });

    // FAQs
    Route::group([
        'prefix' => 'faqs',
        'middleware' => ['permission:view faqs'],
        'as' => 'admincp.faqs.',
    ], function () {
        Route::get('/', [FaqsController::class, 'index'])->name('index');

        Route::group(['middleware' => ['permission:add faqs']], function () {
            Route::get('/create', [FaqsController::class, 'create'])->name('create');
            Route::post('/create', [FaqsController::class, 'store'])->name('store');
        });

        Route::group(['middleware' => ['permission:edit faqs']], function () {
            Route::post('/', [FaqsController::class, 'updateAll'])->name('save');
            Route::get('/{faq}', [FaqsController::class, 'edit'])->name('edit');
            Route::post('/{faq}', [FaqsController::class, 'update'])->name('update');
        });

        Route::group(['middleware' => ['permission:delete faqs']], function () {
            Route::delete('/{faq}', [FaqsController::class, 'destroy'])->name('destroy');
        });
    });

    // News
    Route::group([
        'prefix' => 'news',
        'middleware' => ['permission:view news'],
        'as' => 'admincp.news.',
    ], function () {
        Route::get('/', [NewsController::class, 'index'])->name('index');

        Route::group(['middleware' => ['permission:add news']], function () {
            Route::get('/create', [NewsController::class, 'create'])->name('create');
            Route::post('/create', [NewsController::class, 'store'])->name('store');
        });

        Route::group(['middleware' => ['permission:edit news']], function () {
            Route::post('/', [NewsController::class, 'updateAll'])->name('save');
            Route::get('/{news}', [NewsController::class, 'edit'])->name('edit');
            Route::post('/{news}', [NewsController::class, 'update'])->name('update');
        });

        Route::group(['middleware' => ['permission:delete news']], function () {
            Route::delete('/{news}', [NewsController::class, 'destroy'])->name('destroy');
        });
    });

    // Go (Link) Rewrites
    Route::group([
        'prefix' => 'go-rewrites',
        'middleware' => ['permission:view go-rewrites'],
        'as' => 'admincp.go-rewrites.',
    ], function () {
        Route::get('/', [GoRewritesController::class, 'index'])->name('index');
        Route::post('/', [GoRewritesController::class, 'updateAll'])->name('index.post');
        Route::get('/preview', [GoRewritesController::class, 'previewAllActive'])->name('preview-all-active');

        Route::group(['middleware' => ['permission:add go-rewrites']], function () {
            Route::get('/create', [GoRewritesController::class, 'create'])->name('create');
            Route::post('/create', [GoRewritesController::class, 'store'])->name('store');
        });

        Route::group(['middleware' => ['permission:edit go-rewrites']], function () {
            Route::get('/{go_rewrite}', [GoRewritesController::class, 'edit'])->name('edit');
            Route::post('/{go_rewrite}', [GoRewritesController::class, 'update'])->name('update');
            Route::get('/{go_rewrite}/preview', [GoRewritesController::class, 'preview'])->name('preview');
        });

        Route::group(['middleware' => ['permission:delete go-rewrites']], function () {
            Route::delete('/{go_rewrite}', [GoRewritesController::class, 'destroy'])->name('destroy');
        });
    });

    // Images
    Route::group([
        'as' => 'admincp.',
    ], function () {
        Route::resource('images', ImagesController::class)->only(['index', 'store', 'destroy']);
    });

    // Statuses
    Route::group([
        'prefix' => 'statuses',
        'as' => 'admincp.statuses.',
    ], function () {
        Route::get('/', [StatusController::class, 'index'])->name('index');
        Route::post('/update', [StatusController::class, 'update'])->name('update');
    });

    // Loantypes
    Route::group([
        'prefix' => 'loantypes',
        'middleware' => ['permission:view loantypes'],
        'as' => 'admincp.loantypes.',
    ], function () {
        Route::get('/', [LoantypesController::class, 'index'])->name('index');

        Route::group(['middleware' => ['permission:add loantypes']], function () {
            Route::get('/create', [LoantypesController::class, 'create'])->name('create');
            Route::post('/create', [LoantypesController::class, 'store'])->name('store');
        });

        Route::group(['middleware' => ['permission:edit loantypes']], function () {
            Route::post('/', [LoantypesController::class, 'updateAll'])->name('updateAll');
            Route::get('/{loantype}', [LoantypesController::class, 'edit'])->name('edit');
            Route::post('/{loantype}', [LoantypesController::class, 'update'])->name('update');
        });

        Route::group(['middleware' => ['permission:delete loantypes']], function () {
            Route::delete('/{loantype}', [LoantypesController::class, 'destroy'])->name('destroy');
        });
    });

    // Lenders
    Route::group([
        'prefix' => 'lenders',
        'middleware' => ['permission:view lenders'],
        'as' => 'admincp.lenders.',
    ], function () {
        // Lenders
        Route::get('/', [LendersController::class, 'index'])->name('index');
        Route::get('/providers/links/', [LendersController::class, 'linkVisits'])->name('providers.links.index');

        // Lender Filters
        Route::group([
            'prefix' => 'filters',
            'as' => 'filters.',
        ], function () {
            Route::get('/', [LenderFiltersController::class, 'index'])->name('index');
            Route::get('/create', [LenderFiltersController::class, 'create'])->name('create');
            Route::post('/create', [LenderFiltersController::class, 'store'])->name('store');
            Route::get('/{filter}', [LenderFiltersController::class, 'edit'])->name('edit');
            Route::post('/{filter}', [LenderFiltersController::class, 'update'])->name('update');
            Route::delete('/{filter}', [LenderFiltersController::class, 'destroy'])->name('destroy');
        });

        Route::group(['middleware' => ['permission:edit lenders']], function () {
            Route::post('/', [LendersController::class, 'updateAll'])->name('index.post');
            Route::get('/edit/{lender_id}', [LendersController::class, 'edit'])->name('edit.index');
            Route::post('/edit/{lender_id}', [LendersController::class, 'update'])->name('edit.post');
            Route::put('/edit/{lender_id}/campaign-id', [LendersController::class, 'updateCampaignId'])
                ->name('update-campaign-id');
        });

        Route::group(['middleware' => ['permission:add lenders']], function () {
            Route::get('/add', [LendersController::class, 'create'])->name('add.index');
            Route::post('/add', [LendersController::class, 'store'])->name('add.post');
        });

        Route::group(['middleware' => ['permission:delete lenders']], function () {
            Route::delete('/{lender}', [LendersController::class, 'destroy'])->name('destroy');
        });
    });

    // Redirects
    Route::group([
        'prefix' => 'redirects',
        'middleware' => ['permission:view redirects'],
        'as' => 'admincp.redirects.',
    ], function () {
        Route::get('/', [RedirectsController::class, 'index'])->name('index');

        Route::group(['middleware' => ['permission:add redirects']], function () {
            Route::get('/create', [RedirectsController::class, 'create'])->name('create');
            Route::post('/create', [RedirectsController::class, 'store'])->name('store');
        });

        Route::group(['middleware' => ['permission:edit redirects']], function () {
            Route::get('/{redirect}', [RedirectsController::class, 'edit'])->name('edit');
            Route::post('/{redirect}', [RedirectsController::class, 'update'])->name('update');
        });

        Route::group(['middleware' => ['permission:delete redirects']], function () {
            Route::delete('/{redirect}', [RedirectsController::class, 'destroy'])->name('destroy');
        });
    });

    // Reviews
    Route::group([
        'prefix' => 'reviews',
        'middleware' => ['permission:view reviews'],
        'as' => 'admincp.reviews.',
    ], function () {
        Route::get('/', [ReviewsController::class, 'index'])->name('index');

        Route::group(['middleware' => ['permission:add reviews']], function () {
            Route::get('/create', [ReviewsController::class, 'create'])->name('create');
            Route::post('/create', [ReviewsController::class, 'store'])->name('store');
        });

        Route::group(['middleware' => ['permission:edit reviews']], function () {
            Route::get('/{review}', [ReviewsController::class, 'edit'])->name('edit');
            Route::post('/{review}', [ReviewsController::class, 'update'])->name('update');
        });

        Route::group(['middleware' => ['permission:delete reviews']], function () {
            Route::delete('/{review}', [ReviewsController::class, 'destroy'])->name('destroy');
        });
    });

    // Sections
    Route::group([
        'prefix' => 'sections',
        'middleware' => ['permission:view sections'],
        'as' => 'admincp.sections.',
    ], function () {
        Route::get('/', [SectionsController::class, 'index'])->name('index');

        Route::group(['middleware' => ['permission:edit sections']], function () {
            Route::post('/', [SectionsController::class, 'updateAll'])->name('save');
            Route::get('/edit/{section}', [SectionsController::class, 'edit'])->name('edit');
            Route::post('/edit/{section}', [SectionsController::class, 'update'])->name('update');
            Route::delete('/{section}', [SectionsController::class, 'destroy'])->name('destroy');
        });

        Route::group(['middleware' => ['permission:add sections']], function () {
            Route::get('/create', [SectionsController::class, 'create'])->name('create');
            Route::post('/create', [SectionsController::class, 'store'])->name('store');
        });
    });

    // Settings
    Route::group([
        'prefix' => 'settings',
        'middleware' => ['permission:view settings'],
        'as' => 'admincp.settings.',
    ], function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::post('/', [SettingsController::class, 'update'])->name('index.post')
            ->middleware('permission:edit settings');
    });

    // Statistics
    Route::group([
        'prefix' => 'statistics',
        'middleware' => ['permission:view statistics'],
        'as' => 'admincp.statistics.',
    ], function () {
        Route::get('/', [StatisticsController::class, 'index'])->name('index');
    });

    // Visitors
    Route::group([
        'prefix' => 'visitors',
        'middleware' => ['permission:view visitors'],
        'as' => 'admincp.visitors.',
    ], function () {
        Route::get('/', [VisitorsController::class, 'index'])->name('index');
        Route::get('/{visitor}', [VisitorsController::class, 'show'])->name('show');
    });
});
