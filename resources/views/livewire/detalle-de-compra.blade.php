<div class="p-2"> 
    <!-- Cargador -->
    <div class="py-40 @if ($ContenidoCargado) hidden @endif">
        <div class="flex justify-center items-center wrap py-20">
            <div class="pr-2"><div class="cargadorPrincipal"></div></div>
            <p class="ml-3 text-lg text-gray-700">Cargando...</p>
        </div>
    </div>

    <!-- Contenido principal -->
    <div wire:init="inicializarContenido" @if (!$ContenidoCargado) hidden @endif>
        @if ($tasaDolar != false)
            <div class="flex flex-col mt-8 p-2 sm:p-4 bg-gray-200 font-sans antialiased rounded-xl shadow-2xl min-h-dvh">
                <!-- Titulo y boton de regresar atras -->
                <div class="flex items-center mb-6">
                    <!-- Boton de regresar a la página anterior -->
                    <div class="px-4" x-data="{volverACatalogo() { window.history.back();}}">
                        <button @click="volverACatalogo()" title="Regresar" class="inline-flex items-center justify-center p-2 rounded-full text-white bg-black transition duration-300 ease-in-out shadow-lg hover:bg-white hover:text-black focus:outline-none focus:ring-4 focus:ring-gray-500 cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-10">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 9-3 3m0 0 3 3m-3-3h7.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </button>
                    </div>
                    <!-- Titulo -->
                    <h1 class="pl-3 text-2xl font-extrabold text-gray-900 dark:text-white md:text-3xl lg:text-3xl">
                        <span class="text-transparent bg-clip-text bg-gradient-to-r to-gray-700 from-black"> Carrito de Pedido</span>
                        🛒 
                    </h1>
                </div>

                {{-- Alerta de Mensajes (Error o Stock-Issue) --}}
                @if (session()->has('error'))
                    <div class="p-4 mb-4 text-red-700 bg-red-100 rounded-sm border-red-500 border-l-4 text-sm"  role="alert">
                        <p class="font-bold">Atención!</p>
                        <p>{{ session('error') }}</p>
                    </div>
                @endif
                

                <div class="grid grid-cols-1 p-4 bg-gray-100 overflow-x-auto">
                    {{-- Campo de Búsqueda, Controles de Filtro y Paginación --}}
                    <div class="mb-4 flex flex-row flex-wrap justify-between items-center space-y-4 md:space-y-0 col-span-1">
                        <input 
                            wire:model.live.debounce.300ms="search" {{-- Añadido debounce --}}
                            type="text" 
                            placeholder="Buscar por producto o variante..."
                            class="w-full md:w-1/3 px-4 py-2 border border-gray-300 rounded-md focus:ring-[#daadaf] focus:border-[#e8a3a3] shadow-sm"
                        >
                    
                        <div class="flex items-center ml-4">
                            {{-- Selector de Elementos por Página --}}
                            <label for="perPage" class="text-sm text-gray-700 mr-2"><b>Mostrar:</b></label>
                            <select 
                                wire:model.live="perPage" 
                                id="perPage"
                                class="pr-8 py-1 border border-gray-300 rounded-md text-sm focus:ring-[#daadaf] focus:border-[#e8a3a3]"
                            >
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="25">25</option>
                                <option value="30">30</option>
                                <option value="40">40</option>
                                <option value="50">50</option>
                            </select>
                            <span class="ml-2 text-sm text-gray-700"><b>productos</b></span>
                        </div>
                    </div>

                    @if (!empty($agrupado))
                        <table class="w-full min-w-full divide-y divide-gray-200 shadow-lg col-span-1">
                            <!-- Encabezado de la tabla -->
                            <thead class="bg-black text-white">
                                <tr>
                                    <!-- <th class="px-6 py-3 text-left text-xs font-medium border uppercase tracking-wider">Nombre Producto Principal</th> -->
                                    <th class="px-6 py-3 text-left text-xs font-medium border uppercase tracking-wider">Producto</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium border uppercase tracking-wider">cant. Total</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium border uppercase tracking-wider">Tarifa Aplicada</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium border uppercase tracking-wider">P.U. Aplicado</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium border uppercase tracking-wider">Subtotal Producto</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium border uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <!-- Cuerpo de la tabla -->
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($agrupado as $producto)
                                    @php $rowCount = count($producto['variantes']); @endphp

                                    @foreach ($producto['variantes'] as $index => $variante)
                                        @php
                                            $isStockIssue = array_key_exists($variante['id_variante'], $stockValidationErrors);
                                            $issueDetails = $isStockIssue ? $stockValidationErrors[$variante['id_variante']] : null;
                                        @endphp

                                        <tr wire:key="variant-{{ $variante['id_variante'] }}">
                                            {{-- Columna Variante --}}
                                            <td class="w-1/6 px-6 py-2 text-sm text-gray-700 border border-gray-300 ">
                                                <div class="flex flex-col justify-center items-center">
                                                    <img class="w-1/2 aspect-square object-contain h-full" src="{{ asset('storage').'/'.$variante["foto"]}}" alt="Imagen de {{ $variante["name_variante"] }}" title="{{ $variante["name_variante"] }}">
                                                    <b class="w-2/2 shrink-0  text-center">{{ $variante['name_variante'] }} ({{ $variante['qty'] }} uds.)</b>
                                                </div>

                                                {{-- Mostrar la alerta de stock faltante justo debajo del nombre --}}
                                                @if ($isStockIssue)
                                                    <p class="text-xs font-bold text-red-600 mt-1">
                                                        ⚠️ ¡Stock insuficiente! Solicitado: {{ $issueDetails['required'] }}. Disponible: {{ $issueDetails['available'] }}.
                                                    </p>
                                                @endif
                                            </td>

                                            {{-- TARIFA, P.U., Cantidad Total y Subtotal (solo en la primera fila) --}}
                                            @if ($index === 0)
                                                <td class="text-center px-6 py-4 whitespace-nowrap text-sm font-bold border border-gray-300" rowspan="{{ $rowCount }}">
                                                    {{ $producto['Cantidad Total'] }}
                                                </td>

                                                <td class="px-6 py-4 whitespace-nowrap text-xs text-center font-semibold border border-gray-300" rowspan="{{ $rowCount }}">
                                                    <span class="@if($producto['Tarifa Aplicada'] == 'Mayorista') bg-blue-100 text-blue-800 @else bg-yellow-100 text-yellow-800 @endif py-1 px-3 inline-flex text-xs leading-5 rounded-full">
                                                        {{ $producto['Tarifa Aplicada'] }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium border border-gray-300" rowspan="{{ $rowCount }}">
                                                    ${{ $producto['P.U. Aplicado'] }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold bg-green-50/50 border border-gray-300" rowspan="{{ $rowCount }}">
                                                    ${{ $producto['Subtotal'] }}
                                                </td>
                                            @endif

                                            {{-- COLUMNA DE ACCIONES (repetida en cada fila de variante) --}}
                                            <td class="px-6 py-4 whitespace-nowrap text-center border border-gray-300">
                                                <div class="flex items-center justify-center space-x-2">
                                                    {{-- Campo de Edición de Cantidad (Vinculado a Livewire) --}}
                                                    <input
                                                        type="number"
                                                        min="1"
                                                        class="w-16 text-sm text-center border-gray-300 rounded-md focus:ring-[#daadaf] focus:border-[#e8a3a3]"
                                                        wire:model.live.debounce.500ms="cartData.{{ $variante['id_variante'] }}.qty"
                                                    >
                                                    
                                                    {{-- Botón Descartar --}}
                                                    <button
                                                        wire:click="discardVariant({{ $variante['id_variante'] }})"
                                                        class="text-red-600 hover:text-red-900 focus:outline-none p-1 cursor-pointer hover:scale-115  duration-100 transition-all"
                                                        title="Descartar producto"
                                                    >
                                                        🗑️
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    {{-- Fila separadora entre productos --}}
                                    <tr class="h-4 bg-gray-100/50"><td colspan="7"></td></tr>
                                @endforeach
                            </tbody>
                            
                            {{-- Sección de Totales (Corregido colspan) --}}
                            <tfoot>
                                <tr>
                                    <td colspan="6">
                                        {{-- Controles de Paginación y Conteo --}}
                                        @if ($totalItems > $perPage)
                                            <div class="flex flex-row justify-between items-center px-4 py-3 bg-white border-t border-gray-200 sm:px-6">
                                                
                                                {{-- Conteo de Elementos --}}
                                                <div class="mb-2 md:mb-0 text-sm text-gray-700">
                                                    Mostrando 
                                                    <span class="font-medium">{{ ($currentPage - 1) * $perPage + 1 }}</span> a
                                                    <span class="font-medium">{{ min($currentPage * $perPage, $totalItems) }}</span> de
                                                    <span class="font-medium">{{ $totalItems }}</span> productos.
                                                </div>

                                                {{-- Controles de Paginación --}}
                                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                                    {{-- Botón Anterior --}}
                                                    <button 
                                                        wire:click="gotoPage({{ $currentPage - 1 }})" 
                                                        @if ($currentPage <= 1) disabled @endif
                                                        class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 cursor-pointer">
                                                        Anterior
                                                    </button>

                                                    {{-- Iteración de páginas (Simplificada) --}}
                                                    @for ($page = 1; $page <= $totalPages; $page++)
                                                        <button 
                                                            wire:click="gotoPage({{ $page }})"
                                                            class="relative inline-flex items-center px-4 py-2 border text-sm font-medium cursor-pointer 
                                                                @if ($page == $currentPage) 
                                                                    bg-black text-white border-[#daadaf]
                                                                @else 
                                                                    bg-white border-gray-300 text-gray-700 hover:bg-gray-50 
                                                                @endif">
                                                            {{ $page }}
                                                        </button>
                                                    @endfor
                                                    
                                                    {{-- Botón Siguiente --}}
                                                    <button 
                                                        wire:click="gotoPage({{ $currentPage + 1 }})" 
                                                        @if ($currentPage >= $totalPages) disabled @endif
                                                        class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 cursor-pointer">
                                                        Siguiente
                                                    </button>
                                                </nav>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                        @if ($search == "")
                            {{-- BLOQUE DE RESUMEN (Ocupa la parte derecha, se mantiene fijo) --}}
                            <div class="flex justify-start md:justify-end">
                                <div class="min-w-xs lg:w-1/4 bg-white p-6 shadow-xl rounded-lg border border-gray-200 overflow-x-auto">
                                    <h3 class="text-lg font-bold mb-4 border-b pb-2">Resumen del Pedido</h3>
                                    
                                    <div class="space-y-2 text-sm mb-1">
                                        <div class="flex justify-between">
                                            <span>Subtotal Productos (USD):</span>
                                            <span class="font-medium">${{ number_format($subtotalProductos, 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span>Ref (Bs):</span>
                                            <span class="font-medium">{{ number_format($subtotalProductos * $tasaDolar, 2) }}</span>
                                        </div>
                                    </div>
                                    {{-- Botones de Acción --}}
                                    <div class="space-y-3">
                                        {{-- BOTÓN DE PAGO --}}
                                        <div class="mt-4 w-full">
                                            <button
                                                wire:click="checkStockAndProceed"
                                                class="w-full px-8 py-3 bg-[#ef9696] text-white font-bold rounded-lg shadow-lg hover:bg-[#ed8282] transition duration-150 cursor-pointer"
                                                wire:loading.attr="disabled"
                                            >
                                                Pagar {{ number_format($subtotalProductos * $tasaDolar, 2) }} Bs.
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        {{-- Mensaje de estado --}}
                        <div class="text-gray-500 text-center py-8">
                            @if($this->search)
                                {{-- Caso: No hay resultados en la búsqueda --}}
                                <p>No se encontraron resultados para: <span class="font-bold">"{{ $this->search }}"</span></p>
                            @else
                                {{-- Caso: El carrito está vacío --}}
                                <p class="mb-4">Aún no has añadido productos a tu carrito, visita nuestro:</p>
                                
                                <div class="flex flex-col sm:flex-row justify-center items-center gap-4 mt-4">
                                    {{-- Enlace Catálogo Mayorista --}}
                                    <a href="{{ route('catalogoMay') }}" class="group flex items-center px-4 py-2 bg-black text-white rounded-lg hover:bg-white hover:text-black transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="w-4 h-4 mr-2 fill-current group-hover:scale-110 group-hover:fill-black transition-transform">
                                            <path d="M208 144L208 192L144 192C117.5 192 96 213.5 96 240L96 448C96 501 139 544 192 544L448 544C501 544 544 501 544 448L544 240C544 213.5 522.5 192 496 192L432 192L432 144C432 82.1 381.9 32 320 32C258.1 32 208 82.1 208 144z"/>
                                        </svg>
                                        CATÁLOGO AL MAYOR 
                                    </a>

                                    {{-- Enlace Catálogo Minorista --}}
                                    <a href="{{ route('catalogoMin') }}" class="group flex items-center px-4 py-2 bg-black text-white rounded-lg hover:bg-white hover:text-black transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="w-4 h-4 mr-2 fill-current group-hover:scale-110 group-hover:fill-black transition-transform">
                                            <path d="M208 144L208 192L144 192C117.5 192 96 213.5 96 240L96 448C96 501 139 544 192 544L448 544C501 544 544 501 544 448L544 240C544 213.5 522.5 192 496 192L432 192L432 144C432 82.1 381.9 32 320 32C258.1 32 208 82.1 208 144z"/>
                                        </svg>
                                        CATÁLOGO AL DETAL
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @else
            <!-- MENSAJES DE ERROR GENERAL -->
            @if (session()->has('errorTasa'))
                <div class="p-4 m-8 text-red-700 bg-red-100 rounded-sm border-red-500 border-l-4 text-sm"  role="alert">
                    <p class="font-bold mb-1">Atención!</p>
                    <p>{{ session('errorTasa') }}</p>
                </div>
            @endif
        @endif
    </div>
</div>
