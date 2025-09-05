<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache">
    <title>{{ $title ?? 'Pakka App' }}</title>
    @vite('resources/css/app.css') {{-- TailwindやCSSビルド用 --}}
</head>
<body class="bg-gray-900 text-gray-300">

    {{-- 共通ヘッダー部分 --}}
    <header class="bg-gray-800 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">
                {{ $title ?? 'My Pakka App' }}
            </h1>
            <nav>
                <ul class="flex space-x-4">
                    <li><a href="{{ url('/') }}" class="hover:underline">Home</a></li>
                    <li><a href="{{ url('/rewards/home') }}" class="hover:underline">Rewards</a></li>
                    <li><a href="{{ url('/about') }}" class="hover:underline">About</a></li>
                </ul>
            </nav>
        </div>
    </header>

    {{-- ページごとの内容 --}}
    <main class="container mx-auto p-6">
        @yield('content')
    </main>

    @stack('scripts')

</body>
</html>
