@props(['label', 'name', 'value' => null])

<label for="{{ $name }}"
    {{ $attributes->merge(['class' => 'flex items-center text-sm text-base-color']) }}>
    <input type="hidden" name="{{ $name }}" value="0">
    <input type="checkbox" id="{{ $name }}" name="{{ $name }}" value="{{ $value }}"
        {{ $attributes }} class="h-4 w-4 text-brand-primary focus:ring-brand-primary border-gray-300 rounded mr-1">
    {{ $label }}
</label>

@error($name)
    <span class="text-red-500">{{ $message }}</span>
@enderror
