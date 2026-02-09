<!-- Componente Livewire -->
<div class="bg-gray-200 py-2">
    <!-- Cargador -->
    <div class="py-40 @if ($ContenidoCargado) hidden @endif">
        <div class="flex justify-center items-center wrap py-20">
            <div class="pr-2"><div class="cargadorPrincipal"></div></div>
            <p class="ml-3 text-lg text-gray-700">Cargando Catálogo...</p>
        </div>
    </div>

    <!-- Contenido principal -->
    <div wire:init="inicializarContenido" @if (!$ContenidoCargado) hidden @endif>
        <!-- Contenido -->
        <section class="bg-gray-200 antialiased pt-8 border-transparent outline-transparent">
            <!-- botones de filtrados -->
            <div class="flex justify-center pb-8 sm:pb-2 sm:justify-end flex-wrap gap-3">
                <!-- boton ordenar -->
                <div x-data="{ open: false, currentSort: 'Más nuevo' }" @click.away="open = false" class="relative inline-block text-left">
                    <div class="flex gap-3">
                        <label for="" class="text-sm font-medium text-gray-700 w-fit">Ordenar por:</label>
                        <button @click="open = !open" type="button" id="menu-button" aria-expanded="true" aria-haspopup="true" title="Ordenar por"
                        class="p-2 mx-0 lg:mx-2 inline-flex justify-center w-full rounded-lg border border-black bg-black  text-sm font-medium text-white shadow-sm hover:bg-white hover:text-black focus:outline-none focus:ring-3 focus:ring-gray-600 focus:bg-white focus:text-black cursor-pointer">
                            <span x-text="currentSort"></span>
                            <svg class="-mr-1 ml-2 h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.23 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>

                    <div 
                        x-show="open" 
                        x-transition:enter="transition ease-out duration-100" 
                        x-transition:enter-start="transform opacity-0 scale-95" 
                        x-transition:enter-end="transform opacity-100 scale-100" 
                        x-transition:leave="transition ease-in duration-75" 
                        x-transition:leave-start="transform opacity-100 scale-100" 
                        x-transition:leave-end="transform opacity-0 scale-95" 
                        class="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" 
                        role="menu" 
                        aria-orientation="vertical" 
                        aria-labelledby="menu-button" 
                        tabindex="-1"
                    >
                        <div class="py-1" role="none">
                            <a href="#"  wire:click.prevent="ordenarProducto('desc')"  @click="currentSort = 'Más nuevo'; open = false" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"  role="menuitem"  tabindex="-1">Más nuevo</a>

                            <a href="#"  wire:click.prevent="ordenarProducto('asc')"  @click="currentSort = 'Más viejo'; open = false" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"  role="menuitem"  tabindex="-1">Más viejo</a>

                            <a href="#"  wire:click.prevent="ordenarProducto('precioASC')"  @click="currentSort = 'Precio (Asc.)'; open = false" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"  role="menuitem"  tabindex="-1">Precio (Asc.)</a>

                            <a href="#"  wire:click.prevent="ordenarProducto('precioDESC')"  @click="currentSort = 'Precio (Desc.)'; open = false" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"  role="menuitem"  tabindex="-1">Precio (Desc.)</a>
                        </div>
                    </div>
                </div>

                <!-- boton de filtrar categoria -->
                <div x-data="{modalCategoria: false}">
                    <button x-ref="openButtonModal" x-on:click="modalCategoria = true; $nextTick(() => $refs.modal.focus())" type="button" class="p-2 mx-2 bg-black text-white flex w-full sm:w-auto items-center justify-center rounded-lg border border-black font-medium  hover:bg-white hover:text-black focus:z-10 focus:outline-none focus:ring-3 focus:ring-gray-600 focus:bg-white focus:text-black cursor-pointer">
                        <svg class="-ms-0.5 me-2 h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M18.796 4H5.204a1 1 0 0 0-.753 1.659l5.302 6.058a1 1 0 0 1 .247.659v4.874a.5.5 0 0 0 .2.4l3 2.25a.5.5 0 0 0 .8-.4v-7.124a1 1 0 0 1 .247-.659l5.302-6.059c.566-.646.106-1.658-.753-1.658Z" />
                        </svg>
                        Categorías 
                        <svg class="-me-0.5 ms-2 h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                        </svg>
                    </button>
                    <!-- Modal de categorías  -->
                    <div x-cloak x-show="modalCategoria" x-transition.opacity.duration.200ms x-trap.inert.noscroll="modalCategoria" x-on:keydown.escape.window="modalCategoria = false; $nextTick(() => $refs.openButtonModal.focus())" x-on:click.self="modalCategoria = false; $nextTick(() => $refs.openButtonModal.focus())" class="fixed inset-0 z-60 flex w-full items-start justify-center sm:items-center bg-black/20 p-4 pb-8 backdrop-blur-md lg:p-8" role="dialog" aria-modal="true" aria-labelledby="defaultModalTitle">
                        <!-- Modal Dialog -->
                        <div x-ref="modal" tabindex="-1" x-show="modalCategoria" x-transition:enter="transition ease-out duration-200 delay-100 motion-reduce:transition-opacity" x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100" class="flex max-w-lg flex-col gap-4  max-h-[90vh] overflow-y-auto rounded-sm border border-neutral-700 bg-neutral-900 text-neutral-300">
                            <!-- Dialog Header -->
                            <div class="flex items-center justify-between border-b  p-4 border-neutral-700 bg-neutral-950/20">
                                <h3 id="defaultModalTitle" class="font-semibold tracking-wide  text-white flex items-center">
                                    <svg class="-ms-0.5 me-2 h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M18.796 4H5.204a1 1 0 0 0-.753 1.659l5.302 6.058a1 1 0 0 1 .247.659v4.874a.5.5 0 0 0 .2.4l3 2.25a.5.5 0 0 0 .8-.4v-7.124a1 1 0 0 1 .247-.659l5.302-6.059c.566-.646.106-1.658-.753-1.658Z" />
                                    </svg>
                                    Categorías
                                </h3>
                                <button x-on:click="modalCategoria = false; $nextTick(() => $refs.openButtonModal.focus())" aria-label="close modal" class="cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" stroke="currentColor" fill="none" stroke-width="1.4" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            <!-- Dialog Body -->
                            <form id="form-categoria" class="px-4">
                                <!-- Seccion de categorias -->
                                <div class="mb-4">
                                    <p class="font-semibold mb-3">Seleccionar Categorías</p>
                                    <div class="grid grid-cols-3 lg:grid-cols-4 gap-3">
                                        @if(!empty($todasCategorias))
                                            @foreach($todasCategorias as $cat)
                                                <label class="flex items-center gap-2 text-sm">
                                                    <input wire:model.defer="selectedCategory" type="checkbox" value="{{ $cat["id"] }}" class="h-4 w-4" />
                                                    <span>{{ $cat["name"] }}</span>
                                                </label>
                                            @endforeach
                                        @else
                                            <p class="text-sm text-gray-400">No hay Categorías</p>
                                        @endif
                                    </div>
                                </div>
                            </form>
                            <!-- Dialog Footer -->
                            <div class="flex flex-col-reverse justify-between gap-2 border-t p-4 border-neutral-700 bg-neutral-950/20 sm:flex-row sm:items-center md:justify-end">
                                <!-- Boton de cerrar -->
                                <button x-on:click="modalCategoria = false" type="button" class="whitespace-nowrap rounded-sm px-4 py-2 text-center text-sm font-medium tracking-wide text-neutral-600 transition hover:opacity-75 hover:bg-white hover:text-black focus:z-10 focus:outline-none focus:ring-3 focus:ring-gray-600 focus:bg-white focus:text-black cursor-pointer">Cerrar</button>
                                <!-- boton de resetar -->
                                <button type="reset" form="form-categoria"
                                    wire:click="resetFilters('categoria')"
                                    x-on:click="modalCategoria = false; $nextTick(() => $refs.openButtonModal.focus())"
                                    class="whitespace-nowrap rounded-sm bg-black border border-black px-4 py-2 text-center text-sm font-medium tracking-wide text-neutral-100 fill-white hover:bg-white hover:fill-black focus:z-10 focus:outline-none focus:ring-3 focus:ring-gray-600 focus:bg-white focus:text-black cursor-pointer flex justify-center"
                                    title="Resetear filtros">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="h-5"><!--!Font Awesome Free v7.1.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path  d="M210.5 480L333.5 480L398.8 414.7L225.3 241.2L98.6 367.9L210.6 479.9zM256 544L210.5 544C193.5 544 177.2 537.3 165.2 525.3L49 409C38.1 398.1 32 383.4 32 368C32 352.6 38.1 337.9 49 327L295 81C305.9 70.1 320.6 64 336 64C351.4 64 366.1 70.1 377 81L559 263C569.9 273.9 576 288.6 576 304C576 319.4 569.9 334.1 559 345L424 480L544 480C561.7 480 576 494.3 576 512C576 529.7 561.7 544 544 544L256 544z"/></svg>
                                </button>
                                <!-- Boton aplicar filtro -->
                                <button x-on:click="modalCategoria = false" wire:click="applyFilters" type="button" class="whitespace-nowrap rounded-sm bg-black border border-black px-4 py-2 text-center text-sm font-medium tracking-wide text-neutral-100 hover:bg-white hover:text-black focus:z-10 focus:outline-none focus:ring-3 focus:ring-gray-600 focus:bg-white focus:text-black cursor-pointer">Aplicar filtro</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- boton de filtrar por caracteristicas -->
                <div x-data="{modalFiltro: false}">
                    <button x-ref="openButton" x-on:click="modalFiltro = true; $nextTick(() => $refs.modal.focus())" type="button" class="p-2 mx-2 bg-black text-white flex w-full sm:w-auto items-center justify-center rounded-lg border border-black font-medium  hover:bg-white hover:text-black focus:z-10 focus:outline-none focus:ring-3 focus:ring-gray-600 focus:bg-white focus:text-black cursor-pointer">
                        <svg class="-ms-0.5 me-2 h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M18.796 4H5.204a1 1 0 0 0-.753 1.659l5.302 6.058a1 1 0 0 1 .247.659v4.874a.5.5 0 0 0 .2.4l3 2.25a.5.5 0 0 0 .8-.4v-7.124a1 1 0 0 1 .247-.659l5.302-6.059c.566-.646.106-1.658-.753-1.658Z" />
                        </svg>
                        Filtros
                        <svg class="-me-0.5 ms-2 h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                        </svg>
                    </button>
                    <!-- Modal de Filtros -->
                    <div x-cloak x-show="modalFiltro" x-transition.opacity.duration.200ms x-trap.inert.noscroll="modalFiltro" x-on:keydown.escape.window="modalFiltro = false; $nextTick(() => $refs.openButton.focus())" x-on:click.self="modalFiltro = false; $nextTick(() => $refs.openButton.focus())" class="fixed inset-0 z-60 flex w-full items-start justify-center sm:items-center bg-black/20 p-4 pb-8 backdrop-blur-md lg:p-8" role="dialog" aria-modal="true" aria-labelledby="defaultModalTitle">
                        <!-- Modal Dialog -->
                        <div x-ref="modal" tabindex="-1" x-show="modalFiltro" x-transition:enter="transition ease-out duration-200 delay-100 motion-reduce:transition-opacity" x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100" class="flex max-w-lg flex-col gap-4  max-h-[90vh] overflow-y-auto rounded-sm border border-neutral-700 bg-neutral-900 text-neutral-300">
                            <!-- Dialog Header -->
                            <div class="flex items-center justify-between border-b p-4 border-neutral-700 bg-neutral-950/20">
                                <h3 id="defaultModalTitle" class="font-semibold tracking-wide text-white flex items-center">
                                    <svg class="-ms-0.5 me-2 h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M18.796 4H5.204a1 1 0 0 0-.753 1.659l5.302 6.058a1 1 0 0 1 .247.659v4.874a.5.5 0 0 0 .2.4l3 2.25a.5.5 0 0 0 .8-.4v-7.124a1 1 0 0 1 .247-.659l5.302-6.059c.566-.646.106-1.658-.753-1.658Z" />
                                    </svg>
                                    Filtros
                                </h3>
                                <button x-on:click="modalFiltro = false; $nextTick(() => $refs.openButton.focus())" aria-label="close modal" class="cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" stroke="currentColor" fill="none" stroke-width="1.4" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            <!-- Dialog Body -->
                            <form id="form-filtro" class="px-4">
                                <!-- Seccion de Tallas -->
                                <div class="mb-2">
                                    <p class="font-semibold mb-3">Tallas</p>
                                    <div class="grid grid-cols-4 md:grid-cols-8 gap-3 place-items-center">
                                        @if(!empty($availableSizes))
                                            @foreach($availableSizes as $size)
                                                <label class="flex items-center gap-2 text-sm">
                                                    <input wire:model.defer="selectedSizes" type="checkbox" value="{{ $size }}" class="h-4 w-4" />
                                                    <span>{{ $size }}</span>
                                                </label>
                                            @endforeach
                                        @else
                                            <p class="text-sm text-gray-400">No hay tallas</p>
                                        @endif
                                    </div>
                                </div> 
                                <!-- Seccion de colores -->
                                <div class="my-4">
                                    <p class="font-semibold mb-3">Colores</p>
                                    <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-10 gap-3 place-items-center">
                                        @if(!empty($availableColors))
                                            @foreach($availableColors as $color)
                                                <label class="relative flex items-center justify-center rounded-full w-fit cursor-pointer" title="{{ $color["value"] }}">
                                                    <div class="h-6 w-6 rounded-full absolute" @style(['background-color: ' . ($color['color_code'] ?? 'transparent')])></div>
                                                    <input wire:model.defer="selectedColors" type="checkbox" value="{{ $color["value"] }}" 
                                                    class="h-7 w-7 bg-transparent rounded-full text-[#155dfc]  focus:ring-2 focus:ring-offset-0 focus:ring-[#e8a3a3] border-transparent"/>
                                                </label>
                                            @endforeach
                                        @else
                                            <p class="text-sm text-gray-400">No hay colores</p>
                                        @endif
                                    </div>
                                </div>
                                <!-- Seccion de Estampado -->
                                <div class="my-4">
                                    <p class="font-semibold mb-3">Estampado</p>
                                    <div class="grid grid-cols-3 lg:grid-cols-4 gap-3">
                                        @if(!empty($availableEstampado))
                                            @foreach($availableEstampado as $est)
                                                <label class="flex items-center gap-2 text-sm">
                                                    <input wire:model.defer="selectedEstampado" type="checkbox" value="{{ $est }}" class="h-4 w-4" />
                                                    <span>{{ $est }}</span>
                                                </label>
                                            @endforeach
                                        @else
                                            <p class="text-sm text-gray-400">No hay Estampado</p>
                                        @endif
                                    </div>
                                </div>
                                {{--  
                                    <!-- Seccion de Materiales -->
                                    <div class="my-4">
                                        <p class="font-semibold mb-3">Materiales</p>
                                        <div class="grid grid-cols-3 lg:grid-cols-4 gap-3">
                                            @if(!empty($availableMaterials))
                                                @foreach($availableMaterials as $mat)
                                                    <label class="flex items-center gap-2 text-sm">
                                                        <input wire:model.defer="selectedMaterials" type="checkbox" value="{{ $mat }}" class="h-4 w-4" />
                                                        <span>{{ $mat }}</span>
                                                    </label>
                                                @endforeach
                                            @else
                                                <p class="text-sm text-gray-400">No hay materiales</p>
                                            @endif
                                        </div>
                                    </div>
                                --}}
                            </form>
                            <!-- Dialog Footer -->
                            <div class="flex flex-col-reverse justify-between gap-2 border-t p-4 border-neutral-700 bg-neutral-950/20 sm:flex-row sm:items-center md:justify-end">
                                <!-- Boton de cerrar -->
                                <button x-on:click="modalFiltro = false" type="button" class="whitespace-nowrap rounded-sm px-4 py-2 text-center text-sm font-medium tracking-wide text-neutral-600 transition hover:opacity-75 hover:bg-white hover:text-black focus:z-10 focus:outline-none focus:ring-3 focus:ring-gray-600 focus:bg-white focus:text-black cursor-pointer">Cerrar</button>
                                <!-- boton de resetar -->
                                <button type="reset" form="form-filtro"
                                    wire:click="resetFilters('filtros')"
                                    x-on:click="modalFiltro = false; $nextTick(() => $refs.openButton.focus())"
                                    class="whitespace-nowrap rounded-sm bg-black border border-black px-4 py-2 text-center text-sm font-medium tracking-wide text-neutral-100 fill-white hover:bg-white hover:fill-black focus:z-10 focus:outline-none focus:ring-3 focus:ring-gray-600 focus:bg-white focus:text-black cursor-pointer flex justify-center"
                                    title="Resetear filtros">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="h-5"><!--!Font Awesome Free v7.1.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path  d="M210.5 480L333.5 480L398.8 414.7L225.3 241.2L98.6 367.9L210.6 479.9zM256 544L210.5 544C193.5 544 177.2 537.3 165.2 525.3L49 409C38.1 398.1 32 383.4 32 368C32 352.6 38.1 337.9 49 327L295 81C305.9 70.1 320.6 64 336 64C351.4 64 366.1 70.1 377 81L559 263C569.9 273.9 576 288.6 576 304C576 319.4 569.9 334.1 559 345L424 480L544 480C561.7 480 576 494.3 576 512C576 529.7 561.7 544 544 544L256 544z"/></svg>
                                </button>
                                <!-- Boton aplicar filtro -->
                                <button x-on:click="modalFiltro = false" wire:click="applyFilters" type="button" class="whitespace-nowrap rounded-sm bg-black border border-black px-4 py-2 text-center text-sm font-medium tracking-wide text-neutral-100 hover:bg-white hover:text-black focus:z-10 focus:outline-none focus:ring-3 focus:ring-gray-600 focus:bg-white focus:text-black cursor-pointer">Aplicar filtros</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- bloque de ola -->
            <div class="bg-black">
                <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                    <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="fill-gray-200"></path>
                </svg>
            </div>

            <!-- contenedores de articulos -->
            <div class="bg-black pb-10 px-4 grid align-items-stretch justify-items-stretch gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4" wire:loading.remove wire:target="ordenarProducto, resetFilters, applyFilters">
                @forelse ($products as $product)
                    <!-- validar si existe variantes en el objecto -->
                    @if (!$product->variants->isEmpty())
                        <!-- Procesar datos: -->
                        {{-- Lógica de Extracción de Datos para la Tarjeta --}}
                            @php
                            // INICIO DE BLOQUE PHP: Para la lógica compleja que no se muestra directamente.

                            // 1. Imagen Aleatoria: selecciona una variante que tenga una imagen relacionada
                            // (la relación 'image' apunta a la tabla 'images' y contiene 'main_image_url').
                            $displayVariant = $product->variants
                                ->filter(fn($v) => optional($v->image)->main_image_url != null)
                                ->shuffle()
                                ->first();

                            // Asigna la URL de la imagen de la variante aleatoria o una imagen por defecto.
                            $displayImage = optional($displayVariant->image)->main_image_url ?? '/default-placeholder.jpg';


                            // 2. Extracción de Atributos Únicos
                            // Combina (flatMap) todos los 'attributeValues' de TODAS las variantes del producto.
                            // Esto crea una única colección plana de todos los atributos disponibles (ej: todos los colores, todas las tallas).
                            $allAttributes = $product->variants->flatMap(fn($v) => $v->attributeValues);

                            // 3. INICIO DE BLOQUE PHP PARA PROCESAR LOS ATRIBUTOS ÚNICOS.
                            // Extrae todos los valores de atributo cuyo 'attribute->name' sea 'Color'.

                            $availableColors = $allAttributes
                                ->filter(fn($av) => optional($av->attribute)->name === 'Color')
                                ->pluck('color_code') // Obtiene solo los valores (ej: ['Rojo', 'Azul', 'Rojo'])
                                ->unique(); // Elimina duplicados (ej: ['Rojo', 'Azul'])

                            // Extrae los primeros 4 para la lista y el resto se cuenta.
                            if ($availableColors->count() > 10) {
                                $availableColors[] = 'otros';
                            }

                            // Hace lo mismo para el atributo 'Talla' (Size).
                            $uniqueSizes = $allAttributes
                                ->filter(fn($av) => optional($av->attribute)->name === 'Talla')
                                ->pluck('value') // Obtiene solo los valores (ej: ['Rojo', 'Azul', 'Rojo'])
                                ->unique() // Elimina duplicados (ej: ['Rojo', 'Azul'])
                                ->sort(); // Ordena alfabéticamente

                            // Extrae los primeros 4 para la lista y el resto se cuenta.
                            $availableSizes = $uniqueSizes->take(4)->implode(', ');
                            if ($uniqueSizes->count() > 4) {
                                $availableSizes .= ', otros...';
                            }
                                
                            // Hace lo mismo para el atributo 'Material'.
                            $uniqueMaterials = $allAttributes
                                ->filter(fn($av) => optional($av->attribute)->name === 'Material')
                                ->pluck('value')
                                ->unique()
                                ->sort();

                            // Extrae los primeros 4 para la lista y el resto se cuenta.
                            $availableMaterials = $uniqueMaterials->take(4)->implode(', ');
                            if ($uniqueMaterials->count() > 4) {
                                $availableMaterials .= ', otros...';
                            }

                            // Hace lo mismo para el atributo 'Material'.
                            $uniqueEstampados = $allAttributes
                                ->filter(fn($av) => optional($av->attribute)->name === 'Estampado')
                                ->pluck('value')
                                ->filter(fn($value) => strtolower($value) !== 'ninguno')
                                ->unique()
                                ->sort();
                            
                            // Extrae los primeros 4 para la lista y el resto se cuenta.
                            $availableEstampado = $uniqueEstampados->take(4)->implode(', ');
                            if ($uniqueEstampados->count() > 4) {
                                $availableEstampado .= ', otros';
                            }
                        @endphp

                        <!-- tarjeta del articulo -->
                        <div class="bg-white p-6 border border-gray-400 rounded-lg shadow-sm hover:scale-104 transition-all duration-400">
                            <div class="h-70 w-full">
                                <!-- enlace del producto -->
                                <a href="{{ route('producto', ['id' => $product->id, 'cat' => $seccionCatalogo]) }}">
                                    <!-- imagen del producto -->
                                    <img class="w-full aspect-square object-contain h-full" src="{{ asset('storage').'/'.$displayImage}}" alt="Imagen de {{ $product->name }}" title="{{ $product->name }}">
                                </a>
                                </button>
                            </div>

                            <div class="pt-6">
                                <!-- Titulo del producto -->
                                <a href="{{ route('producto', ['id' => $product->id, 'cat' => $seccionCatalogo]) }}" class="capitalize font-semibold leading-tight text-black text-lg hover:underline">{{ $product->name }}</a>
                                {{-- Resumen de Atributos --}}
                                <div class="mt-2 text-sm text-gray-700 mb-4 space-y-1.5">
                                    @if ($availableColors)
                                        <div>
                                            <p><strong>Colores:</strong></p>

                                            <div class="flex row gap-1">
                                                @foreach($availableColors as $c)
                                                    @if ($c == "otros")
                                                        <b class="font-normal">...</b>
                                                    @else
                                                        <div class="flex row gap-1">
                                                            <div class="w-4 h-4 border-transparent rounded-xs" @style(['background-color: ' . ($c ?? 'transparent')])></div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    @if ($availableEstampado)
                                        <p><strong>Estampado:</strong> {{ $availableEstampado }}</p>
                                    @endif
                                    @if ($availableSizes)
                                        <p><strong>Tallas:</strong> {{ $availableSizes }}</p>
                                    @endif
                                    @if ($availableMaterials)
                                        <p><strong>Material:</strong> {{ $availableMaterials }}</p>
                                    @endif
                                </div>

                                <ul class="mt-2 flex items-center gap-4">
                                    <li class="flex items-center gap-2">
                                        <svg class="h-4 w-4 text-gray-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h6l2 4m-8-4v8m0-8V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v9h2m8 0H9m4 0h2m4 0h2v-4m0 0h-5m3.5 5.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Zm-10 0a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z" />
                                        </svg>
                                        <p class="text-sm font-medium text-gray-600">Entrega rápida</p>
                                    </li>

                                    <li class="flex items-center gap-2">
                                        <svg class="h-4 w-4 text-gray-600 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M8 7V6c0-.6.4-1 1-1h11c.6 0 1 .4 1 1v7c0 .6-.4 1-1 1h-1M3 18v-7c0-.6.4-1 1-1h11c.6 0 1 .4 1 1v7c0 .6-.4 1-1 1H4a1 1 0 0 1-1-1Zm8-3.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z" />
                                        </svg>
                                        <p class="text-sm font-medium text-gray-600">Mejor precio</p>
                                    </li>
                                </ul>

                                <!-- Precio -->
                                <div class="mt-4 flex items-center justify-between gap-4">
                                    <p class="text-2xl font-extrabold leading-tight text-gray-900 e">${{ number_format($seccionCatalogo == "mayorista" ? $product->price_wholesale : $product->price_retail, 2) }}</p>
                                    <a href="{{ route('producto', ['id' => $product->id, 'cat' => $seccionCatalogo]) }}">
                                        <!-- inline-flex items-center rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4  focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800 -->
                                        <button type="button" class="p-5 bg-black text-white inline-flex items-center border rounded-lg font-medium hover:bg-white hover:text-black focus:outline-none focus:ring-4 focus:ring-gray-600 focus:bg-white focus:text-black cursor-pointer group">
                                            <svg class="-ms-2 me-2 h-5 w-5 group-hover:-rotate-40 transition-all" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h1.5L8 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm.75-3H7.5M11 7H6.312M17 4v6m-3-3h6" />
                                            </svg>
                                            Comprar
                                        </button>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                    <p class="bg-gray-200">No hay productos disponibles.</p>
                @endforelse
            </div>

            <!-- Cargador secundario -->
            <div class="bg-black pb-10 px-4  align-items-stretch w-full" wire:loading.delay wire:target="ordenarProducto, resetFilters, applyFilters">
                <div class="flex justify-center items-center wrap py-20">
                    <div class="pr-2"><div class="cargadorPrincipal"></div></div>
                    <p class="ml-3 text-lg text-white">Cargando Catálogo...</p>
                </div>
            </div>

            <!-- bloque de ola -->
            <div class="bg-gray-200">
                <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                    <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="shape-fill"></path>
                </svg>
            </div>
            
            <!-- boton y paginación -->
            <div id="catalogo-paginacion" class="mt-8 p-4 flex items-center justify-center">
                <div class="scale-140">
                    {{-- Solo muestra los enlaces si hay datos --}}
                    @if (!empty($products))
                        {{ $products->links() }} 
                    @endif
                </div>
            </div>
        </section>
    </div>
</div>
