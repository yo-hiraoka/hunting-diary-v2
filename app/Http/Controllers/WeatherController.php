<?php

namespace App\Http\Controllers;

use App\Services\OpenMeteoWeatherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class WeatherController extends Controller
{
    public function show(
        Request $request,
        OpenMeteoWeatherService $weatherService
    ): JsonResponse {
        $validated = $request->validate([
            'date' => ['required', 'date_format:Y-m-d'],
        ]);

        $user = $request->user();

        if (
            $user->weather_latitude === null ||
            $user->weather_longitude === null
        ) {
            return response()->json([
                'message' => '先に固定地域を設定してください。',
            ], 422);
        }

        try {
            $weather = $weatherService->getDailyWeather(
                (float) $user->weather_latitude,
                (float) $user->weather_longitude,
                $validated['date']
            );
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'data' => $weather,
        ]);
    }
}
