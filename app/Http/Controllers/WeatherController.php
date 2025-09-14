<?php

namespace App\Http\Controllers;

use App\Http\Requests\WeatherRequest;
use App\Services\WeatherService;
use Illuminate\Http\Request;
use Illuminate\Http\Client\RequestException;

class WeatherController extends Controller
{
    public function __construct(private WeatherService $weather) {}

    public function index(Request $request)
    {
        // 初期表示用（例として甲府をデフォルトに）
        $defaultCity = 'Kofu,JP';
        $data = null;
        $error = null;

        if ($request->has('city')) {
            try {
                $data = $this->weather->getCurrentByCity($request->get('city'));
            } catch (RequestException $e) {
                $error = $e->getMessage();
            }
        } else {
            // 初回にサンプル表示したい場合はコメントアウト解除
            // try {
            //     $data = $this->weather->getCurrentByCity($defaultCity);
            // } catch (RequestException $e) {
            //     $error = $e->getMessage();
            // }
        }

        return view('weather', [
            'data' => $data,
            'error' => $error,
        ]);
    }

    public function show(WeatherRequest $request)
    {
        $city = $request->validated('city');

        try {
            $data = $this->weather->getCurrentByCity($city);
            return view('weather', [
                'data' => $data,
                'error' => null,
            ]);
        } catch (RequestException $e) {
            return view('weather', [
                'data' => null,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
