@extends('layouts.bookmark')

@section('content')
<div class="justify-center items-center my-10 px-24 py-10
            bg-black/50 backdrop-blur-sm rounded-xl
            text-white">
    <h2 class="text-2xl font-bold mb-6 text-center">
        {{ old('mode', $mode) === 'edit' ? '編集' : '新規登録' }}
    </h2>

    <form method="POST"
          action="{{ old('mode', $mode) === 'edit'
                    ? route('bookmark.update')
                    : route('bookmark.create') }}"
          enctype="multipart/form-data"
          class="space-y-5">
        @csrf

        <input type="hidden" name="mode" value="{{ old('mode', $mode) }}">
        @if(old('mode', $mode) === 'edit')
            <input type="hidden" name="id" value="{{ old('id', $bookmark->id ?? '') }}">
        @endif

        <div class="grid grid-cols-3 gap-6">
            <!-- 名前 -->
            <div class="col-span-2">
                <label class="block text-lg font-medium">名前</label>
                <input type="text" name="name"  id="form-name"
                    value="{{ old('name', $bookmark->name ?? '') }}"
                    class="block w-full rounded-md shadow-sm
                        bg-white/30 focus:bg-white
                        mt-1 py-1 px-5 text-black text-lg
                        focus:border-sky-500" required>
                @error('name')
                    <p class="bg-red-500 text-sm px-3 py-1 rounded mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 表示順 -->
            <div>
                <label class="block text-lg font-medium ">表示順</label>
                <input type="number" name="priority" id="form-priority"
                    min="1" max="{{ old('mode', $mode) === 'edit' ? $bookmarksCount : $bookmarksCount + 1 }}"
                    value="{{ old('priority', $bookmark->priority ?? ( ($bookmarksCount ?? null) ? $bookmarksCount + 1 : 1 )) }}"
                    class="block w-full rounded-md shadow-sm
                        bg-white/30 focus:bg-white
                        mt-1 py-1 px-5 text-black text-lg
                        focus:border-sky-500" required>
                @error('priority')
                    <p class="bg-red-500 text-sm px-3 py-1 rounded mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- URL -->
        <div>
            <label class="block text-lg font-medium">URL</label>
            <input type="url" name="url"
                   value="{{ old('url', $bookmark->link_url ?? '') }}"
                   class="block w-full rounded-md bg-white/30 focus:bg-white mt-1 py-1 px-5 text-black text-lg">
            @error('url')
                <p class="bg-red-500 text-white text-sm px-3 py-1 rounded mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- 画像ファイル -->
        <div>
            <label class="block text-lg font-medium">画像ファイル</label>
            <input type="file" name="img_name" accept="image/*"
                   class="mt-1 block w-full text-sm text-white
                        file:mr-4 file:py-2 file:px-6 file:rounded-md file:border-0
                        file:text-sm file:font-semibold file:bg-black/50 file:text-white/80
                        file:cursor-pointer hover:file:bg-black/70 hover:text-white">
            @error('img_name')
                <p class="bg-red-500 text-white text-sm px-3 py-1 rounded mt-1">{{ $message }}</p>
            @enderror

            @if(isset($bookmark) && $bookmark?->img_name)
                <p class="mt-2 text-white/80 text-sm">現在の画像: {{ $bookmark->img_name }}</p>
            @endif
        </div>

        <!-- ボタン -->
        <div class="flex justify-end space-x-4 pt-4">
            <a href="{{ route('bookmark') }}"
               class="px-6 py-2 rounded-md text-white/70 font-semibold
                    bg-black/50 hover:bg-black/30 hover:text-white
                    hover:outline hover:outline-2 hover:outline-offset-1
                    cursor-pointer">
                キャンセル
            </a>
            <button type="submit"
                class="px-12 py-2 rounded-md text-white font-semibold
                    bg-black/50 hover:bg-black/30
                    hover:outline hover:outline-2 hover:outline-offset-1
                    cursor-pointer">
                保存
            </button>
        </div>
    </form>
</div>
@endsection
