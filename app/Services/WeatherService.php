<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class WeatherService
{
    public function getCurrentByCity(string $city): array
    {
        $base  = config('services.openweather.base');
        $key   = config('services.openweather.key');
        $lang  = config('services.openweather.lang', 'ja');
        $units = config('services.openweather.units', 'metric');

        $response = Http::timeout(8)
            ->retry(2, 200) // 短いリトライ
            ->get("{$base}/weather", [
                'q'     => $city,
                'appid' => $key,
                'lang'  => $lang,
                'units' => $units,
            ]);

        if ($response->failed()) {
            // OpenWeatherのエラーフォーマットをそのまま伝搬（UIで扱いやすい形に）
            $code = $response->status();
            $body = $response->json() ?? ['message' => 'OpenWeather API request failed'];
            throw new RequestException($response, "OpenWeather error: {$code} - " . ($body['message'] ?? 'unknown'));
        }

        return $response->json();
    }
}