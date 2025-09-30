<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Start Page</title>
    @vite('resources/css/app.css') <!-- TailwindやCSSビルド用 -->
</head>
<body class="mx-auto py-6 px-6 lg:px-30"
        style="
        background: radial-gradient(circle, rgba(252,255,216,1) 0%, rgba(183,164,115,1) 100%);
        background-attachment: fixed;
        min-height: 100vh;
    ">

    <!-- フラッシュメッセージ -->
    @if (session('success'))
        <div id="flash-success"
            class="relative flex items-center justify-between
                    rounded-xl bg-sky-600 text-white px-5 py-2 mb-2
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
                    rounded-xl bg-red-800 text-white px-5 py-2
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
    @yield('content')

    @stack('scripts')

</body>
</html>
