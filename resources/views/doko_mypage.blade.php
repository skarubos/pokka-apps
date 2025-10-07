@extends('layouts.app')

@section('content')
    <div id="answer-container" class="relative flex flex-col items-center">
        <p class="p-3 text-xl text-center">
            ログイン中： {{ $user->name }}
        </p>
    </div>
    <div class="items-center text-center py-6 text-2xl">
        @if($bestGame)
            <div class="mb-6">
                <p>
                    <strong>自己ベスト:</strong>
                    <span class="text-5xl font-bold"> {{ $bestGame->result }} </span>
                    / 5000
                </p>
            </div>
        @else
            <div class="mb-6 italic">
                まだプレイしたことがありません。
            </div>
        @endif
    </div>

    <div class="text-xl font-bold">
        <div class="flex items-center justify-center mt-16 mb-6 p-6
                    relative border-3 border-gray-500 rounded-2xl">
            <h2 class="absolute -top-5 left-1/2 -translate-x-1/2 px-3 bg-gray-900 text-2xl font-bold">
                マップを選択して開始
            </h2>

            <div class="flex grid grid-cols-3 gap-4">
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
        <form method="POST" action="{{ route('doko.next') }}" class="">
            @csrf
            <input type="hidden" name="game_id" value="">
            <input type="hidden" name="game_mode_id" value="">
            <button type="submit"
                class="flex items-center m-auto justify-center
                w-64 h-20 rounded-xl cursor-pointer
                bg-white/30 hover:bg-white/50
                hover:outline-3 outline-offset-3 outline-white/50">
                再開
            </button>
        </form>
        
    </div>
@endsection

@push('scripts')
@endpush