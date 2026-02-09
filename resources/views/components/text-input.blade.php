@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-[#efb7b7] focus:ring-[#efb7b7] rounded-md shadow-sm']) }}>
