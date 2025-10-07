@extends('layouts.app')

@section('content')
    <div id="answer-container" class="relative flex flex-col items-center">
        <p class="pt-12 pb-3 text-xl">
            <span class="relative -top-2">¿¿¿¿¿¿¿¿</span>
            <span class="px-6 text-5xl tracking-widest">どこだココ</span>
            <span class="relative -top-1">????????</span>
        </p>
        <p class="p-3 text-xl text-center">
            ここはドコ？ 手掛かりは1枚の衛星写真のみ！<br>
            俯瞰して、ズームして、高得点を狙おう！
        </p>
        <div class="flex items-center mt-16 mb-6 p-6
                    relative border-3 border-gray-500 rounded-2xl">
            <h2 class="absolute -top-5 left-1/2 -translate-x-1/2 px-3 bg-gray-900 text-2xl font-bold">
                マップを選択して開始
            </h2>

            <div class="flex justify-center space-x-6">
                @foreach ($gameModes as $mode)
                    <a href="{{ route('doko.start', ['mode' => $mode->id]) }}"
                    class="inline-block items-center justify-center
                            w-62 h-20 rounded-xl cursor-pointer
                            text-xl font-bold text-center
                            bg-white/30 hover:bg-white/50
                            hover:outline-3 outline-offset-3 outline-white/50">
                        @php
                            // 例: 「日本（完全ランダム）」を「日本」と「（完全ランダム）」に分割
                            if (preg_match('/^(.+?)(（.+）)$/u', $mode->name, $matches)) {
                                $before = $matches[1];
                                $inside = $matches[2];
                            } else {
                                $before = $mode->name;
                                $inside = '';
                            }
                        @endphp

                        <div class="items-center pt-3">
                            <div>{{ $before }}</div>
                        @if($inside)
                            <div class="text-lg text-gray-200">
                                {{ $inside }}
                            </div>
                        @endif
                        </div>
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