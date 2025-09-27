@extends('layouts.app')
@section('content')
<div class="flex items-center justify-center my-10">
    <div class="bg-gray-800 p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center">ログイン</h2>
        @if ($errors->any())
            <div>{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- メールアドレス -->
            <div>
                <label for="email" class="block text-sm font-medium mb-1">メールアドレス</label>
                <input type="email" id="email" name="email" required autofocus
                       class="w-full px-4 py-2 rounded-md bg-gray-700 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400"
                       placeholder="you@example.com">
            </div>

            <!-- パスワード -->
            <div>
                <label for="password" class="block text-sm font-medium mb-1">パスワード</label>
                <input type="password" id="password" name="password" required
                       class="w-full px-4 py-2 rounded-md bg-gray-700 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400"
                       placeholder="••••••••">
            </div>

            <!-- ログイン状態を保持 -->
            <div class="flex items-center">
                <input type="checkbox" id="remember" name="remember"
                       class="h-4 w-4 text-blue-600 bg-gray-700 border-gray-600 rounded focus:ring-blue-500">
                <label for="remember" class="ml-2 text-sm">ログイン状態を保持</label>
            </div>

            <!-- ボタン -->
            <div>
                <button type="submit"
                        class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 rounded-md
                            font-semibold text-white transition duration-200 cursor-pointer">
                    ログイン
                </button>
            </div>
        </form>
    </div>
</div>
@endsection