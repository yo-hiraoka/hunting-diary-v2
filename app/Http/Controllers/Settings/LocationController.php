<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Services\OpenMeteoGeocodingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class LocationController extends Controller
{
    public function edit(Request $request): View
    {
        return view('settings.location.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(
        Request $request,
        OpenMeteoGeocodingService $geocodingService
    ): RedirectResponse {
        $validated = $request->validate([
            'weather_prefecture' => [
                'required',
                'string',
                'max:20',
            ],
            'weather_city' => [
                'required',
                'string',
                'max:100',
            ],
        ]);

        try {
            $coordinates = $geocodingService->findCoordinates(
                $validated['weather_prefecture'],
                $validated['weather_city']
            );
        } catch (Throwable) {
            throw ValidationException::withMessages([
                'weather_city' => [
                    '地域情報サービスへ接続できませんでした。'
                    .'しばらくしてからもう一度お試しください。',
                ],
            ]);
        }

        if ($coordinates === null) {
            throw ValidationException::withMessages([
                'weather_city' => [
                    '入力された都道府県と市区町村を確認できませんでした。',
                ],
            ]);
        }

        $request->user()->update([
            'weather_prefecture' => $validated['weather_prefecture'],

            'weather_city' => $validated['weather_city'],

            'weather_latitude' => $coordinates['latitude'],

            'weather_longitude' => $coordinates['longitude'],
        ]);

        return to_route('settings.location.edit')
            ->with(
                'status',
                '固定地域と位置情報を保存しました。'
            );
    }
}
