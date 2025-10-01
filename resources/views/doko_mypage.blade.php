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

    <div id="answer-container" class="">
        <div class="flex space-x-4 items-center justify-center text-2xl">
            <a href="{{ route('doko.start') }}" 
            class="flex items-center justify-center
                w-60 h-30 rounded-xl cursor-pointer mt-12
                bg-white/30 hover:bg-white/60 transition">
                開始
            </a>
            <a href="{{ route('doko.next') }}" 
            class="flex items-center justify-center
                w-60 h-30 rounded-xl cursor-pointer mt-12
                bg-white/30 hover:bg-white/60 transition">
                再開
            </a>
        </div>
    </div>
@endsection

@push('scripts')
@endpush