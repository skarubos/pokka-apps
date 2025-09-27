@extends('layouts.app')
@section('content')
<div class="min-h-screen flex items-center justify-center">
    <div class="bg-gray-800 p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center">新規ユーザー登録</h2>
        @if ($errors->any())
            <div>
                @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif
        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <!-- ユーザー名 -->
            <div>
                <label for="name" class="block text-sm font-medium mb-1">ユーザー名</label>
                <input type="text" id="name" name="name" required
                       class="w-full px-4 py-2 rounded-md bg-gray-700 border border-gray-600 
                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 
                              text-white placeholder-gray-400"
                       placeholder="Your Name">
            </div>

            <!-- メールアドレス -->
            <div>
                <label for="email" class="block text-sm font-medium mb-1">メールアドレス</label>
                <input type="email" id="email" name="email" required
                       class="w-full px-4 py-2 rounded-md bg-gray-700 border border-gray-600 
                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 
                              text-white placeholder-gray-400"
                       placeholder="you@example.com">
            </div>

            <!-- パスワード -->
            <div>
                <label for="password" class="block text-sm font-medium mb-1">パスワード</label>
                <input type="password" id="password" name="password" required
                       class="w-full px-4 py-2 rounded-md bg-gray-700 border border-gray-600 
                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 
                              text-white placeholder-gray-400"
                       placeholder="••••••••">
            </div>

            <!-- パスワード確認 -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium mb-1">パスワード確認</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                       class="w-full px-4 py-2 rounded-md bg-gray-700 border border-gray-600 
                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 
                              text-white placeholder-gray-400"
                       placeholder="••••••••">
            </div>

            <!-- 登録ボタン -->
            <div>
                <button type="submit"
                        class="w-full py-2 px-4 bg-green-600 hover:bg-green-700 rounded-md font-semibold 
                               text-white transition duration-200 cursor-pointer">
                    登録
                </button>
            </div>
        </form>
    </div>
</div>
@endsection