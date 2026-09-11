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
    Route::get('/settings/location', [LocationController::class, 'edit'])
        ->name('settings.location.edit');
    Route::get('/diaries/{diary}/pdf', DiaryPdfController::class)
        ->name('diaries.pdf');

    Route::patch('/settings/location', [LocationController::class, 'update'])
        ->name('settings.location.update');
});
Route::get('/diaries', [
    DiaryController::class,
    'index',
])->name('diaries.index');

Route::get('/diaries/create', [
    DiaryController::class,
    'create',
])->name('diaries.create');

Route::get('/diaries/trash', [
    DiaryController::class,
    'trash',
])->name('diaries.trash');

Route::get('/diaries/pdf/list', DiaryListPdfController::class)
    ->name('diaries.pdf.list');

Route::post('/diaries', [
    DiaryController::class,
    'store',
])->name('diaries.store');

Route::get('/diaries/{diary}', [
    DiaryController::class,
    'show',
])->name('diaries.show');

Route::get('/diaries/{diary}/edit', [
    DiaryController::class,
    'edit',
])->name('diaries.edit');

Route::patch('/diaries/{diary}', [
    DiaryController::class,
    'update',
])->name('diaries.update');

Route::delete('/diaries/{diary}', [
    DiaryController::class,
    'destroy',
])->name('diaries.destroy');

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
