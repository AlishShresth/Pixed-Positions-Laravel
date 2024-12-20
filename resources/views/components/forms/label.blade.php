@props(['name', 'label'])

<div class="inline-flex items-center gap-x-2">
    <span class="w-2 h-2 bg-white inline-block"></span>
    <label for="{{ $name }}" class="font-bold">{{ $label }}</label>
</div>
