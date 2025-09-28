@extends('layouts.app')

@section('content')
    <div id="answer-container" class="relative flex flex-col items-center">
        <p class="p-3 text-lg">{{ $data['latA'] }}</p>
        <p class="p-3 text-lg">{{ $data['lngA'] }}</p>
        <button type="submit"
            class="flex fixed right-10 top-1/2 -translate-y-1/2
                items-center justify-center
                w-20 h-40 rounded-xl cursor-pointer
                bg-white/30 hover:bg-white/60 transition">
            次へ
        </button>
    </div>
@endsection

@push('scripts')
@endpush