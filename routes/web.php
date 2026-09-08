<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LoginOtpController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Authorities\MyDashboardController;
use App\Http\Controllers\CompanyManifest\ActivityController;
use App\Http\Controllers\CompanyManifest\ManifestFormController;
use App\Http\Controllers\CompanyManifest\PaymentFormController;
use App\Http\Controllers\CompanyManifest\PaymentStatusController;
use App\Http\Controllers\CompanyProfile\BoatController;
use App\Http\Controllers\CompanyProfile\CompanyController;
use App\Http\Controllers\CompanyProfile\Step1Controller;
use App\Http\Controllers\CompanyProfile\Step2Controller;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Docs\ExternalApiDocsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Resources\CompanyController as ResourcesCompanyController;
use App\Http\Controllers\Resources\DepartureController as ResourcesDepartureController;
use App\Http\Controllers\Resources\FileableController;
use App\Http\Controllers\SenangPayController;
use App\Http\Controllers\SupportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name("home");

Route::get('/login', [LoginController::class, 'form'])->name("login");
Route::post('/login', [LoginController::class, 'authenticate'])
    ->name("login.submit");

Route::get('/register', [RegisterController::class, 'form'])->name("register");
Route::post('/register', [RegisterController::class, 'store'])
    ->name("register.submit");

Route::post('/logout', [AuthController::class, 'logout'])->name("logout");
Route::get('/logout', [AuthController::class, 'logout'])->name("logout");

Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])->name('forgot-password');
Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])->name('forgot-password');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'edit'])->name('reset-password');
Route::post('/reset-password', [ResetPasswordController::class, 'update'])->name('reset-password.submit');


Route::get('/auth_callback', [AuthController::class, 'callback'])->name('auth.callback');



// Route for SenangPay's return URL (GET request from browser)
Route::get('/senangpay/return', [SenangPayController::class, 'handleReturn'])->name('senangpay.return');

// Route for SenangPay's callback URL (POST request from SenangPay server)
Route::post('/senangpay/callback', [SenangPayController::class, 'handleCallback'])->name('senangpay.callback');


// Swagger UI for the External Partner API (docs/external-api/openapi.yaml).
Route::group(['prefix' => 'docs/external-api', 'as' => 'docs.external-api.'], function () {
    Route::get('/', [ExternalApiDocsController::class, 'index'])->name('index');
    Route::get('/openapi.yaml', [ExternalApiDocsController::class, 'spec'])->name('spec');
});


Route::group(["middleware" => "auth"], function () {

    Route::get('/login-otp', [LoginOtpController::class, 'form'])->name("login-otp");
    Route::post('/login-otp', [LoginOtpController::class, 'authenticate'])
        ->name("login-otp.submit");
    Route::post('/back-to-login', [LoginOtpController::class, 'backToLogin'])
        ->name("login-otp.back-to-login");
    Route::post('/login-otp/resend', [LoginOtpController::class, 'resend'])
        ->name("login-otp.resend");

    // Route::group(["middleware" => "auth.verified"], function () {

    Route::get('/dashboard', [DashboardController::class, "index"])->name('panel.dashboard');

    Route::get('/profile', [ProfileController::class, "show"])->name('panel.profile');
    Route::get('/profile/edit', [ProfileController::class, "edit"])->name('panel.profile.edit');
    Route::put('/profile', [ProfileController::class, "update"])->name('panel.profile.update');
    Route::put('/profile/credentials', [ProfileController::class, "updateCredentials"])->name('panel.profile.update-creds');


    Route::group(["middleware" => "menu.autho"], function () {

        Route::group(['prefix' => 'user-activity', 'as' => 'panel.user-activity.'], function () {
            Route::get("/", [ActivityController::class, 'index'])->name('index');
            Route::get("/{manifest}", [ActivityController::class, 'show'])->name('show');
            Route::post("/{manifest}", [ActivityController::class, 'approve'])->name('approve');
            Route::delete("/{manifest}", [ActivityController::class, 'destroy'])->name('delete');

            Route::get("/{manifest}/download", [ActivityController::class, 'download'])->name('download');
        });

        Route::group(['prefix' => 'company-profile', 'as' => 'panel.company-profile.'], function () {
            Route::get("/", [CompanyController::class, 'index'])->name('index');
            // Route::get("/show", [CompanyController::class, 'show'])->name('show');
            Route::get("/edit", [Step1Controller::class, 'edit'])->name('step1.edit');
            Route::post("/", [Step1Controller::class, 'update'])->name('step1.update');
            Route::post("/boat", [BoatController::class, 'update'])->name('boat.update');
            Route::delete("/boat/{boat}", [BoatController::class, 'destroy'])->name('boat.delete');

            Route::get("/step2", [Step2Controller::class, 'edit'])->name('step2.edit');
            Route::post("/step2", [Step2Controller::class, 'update'])->name('step2.update');
        });

        Route::group(['prefix' => 'manifest', 'as' => 'panel.manifest.'], function () {
            Route::get("/", [ManifestFormController::class, 'create'])->name('create');
            Route::post("/", [ManifestFormController::class, 'store'])->name('store');

            Route::get("/{manifest}", [ManifestFormController::class, 'show'])->name('show');
            Route::get("/{manifest}/approved", [ManifestFormController::class, 'showApproved'])->name('show-approved');
            Route::get("/{manifest}/download", [ManifestFormController::class, 'download'])->name('download');

            Route::get("/{manifest}/edit", [ManifestFormController::class, 'edit'])->name('edit');
            Route::put("/{manifest}", [ManifestFormController::class, 'update'])->name('update');
        });



        Route::group(['prefix' => 'payment', 'as' => 'panel.payment.'], function () {
            Route::get("/", [PaymentFormController::class, 'index'])->name('index');
            Route::get("/confirmation", [PaymentFormController::class, 'confirmation'])->name('confirmation');
            Route::get("/create", [PaymentFormController::class, 'create'])->name('create');
            Route::get("/receipts", [PaymentFormController::class, 'downloadReceipt'])->name('receipts');
            Route::post("/", [PaymentFormController::class, 'store'])->name('store');

            Route::get("/{payment}", [PaymentFormController::class, 'show'])->name('show');
            Route::get("/{payment}/edit", [PaymentFormController::class, 'edit'])->name('edit');
            Route::put("/{payment}", [PaymentFormController::class, 'update'])->name('update');

            Route::delete("/{payment}", [PaymentFormController::class, 'destroy'])->name('destroy');
        });


        Route::group(['prefix' => 'payment-status', 'as' => 'panel.payment-status.'], function () {
            Route::get("/", [PaymentStatusController::class, 'index'])->name('index');
            Route::get("/{manifest}", [PaymentStatusController::class, 'show'])->name('show');
            Route::get("/{manifest}/download", [PaymentStatusController::class, 'download'])->name('download');
        });

        Route::group(['prefix' => 'support', 'as' => 'panel.support.'], function () {
            Route::get("/", [SupportController::class, 'create'])->name('create');
            Route::post("/", [SupportController::class, 'store'])->name('store');
        });

        Route::group(['prefix' => 'report', 'as' => 'panel.report.'], function () {
            Route::get("/", [ReportController::class, 'index'])->name('index');
            Route::get("/download", [ReportController::class, 'download'])->name('download');
        });


        Route::group(['prefix' => 'autho-my-dashboard', 'as' => 'panel.autho-my-dashboard.'], function () {
            Route::get("/", [MyDashboardController::class, 'index'])->name('index');
            Route::get("/{manifest}", [MyDashboardController::class, 'show'])->name('show');
            Route::post("/{manifest}", [MyDashboardController::class, 'approve'])->name('approve');

            Route::get("/{manifest}/download", [MyDashboardController::class, 'download'])->name('download');
        });

        // ./web/userManagmentRoutes.php
        Route::group([], __DIR__ . '/web/userManagmentRoutes.php');

        // ./web/masterRoutes.php
        Route::group([], __DIR__ . '/web/masterRoutes.php');
    });



    Route::group(['prefix' => 'resources', 'as' => 'resources.'], function () {
        Route::resource('fileable', FileableController::class)->only(['show']);
        Route::resource('company', ResourcesCompanyController::class)->only(['index', 'show']);
        Route::resource('departure', ResourcesDepartureController::class)->only(['index', 'show']);
    });
    // });
});
