@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-black mb-4']) }}>
    {{ $value ?? $slot }}
</label>
