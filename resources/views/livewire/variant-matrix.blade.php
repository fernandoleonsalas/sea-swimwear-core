<div class="mt-6 p-2 bg-gray-200 mb-10">
    <!-- Cargador -->
    <div class="py-40 @if ($ContenidoCargado) hidden @endif">
        <div class="flex justify-center items-center wrap py-5">
            <div class="pr-2"><div class="cargadorPrincipal"></div></div>
            <p class="ml-3 text-lg text-gray-700">Cargando...</p>
        </div>
    </div>

    <!-- Contenido principal -->
    <div wire:init="inicializarContenido" @if (!$ContenidoCargado) hidden @endif>
        <!-- Mensajes Exito -->
        @if (session()->has('success'))
            <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                <span class="font-medium">Éxito!</span> {{ session('success') }}
            </div>
        @endif

        <!-- Mensajes Error -->
        @if (session()->has('error'))
            <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                <span class="font-medium">Error!</span> {{ session('error') }}
            </div>
        @endif

        <!-- Seccion de tabla de compras -->
        <div x-show="estadoBotonTabla" id="tabla-compras" x-transition class="w-full p-8 bg-white border border-gray-100 shadow-xl rounded-2xl">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                <div>
                    <h2 class="text-3xl font-light text-gray-900 tracking-tight">
                        Pedido: <span class="font-bold text-black italic">{{ $productName }}</span>
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-800">
                            Mínimo de pedido (MOQ): {{ $minPieces }} {{ $minPieces > 1 ? 'unidades' : 'unidad' }}
                        </span>
                    </p>
                </div>
                <button onclick="window.history.back()" class="group flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-black transition-colors cursor-pointer">
                    <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Volver al Catálogo
                </button>
            </div>

            <div wire:key="tabla-compra-{{ $select_key }}">
                <!-- Tabla de compra -->
                <div class="overflow-auto border border-gray-100 rounded-xl">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase tracking-widest bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-6 py-4 font-bold border-b">Variante / Talla</th>
                            @foreach($sizes as $size)
                                <th class="px-6 py-4 text-center border-b font-black">{{ $size }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($colorsEstampado as $valor)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900 bg-gray-50/30">{{ $valor }}</td>
                                @foreach($sizes as $size)
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $variantData = $effectiveMatrix[$valor][$size] ?? null;
                                            $variantExists = $variantData && !empty($variantData['variant_id']);
                                            $stockMax = $variantData['stock'] ?? 0;
                                        @endphp

                                        @if($variantExists)
                                            <div class="flex flex-col items-center gap-1">
                                                <input type="number" min="0" max="{{ $stockMax }}"
                                                    class="w-16 text-center border-gray-200 rounded-lg focus:ring-black focus:border-black transition-all bg-white"
                                                    wire:model.live.debounce.300ms="quantities.{{ $variantData['variant_id'] }}">
                                                <span class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">Stock: {{ $stockMax }}</span>
                                            </div>
                                        @else
                                            <span class="text-gray-300 text-xs italic">N/A</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
                <div class="mt-8 flex flex-col sm:flex-row items-center justify-between gap-6 border-t pt-8">
                    <div class="text-gray-500 flex items-center">
                        Total unidades: <span class="text-2xl font-black text-black ml-2">{{ $totalQuantity }}</span>
                    </div>
                    <button wire:click="addToCart"  wire:loading.attr="disabled" wire:target="addToCart"
                        class="w-full sm:w-auto px-10 py-4 bg-black text-white rounded-full font-bold tracking-widest text-xs hover:bg-gray-800 transition-all active:scale-95 disabled:opacity-30 cursor-pointer" @if(!$this->canAddToCart) disabled @endif>
                        AÑADIR AL CARRITO
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
