<div class="p-2">
    <div class="flex flex-col mt-8 p-2 sm:p-4 bg-white font-sans antialiased rounded-xl shadow-2xl min-h-dvh">
        <!-- Tabla de gestion de producto y modal -->
        <div class="grid grid-cols-1 p-4 bg-white overflow-x-auto" x-data="{modalEditarProd: false}">
            {{-- Campo de Búsqueda, Controles de Filtro y Paginación --}}
            <div class="mb-4 flex flex-row flex-wrap justify-between items-center space-y-4 md:space-y-0 col-span-1">
                <input 
                    wire:model.live.debounce.600ms="campoBusqueda" 
                    type="text" 
                    placeholder="Buscar por producto o variante..."
                    class="w-full md:w-1/3 px-4 py-2 border border-gray-300 rounded-md focus:ring-[#daadaf] focus:border-[#e8a3a3] shadow-sm"
                >
            
                <div class="flex items-center ml-4">
                    {{-- Selector de Elementos por Página --}}
                    <label for="productoMostrar" class="text-sm text-gray-700 mr-2"><b>Mostrar:</b></label>
                    <select 
                        wire:model.live="productoMostrar" 
                        id="productoMostrar"
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

            {{-- Contenido principal --}}
            @if (!empty($listaProductos))
                <table class="w-full min-w-full divide-y divide-gray-200 shadow-lg col-span-1">
                    <!-- Encabezado de la tabla -->
                    <thead class="bg-black text-white">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-medium border uppercase tracking-wider">Presentación</th>
                            <th class="px-6 py-3 text-center text-xs font-medium border uppercase tracking-wider">Variante (SKU)</th>
                            <th class="px-6 py-3 text-center text-xs font-medium border uppercase tracking-wider">stock</th>
                            <th class="px-6 py-3 text-center text-xs font-medium border uppercase tracking-wider">Estado</th>
                        </tr>
                    </thead>
                    <!-- Cuerpo de la tabla -->
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($listaProductos as $key => $producto)
                            <tr class="bg-[#e8a3a39b]">
                                <td colspan="4" class="text-sm p-2 px-4 border border-gray-300 text-gray-800">
                                    <b>Producto:</b> {{ $producto["product_name"] }}
                                </td>
                            </tr>

                            @foreach ($producto["image_groups"] as $variante)
                                @php
                                    // Extraer datos del variante padre y la imagen principal
                                    [$p_id,$p_nombre,$p_img] = [$variante['product_id'],$variante['product_name'],$variante['image'] ?? 'placeholder.jpg'];
                                    // Extraer el grupo de sub-variantes por variante, Y el numero total
                                    $variantesDelGrupo = $variante['variants_by_image'];
                                    $rowCount = count($variantesDelGrupo);
                                @endphp

                                @foreach ($variantesDelGrupo as $subVariantKey => $subVariantes)
                                    @php
                                        // La variable $subVariantes es un grupo de variantes que comparten imagen (producto con variantes por imagen)
                                        // Vamos a tomar la primera variante del grupo ($subVariantes[0]) para mostrar sus datos en esta fila.
                                        // Si $subVariantes es un array de variantes, y queremos mostrar los detalles de CADA variante por separado:
                                        $variante = $subVariantes; // Tomamos la primera para los detalles, asumiremos que todas en este grupo son similares para esta columna
                                    @endphp

                                    <tr class="hover:bg-gray-200 {{ $key % 2 === 0 ? 'bg-white' : 'bg-gray-100' }}">
                                        {{-- Columna variante Principal imagen (Solo en la primera fila del grupo) --}}
                                        @if ($subVariantKey === 0)
                                            <td class="py-2 text-sm text-gray-700 border border-gray-300 w-20" rowspan="{{ $rowCount }}">
                                                <div class="relative group"> 
                                                    <img class="aspect-square object-contain" src="{{ asset('storage').'/'.$p_img['main_image_url']}}" alt="Imagen de {{ $p_nombre }}" title="Imagen de {{ $p_nombre }}">
                                                    
                                                    <button wire:click="modaleditProduct({{ $p_id }},{{ $p_img['id'] }},'{{ $p_img['main_image_url']}}')" 
                                                    wire:target="modaleditProduct({{ $p_id }},{{ $p_img['id'] }},'{{ $p_img['main_image_url']}}')" 
                                                    wire:loading.attr="disabled"
                                                    wire:loading.class="opacity-50 cursor-not-allowed"
                                                    data-modal-target="editar-modal-p" 
                                                    class="mx-3 absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 cursor-pointer">
                                                        <span class="text-white text-lg font-bold" wire:loading.remove wire:target="modaleditProduct">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
                                                        </span>
                                                        <span wire:loading wire:target="modaleditProduct">
                                                            Cargando...
                                                        </span>
                                                    </button>
                                                </div>
                                            </td>
                                        @endif
                                        
                                        {{-- Columna 3: ATRIBUTOS DE LA SUB-VARIANTE --}}
                                        <td class="px-6 py-2 text-sm text-gray-700 border border-gray-300 min-w-2xs">
                                            @php
                                                // 1. Convertir el array de atributos a un array asociativo (Nombre => Valor) para fácil acceso
                                                $atributosMapa = collect($variante["attribute_values"])
                                                    ->keyBy(fn($attr) => $attr['attribute']['name'])
                                                    ->map(fn($attr) => $attr['value']);
                                            @endphp
                                            <!-- Mostrar informacion general -->
                                            {{-- 3. Mostrar el COLOR y el ESTAMPADO (SOLO EN LA PRIMERA FILA) --}}
                                            {{-- La variable $subVariantKey nos dice si es la primera fila del grupo (índice 0) --}}
                                            @if ($subVariantKey === 0)
                                                <p class="text-[1rem} text-gray-700"><b>Inf. General</b></p>
                                                <p class="text-xs text-gray-600">
                                                    {{-- Muestra el Color --}}
                                                    @if (isset($atributosMapa['Color']))
                                                        <span class="font-semibold">Color:</span> <b>{{ $atributosMapa['Color'] }}</b>
                                                    @endif
                                                    {{-- Muestra el Estampado --}}
                                                    @if (isset($atributosMapa['Color']))
                                                        <span class="ml-2 font-semibold">Estampado:</span> <b>{{ $atributosMapa['Estampado'] }}</b>
                                                    @endif
                                                </p>
                                                <hr class="mb-1">

                                            @endif
                                            {{-- 3. Mostrar la TALLA (La cual siempre debe repetirse por fila) --}}
                                            @if (isset($atributosMapa['Talla']))
                                                <p class="text-xs text-gray-600">
                                                    <span class="font-semibold">Talla:</span> 
                                                    <b>{{ $atributosMapa['Talla'] }}</b>
                                                </p>
                                            @endif
                                            {{-- 2. Mostrar el SKU de la variante --}}
                                            <!-- <p class="text-xs text-gray-500 mt-2"><b>SKU:</b> {{ $variante['full_sku'] ?? 'N/A' }}</p> -->
                                        </td>

                                        {{-- Columna 3: STOCK (INPUT Y BOTÓN DE GUARDAR CONDICIONAL) 🚀 --}}
                                        <td class="px-6 py-2 text-sm text-gray-700 border border-gray-300 w-3xs">
                                            <div class="flex items-center space-x-2">
                                                {{-- Input de Stock con two-way binding --}}
                                                <input 
                                                    type="number" 
                                                    min="0"
                                                    wire:model.live.debounce.500ms="editingStock.{{ $subVariantes["id"] }}"
                                                    class="w-20 p-1 border border-gray-300 rounded text-sm text-right focus:ring-indigo-500 focus:border-indigo-500"
                                                >

                                                {{-- Botón de Guardar, visible si el stock ha cambiado --}}
                                                @if (isset($editingStock[$subVariantes["id"]]) && isset($originalStock[$subVariantes["id"]]) && $editingStock[$subVariantes["id"]] != $originalStock[$subVariantes["id"]])
                                                    <button 
                                                        wire:click="actualizarStock({{ $subVariantes["id"] }})"
                                                        wire:loading.attr="disabled"
                                                        wire:target="actualizarStock({{ $subVariantes["id"] }})"
                                                        class="bg-green-500 hover:bg-green-600 text-white font-bold py-1 px-2 rounded text-xs transition duration-150 ease-in-out whitespace-nowrap cursor-pointer"
                                                    >
                                                        <span wire:loading.remove wire:target="actualizarStock({{ $subVariantes["id"] }})">Guardar</span>
                                                        <span wire:loading wire:target="actualizarStock({{ $subVariantes["id"] }})">...</span>
                                                    </button>
                                                @endif
                                            </div>
                                            {{-- Muestra errores o mensajes de éxito específicos para esta variante --}}
                                            @error('editingStock.' . $subVariantes["id"]) 
                                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p> 
                                            @enderror
                                            <!-- Mensjae de error -->
                                            @if (session()->has('error_stock_db_' . $subVariantes["id"]))
                                                <p class="text-red-500 text-xs mt-1">{{ session('error_stock_db_' . $subVariantes["id"]) }}</p>
                                            @endif
                                            <!-- Mensaje de exito -->
                                            @if (session()->has('exito_stock_db_' . $subVariantes["id"]))
                                                <p class="text-green-500 text-xs mt-1">{{ session('exito_stock_db_' . $subVariantes["id"]) }}</p>
                                            @endif
                                        </td>

                                        {{-- Columna 4: ESTADO (Burbuja de color + Menú de acciones) --}}
                                        <td class="px-6 py-2 text-sm text-gray-700 border border-gray-300 w-3xs"  x-data="{ openActions: false }">
                                            {{-- 1. Mostrar el Estado de la variante (Burbuja de color) --}}
                                            <b class="p-1 flex gap-2">
                                                <span class="py-1 px-4 rounded-full"
                                                    :class="{ 
                                                        'bg-green-100 text-green-800': '{{  $subVariantes["status"]  }}' === 'active',
                                                        'bg-red-100 text-red-800': '{{  $subVariantes["status"]  }}' === 'inactive' 
                                                    }">
                                                    {{  $subVariantes["status"]  === 'active' ? 'Activo' : 'Inactivo'}} 
                                                </span>

                                                {{-- 2. Botón para alternar la visibilidad de las acciones --}}
                                                <button 
                                                    @click="openActions = !openActions" {{-- Alternar la visibilidad al hacer clic --}}
                                                    class="cursor-pointer text-gray-700 hover:text-indigo-600 transition-colors"
                                                    title="Mostrar/Ocultar Acciones">
                                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" />
                                                    </svg>
                                                </button>
                                            </b>
                                            {{-- 3. Contenedor de Acciones (VISIBLE SOLO CUANDO openActions ES TRUE) --}}
                                            <div 
                                                x-show="openActions"
                                                @click.away="openActions = false" {{-- Ocultar si se hace clic fuera del contenedor --}}
                                                x-transition:enter="transition ease-out duration-100"
                                                x-transition:enter-start="opacity-0 scale-95"
                                                x-transition:enter-end="opacity-100 scale-100"
                                                x-transition:leave="transition ease-in duration-75"
                                                x-transition:leave-start="opacity-100 scale-100"
                                                x-transition:leave-end="opacity-0 scale-95"
                                                class="absolute z-10 mt-1 w-auto origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 p-2 space-y-2 flex flex-col items-start"
                                            >
                                                    @if ($subVariantes["status"] === 'active')
                                                        {{-- Botón para inactivar si está "active" --}}
                                                        <button 
                                                            wire:click="editarEstadoV({{ $subVariantes["id"] }},'inactive')"
                                                            wire:target="editarEstadoV({{ $subVariantes["id"] }},'inactive')"
                                                            wire:loading.attr="disabled"
                                                            wire:loading.class="opacity-50 cursor-not-allowed"
                                                            class="w-full justify-start cursor-pointer text-white bg-red-500 hover:bg-red-600 focus:ring-2 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-xs px-2 py-1 inline-flex items-center transition-colors" 
                                                            title="Inactivar variante">
                                                            <svg class="w-3 h-3 me-1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-x"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg>
                                                            Inactivar
                                                        </button>
                                                    @elseif ($subVariantes["status"] === 'inactive')
                                                        {{-- Botón para REVERTIR/COLOCAR OTRA VEZ "active" si está "Cancelado" --}}
                                                        <button 
                                                            wire:click="editarEstadoV({{ $subVariantes["id"] }},'active')"
                                                            wire:target="editarEstadoV({{ $subVariantes["id"] }},'active')"
                                                            wire:loading.attr="disabled"
                                                            wire:loading.class="opacity-50 cursor-not-allowed"
                                                            class="w-full justify-start cursor-pointer text-white bg-green-500 hover:bg-green-600 focus:ring-2 focus:outline-none focus:green-blue-300 font-medium rounded-lg text-xs px-2 py-1 inline-flex items-center transition-colors" 
                                                            title="Revertir Inactivo a 'Activo'">
                                                            <svg class="w-3 h-3 me-1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-rotate-clockwise"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 12a2 2 0 0 0 2 2h5.5l-.326 2.067a1 1 0 0 0 1.956 .305l.598 -3.81a.999 .999 0 0 0 -.13 -1.218l-.517 -.517" /><path d="M7 16l-3.5 -3.5a16 16 0 0 1 1.761 -2.96l.872 -.927" /><path d="M20 12l-1.5 -1.5" /><path d="M18.5 10.5l-2 -2" /><path d="M16.5 8.5l-2.5 -2.5" /><path d="M14 6l-3 -3" /><path d="M11 3l-2.5 2.5" /><path d="M12 20a8 8 0 0 0 6.698 -13.048" /></svg>
                                                            Activar
                                                        </button>
                                                    @else
                                                        {{-- Si el estado es "Confirmado" o no requiere acción inmediata --}}
                                                        <span class="text-gray-500 text-xs py-1 px-2">Sin acciones pendientes.</span>
                                                    @endif
                                            </div>

                                            <!-- Mensjae de error -->
                                            @if (session()->has('error_status_db_' . $subVariantes["id"]))
                                                <p class="text-red-500 text-xs mt-1">{{ session('error_status_db_' . $subVariantes["id"]) }}</p>
                                            @elseif (session()->has('exito_status_db_' . $subVariantes["id"]))
                                                <!-- Mensaje de exito -->
                                                <p class="text-green-500 text-xs mt-1">{{ session('exito_status_db_' . $subVariantes["id"]) }}</p>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                            {{-- Fila separadora entre productos --}}
                            <tr class="h-6 bg-gray-100/50"><td colspan="4"></td></tr>
                        @endforeach
                    </tbody>
                    {{-- Sección de Totales --}}
                    <tfoot>
                        <tr>
                            <td colspan="7">
                                {{-- Controles de Paginación y Conteo --}}
                                @if ($totalItems > $productoMostrar)
                                    <div class="flex flex-row justify-between items-center px-4 py-3 bg-white border-t border-gray-200 sm:px-6">
                                        {{-- Conteo de Elementos --}}
                                        <div class="mb-2 md:mb-0 text-sm text-gray-700">
                                            Mostrando 
                                            <span class="font-medium">{{ ($currentPage - 1) * $productoMostrar + 1 }}</span> a
                                            <span class="font-medium">{{ min($currentPage * $productoMostrar, $totalItems) }}</span> de
                                            <span class="font-medium">{{ $totalItems }}</span> productos (Variantes).
                                        </div>
                                        {{-- Controles de Paginación --}}
                                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                            {{-- Botón Anterior --}}
                                            <button 
                                                wire:click="mostrarPagina({{ $currentPage - 1 }})" 
                                                @if ($currentPage <= 1) disabled @endif
                                                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 cursor-pointer">
                                                Anterior
                                            </button>
                                            {{-- Iteración de páginas (Simplificada) --}}
                                            @for ($page = 1; $page <= $totalPages; $page++)
                                                <button 
                                                    wire:click="mostrarPagina({{ $page }})"
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
                                                wire:click="mostrarPagina({{ $currentPage + 1 }})" 
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
            @else
                {{-- Mensaje si no hay resultados por búsqueda o carrito vacío --}}
                <p class="text-gray-500 text-center py-8">
                    {{ $campoBusqueda ? 'No se encontraron resultados para: "' . $campoBusqueda . '"' : 'La lista está vacía o no se encontraron productos.' }}
                </p>    
            @endif
        </div>

        <!-- Modal de editar imagen, categoria e informacion del producto -->
        <div 
            wire:ignore.self
            x-data
            id="editar-modal-p" 
            tabindex="-1" 
            class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-md max-h-full">
                <div class="relative bg-gray-200 rounded-lg shadow-sm">
                    <button wire:click="closeModal" type="button" class="absolute top-3 end-2.5 text-gray-600 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center hover:cursor-pointer data-modal-hide="editar-modal-p" title="Cerrar modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Cerrar modal</span>
                    </button>
                    
                    <div class="p-4">
                        <!-- Titulo en el modal -->
                        <h3 class="pb-3 mb-5 text-lg font-extrabold text-gray-900 dark:text-white border-b border-gray-300">
                            <span class="text-transparent bg-clip-text bg-gradient-to-r to-gray-700 from-black" x-text="$wire.tituloModal"></span>
                        </h3>

                        <!-- Contenido del modal -->
                        <form wire:submit.prevent="guardarProceso" id="formRegistrar" class="grid grid-cols-2 gap-3">
                            <h3 class="font-bold text-gray-900">#Sección de Producto</h3>
                            <!-- campo categoria -->
                            <div class="col-span-2">
                                <div
                                    wire:ignore 
                                    wire:key="category-select-{{ $campos_key }}"
                                    x-data="{ 
                                        selectedOptions:  @js($categoriasSelect), 
                                        options:  @js($this->listaCategorias),
                                        tomSelectInstance: null // Propiedad temporal para la instancia de Tom Select
                                    }"
                                    x-init=" $nextTick(() => {
                                        const selectEl = $refs.selectElement; // Obtener la referencia al elemento
                                        // 1. Inicializar Tom Select con el plugin 'create','remove','change'
                                        tomSelectInstance = new TomSelect(selectEl, {
                                            // --- Configuración de Plugins ---
                                            plugins: ['remove_button', 'clear_button'],
                                            // --- Configuración de Datos ---
                                            valueField: 'value', // El campo del objeto de opciones que se usará como valor (ID)
                                            labelField: 'text', // El campo del objeto de opciones que se mostrará al usuario
                                            searchField: 'text', // El campo por el cual se permite la búsqueda
                                            options: options, // El arreglo de opciones iniciales de Alpine.js
                                            items: selectedOptions, // El arreglo de valores pre-seleccionados de Alpine.js
                                            // --- Configuración de Creación ---
                                            // La función se llama cuando un usuario intenta crear una opción nueva.
                                            create: function(input, callback) {
                                                // Generar un valor temporal único para el frontend
                                                const valorTemporal = 'temp-' + input;
                                                // 1. Disparar evento a Livewire para registrar la nueva opción como PENDIENTE.
                                                // Esto permite a Livewire mantener un registro de las opciones nuevas
                                                // que deben guardarse (y eliminarlas si el usuario las deselecciona).
                                                // $wire.dispatch('agregar-nueva-opcion', {
                                                    // valorTemporal: valorTemporal, 
                                                    // nuevaOpcion: input
                                                // });
                                                // CRUCIAL: El 'value' debe ser único (aquí usamos el texto como valor).
                                                const newOption = {
                                                    value: input, // Usar el texto escrito como valor temporal
                                                    text: input
                                                };
                                                // 2. Llamar al callback con el objeto para que Tom Select añada la opción a su lista y la seleccione inmediatamente.
                                                callback(newOption);
                                                // 3. Sincronizar el estado de Alpine manualmente
                                                // Tom Select ya habrá actualizado el input, pero esta línea es una garantía:
                                                selectedOptions = selectEl.tomselect.getValue();
                                                // Asegúrate de que, después del callback, también llamas a $wire.set:
                                                setTimeout(() => {
                                                    $wire.set('categoriasSelect', selectEl.tomselect.getValue());
                                                }, 50);
                                            },
                                            
                                            // filtro (solo permite crear si el input no está vacío)
                                            createFilter: true, // Esto activa el botón de Add si no hay coincidencias.
                                            // 4. Lógica de sincronización de datos de Alpine y livewire
                                            onChange: (value) => {
                                                // Actualiza la variable de Alpine cuando Tom Select cambia
                                                selectedOptions = [value];
                                                // Usamos $wire.set para forzar a Livewire a actualizar
                                                $wire.set('categoriasSelect', value);
                                            }
                                        });
                                    });
                                ">
                                    <label for="my_select" class="block mb-2.5 text-sm font-medium text-heading">Categorías:</label>
                                    <select x-ref="selectElement" id="my_select" multiple wire:model="categoriasSelect" class="w-full border border-default-medium rounded-base focus:ring-gray-300 focus:border-gray-400">
                                    </select>
                                </div>
                                @error('categoriasSelect') 
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                @enderror 
                            </div>

                            <!-- campo nombre del producto -->
                            <div wire:key="campoNombreP-{{ $campos_key }}" class="col-span-2">
                                <label for="nombreProdutoA" class="block mb-2.5 text-sm font-medium text-heading">Nombre del Producto</label>
                                <input type="text" id="nombreProdutoA" wire:model.live="nombreProdutoA" class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-gray-500 focus:border-gray-800 block w-full px-3 py-2.5 shadow-xs placeholder:text-body">
                                @error('nombreProdutoA') 
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                @enderror 
                            </div>

                            <!-- campo de descripcion -->
                            <div wire:key="campoDescripcion-{{ $campos_key }}" class="col-span-2">
                                <label for="descripcion" class="block mb-2.5 text-sm font-medium text-heading">Descripción del Producto</label>
                                <textarea id="descripcion" wire:model.live="descripcionProducto" rows="4" class="block bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-gray-500 focus:border-gray-800 block w-full p-3.5 shadow-xs placeholder:text-body" placeholder="Escribe aquí la descripción del producto."></textarea>                    
                                @error('descripcionProducto') 
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                @enderror 
                            </div>

                            <!-- Campo precio minorista -->
                            <div wire:key="campoPriceMinorista-{{ $campos_key }}" class="col-span-2 md:col-span-1">
                                <label for="precioMinorista" class="block mb-2.5 text-sm font-medium text-heading">Precio minorista</label>
                                <input type="number"  step="0.01" min="0" id="precioMinorista" wire:model.live="precioMinProducto" class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-gray-500 focus:border-gray-800 block w-full px-3 py-2.5 shadow-xs placeholder:text-body" placeholder="$2999">
                                @error('precioMinProducto') 
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                @enderror 
                            </div>

                            <!-- Campo precio mayorista -->
                            <div wire:key="campoPriceMayorista-{{ $campos_key }}" class="col-span-2 md:col-span-1">
                                <label for="precioMayorista" class="block mb-2.5 text-sm font-medium text-heading">Precio mayorista</label>
                                <input type="number"  step="0.01" min="0" id="precioMayorista" wire:model.live="precioMayProducto" class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-gray-500 focus:border-gray-800 block w-full px-3 py-2.5 shadow-xs placeholder:text-body" placeholder="$3999">
                                @error('precioMayProducto') 
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                @enderror 
                            </div>

                            <!-- Campo de Mínimo De Piezas al por mayor -->
                            <div wire:key="campoMinPiezas-{{ $campos_key }}" class="col-span-2">
                                <label for="minPiezas" class="block mb-2.5 text-sm font-medium text-heading">Mínimo De Piezas al por mayor</label>
                                <input type="number"  step="0" min="0" id="minPiezas" wire:model.live="minPiezasMay" class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-gray-500 focus:border-gray-800 block w-full px-3 py-2.5 shadow-xs placeholder:text-body" placeholder="0">
                                @error('minPiezasMay') 
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                @enderror 
                            </div>


                            <!-- campo de estado del producto -->
                            <div wire:key="campoEstado-{{ $campos_key }}" class="col-span-2">
                                <label for="estadoProducto" class="block mb-2.5 text-sm font-medium text-heading">Estatus</label>
                                <select wire:model="estadoProducto" id="estadoProducto" class="text-sm w-full border border-default-medium rounded-base focus:ring-gray-300 focus:border-gray-400">
                                    <option value="">Selecciona</option>
                                    @foreach (["active" => "Activo", "inactive" => "Inactivo"] as $clave => $valor)
                                        <option value="{{ $clave }}">{{ $valor }}</option>
                                    @endforeach
                                </select>
                                @error('estadoProducto') 
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                @enderror 
                            </div>
                            <h3 class="font-bold text-gray-900">#Sección de Variante</h3>

                            <!-- campo de imagen -->
                            <div wire:key="campoImg-{{ $campos_key }}" class="col-span-2">
                                <label for="nuevaImagen" class="block mb-1 text-sm font-medium text-heading">Cambiar imagen (Variante)</label>
                                {{-- 1. Campo de entrada para el archivo --}}
                                <input type="file" id="nuevaImagen" wire:model="nuevaImagen" accept="image/*" class="bg-neutral-secondary-medium  text-heading text-sm rounded-base focus:ring-gray-500 focus:border-gray-800 block w-full px-3 py-2.5 shadow-xs placeholder:text-body" title="Actualizar imagen">
                                {{-- 2. Mostrar una previsualización (Opcional, pero muy útil) --}}
                                @if (!empty($nuevaImagen))
                                    <p class="block mb-2.5 text-sm mt-2 font-medium text-heading">Previsualización de la nueva imagen:</p>
                                    {{-- El método temporaryUrl() solo funciona con el trait WithFileUploads --}}
                                    <img src="{{ $nuevaImagen->temporaryUrl() }}" class="mt-1 w-24 h-24 object-cover rounded-md">
                                @endif
                                {{-- Manejo de errores de validación --}}
                                @error('nuevaImagen') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>
                            <!-- seccion de botones -->
                            <div class="mt-2 py-3 col-span-2 border-t border-gray-300 text-center flex flex-col">
                                <div>
                                    <!-- Mensaje de exito -->
                                    {{-- La clave de sesión 'message_exitoso' tiene prioridad --}}
                                    @if (session()->has('message_exitoso_modal'))
                                        <div x-data="{ show: true }"
                                            x-show="show" 
                                            x-init="setTimeout(() => show = false, 7000)" 
                                            x-transition:leave.duration.500ms
                                            class="p-4 mb-4 text-green-700 bg-green-100 rounded-sm flex" 
                                            role="alert">
                                            
                                            {{ session('message_exitoso_modal') ?? "Operación exitosa."}}
                                            
                                            <button @click="show = false" class="ml-auto focus:outline-none">&times;</button>
                                        </div>
                                    @else 
                                        <!-- Mensaje de error -->
                                        {{-- Si no hay éxito, verificamos si hay un error --}}
                                        @if (session()->has('error_mensaje_modal'))
                                            <div x-data="{ show: true }"
                                                x-show="show" 
                                                x-init="setTimeout(() => show = false, 10000)"
                                                x-transition:leave.duration.500ms
                                                class="p-4 mb-4 text-red-700 bg-red-100 rounded-sm flex" 
                                                role="alert">
                                                
                                                {{ session('error_mensaje_modal') ?? "Ocurrió un error. Inténtalo de nuevo." }} 
                                                
                                                <button @click="show = false" class="ml-auto focus:outline-none">&times;</button>
                                            </div>
                                        @endif
                                        <div class="">
                                            <button type="submit" form="formRegistrar" class="inline py-2.5 px-5 ms-3 mb-2 text-sm text-green-700 bg-green-100 hover:bg-green-200 
                                            w-full sm:w-auto items-center justify-center rounded-lg border  focus:z-10 focus:outline-none focus:ring-3 focus:ring-gray-600 cursor-pointer">
                                                Actualizar
                                            </button>

                                            <button wire:click="closeModal" type="button" 
                                            class="inline py-2.5 px-5 ms-3 text-sm  mb-2 
                                            bg-black text-white w-full sm:w-auto items-center justify-center rounded-lg border border-black font-medium  hover:bg-white hover:text-black focus:z-10 focus:outline-none focus:ring-3 focus:ring-gray-600 focus:bg-white focus:text-black cursor-pointer">
                                                No, cancelar
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
