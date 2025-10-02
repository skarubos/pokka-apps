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
        <table class="items-center mx-auto table-auto border-collapse border border-gray-400 wmb-2 text-xl">
            <thead>
                <tr class="bg-gray-600">
                    <th class="border border-gray-400 px-4 py-2">ステージ</th>
                    <th class="border border-gray-400 px-4 py-2">出題場所</th>
                    <th class="border border-gray-400 px-4 py-2">距離 (km)</th>
                    <th class="border border-gray-400 px-4 py-2">スコア</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                    <tr>
                        <td class="border border-gray-400 px-4 py-2 text-center">{{ $log->stage }}</td>
                        <td class="border border-gray-400 px-4 py-2 text-center">
                            @if($myGame->game_mode_id == 1)
                                {{ $log->country }} : {{ $log->region }} （ {{ $log->name }} ）
                            @elseif($myGame->game_mode_id == 2)
                                {{ $log->region }} {{ $log->sub_region }}
                            @endif
                        </td>
                        <td class="border border-gray-400 px-4 py-2 text-center">
                            {{ $log->distance !== null ? $log->distance : '-' }}
                        </td>
                        <td class="border border-gray-400 px-4 py-2 text-center">
                            {{ $log->score !== null ? $log->score : '-' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- 戻るボタンや再挑戦リンク --}}
        <div class="flex space-x-4 items-center justify-center">
            <a href="{{ route('doko.mypage') }}" 
            class="flex items-center justify-center
                w-60 h-30 rounded-xl cursor-pointer mt-12
                bg-white/30 hover:bg-white/60 transition">
                マイページ
            </a>
            <a href="{{ route('doko.start', ['mode' => $myGame->game_mode_id]) }}" 
            class="flex items-center justify-center
                w-60 h-30 rounded-xl cursor-pointer mt-12
                bg-white/30 hover:bg-white/60 transition">
                再挑戦
            </a>
        </div>
    </div>
@endsection