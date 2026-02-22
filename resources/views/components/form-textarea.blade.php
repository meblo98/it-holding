@props(['name', 'label', 'value' => null, 'placeholder' => '', 'rows' => 4])

<div>
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">
        {{ $label }}
    </label>
    <div class="mt-1">
        <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows }}" placeholder="{{ $placeholder }}"
            {{ $attributes->merge(['class' => 'block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors duration-200']) }}>{{ old($name, $value) }}</textarea>
    </div>
    @error($name)
        <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
    @enderror
</div>
