@props([
	'myAge',
])

<div {{ $attributes }}>
	<h1>{{ $name ?? '無し' }}</h1>
	<h1>{{ $myAge ?? '無し' }}</h1>
</div>