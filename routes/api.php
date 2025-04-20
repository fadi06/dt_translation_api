<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TranslationController;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::group(['prefix' => 'translations', 'as' => 'translations.'], function () {
        Route::get('/', [TranslationController::class, 'index'])->name('index');
        Route::post('/', [TranslationController::class, 'store'])->name('store');
        Route::get('/search', [TranslationController::class, 'search'])->name('search');
        Route::get('/{translation}', [TranslationController::class, 'show'])->name('show');
        Route::put('/{translation}', [TranslationController::class, 'update'])->name('update');
        Route::delete('/{translation}', [TranslationController::class, 'destroy'])->name('destroy');
        Route::get('/export/{locale}/{tag?}', [TranslationController::class, 'export'])->name('export');
    });

    Route::post('/logout', [AuthController::class, 'logout']);
});
