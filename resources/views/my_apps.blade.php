@extends('layouts.app')

@section('content')

    <div class="mx-auto max-w-7xl py-8">
        <ul role="list" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($data as $app)
                <li class="col-span-1 divide-y divide-gray-200 rounded-lg bg-gray-800 shadow border-1 border-gray-700 hover:border-white hover:shadow-md transition-shadow">
                    <a href="{{ $app->url }}" target="_blank" class="flex w-full items-center justify-between space-x-6 p-6">
                        <div class="flex-1 truncate !mr-1">
                            <div class="flex items-center space-x-3">
                                <h3 class="truncate text-lg font-medium text-gray-100">
                                    {{ $app->name }}
                                </h3>
                            </div>
                            <p class="truncate text-sm text-gray-500">
                                {{ $app->url }}
                            </p>
                            <p class="mt-2 text-sm text-gray-300 line-clamp-2">
                                {{ $app->explanation }}
                            </p>
                        </div>
                        {{-- 大なりアイコン（Heroicons） --}}
                        <svg class="h-5 w-5 text-gray-400 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </li>
            @empty
                <li class="text-lg text-gray-300 p-5">-- まだ登録がありません --</li>
            @endforelse
        </ul>
        <div  class="flex justify-between mt-5 text-white">
            <a href="{{ route('myapps.sort') }}" class="inline-flex w-full justify-center items-center px-3 py-2 mr-5 bg-gray-800 rounded hover:bg-gray-700">
                <svg class="h-5 w-5 mr-2 text-white flex-shrink-0"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">
                <path stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M8 3v18m0 0l-4.5-4.5M8 21l4.5-4.5M16 21V3m0 0l4.5 4.5M16 3l-4.5 4.5" />
                </svg>
                並べ替え
            </a>
            <a href="{{ route('myapps.edit') }}" class="inline-flex w-full justify-center items-center px-3 py-2 bg-gray-800 rounded hover:bg-gray-700">
                <svg class="h-5 w-5 mr-2 text-white flex-shrink-0"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">
                <path stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L7.5 21H3v-4.5L16.732 3.732z" />
                </svg>
                編集
            </a>
        </div>
    </div>

@endsection