<?php

use App\Http\Controllers\UserManagement\RoleController;
use App\Http\Controllers\UserManagement\UserApprovalController;
use App\Http\Controllers\UserManagement\UserCompanyController;
use App\Http\Controllers\UserManagement\UserController;
use App\Http\Controllers\UserManagement\UserJettyController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'user-account', 'as' => 'panel.user.'], function () {
    Route::get('/', [UserController::class, 'index'])->name("index");
    Route::get('/create', [UserController::class, 'create'])->name("create");
    Route::post('/', [UserController::class, 'store'])->name("store");
    Route::get('/{user}', [UserController::class, 'show'])->name("show");
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name("edit");
    Route::put('/{user}', [UserController::class, 'update'])->name("update");
    Route::put('/{user}/credentials', [UserController::class, 'updateCredentials'])->name("update-credentials");
    Route::delete('/{user}', [UserController::class, 'destroy'])->name("delete");
});

Route::group(['prefix' => 'role', 'as' => 'panel.role.'], function () {
    Route::get('/', [RoleController::class, 'index'])->name("index");
    Route::get('/create', [RoleController::class, 'create'])->name("create");
    Route::post('/', [RoleController::class, 'store'])->name("store");
    Route::get('/{role}', [RoleController::class, 'show'])->name("show");
    Route::get('/{role}/edit', [RoleController::class, 'edit'])->name("edit");
    Route::put('/{role}', [RoleController::class, 'update'])->name("update");
    Route::delete('/{role}', [RoleController::class, 'destroy'])->name("delete");
});

Route::group(['prefix' => 'user-approval', 'as' => 'panel.user-approval.'], function () {
    Route::get('/', [UserApprovalController::class, 'index'])->name("index");
    Route::post('/{user}', [UserApprovalController::class, 'approve'])->name("approve");
});

Route::group(['prefix' => 'user-jetty', 'as' => 'panel.user-jetty.'], function () {
    Route::get('/', [UserJettyController::class, 'index'])->name("index");
    Route::get('/create', [UserJettyController::class, 'create'])->name("create");
    Route::post('/', [UserJettyController::class, 'store'])->name("store");
    Route::get('/{user}', [UserJettyController::class, 'show'])->name("show");
    Route::get('/{user}/edit', [UserJettyController::class, 'edit'])->name("edit");
    Route::put('/{user}', [UserJettyController::class, 'update'])->name("update");
    Route::put('/{user}/credentials', [UserJettyController::class, 'updateCredentials'])->name("update-credentials");
    Route::delete('/{user}', [UserJettyController::class, 'destroy'])->name("delete");
});

Route::group(['prefix' => 'user-company', 'as' => 'panel.user-company.'], function () {
    Route::get('/', [UserCompanyController::class, 'index'])->name("index");
    Route::get('/create', [UserCompanyController::class, 'create'])->name("create");
    Route::post('/', [UserCompanyController::class, 'store'])->name("store");
    Route::get('/{user}', [UserCompanyController::class, 'show'])->name("show");
    Route::get('/{user}/edit', [UserCompanyController::class, 'edit'])->name("edit");
    Route::put('/{user}', [UserCompanyController::class, 'update'])->name("update");
    Route::put('/{user}/credentials', [UserCompanyController::class, 'updateCredentials'])->name("update-credentials");
    Route::delete('/{user}', [UserCompanyController::class, 'destroy'])->name("delete");
});
