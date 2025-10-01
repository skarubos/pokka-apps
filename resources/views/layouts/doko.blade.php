<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache">
    <title>{{ $title ?? 'Doko Game' }}</title>
    @vite('resources/css/app.css') {{-- TailwindやCSSビルド用 --}}
</head>
<body class="bg-gray-900 text-gray-300">

    {{-- フラッシュメッセージ --}}
    @if (session('success'))
        <div id="flash-success"
            class="relative flex items-center justify-between
                    rounded-xl bg-green-800 text-green-100 px-5 py-2 mb-2
                    transition-opacity duration-300">
            <span>{{ session('success') }}</span>
            <button type="button"
                    onclick="document.getElementById('flash-success').classList.add('opacity-0'); setTimeout(()=>document.getElementById('flash-success').remove(),300)"
                    class="cursor-pointer ml-4 font-bold text-white/80 hover:text-white">
                ✕
            </button>
        </div>
    @endif
    @if (session('error'))
        <div id="flash-error"
            class="relative flex items-center justify-between
                    rounded-xl bg-red-900 text-red-100 px-5 py-2
                    transition-opacity duration-300">
            <span>{{ session('error') }}</span>
            <button type="button"
                    onclick="document.getElementById('flash-error').classList.add('opacity-0'); setTimeout(()=>document.getElementById('flash-error').remove(),300)"
                    class="cursor-pointer ml-4 font-bold text-white/80 hover:text-white">
                ✕
            </button>
        </div>
    @endif


    {{-- ページごとの内容 --}}
    <main class="flex flex-col h-screen">
        @yield('content')
    </main>

    @stack('scripts')

</body>
</html>
