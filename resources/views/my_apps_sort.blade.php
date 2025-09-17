@extends('layouts.app')

@section('content')

    <div class="mx-auto max-w-7xl py-8">
        <form id="sort-form" action="{{ route('myapps.sort') }}" method="POST">
        @csrf
        <input type="hidden" name="order" id="order-input">
        <ul id="sortable-list" class="">
            @foreach ($data as $app)
                <li data-id="{{ $app->id }}" class="w-full my-2 py-2 px-5 rounded-lg bg-gray-800 shadow border-1 border-gray-700 hover:border-white hover:shadow-md transition-shadow">
                    <div class="flex items-center space-x-3">
                        <span class="inline-flex flex-shrink-0 items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">
                            No: {{ $app->sort_order }}
                        </span>
                        <h3 class="truncate text-lg font-medium text-gray-100">
                            {{ $app->name }}
                        </h3>
                        <p class="truncate text-sm text-gray-500">
                            {{ $app->url }}
                        </p>
                    </div>
                </li>
            @endforeach
        </ul>

        <button type="submit" id="save-order" class="inline-flex w-full cursor-pointer justify-center items-center px-3 py-2 mt-5 bg-gray-800 rounded hover:bg-gray-700">
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
        </button>
        </form>
    </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    const sortable = new Sortable(document.getElementById('sortable-list'), {
        animation: 150
    });

    document.getElementById('sort-form').addEventListener('submit', function (e) {
        const order = [];
        document.querySelectorAll('#sortable-list li').forEach((el, index) => {
            order.push({
                id: el.dataset.id,
                sort_order: index + 1
            });
        });
        // JSON文字列としてhiddenにセット
        document.getElementById('order-input').value = JSON.stringify(order);
    });
</script>
@endpush