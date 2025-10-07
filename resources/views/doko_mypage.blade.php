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
        <h2 class="text-center text-2xl font-bold mb-6">マップの種類を選択して開始</h2>
        <div class="flex justify-center space-x-6 mb-6">
            @foreach ($gameModes as $mode)
                <a href="{{ route('doko.start', ['mode' => $mode->id]) }}"
                    class="flex items-center justify-center
                        w-64 h-20 rounded-xl cursor-pointer
                        bg-white/30 hover:bg-white/50
                        hover:outline-3 outline-offset-3 outline-white/50">
                    <span class="">
                        {{ $mode->name }}
                    </span>
                </a>
            @endforeach
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