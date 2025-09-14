@extends('layouts.app')
@vite('resources/css/app.css')
@section('content')

    <div class="mx-auto max-w-2xl p-6">
        <h1 class="text-2xl font-semibold mb-4">現在の天気</h1>

        <form method="POST" action="{{ route('weather.show') }}" class="mb-6 flex gap-2">
            @csrf
            <input
                type="text"
                name="city"
                placeholder="例: Kofu,JP"
                value="{{ old('city') }}"
                class="border rounded px-3 py-2 w-full"
                required
            />
            <x-button type="submit" class="!px-25 !my-0 text-lg text-center cursor-pointer whitespace-nowrap">取得</x-button>
        </form>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded mb-4">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- @if ($error)
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-3 rounded mb-4">
                {{ $error }}
            </div>
        @endif -->

        @if ($data)
            @php
                // 必要な項目だけに整形
                $cityName = $data['name'] ?? '';
                $weather  = $data['weather'][0]['description'] ?? '';
                $icon     = $data['weather'][0]['icon'] ?? null;
                $temp     = $data['main']['temp'] ?? null;
                $feels    = $data['main']['feels_like'] ?? null;
                $humidity = $data['main']['humidity'] ?? null;
                $wind     = $data['wind']['speed'] ?? null;
            @endphp

            <div class="border rounded p-4">
                <div class="flex items-center gap-3">
                    @if ($icon)
                        <img src="https://openweathermap.org/img/wn/{{ $icon }}@2x.png" alt="icon">
                    @endif
                    <div>
                        <div class="text-xl font-medium">{{ $cityName }}</div>
                        <div class="text-gray-400">{{ $weather }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div class="p-3 rounded">
                        <div class="text-sm text-gray-400">気温</div>
                        <div class="text-lg">{{ $temp }} ℃</div>
                    </div>
                    <div class="p-3 rounded">
                        <div class="text-sm text-gray-400">体感温度</div>
                        <div class="text-lg">{{ $feels }} ℃</div>
                    </div>
                    <div class="p-3 rounded">
                        <div class="text-sm text-gray-400">湿度</div>
                        <div class="text-lg">{{ $humidity }} %</div>
                    </div>
                    <div class="p-3 rounded">
                        <div class="text-sm text-gray-400">風速</div>
                        <div class="text-lg">{{ $wind }} m/s</div>
                    </div>
                </div>
            </div>
        @endif
    </div>

@endsection