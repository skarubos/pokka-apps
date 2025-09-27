@extends('layouts.bookmark')

@section('content')
<div class="mx-auto max-w-7xl pt-8 pb-16">
    <form id="sort-form" action="{{ route('bookmark.sort') }}" method="POST">
        @csrf
        <input type="hidden" name="order" id="order-input">
        <ul role="list" id="sortable-list" class="grid grid-cols-3 gap-2 lg:grid-cols-4">
            @forelse ($bookmarks as $bookmark)
                <li data-id="{{ $bookmark->id }}"
                    data-name="{{ $bookmark->name }}"
                    data-url="{{ $bookmark->link_url }}"
                    data-priority="{{ $bookmark->priority }}"
                    data-img-name="{{ $bookmark->img_name }}" class="">
                    <a href="{{ $bookmark->link_url }}" target="_blank" 
                        class="w-full py-3 lg:px-2 flex flex-col items-center">
                        <img src="{{ asset('storage/images/' . $bookmark->img_name) }}"
                            alt="icon"
                            class="w-22 h-22 lg:w-52 lg:h-52
                                    rounded-full object-cover
                                    bg-white shadow-xl/30
                                    hover:outline hover:outline-5 hover:outline-offset-6 hover:outline-sky-500" />
                        <div class="nametag mt-3 px-2 lg:px-6 bg-black/50 rounded-full shadow-lg/30">
                            <h3 class="truncate text-center text-sm lg:text-lg font-medium text-gray-100">
                                {{ $bookmark->name }}
                            </h3>
                        </div>
                    </a>
                </li>
            @empty
                <li class="text-lg text-gray-300 p-5">-- まだ登録がありません --</li>
            @endforelse
        </ul>

        <!-- 並べ替えメニュー -->
        <div id="sort-menu" class="hidden fixed top-5 left-1/2 -translate-x-1/2
                    justify-center items-center px-12 py-3
                    bg-black/50 backdrop-blur-sm rounded-2xl
                    text-white">
            <p class="inline-block text-xl">
                アイコンをドラックして並べ替えできます
            </p>
            <button type="submit" id="save-order" 
                    class="inline-block cursor-pointer justify-center items-center
                        px-8 py-2 ml-6 border-2 border-gray-200 rounded-full 
                        bg-black/30 hover:bg-sky-500 transition
                        font-bold text-lg">
                並び順を保存
            </button>
        </div>
    </form>

    <!-- 右側の固定メニュー -->
    <div class="hidden lg:flex">
        <!-- ページ最上部へスクロール -->
        <a href="#"
            onclick="window.scrollTo({top:0, behavior:'smooth'}); return false;"
            class="flex group fixed right-5 top-1/4 -translate-y-1/2
                    items-center justify-center
                    w-20 h-20">
            <svg class="w-20 h-20"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 22 22">
                <mask id="icon-mask-up">
                <rect width="100%" height="100%" fill="white" />
                <g transform="scale(0.6) translate(7,7)">
                    <!-- 上矢印 -->
                    <path d="M5 15l6-6 6 6"
                        stroke="black" stroke-width="3"
                        stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                </g>
                </mask>
                <rect x="0" y="0" width="22" height="22" rx="6" ry="6"
                    class="fill-black/20 group-hover:fill-black/50 transition"
                    mask="url(#icon-mask-up)" />
            </svg>
        </a>

        <!-- ページ最下部へスクロール -->
        <a href="#"
            onclick="window.scrollTo({top:document.body.scrollHeight, behavior:'smooth'}); return false;"
            class="flex group fixed right-5 bottom-1/4 translate-y-1/2
                    items-center justify-center
                    w-20 h-20">
            <svg class="w-20 h-20"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 22 22">
                <mask id="icon-mask-down">
                <rect width="100%" height="100%" fill="white" />
                <g transform="scale(0.6) translate(7,7)">
                    <!-- 下矢印 -->
                    <path d="M5 9l6 6 6-6"
                        stroke="black" stroke-width="3"
                        stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                </g>
                </mask>
                <rect x="0" y="0" width="22" height="22" rx="6" ry="6"
                    class="fill-black/20 group-hover:fill-black/50 transition"
                    mask="url(#icon-mask-down)" />
            </svg>
        </a>

        <!-- 新規作成ボタン -->
        <a href="{{ route('bookmark.form', ['mode' => 'create']) }}"
            id="create-new"
            class="flex group fixed right-5 bottom-[calc(50%+0.2rem)]
                    items-center justify-center
                    w-20 h-20 cursor-pointer">

            <svg class="w-20 h-20"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 22 22">
                <mask id="icon-mask-plus">
                <rect width="100%" height="100%" fill="white" />
                <g transform="scale(0.6) translate(7,7)">
                    <!-- プラスマーク -->
                    <path d="M11 5v12M5 11h12"
                        stroke="black" stroke-width="3"
                        stroke-linecap="round" />
                </g>
                </mask>
                <rect x="0" y="0" width="22" height="22" rx="6" ry="6"
                    class="fill-black/10 hover:fill-black/50 transition"
                    mask="url(#icon-mask-plus)" />
            </svg>
        </a>

        <!-- 並べ替えボタン -->
        <a href="#"
            id="toggle-sort"
            class="flex group fixed right-5 top-[calc(50%+0.2rem)]
                    items-center justify-center
                    w-20 h-20 cursor-pointer">

            <!-- 並べ替えアイコン -->
            <svg id="icon-sort" class="w-20 h-20 block" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22">
                <mask id="sort-mask">
                <rect width="100%" height="100%" fill="white" />
                <g transform="scale(0.5) translate(10,10)">
                    <path d="M8 3v18m0 0l-4.5-4.5M8 21l4.5-4.5M16 21V3m0 0l4.5 4.5M16 3l-4.5 4.5"
                        stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                </g>
                </mask>
                <rect width="22" height="22" rx="6" ry="6"
                    class="fill-black/10 group-hover:fill-black/50 transition"
                    mask="url(#sort-mask)" />
            </svg>

            <!-- バツ印アイコン -->
            <svg id="icon-close" class="w-20 h-20 hidden" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22">
                <mask id="close-mask">
                <rect width="100%" height="100%" fill="white" />
                <path d="M6 6l10 10M16 6L6 16"
                        stroke="black" stroke-width="2" stroke-linecap="round" />
                </mask>
                <rect width="22" height="22" rx="6" ry="6"
                    class="fill-black/50 group-hover:fill-sky-500/80 transition"
                    mask="url(#close-mask)" />
            </svg>
        </a>
    </div>
</div>


<!-- 編集・削除用メニュー（右クリックで表示） -->
<div id="context-menu"
    class="hidden absolute z-50 w-30 py-3
            bg-gray-800 rounded-xl shadow-lg
            text-lg text-white text-center">
    <ul>
        <li id="menu-edit" class="py-2 hover:bg-gray-600 cursor-pointer">編集</li>
        <li id="menu-delete">
            <form id="delete-form" method="POST" action="">
                @csrf
                @method('DELETE')
                <button type="submit"
                        id="menu-delete"
                        class="block w-full px-4 py-2 text-red-400 hover:bg-gray-600 cursor-pointer">
                    削除
                </button>
            </form>
        </li>
    </ul>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    const elements = {
        btnNew: document.getElementById('create-new'),
        btnSort: document.getElementById('toggle-sort'),
        iconSort: document.getElementById('icon-sort'),
        iconClose: document.getElementById('icon-close'),
        sortMenu: document.getElementById('sort-menu'),
        sortableList: document.getElementById('sortable-list'),
        orderInput: document.getElementById('order-input'),
        contextMenu: document.getElementById('context-menu'),
        menuEdit: document.getElementById('menu-edit'),
        menuDelete: document.getElementById('menu-delete'),
    };

    // 並べ替え機能の初期化
    const sortable = new Sortable(elements.sortableList, {
        animation: 150,
        disabled: true,
    });

    // 並べ替えフォームの送信処理
    document.getElementById('sort-form').addEventListener('submit', (e) => {
        const order = Array.from(elements.sortableList.querySelectorAll('li')).map((el, index) => ({
            id: el.dataset.id,
            priority: index + 1,
        }));
        elements.orderInput.value = JSON.stringify(order);
    });

    // 並べ替えモードの切り替え
    function toggleSortMode() {
        const isVisible = !elements.sortMenu.classList.contains('hidden');
        elements.iconSort.classList.toggle('hidden', !isVisible);
        elements.iconClose.classList.toggle('hidden', isVisible);
        elements.btnNew.classList.toggle('hidden', !isVisible);
        elements.sortMenu.classList.toggle('hidden', isVisible);
        sortable.option("disabled", isVisible);
    }

    // コンテキストメニューを表示
    function showContextMenu(e, li) {
        e.preventDefault();
        elements.contextMenu.style.top = `${e.pageY}px`;
        elements.contextMenu.style.left = `${e.pageX}px`;
        elements.contextMenu.classList.remove('hidden');
        elements.contextMenu.dataset.currentId = li.dataset.id;
    }

    // イベントリスナーの登録
    function addEventListeners() {
        // 右側固定ボタン：並べ替え
        elements.btnSort.addEventListener('click', (e) => {
            e.preventDefault();
            toggleSortMode();
        });

        // 右クリックでコンテキストメニューを表示
        elements.sortableList.addEventListener('contextmenu', (e) => {
            // 右クリックされた要素が nametag 内かどうかを判定
            const nametag = e.target.closest('.nametag');
            if (nametag) {
                const li = nametag.closest('li');
                if (li) showContextMenu(e, li);
            }
        });

        // コンテキストメニューの編集ボタン
        elements.menuEdit.addEventListener('click', () => {
            const id = elements.contextMenu.dataset.currentId;
            window.location.href = "{{ route('bookmark.form') }}" + `?mode=edit&id=${encodeURIComponent(id)}`;
        });

        // コンテキストメニューの削除ボタン
        elements.menuDelete.addEventListener('click', (e) => {
            e.preventDefault();
            const id = elements.contextMenu.dataset.currentId;

            if (confirm('本当に削除しますか？')) {
                const form = document.getElementById('delete-form');
                form.action = `/startpage/${id}`;
                form.submit();
            }
        });

        // クリックやスクロールでコンテキストメニューを非表示
        document.addEventListener('click', () => elements.contextMenu.classList.add('hidden'));
        document.addEventListener('scroll', () => elements.contextMenu.classList.add('hidden'));
    }

    // 初期化
    document.addEventListener('DOMContentLoaded', addEventListeners);
</script>
@endpush