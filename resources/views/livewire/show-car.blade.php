<div>
    <!-- Mostrar boton o icono del carrito de compra -->
    <div title="Ver Carrito de Compras">
        <a href="/carrito" class="fixed bottom-4 right-4 bg-[#daadaf] hover:bg-[#e8a3a3] hover:scale-105 border border-gray-200 text-black p-3 rounded-full shadow-xl z-50 flex items-center space-x-2 transition duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 16" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.023.832l.236 1.768a2.536 2.536 0 0 0 2.518 2.064h9.248c.904 0 1.713-.67 1.83-1.603l.564-3.69c.071-.466-.277-.9-.752-.9H4.498m0 0a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-3.906m0 0-2.883 2.883m2.883-2.883L13.25 5m2.883 2.883 2.883-2.883m-2.883 2.883L16.42 10.156" />
            </svg>
            <span class="font-bold text-2xl">{{ $cartCount ?? 0 }}</span>
        </a>
    </div>
</div>
