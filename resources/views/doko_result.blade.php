@extends('layouts.app')

@section('content')
    <div class="items-center text-center text-2xl">
        <h2 class="text-2xl font-bold mb-4">ゲーム結果</h2>

        {{-- ゲーム概要 --}}
        <div class="mb-6">
            <p>
                <strong>合計スコア:</strong>
                <span class="text-5xl font-bold"> {{ $myGame->result }} </span>
                / 2500
            </p>
        </div>

        {{-- 各ステージの結果一覧 --}}
        <table class="table-auto border-collapse border border-gray-400 w-full mb-6">
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
                        <td class="border border-gray-400 px-4 py-2 text-center">{{ $log->location_id }}</td>
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
        <div class="flex space-x-4">
            <a href="{{ route('doko.start') }}" 
            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                新しいゲームを始める
            </a>
            <a href="{{ route('doko.home') }}" 
            class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                ホームに戻る
            </a>
        </div>
    </div>
@endsection