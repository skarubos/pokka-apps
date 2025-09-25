<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache">
    <meta name="referrer" content="no-referrer">
    <title>Start Page</title>
    @vite('resources/css/app.css') <!-- TailwindやCSSビルド用 -->
</head>
<body class="container mx-auto p-4 lg:p-6"
        style="
        background: radial-gradient(circle, rgba(252,255,216,1) 0%, rgba(183,164,115,1) 100%);
        background-attachment: fixed;
        min-height: 100vh;
    ">

    <!-- フラッシュメッセージ -->
    @if (session('success'))
        <div id="flash-success"
            class="relative flex items-center justify-between
                    rounded-xl bg-sky-600 text-white px-5 py-2 mb-2
                    transition-opacity duration-300">
            <span>{{ session('success') }}</span>
            <button type="button"
                    onclick="document.getElementById('flash-success').classList.add('opacity-0'); setTimeout(()=>document.getElementById('flash-success').remove(),300)"
                    class="cursor-pointer ml-4 font-bold text-white/80 hover:text-white">
                ✕
            </button>
        </div>
    @endif
    @if (session('error'))
        <div id="flash-error"
            class="relative flex items-center justify-between
                    rounded-xl bg-red-800 text-white px-5 py-2
                    transition-opacity duration-300">
            <span>{{ session('error') }}</span>
            <button type="button"
                    onclick="document.getElementById('flash-error').classList.add('opacity-0'); setTimeout(()=>document.getElementById('flash-error').remove(),300)"
                    class="cursor-pointer ml-4 font-bold text-white/80 hover:text-white">
                ✕
            </button>
        </div>
    @endif


    <div id="main-contents" class="mx-auto max-w-7xl pt-8 pb-16">
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
                            <img src="{{ asset('images/' . $bookmark->img_name) }}"
                                alt="icon"
                                class="w-22 h-22 lg:w-52 lg:h-52
                                        rounded-full object-cover
                                        bg-white shadow-xl/30
                                        hover:outline hover:outline-5 hover:outline-offset-6 hover:outline-sky-500" />
                            <div id="nametag" class="mt-3 px-2 lg:px-6 bg-black/50 rounded-full shadow-lg/30">
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
            <a href="#"
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

    <!-- 入力フォーム -->
    <div id="input-form"
        class="hidden justify-center items-center
            my-10 px-18 py-10
            bg-black/50 backdrop-blur-sm rounded-xl
            text-white">
        <h2 id="form-title" class="text-2xl font-bold py-3 mb-6 text-center"></h2>

        <form  id="bookmark-form" method="POST" action=""
            enctype="multipart/form-data"
            class="space-y-5">
            <input type="hidden" name="id" id="form-id">
            @csrf
            <div class="grid grid-cols-3 gap-6">
                <!-- 名前 -->
                <div class="col-span-2">
                    <label class="block text-lg font-medium text-white">名前</label>
                    <input type="text" name="name"  id="form-name"
                            class="block w-full rounded-md shadow-sm
                                bg-white/30 focus:bg-white
                                mt-1 py-1 px-5 text-black text-lg
                                focus:border-sky-500" required>
                </div>

                <!-- 表示順 -->
                <div>
                    <label class="block text-lg font-medium text-white">表示順</label>
                    <input type="number" name="priority" id="form-priority"
                            min="1" max="{{ count($bookmarks) + 1 }}"
                            class="block w-full rounded-md shadow-sm
                                bg-white/30 focus:bg-white
                                mt-1 py-1 px-5 text-black text-lg
                                focus:border-sky-500" required>
                </div>
            </div>

            <!-- URL -->
            <div>
                <label class="block text-lg font-medium text-white">URL</label>
                <input type="url" name="url" id="form-url"
                        class="block w-full rounded-md shadow-sm
                            bg-white/30 focus:bg-white
                            mt-1 py-1 px-5 text-black text-lg
                            focus:border-sky-500" required>
            </div>

            <!-- file upload -->
            <div class="w-1/2">
                <label class="block text-lg font-medium text-white">画像ファイル</label>
                <input type="file" name="img_name" id="form-img-name" accept="image/*"
                        class="mt-1 block w-full text-sm text-white
                            file:mr-4 file:py-2 file:px-6
                            file:rounded-md file:border-0
                            file:text-sm file:font-semibold
                            file:bg-black/50 file:text-white/80
                            file:cursor-pointer
                            hover:file:bg-black/70 hover:text-white">
            </div>

            <!-- buttons -->
            <div class="flex justify-end space-x-5 pt-4 px-3s">
                <a id="cancel-btn" href="#"
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

    <!-- 編集・削除用メニュー（右クリックで表示） -->
    <div id="context-menu"
        class="hidden absolute z-50 w-30 py-3
                bg-gray-800 rounded-xl shadow-lg
                text-lg text-white text-center">
        <ul>
            <li id="menu-edit" class="py-2 hover:bg-gray-600 cursor-pointer">編集</li>
            <li id="menu-delete" class="py-2 hover:bg-gray-600 cursor-pointer">削除</li>
        </ul>
    </div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    // DOM要素の取得
    const elements = {
        mainContents: document.getElementById('main-contents'),
        inputForm: document.getElementById('input-form'),
        formTitle: document.getElementById('form-title'),
        formId: document.getElementById('form-id'),
        formName: document.getElementById('form-name'),
        formUrl: document.getElementById('form-url'),
        formPriority: document.getElementById('form-priority'),
        btnNew: document.getElementById('create-new'),
        btnSort: document.getElementById('toggle-sort'),
        iconSort: document.getElementById('icon-sort'),
        iconClose: document.getElementById('icon-close'),
        sortMenu: document.getElementById('sort-menu'),
        contextMenu: document.getElementById('context-menu'),
        cancelBtn: document.getElementById('cancel-btn'),
        sortableList: document.getElementById('sortable-list'),
        orderInput: document.getElementById('order-input'),
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

    // 新規作成フォームを開く
    function openCreateForm() {
        elements.formTitle.textContent = "新規登録";
        elements.formId.value = "";
        elements.formName.value = "";
        elements.formUrl.value = "";
        elements.formPriority.value = {{ count($bookmarks) + 1 }};
        toggleVisibility(false);
    }

    // 編集フォームを開く
    function openEditForm(li) {
        elements.formTitle.textContent = "編集";
        elements.formId.value = li.dataset.id;
        elements.formName.value = li.dataset.name;
        elements.formUrl.value = li.dataset.url;
        elements.formPriority.value = li.dataset.priority;
        toggleVisibility(false);
    }

    // メインコンテンツとフォームの表示切り替え
    function toggleVisibility(showMain = true) {
        elements.mainContents.classList.toggle('hidden', !showMain);
        elements.inputForm.classList.toggle('hidden', showMain);
    }

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
        // 右側固定ボタン：新規作成
        elements.btnNew.addEventListener('click', (e) => {
            e.preventDefault();
            openCreateForm();
        });

        // 右側固定ボタン：並べ替え
        elements.btnSort.addEventListener('click', (e) => {
            e.preventDefault();
            toggleSortMode();
        });

        // 入力フォーム：キャンセルボタン
        elements.cancelBtn.addEventListener('click', () => {
            toggleVisibility(true);
        });

        // 右クリックでコンテキストメニューを表示
        elements.sortableList.addEventListener('contextmenu', (e) => {
            const li = e.target.closest('li');
            if (li) showContextMenu(e, li);
        });

        // コンテキストメニューの編集ボタン
        elements.menuEdit.addEventListener('click', () => {
            const li = document.querySelector(`#sortable-list li[data-id="${elements.contextMenu.dataset.currentId}"]`);
            if (li) openEditForm(li);
            elements.contextMenu.classList.add('hidden');
        });

        // コンテキストメニューの削除ボタン
        elements.menuDelete.addEventListener('click', () => {
            alert(`ID:${elements.contextMenu.dataset.currentId} を削除します`);
            elements.contextMenu.classList.add('hidden');
        });

        // クリックやスクロールでコンテキストメニューを非表示
        document.addEventListener('click', () => elements.contextMenu.classList.add('hidden'));
        document.addEventListener('scroll', () => elements.contextMenu.classList.add('hidden'));
    }

    // 初期化
    document.addEventListener('DOMContentLoaded', addEventListeners);
</script>

</body>