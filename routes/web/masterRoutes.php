<?php

use App\Http\Controllers\Master\ActivityController;
use App\Http\Controllers\Master\DestinationController;
use App\Http\Controllers\Master\NationalityController;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'nationality', 'as' => 'panel.nationality.'], function () {
    Route::get('/', [NationalityController::class, 'index'])->name("index");
    Route::get('/create', [NationalityController::class, 'create'])->name("create");
    Route::post('/', [NationalityController::class, 'store'])->name("store");
    Route::get('/{nationality}', [NationalityController::class, 'show'])->name("show");
    Route::get('/{nationality}/edit', [NationalityController::class, 'edit'])->name("edit");
    Route::put('/{nationality}', [NationalityController::class, 'update'])->name("update");
    Route::delete('/{nationality}', [NationalityController::class, 'destroy'])->name("delete");
});

Route::group(['prefix' => 'activity', 'as' => 'panel.activity.'], function () {
    Route::get('/', [ActivityController::class, 'index'])->name("index");
    Route::get('/create', [ActivityController::class, 'create'])->name("create");
    Route::post('/', [ActivityController::class, 'store'])->name("store");
    Route::get('/{activity}', [ActivityController::class, 'show'])->name("show");
    Route::get('/{activity}/edit', [ActivityController::class, 'edit'])->name("edit");
    Route::put('/{activity}', [ActivityController::class, 'update'])->name("update");
    Route::delete('/{activity}', [ActivityController::class, 'destroy'])->name("delete");
});

Route::group(['prefix' => 'destination', 'as' => 'panel.destination.'], function () {
    Route::get('/', [DestinationController::class, 'index'])->name("index");
    Route::get('/create', [DestinationController::class, 'create'])->name("create");
    Route::post('/', [DestinationController::class, 'store'])->name("store");
    Route::get('/{destination}', [DestinationController::class, 'show'])->name("show");
    Route::get('/{destination}/edit', [DestinationController::class, 'edit'])->name("edit");
    Route::put('/{destination}', [DestinationController::class, 'update'])->name("update");
    Route::delete('/{destination}', [DestinationController::class, 'destroy'])->name("delete");
});
