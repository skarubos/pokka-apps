@extends('layouts.app')

@section('content')

<div class="mx-auto max-w-7xl px-4px-6 py-8">
    <ul role="list" class="grid grid-cols-1 gap-6grid-cols-2 lg:grid-cols-3">

        @foreach ($data as $app)
        <li class="col-span-1 rounded-lg m-2 bg-gray-800 shadow border border-gray-700 hover:border-white hover:shadow-md transition-shadow p-6">
            
            {{-- 入力欄部分 --}}
            <div class="space-y-4">
                @if($app->id == 100)
                    <p class="text-center font-bold text-gray-100">新規登録</p>
                @else
                    <div class="inline-flex">
                        <label class="font-medium text-gray-400">ID：</label>
                        <p class="text-gray-200">{{ $app->id }}</p>
                    </div>
                @endif

                <div>
                    <label for="name-{{ $app->id }}" class="block font-medium text-gray-400">Name</label>
                    <input type="text" form="update-form-{{ $app->id }}" name="name" id="name-{{ $app->id }}"
                        value="{{ old('name', $app->name) }}"
                        class="mt-1 block w-full rounded-md py-1 px-2 bg-gray-700 border-gray-600 text-gray-100 focus:border-blue-500 focus:ring-blue-500" />
                </div>

                <div>
                    <label for="url-{{ $app->id }}" class="block font-medium text-gray-400">URL</label>
                    <input type="text" form="update-form-{{ $app->id }}" name="url" id="url-{{ $app->id }}"
                        value="{{ old('url', $app->url) }}"
                        class="mt-1 block w-full rounded-md py-1 px-2 bg-gray-700 border-gray-600 text-gray-100 focus:border-blue-500 focus:ring-blue-500" />
                </div>

                <div>
                    <label for="explanation-{{ $app->id }}" class="block font-medium text-gray-400">Explanation</label>
                    <textarea form="update-form-{{ $app->id }}" name="explanation" id="explanation-{{ $app->id }}" rows="3"
                            class="mt-1 block w-full rounded-md py-1 px-2 bg-gray-700 border-gray-600 text-gray-100 focus:border-blue-500 focus:ring-blue-500">{{ old('explanation', $app->explanation) }}</textarea>
                </div>
            </div>

            {{-- ボタン行（横並び） --}}
            <div class="flex gap-2 mt-4">
                {{-- 更新フォーム --}}
                <form id="update-form-{{ $app->id }}" action="{{ route('myapps.update', $app->id) }}" method="POST" class="flex-1">
                    @csrf
                    @method('PUT')
                    <button type="submit"
                            class="w-full inline-flex cursor-pointer items-center justify-center px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
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
                        {{ $app->id == 100 ? '登録' : '変更' }}
                    </button>
                </form>

                {{-- 削除フォーム --}}
                @if($app->id != 100)
                <form action="{{ route('myapps.destroy', $app->id) }}" method="POST" class="flex-1" onsubmit="return confirm('削除してよろしいですか？');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full inline-flex cursor-pointer items-center justify-center px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        <svg class="h-5 w-5 mr-2 text-white flex-shrink-0"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M8 7V4a1 1 0 011-1h6a1 1 0 011 1v3" />
                        </svg>
                        削除
                    </button>
                </form>
                @endif
            </div>
        </li>
        @endforeach

    </ul>
</div>

@endsection