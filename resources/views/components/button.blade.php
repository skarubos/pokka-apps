@props([
    'type' => 'button',
    'href' => null
])

@if ($href)
    <a href="{{ $href }}"
       {{ $attributes->merge([
           'class' => 'inline-block px-6 py-2 bg-sky-600/90 hover:bg-sky-600/70 text-white font-semibold rounded shadow'
       ]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}"
        {{ $attributes->merge([
            'class' => 'px-4 py-2 bg-sky-600/90 hover:bg-sky-600/70 text-white font-semibold rounded shadow'
        ]) }}>
        {{ $slot }}
    </button>
@endif