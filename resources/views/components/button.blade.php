{{-- 
<x-button> コンポーネント使用方法

■ 基本
<x-button>ラベル</x-button>
→ <button>タグとして出力（type="button"）

■ type属性を指定（フォーム送信など）
<x-button type="submit">送信</x-button>

■ リンクとして使用
<x-button href="https://example.com">リンク</x-button>
→ <a>タグとして出力

■ クラスや属性を追加
<x-button href="/dashboard" class="bg-green-600 hover:bg-green-500">
    ダッシュボード
</x-button>

※ href が指定されると <a> タグ、それ以外は <button> タグになります
※ 呼び出し側の class/属性はデフォルトとマージされます
--}}

@props([
    'type' => 'button',
    'href' => null
])

@if ($href)
    <a href="{{ $href }}"
       {{ $attributes->merge([
           'class' => 'inline-block px-6 py-2 my-2 bg-sky-600/90 hover:bg-sky-600/70 text-white font-semibold rounded shadow'
       ]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}"
        {{ $attributes->merge([
            'class' => 'inline-block px-6 py-2 my-2 bg-sky-600/90 hover:bg-sky-600/70 text-white font-semibold rounded shadow'
        ]) }}>
        {{ $slot }}
    </button>
@endif