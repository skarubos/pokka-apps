@extends('layouts.app')

@section('content')
    <div class="items-center text-center px-12 py-6 mx-auto text-2xl">

        {{-- 結果概要 --}}
        @if($newRecord)
            <div class="mb-4 text-amber-400 italic font-bold animate-pulse">
                🎉 New Record 🎉
            </div>
        @endif
        <div class="mb-6">
            <p>
                <strong>合計スコア:</strong>
                <span class="text-5xl font-bold"> {{ $myGame->result }} </span>
                / {{ $maxScore }}
            </p>
            @if(!$newRecord)
                <div class="mt-2 italic">
                    （自己ベスト {{ $myBestScore }} ）
                </div>
            @endif
        </div>

        {{-- 各ステージの結果一覧 --}}
        <div class="px-6 pt-3 pb-8 mx-auto mb-3">
        <table class="items-center mx-auto table-auto border border-gray-600 text-xl rounded-2xl overflow-hidden">
            <div class="items-center text-center px-6 py-3 mx-auto">
                <p class="inline-flex text-xl"> 出題マップ： </p>
                <p class="inline-flex"> {{ $myGame->gameMode->name }} </p>
            </div>
            <thead>
                <tr class="bg-gray-600 text-lg">
                    <th class="px-4 py-2">ステージ</th>
                    <th class="px-4 py-2">出題場所</th>
                    <th class="px-4 py-2">距離 (km)</th>
                    <th class="px-4 py-2">スコア</th>
                </tr>
            </thead>     
            <tbody>
                @foreach($logs as $log)
                    <tr class="bg-gray-800">
                        <td class="border-2 border-gray-600 px-4 py-2 text-center">{{ $log->stage }}</td>
                        <td class="border-2 border-gray-600 px-4 py-2 text-center">
                            @if($myGame->game_mode_id == 1)
                                {{ $log->country }} : {{ $log->region }} （ {{ $log->name }} ）
                            @elseif($myGame->game_mode_id == 2)
                                {{ $log->region }} {{ $log->sub_region }}
                            @elseif($myGame->game_mode_id == 3)
                                {{ $log->region }} {{ $log->sub_region }}
                            @elseif($myGame->game_mode_id == 4)
                                {{ $log->country }}
                            @endif
                        </td>
                        <td class="border-2 border-gray-600 px-4 py-2 text-center">
                            {{ $log->distance !== null ? $log->distance : '-' }}
                        </td>
                        <td class="border-2 border-gray-600 px-8 py-2 text-center">
                            {{ $log->score !== null ? $log->score : '-' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>

        {{-- 戻るボタンや再挑戦リンク --}}
        <div class="flex space-x-4 items-center justify-center">
            <a href="{{ route('doko.mypage') }}" 
            class="flex items-center justify-center
                w-60 h-30 rounded-xl cursor-pointer
                bg-white/30 hover:bg-white/60 transition">
                マイページ
            </a>
            <a href="{{ route('doko.start', ['mode' => $myGame->game_mode_id]) }}" 
            class="flex items-center justify-center
                w-60 h-30 rounded-xl cursor-pointer
                bg-white/30 hover:bg-white/60 transition">
                再挑戦
            </a>
        </div>
    </div>
@endsection