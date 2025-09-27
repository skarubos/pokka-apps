<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache">
    <title>{{ $title ?? 'Pakka App' }}</title>
    @vite('resources/css/app.css') {{-- TailwindやCSSビルド用 --}}
</head>
<body class="bg-gray-900 text-white">

    <!-- 共通ヘッダー部分 -->
    <header class="bg-gray-800 text-white">
        <div class="container mx-auto px-4 grid grid-cols-6 items-center">
            <!-- 左側 -->
            <div class="col-span-5 flex py-3 items-center justify-between">
                <!-- タイトル -->
                <h1 class="text-xl font-bold">
                    {{ $title ?? 'My Pakka App' }}
                </h1>

                <!-- メニュー一覧 -->
                <nav>
                    <ul class="flex space-x-4">
                        <li><a href="{{ url('/') }}" class="hover:underline">Home</a></li>
                        <li><a href="{{ url('/rewards/home') }}" class="hover:underline">Rewards</a></li>
                        <li><a href="{{ url('/weather') }}" class="hover:underline">Weather</a></li>
                        <li><a href="{{ url('/myapps') }}" class="hover:underline">AppList</a></li>
                        <div class="inline-flex h-full pl-5 bg-gray-700 rounded-xl">
                        </div>
                    </ul>
                </nav>
            </div>

            <!-- 右側（ログイン情報） -->
            <div class="col-span-1 flex h-full items-center justify-center space-x-3 bg-gray-700">
                @auth
                    <span>{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="ml-3 px-2 cursor-pointer hover:underline">
                            ログアウト
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="hover:underline">ログイン</a>
                    <a href="{{ route('register') }}" class="hover:underline">新規登録</a>
                @endauth
            </div>
        </div>
    </header>


    <!-- フラッシュメッセージ -->
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


    <!-- ページごとの内容 -->
    <main class="container mx-auto p-6">
        @yield('content')
    </main>

    @stack('scripts')

</body>
</html>
