@props(['active'])

@php
$classes = ($active ?? false)
            ? 'px-4 inline-flex items-center px-1 pt-1 border-b-4 border-[#efb7b7] text-sm font-medium leading-5 text-white focus:outline-none focus:border-[#efb7b7] transition duration-150 ease-in-out'
            : 'px-4 inline-flex items-center px-1 pt-1 border-b-3 border-transparent text-sm font-medium leading-5 text-white/50 hover:text-white hover:border-gray-200 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
