@extends('layouts.app')

@section('content')
    <div id="answer-container" class="relative flex flex-col items-center">
        <p class="p-12 text-xl">
            <span class="relative -top-2">¿¿¿¿¿¿¿¿</span>
            <span class="px-6 text-5xl tracking-widest">どこだココ</span>
            <span class="relative -top-1">????????</span>
        </p>
        <p class="p-3 text-xl text-center">
            ここはドコ？ 手掛かりは1枚の衛星写真のみ！<br>
            全体を俯瞰する？ズームして何かを見つける？<br>
            5回のチャンスで高得点を狙おう！
        </p>
        <div class="flex flex-col items-center mt-12 space-y-6">
            <h2 class="text-2xl font-bold">マップの種類を選択して開始</h2>

            <div class="flex justify-center space-x-6 mb-6">
                @foreach ($gameModes as $mode)
                    <a href="{{ route('doko.start', ['mode' => $mode->id]) }}"
                    class="flex items-center justify-center
                            w-62 h-20 rounded-xl cursor-pointer
                            bg-white/30 hover:bg-white/50
                            hover:outline-3 outline-offset-3 outline-white/50">
                        <span class="text-xl font-bold">
                            {{ $mode->name }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
        <a href="{{ route('doko.mypage') }}" target="_self"
            class="flex items-center justify-center
                w-50 h-20 rounded-xl cursor-pointer
                bg-white/30 hover:bg-white/50 transition">
        <p class="text-xl font-bold text-center">マイページ</p>
        </a>
    </div>
@endsection

@push('scripts')
@endpush