<!-- resources/views/components/Input.php -->
<div>
    <label {{ $attributes->merge(['class' => 'block font-medium text-sm text-gray-700']) }}>
        {{ $label ?? $slot }}
    </label>

    <input {{ $attributes->merge(['class' => 'form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm']) }}>
</div>
