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
        <a href="{{ route('doko.start') }}" target="_self"
            class="flex items-center justify-center
                w-60 h-30 rounded-xl cursor-pointer mt-12
                bg-white/30 hover:bg-white/60 transition">
        <p class="text-2xl font-bold text-center">開始</p>
        </a>
        <a href="{{ route('doko.mypage') }}" target="_self"
            class="flex items-center justify-center
                w-60 h-20 rounded-xl cursor-pointer mt-12
                bg-white/30 hover:bg-white/60 transition">
        <p class="text-2xl font-bold text-center">マイページ</p>
        </a>
    </div>
@endsection

@push('scripts')
@endpush