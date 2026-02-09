<button {{ $attributes->merge(['type' => 'submit', 'class' => 'cursor-pointer inline-flex items-center px-4 py-2 bg-black  border border-transparent rounded-md font-semibold text-xs text-white  uppercase tracking-widest hover:bg-black/85 focus:bg-black/90 active:bg-black/90 focus:outline-none focus:ring-2 focus:ring-black/90 focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
