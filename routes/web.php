<?php

use App\Http\Controllers\DiaryController;
use App\Http\Controllers\DiaryListPdfController;
use App\Http\Controllers\DiaryPdfController;
use App\Http\Controllers\Settings\LocationController;
use App\Http\Controllers\WeatherController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return to_route('diaries.index');
    }

    return to_route('login');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/weather', [
        WeatherController::class,
        'show',
    ])->name('weather.show');

    Route::get('/settings/location', [
        LocationController::class,
        'edit',
    ])->name('settings.location.edit');

    Route::patch('/settings/location', [
        LocationController::class,
        'update',
    ])->name('settings.location.update');

    /*
     * diaries/{diary}より前に定義する必要がある固定URL
     */
    Route::get('/diaries/trash', [
        DiaryController::class,
        'trash',
    ])->name('diaries.trash');

    Route::get(
        '/diaries/pdf/list',
        DiaryListPdfController::class
    )->name('diaries.pdf.list');

    /*
     * 日誌の基本CRUD
     */
    Route::resource('diaries', DiaryController::class);

    /*
     * 日誌の追加機能
     */
    Route::get(
        '/diaries/{diary}/pdf',
        DiaryPdfController::class
    )->name('diaries.pdf');

    Route::patch('/diaries/{diary}/restore', [
        DiaryController::class,
        'restore',
    ])
        ->withTrashed()
        ->name('diaries.restore');

    Route::delete('/diaries/{diary}/force-delete', [
        DiaryController::class,
        'forceDelete',
    ])
        ->withTrashed()
        ->name('diaries.force-delete');
});
