<div class="p-2">
    <!-- Seccion de Estadística -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 md:gap-6">
        <!-- Estadística TOTAL DE ORDENES -->
        <div class="rounded-2xl border-2 border-[#5762B3] bg-[#E6E8FF] p-5 md:p-6">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#5762B3]">
                <svg class="fill-gray-800 dark:fill-white/90" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M11.665 3.75621C11.8762 3.65064 12.1247 3.65064 12.3358 3.75621L18.7807 6.97856L12.3358 10.2009C12.1247 10.3065 11.8762 10.3065 11.665 10.2009L5.22014 6.97856L11.665 3.75621ZM4.29297 8.19203V16.0946C4.29297 16.3787 4.45347 16.6384 4.70757 16.7654L11.25 20.0366V11.6513C11.1631 11.6205 11.0777 11.5843 10.9942 11.5426L4.29297 8.19203ZM12.75 20.037L19.2933 16.7654C19.5474 16.6384 19.7079 16.3787 19.7079 16.0946V8.19202L13.0066 11.5426C12.9229 11.5844 12.8372 11.6208 12.75 11.6516V20.037ZM13.0066 2.41456C12.3732 2.09786 11.6277 2.09786 10.9942 2.41456L4.03676 5.89319C3.27449 6.27432 2.79297 7.05342 2.79297 7.90566V16.0946C2.79297 16.9469 3.27448 17.726 4.03676 18.1071L10.9942 21.5857L11.3296 20.9149L10.9942 21.5857C11.6277 21.9024 12.3732 21.9024 13.0066 21.5857L19.9641 18.1071C20.7264 17.726 21.2079 16.9469 21.2079 16.0946V7.90566C21.2079 7.05342 20.7264 6.27432 19.9641 5.89319L13.0066 2.41456Z" fill=""></path>
                </svg>
            </div>

            <div class="mt-5 flex items-end justify-between">
                <div>
                    <span class="text-sm text-black" _msttexthash="106808" _msthash="152">Órdenes <b>(Total)</b></span>
                    <h4 class="mt-2 font-bold text-black">
                        {{ $totalOrdenes }}                         
                    </h4>
                </div>
            </div>
        </div>

        <!-- Estadística TOTAL DE ORDENES Dinamico -->
        @foreach ($ordenesEstados as $estado => $info)
            <div class="rounded-2xl border-2 p-5  {{ 
                match ($estado) {
                    'Por Verificar' => 'border-yellow-500 bg-yellow-100',
                    'Confirmado'    => 'border-green-500 bg-green-100',
                    'Cancelado'     => 'border-red-500 bg-red-100',
                    default         => 'border-blue-900 bg-blue-100',
                }
            }} ">

                @if ($estado == 'Por Verificar')
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-yellow-500">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M740-208v-112h-40v128l86 86 28-28-74-74ZM480-800 243-663l237 137 237-137-237-137ZM120-321v-318q0-22 10.5-40t29.5-29l280-161q10-5 19.5-8t20.5-3q11 0 21 3t19 8l280 161q19 11 29.5 29t10.5 40v159h-80v-116L479-434 200-596v274l240 139v92L160-252q-19-11-29.5-29T120-321ZM720 0q-83 0-141.5-58.5T520-200q0-83 58.5-141.5T720-400q83 0 141.5 58.5T920-200q0 83-58.5 141.5T720 0ZM480-491Z"/></svg>
                    </div>
                @elseif($estado == 'Confirmado')
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-500">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M620-163 450-333l56-56 114 114 226-226 56 56-282 282Zm220-397h-80v-200h-80v120H280v-120h-80v560h240v80H200q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h167q11-35 43-57.5t70-22.5q40 0 71.5 22.5T594-840h166q33 0 56.5 23.5T840-760v200ZM480-760q17 0 28.5-11.5T520-800q0-17-11.5-28.5T480-840q-17 0-28.5 11.5T440-800q0 17 11.5 28.5T480-760Z"/></svg>
                    </div>
                @elseif($estado == 'Entregado')
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-500">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M240-160q-50 0-85-35t-35-85H40v-440q0-33 23.5-56.5T120-800h560v160h120l120 160v200h-80q0 50-35 85t-85 35q-50 0-85-35t-35-85H360q0 50-35 85t-85 35Zm0-80q17 0 28.5-11.5T280-280q0-17-11.5-28.5T240-320q-17 0-28.5 11.5T200-280q0 17 11.5 28.5T240-240ZM120-360h32q17-18 39-29t49-11q27 0 49 11t39 29h272v-360H120v360Zm600 120q17 0 28.5-11.5T760-280q0-17-11.5-28.5T720-320q-17 0-28.5 11.5T680-280q0 17 11.5 28.5T720-240Zm-40-200h170l-90-120h-80v120ZM360-540Z"/></svg>                    
                    </div>
                @elseif($estado == 'Listo para entregar')
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-500">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M600-800H360v280h240v-280Zm200 0H680v280h120v-280ZM575-440H320v240h222q21 0 40.5-7t35.5-21l166-137q-8-8-18-12t-21-6q-17-3-33 1t-30 15l-108 87H400v-80h146l44-36q5-3 7.5-8t2.5-11q0-10-7.5-17.5T575-440Zm-335 0h-80v280h80v-280Zm40 0v-360q0-33 23.5-56.5T360-880h440q33 0 56.5 23.5T880-800v280q0 33-23.5 56.5T800-440H280ZM240-80h-80q-33 0-56.5-23.5T80-160v-280q0-33 23.5-56.5T160-520h415q85 0 164 29t127 98l27 41-223 186q-27 23-60 34.5T542-120H309q-11 18-29 29t-40 11Z"/></svg>
                    </div>
                @elseif($estado == 'Cancelado')
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-500">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-800 243-663l237 137 237-137-237-137ZM120-321v-318q0-22 10.5-40t29.5-29l280-161q10-5 19.5-8t20.5-3q11 0 21 3t19 8l280 161q19 11 29.5 29t10.5 40v159h-80v-116L479-434 200-596v274l240 139v92L160-252q-19-11-29.5-29T120-321ZM720-80q8 0 14-6t6-14q0-8-6-14t-14-6q-8 0-14 6t-6 14q0 8 6 14t14 6Zm-20-80h40v-160h-40v160ZM720 0q-83 0-141.5-58.5T520-200q0-83 58.5-141.5T720-400q83 0 141.5 58.5T920-200q0 83-58.5 141.5T720 0ZM480-491Z"/></svg>
                    </div>
                @endif


                <div class="mt-5 flex items-end justify-between">
                    <div>
                        <span class="text-sm text-black">
                            Órdenes <b>({{ $estado }})</b>
                        </span>
                        <h4 class="mt-2 font-bold text-black">
                            {{ $info["segundo_mes"] }}                         
                        </h4>
                    </div>

                    @if (in_array($estado, ['Confirmado', 'Cancelado','Entregado']))
                        <span class="bg-white flex items-center gap-1 rounded-full  py-0.5 pl-2 pr-2.5 font-bold text-xs">
                            <!-- No mostrar tendencia para estos estados -->
                        
                            @if ($info["tendencia"] == 'subida')
                                <svg class="fill-current text-green-500" width="10" height="10" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M5.56462 1.62393C5.70193 1.47072 5.90135 1.37432 6.12329 1.37432C6.1236 1.37432 6.12391 1.37432 6.12422 1.37432C6.31631 1.37415 6.50845 1.44731 6.65505 1.59381L9.65514 4.5918C9.94814 4.88459 9.94831 5.35947 9.65552 5.65246C9.36273 5.94546 8.88785 5.94562 8.59486 5.65283L6.87329 3.93247L6.87329 10.125C6.87329 10.5392 6.53751 10.875 6.12329 10.875C5.70908 10.875 5.37329 10.5392 5.37329 10.125L5.37329 3.93578L3.65516 5.65282C3.36218 5.94562 2.8873 5.94547 2.5945 5.65248C2.3017 5.35949 2.30185 4.88462 2.59484 4.59182L5.56462 1.62393Z" fill=""></path>
                                </svg>
                            @elseif($info["tendencia"] == 'estable')
                                <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#000"><path d="M160-280v-120h640v120H160Zm0-280v-120h640v120H160Z"/></svg>
                            @else
                                <svg class="fill-current text-red-500" width="10" height="10" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M5.31462 10.3761C5.45194 10.5293 5.65136 10.6257 5.87329 10.6257C5.8736 10.6257 5.8739 10.6257 5.87421 10.6257C6.0663 10.6259 6.25845 10.5527 6.40505 10.4062L9.40514 7.4082C9.69814 7.11541 9.69831 6.64054 9.40552 6.34754C9.11273 6.05454 8.63785 6.05438 8.34486 6.34717L6.62329 8.06753L6.62329 1.875C6.62329 1.46079 6.28751 1.125 5.87329 1.125C5.45908 1.125 5.12329 1.46079 5.12329 1.875L5.12329 8.06422L3.40516 6.34719C3.11218 6.05439 2.6373 6.05454 2.3445 6.34752C2.0517 6.64051 2.05185 7.11538 2.34484 7.40818L5.31462 10.3761Z" fill=""></path>
                                </svg>
                            @endif
                            {{ $info["porcentaje"] }}%
                        </span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Seccion de tabla -->
    <div class="flex flex-col mt-8 p-2 sm:p-4 bg-white font-sans antialiased rounded-xl shadow-2xl">
        <!-- Seccion de filtros -->
        <div class="mb-2">
            <!-- Seccion de filtro -->
            <div class="flex justify-center flex-wrap gap-x-4 gap-y-6">
                <div x-data="{ 
                    // Obtener la fecha actual para inicializar los valores
                    today: new Date(),
                    selectedYear: '', 
                    selectedMonth: '', 
                    dayStart: '1', 
                    dayEnd: '' 

                }"
                x-init="
                    // 1. Inicializar Año y Mes
                    selectedYear = today.getFullYear().toString(); 
                    // El mes se inicializa con el formato '01' a '12'
                    selectedMonth = (today.getMonth() + 1).toString().padStart(2, '0');
                    // 2. Inicializar Día Final con el día actual
                    dayEnd = today.getDate().toString();
                " class="flex flex-col sm:flex-row gap-3 items-center p-2 rounded-lg border bg-black">
                    
                    <label class="text-sm font-medium text-white">Fecha:</label>

                    <div>
                        <label for="filter-year" class="sr-only">Año</label>
                        <input 
                            type="number" 
                            id="filter-year" 
                            placeholder="Año" 
                            min="2022" 
                            max="2200"
                            step="0000" 
                            x-model="selectedYear"
                            @input="$wire.filtrarOrdensPorFecha(selectedYear, selectedMonth, dayStart, dayEnd)"
                            class="w-20 p-2 border border-black rounded-lg text-sm text-gray-900 focus:ring-black focus:border-black"
                        >
                    </div>

                    <div>
                        <label for="filter-month" class="sr-only">Mes</label>
                        <select 
                            id="filter-month" 
                            x-model="selectedMonth"
                            @change="$wire.filtrarOrdensPorFecha(selectedYear, selectedMonth, dayStart, dayEnd)"
                            class="p-2 px-6 border border-black rounded-lg text-sm text-gray-900 focus:ring-black focus:border-black"
                        >
                            <option value="01">Enero</option>
                            <option value="02">Febrero</option>
                            <option value="03">Marzo</option>
                            <option value="04">Abril</option>
                            <option value="05">Mayo</option>
                            <option value="06">Junio</option>
                            <option value="07">Julio</option>
                            <option value="08">Agosto</option>
                            <option value="09">Septiembre</option>
                            <option value="10">Octubre</option>
                            <option value="11">Noviembre</option>
                            <option value="12">Diciembre</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <label for="day-start" class="text-sm font-medium text-white">Día:</label>
                        <input 
                            type="number" 
                            id="day-start" 
                            placeholder="Inicio" 
                            min="1" 
                            max="31"
                            x-model="dayStart"
                            @input="$wire.filtrarOrdensPorFecha(selectedYear, selectedMonth, dayStart, dayEnd)"
                            class="w-20 p-2 border border-black rounded-lg text-sm text-gray-900 focus:ring-black focus:border-black"
                        >
                        <span class="text-sm text-white">-</span>
                        <input 
                            type="number" 
                            id="day-end" 
                            placeholder="Fin" 
                            min="1" 
                            max="31"
                            x-model="dayEnd"
                            @input="$wire.filtrarOrdensPorFecha(selectedYear, selectedMonth, dayStart, dayEnd)"
                            class="w-20 p-2 border border-black rounded-lg text-sm text-gray-900 focus:ring-black focus:border-black"
                        >
                    </div>
                </div>

            
                <div class="flex items-center justify-center gap-2 flex-wrap">
                    <!-- boton ordenar Por Verificar Confirmado Cancelado-->
                    <div x-data="{ open: false, currentSort: '' }" @click.away="open = false" class="relative inline-block text-left">
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
                                <a href="#"  wire:click.prevent="ordenarPagos('','estado')"  @click="currentSort = ''; open = false" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"  role="menuitem"  tabindex="-1">Todos</a>
                                <a href="#"  wire:click.prevent="ordenarPagos('Por Verificar','estado')"  @click="currentSort = 'Por Verificar'; open = false" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"  role="menuitem"  tabindex="-1">Por Verificar</a>
                                <a href="#"  wire:click.prevent="ordenarPagos('Confirmado','estado')"  @click="currentSort = 'Confirmado'; open = false" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"  role="menuitem"  tabindex="-1">Confirmado</a>
                                <a href="#"  wire:click.prevent="ordenarPagos('Cancelado','estado')"  @click="currentSort = 'Cancelado'; open = false" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"  role="menuitem"  tabindex="-1">Cancelado</a>
                                <a href="#"  wire:click.prevent="ordenarPagos('Listo para entregar','estado')"  @click="currentSort = 'Listo para entregar'; open = false" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"  role="menuitem"  tabindex="-1">Listo para entregar</a>
                                <a href="#"  wire:click.prevent="ordenarPagos('Entregado','estado')"  @click="currentSort = 'Entregado'; open = false" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"  role="menuitem"  tabindex="-1">Entregado</a>
                            </div>
                        </div>
                    </div>

                    <!-- boton ordenar Por metodo de pago-->
                    <div x-data="{ open: false, currentSort: '' }" @click.away="open = false" class="relative inline-block text-left">
                        <div class="flex gap-3">
                            <label for="" class="text-sm font-medium text-gray-700 w-fit">Método Pago:</label>
                            <button @click="open = !open" type="button" id="menu-button" aria-expanded="true" aria-haspopup="true" title="Filtrar por Método:"
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
                                <a href="#"  wire:click.prevent="ordenarPagos('','metodo')"  @click="currentSort = ''; open = false" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"  role="menuitem"  tabindex="-1">Todos los Métodos</a>
                                <a href="#"  wire:click.prevent="ordenarPagos('pago-movil','metodo')"  @click="currentSort = 'pago-movil'; open = false" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"  role="menuitem"  tabindex="-1">Pago Móvil</a>
                                <a href="#"  wire:click.prevent="ordenarPagos('zelle','metodo')"  @click="currentSort = 'zelle'; open = false" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"  role="menuitem"  tabindex="-1">Zelle</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Mensaje de error de filtro -->
        @if (session()->has('errorFiltro'))
            <div class="my-2 mx-4 p-4 text-sm text-red-800 rounded-lg bg-red-100" role="alert">
                <span class="font-medium">Error de Filtro:</span> 
                {!! session('errorFiltro') !!}
            </div>
        @endif
        @if (session()->has('errorPedido'))
            <div class="my-2 mx-4 p-4 text-sm text-red-800 rounded-lg bg-red-100" role="alert">
                <span class="font-medium">Error:</span> 
                {!! session('errorPedido') !!}
            </div>
        @endif

        <!-- Tabla de gestion de ordenes/pagos y modal -->
        <div class="grid grid-cols-1 p-4 bg-white overflow-x-auto" x-data="gestionOrdenes()">
            {{-- Campo de Búsqueda, Controles de Filtro y Paginación --}}
            <div class="mb-4 flex flex-row flex-wrap justify-between items-center space-y-4 md:space-y-0 col-span-1">
                <input 
                    wire:model.live.debounce.600ms="campoBusqueda" 
                    type="text" 
                    placeholder="Buscar..."
                    class="w-full md:w-1/3 px-4 py-2 border border-gray-300 rounded-md focus:ring-[#daadaf] focus:border-[#e8a3a3] shadow-sm"
                >
            
                <div class="flex items-center ml-4">
                    {{-- Selector de Elementos por Página --}}
                    <label for="ordenMostrar" class="text-sm text-gray-700 mr-2"><b>Mostrar:</b></label>
                    <select 
                        wire:model.live="ordenMostrar" 
                        id="ordenMostrar"
                        class="pr-8 py-1 border border-gray-300 rounded-md text-sm focus:ring-[#daadaf] focus:border-[#e8a3a3]"
                    >
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="25">25</option>
                        <option value="30">30</option>
                        <option value="40">40</option>
                        <option value="50">50</option>
                    </select>
                    <span class="ml-2 text-sm text-gray-700"><b>Órdenes</b></span>
                </div>
            </div>

            @if (!empty($listaOrdenes))
                <table class="w-full min-w-full divide-y divide-gray-200 shadow-lg col-span-1">
                    <!-- Encabezado de la tabla -->
                    <thead class="bg-black text-white">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-medium border uppercase tracking-wider">Fecha (Pedido)</th>
                            <th class="px-6 py-3 text-center text-xs font-medium border uppercase tracking-wider">Cliente</th>
                            <th class="px-6 py-3 text-center text-xs font-medium border uppercase tracking-wider">Inf. Pedido</th>
                            <th class="px-6 py-3 text-center text-xs font-medium border uppercase tracking-wider">Inf. Pago</th>
                            <!-- <th class="px-6 py-3 text-center text-xs font-medium border uppercase tracking-wider">Restante</th> -->
                            <th class="px-6 py-3 text-center text-xs font-medium border uppercase tracking-wider">Deposito</th>
                            <th class="px-6 py-3 text-center text-xs font-medium border uppercase tracking-wider">Edo. Pedido</th>
                        </tr>
                    </thead>

                    <!-- Cuerpo de la tabla -->
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($listaOrdenes as $key => $orden)
                            @php
                                $infoG = $orden["infoGeneral"]; // Capturo la informacion general para mostrar en la tabla
                                $infoCli = $orden["cliente_y_orden"];  // Capturo la informacion del cliente para mostrar en el modal cliente
                                $infoProd = $orden["detalle_pedido"];  // Capturo la informacion del pedido para mostrar en el modal de detalle productos
                                $modalData = ['detalle' => $infoProd]; // Creamos un objeto PHP para luego pasarlo como json
                                $infoReporte = $orden["reportes_pago"]; // Capturo la informacion de los reportes de pago del cliente para mostrarlo en el modal.
                                $modalDataR = ['detalleR' => $infoReporte]; // Creamos un objeto PHP para luego pasarlo como json
                            @endphp

                            <tr class="hover:bg-gray-50">
                                {{-- Columna fecha --}}
                                <td class="py-2 text-sm text-gray-700 border border-gray-300">
                                    <p class="text-center">{{ $infoG["fecha_pedido"] }}</p>
                                </td>
                                {{-- Columna nombre cliente --}}
                                <td class="py-2 text-sm text-gray-700 border border-gray-300 min-w-30">
                                    <div class="flex pl-2 capitalize">
                                        <button @click="openModal('{{$infoCli["cedula_cliente"]}}','{{$infoCli["nombre_cliente"]}}','{{ $infoCli["email_cliente"] }}','{{ $infoCli["telefono_cliente"] }}','{{ $infoCli["direccion_cliente"] }}')" data-modal-toggle="cliente-info-modal"
                                        class="cursor-pointer" data-modal-target="cliente-info-modal"  class="focus:outline-none" title="ver Información del cliente">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="#004084" class="icon icon-tabler icons-tabler-filled icon-tabler-info-square-rounded hover:scale-110 transition-transform">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 2l.642 .005l.616 .017l.299 .013l.579 .034l.553 .046c4.687 .455 6.65 2.333 7.166 6.906l.03 .29l.046 .553l.041 .727l.006 .15l.017 .617l.005 .642l-.005 .642l-.017 .616l-.013 .299l-.034 .579l-.046 .553c-.455 4.687 -2.333 6.65 -6.906 7.166l-.29 .03l-.553 .046l-.727 .041l-.15 .006l-.617 .017l-.642 .005l-.642 -.005l-.616 -.017l-.299 -.013l-.579 -.034l-.553 -.046c-4.687 -.455 -6.65 -2.333 -7.166 -6.906l-.03 -.29l-.046 -.553l-.041 -.727l-.006 -.15l-.017 -.617l-.004 -.318v-.648l.004 -.318l.017 -.616l.013 -.299l.034 -.579l.046 -.553c.455 -4.687 2.333 -6.65 6.906 -7.166l.29 -.03l.553 -.046l.727 -.041l.15 -.006l.617 -.017c.21 -.003 .424 -.005 .642 -.005zm0 9h-1l-.117 .007a1 1 0 0 0 0 1.986l.117 .007v3l.007 .117a1 1 0 0 0 .876 .876l.117 .007h1l.117 -.007a1 1 0 0 0 .876 -.876l.007 -.117l-.007 -.117a1 1 0 0 0 -.764 -.857l-.112 -.02l-.117 -.006v-3l-.007 -.117a1 1 0 0 0 -.876 -.876l-.117 -.007zm.01 -3l-.127 .007a1 1 0 0 0 0 1.986l.117 .007l.127 -.007a1 1 0 0 0 0 -1.986l-.117 -.007z" />
                                            </svg>
                                        </button>
                                        <b class="p-1">{{ $infoG["nombre_cliente"] }}</b>
                                    </div>
                                </td>
                                {{-- Columna informacion del pedido --}}
                                <td class="py-2 px-1 text-sm text-gray-700 text-center border border-gray-300 min-w-[120px] max-w-[150px]">
                                    <div class="flex flex-col justify-center items-center space-y-1">
                                        
                                        <div class="w-full text-center">
                                            <span class="text-xs text-gray-500 block">ID Pedido:</span>
                                            <div class="overflow-x-auto whitespace-nowrap py-1 custom-scrollbar">
                                                <b class="bg-[#3b589826] px-1 rounded text-xs">
                                                    #{{ $infoProd["orden_id"] }}
                                                </b>
                                            </div>
                                        </div>

                                        <button @click="modalPedido(@js($modalData))"  data-modal-toggle="pedido-info-modal"  data-modal-target="pedido-info-modal"
                                            class="cursor-pointer text-white bg-[#3b5998] hover:bg-[#3b5998]/90 focus:ring-4 focus:outline-none focus:ring-[#3b5998]/50 font-medium leading-5 rounded-xl text-xs px-3 py-2 text-center inline-flex items-center" 
                                            title="Ver Información del pedido">
                                            <svg class="me-1.5 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 4h1.5L9 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm-8.5-3h9.25L19 7H7.312"/>
                                            </svg>
                                            Ver Pedido
                                        </button>
                                    </div>
                                </td>

                                {{-- Columna informacion del pago --}}
                                <td class="py-2 text-sm text-gray-700 border border-gray-300 min-w-30 text-center">
                                    <button 
                                    wire:click="verModalReporte({{ $key }})"
                                    wire:target="verModalReporte({{ $key }})"
                                    wire:loading.attr="disabled"
                                    wire:loading.class="opacity-50 cursor-not-allowed"
                                    @click="modalReporte(@js($modalDataR))"  data-modal-target="reporte-pago-modal"
                                    class="cursor-pointer text-white bg-green-700 hover:bg-green-700/90 focus:ring-4 focus:outline-none focus:ring-green-700/50 box-border border border-transparent font-medium leading-5 rounded-base text-sm px-4 py-2.5 text-center inline-flex items-center rounded-xl"  title="Ver Información del pago">
                                        <span class="flex flex-row" wire:loading.remove wire:target="verModalReporte({{ $key }})">
                                            <svg class="me-1.5 -ms-0.5 inline" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-cash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 15h-3a1 1 0 0 1 -1 -1v-8a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v3" />
                                                <path d="M7 9m0 1a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v8a1 1 0 0 1 -1 1h-12a1 1 0 0 1 -1 -1z" /><path d="M12 14a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                            </svg>
                                            <span class="inline">Ver Pagos ({{ count($infoReporte) }})</span>
                                        </span>

                                        <span wire:loading wire:target="verModalReporte({{ $key }})">
                                            Cargando...
                                        </span>
                                    </button>
                                </td>

                                <td class="py-2 text-sm text-gray-700 border border-gray-300 text-center">
                                    <b class="p-1">
                                        <span class="py-1 px-2 rounded-full"
                                            :class="{ 
                                                'bg-green-100 text-green-900': '{{ $infoG["estado_deposito"] }}' === '100%',
                                            }">
                                            {{ $infoG["estado_deposito"] }}
                                        </span>
                                    </b>
                                </td>

                                
                                {{-- Columna estado pedido (Combinada con Estado y Acciones Ocultables) --}}
                                <td class="py-2 text-sm text-gray-700 border border-gray-300 text-center relative" x-data="{ openActions: false }">
                                    <div class="flex flex-row items-center justify-center space-x-2">
                                        
                                        {{-- 1. Burbuja de Estado --}}
                                        <span class="py-1 px-2 rounded-full text-sm font-bold"
                                            :class="{ 
                                                'bg-green-100 text-green-800': '{{ $infoG['estado_pedido'] }}' === 'Confirmado',
                                                'bg-yellow-100 text-yellow-800': '{{ $infoG['estado_pedido'] }}' === 'Por Verificar',
                                                'bg-red-100 text-red-800': '{{ $infoG['estado_pedido'] }}' === 'Cancelado',
                                                'bg-blue-100 text-blue-800': '{{ $infoG['estado_pedido'] }}' === 'Entregado' || '{{ $infoG['estado_pedido'] }}' === 'Listo para entregar'
                                            }">
                                            {{ $infoG["estado_pedido"] }}
                                        </span>

                                        {{-- 2. Botón para abrir el menú --}}
                                        @if ($infoG["estado_pedido"] !== 'Entregado')
                                            <button 
                                                @click="openActions = !openActions" {{-- Alternar la visibilidad al hacer clic --}}
                                                class="cursor-pointer text-gray-700 hover:text-indigo-800 transition-colors"
                                                title="Mostrar/Ocultar Acciones">
                                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" />
                                                </svg>
                                            </button>
                                        @endif

                                        {{-- 3. Contenedor de Acciones DINÁMICO --}}
                                        <div 
                                            x-show="openActions"
                                            x-cloak
                                            @click.away="openActions = false" {{-- Ocultar si se hace clic fuera del contenedor --}}
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="opacity-0 scale-95"
                                            x-transition:enter-end="opacity-100 scale-100"
                                            class="absolute z-50 right-0 mt-2 w-55 border border-gray-200 rounded-lg shadow-xl p-2 flex flex-col gap-2 top-10 text-sm bg-white"
                                        >
                                            {{-- CASO: SI ES 'CONFIRMADO' --}}
                                            @if ($infoG["estado_pedido"] === 'Listo para entregar')
                                                <button wire:click="editarPedido({{ $infoProd['orden_id'] }}, 'Entregado')" class="text-left  bg-green-700 text-white px-3 py-2 rounded hover:bg-green-700/90 cursor-pointer">
                                                    ✅ Entregado
                                                </button>
                                            @elseif ($infoG["estado_pedido"] === 'Confirmado')
                                                <button wire:click="editarPedido({{ $infoProd['orden_id'] }}, 'Listo para entregar')" class="text-left  bg-blue-500 text-white px-3 py-2 rounded hover:bg-blue-600 cursor-pointer">
                                                    📦 Listo para Entregar
                                                </button>
                                                <button wire:click="editarPedido({{ $infoProd['orden_id'] }}, 'Entregado')" class="text-left  bg-green-700 text-white px-3 py-2 rounded hover:bg-green-700/90 cursor-pointer">
                                                    ✅ Entregado
                                                </button>
                                                <button wire:click="editarPedido({{ $infoProd['orden_id'] }}, 'Por Verificar')" class="text-left  bg-yellow-500 text-white px-3 py-2 rounded hover:bg-yellow-600 cursor-pointer">
                                                    🔄 Revertir
                                                </button>

                                            {{-- CASO: SI ES 'POR VERIFICAR' --}}
                                            @elseif ($infoG["estado_pedido"] === 'Por Verificar')
                                                <button wire:click="editarPedido({{ $infoProd['orden_id'] }}, 'Confirmado')" class="text-left  bg-green-700 text-white px-3 py-2 rounded hover:bg-green-700/90 cursor-pointer">
                                                    ✔️ Confirmar Pedido
                                                </button>
                                                <button wire:click="editarPedido({{ $infoProd['orden_id'] }}, 'Cancelado')" class="text-left  bg-red-500 text-white px-3 py-2 rounded hover:bg-red-600 cursor-pointer">
                                                    ❌ Cancelar
                                                </button>

                                            {{-- CASO: SI ES 'CANCELADO' --}}
                                            @elseif ($infoG["estado_pedido"] === 'Cancelado')
                                                <button wire:click="editarPedido({{ $infoProd['orden_id'] }}, 'Por Verificar')" class="text-left  bg-yellow-500 text-white px-3 py-2 rounded hover:bg-yellow-600 cursor-pointer">
                                                    🔄 Revertir
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            {{-- Fila separadora entre orden --}}
                            <tr class="h-6 bg-gray-100/50"><td colspan="6"></td></tr>
                        @endforeach
                    </tbody>

                    {{-- Sección de Totales --}}
                    <tfoot>
                        <tr>
                            <td colspan="6">
                                {{-- Controles de Paginación y Conteo --}}
                                @if ($totalItems > $ordenMostrar)
                                    <div class="flex flex-row justify-between items-center px-4 py-3 bg-white border-t border-gray-200 sm:px-6">
                                        {{-- Conteo de Elementos --}}
                                        <div class="mb-2 md:mb-0 text-sm text-gray-700">
                                            Mostrando 
                                            <span class="font-medium">{{ ($currentPage - 1) * $ordenMostrar + 1 }}</span> a
                                            <span class="font-medium">{{ min($currentPage * $ordenMostrar, $totalItems) }}</span> de
                                            <span class="font-medium">{{ $totalItems }}</span> Órdenes.
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
                                                    @class([
                                                        'relative inline-flex items-center px-4 py-2 border text-sm font-medium cursor-pointer',
                                                        'bg-black text-white border-[#daadaf]' => $page == $currentPage,
                                                        'bg-white border-gray-300 text-gray-700 hover:bg-gray-50' => $page != $currentPage
                                                    ])
                                                >
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
                    {{ $campoBusqueda ? 'No se encontraron resultados para: "' . $campoBusqueda . '"' : 'La lista está vacía o no se encontraron Órdenes.' }}
                </p>    
            @endif

            <!-- Modal informacion del cliente -->
            <x-modal-cliente></x-modal-cliente>
            <!-- Modal informacion del pedido -->
            <x-modal-pedidos></x-modal-pedidos>
            <!-- Modal informacion del reportes de pago -->
            <div tabindex="-1" aria-hidden="true"
                wire:ignore.self
                id="reporte-pago-modal" 
                x-data="reporteModal()"
                class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full"
            >
                <div class="relative p-4 w-full max-w-xl max-h-full">
                    <!-- Modal content -->
                    <div class="relative bg-gray-200 border border-default rounded-lg shadow-sm p-4 md:p-6">
                        <!-- Modal header -->
                        <div class="flex items-center justify-between border-b border-gray-300 pb-2 md:pb-3">
                            <h3 class="text-lg font-extrabold text-gray-900 dark:text-white">
                                <span class="flex text-transparent bg-clip-text bg-linear-to-r to-gray-700 from-black">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="#000" class="icon icon-tabler icons-tabler-filled mr-1 icon-tabler-clipboard-data"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17.997 4.17a3 3 0 0 1 2.003 2.83v12a3 3 0 0 1 -3 3h-10a3 3 0 0 1 -3 -3v-12a3 3 0 0 1 2.003 -2.83a4 4 0 0 0 3.997 3.83h4a4 4 0 0 0 3.98 -3.597zm-8.997 7.83a1 1 0 0 0 -1 1v4a1 1 0 0 0 2 0v-4a1 1 0 0 0 -1 -1m3 3a1 1 0 0 0 -1 1v1a1 1 0 0 0 1 1l.117 -.007a1 1 0 0 0 .883 -.993v-1a1 1 0 0 0 -1 -1m3 -1a1 1 0 0 0 -1 1v2a1 1 0 0 0 2 0v-2a1 1 0 0 0 -1 -1m-1 -12a2 2 0 1 1 0 4h-4a2 2 0 1 1 0 -4z" /></svg>
                                    Reportes de Pago
                                </span>
                            </h3>
                            <button type="button" title="Cerrar modal" wire:click="closeModal" class="text-body bg-transparent hover:bg-neutral-tertiary hover:text-heading rounded-base text-sm w-9 h-9 ms-auto inline-flex justify-center items-center text-gray-600 hover:bg-gray-200 hover:text-gray-900 hover:cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span class="sr-only">Cerrar modal</span>
                            </button>
                        </div>
                        
                        <!-- Modal body -->
                        <div class="space-y-4 py-4">
                            <!-- MSM DE EXITO -->
                            @if (session()->has('paymentConfirmed'))
                                <div x-data="{ show: true }"
                                    x-show="show" 
                                    x-init="setTimeout(() => show = false, 7000)" 
                                    x-transition:leave.duration.500ms
                                    class="p-4 mb-4 text-green-700 bg-green-100 rounded-sm flex" 
                                    role="alert">
                                    
                                    {{ session('paymentConfirmed') ?? "Operación exitosa."}}
                                    
                                    <button @click="show = false" class="ml-auto focus:outline-none">&times;</button>
                                </div>
                            @else 
                                <!-- Seccion copiar enlace -->
                                <template x-if="modalReporteData?.detalleR?.[0]?.token">
                                    <div class="bg-white border border-gray-300 rounded-lg overflow-hidden shadow-sm mt-4">
                                        <div class="flex items-stretch">
                                            <div class="bg-gray-100 px-3 flex items-center text-xs font-bold text-gray-600 border-r">
                                                Enlace Pago
                                            </div>

                                            <input type="text" 
                                                readonly 
                                                :value="'{{ route('pago', ['token' => 'TEMP_TOKEN']) }}'.replace('TEMP_TOKEN', modalReporteData.detalleR[0].token)"
                                                class="grow p-3 text-sm border-none focus:ring-0">

                                            <button type="button" 
                                                @click="copyToken('{{ route('pago', ['token' => 'TEMP_TOKEN']) }}'.replace('TEMP_TOKEN', modalReporteData.detalleR[0].token))" 
                                                class="flex items-center justify-center p-3 transition duration-150 border-l border-gray-300 relative cursor-pointer"
                                                :class="copied ? 'text-green-500 bg-green-50' : 'text-gray-500 hover:bg-gray-50'">
                                                
                                                <svg x-show="!copied" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" />
                                                    <path d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z" />
                                                </svg>

                                                <svg x-show="copied" style="display: none;" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 13.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>

                                                <span x-show="copied" style="display: none;" class="ml-1 text-xs font-bold">¡Copiado!</span>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                                
                                <!-- Seccion de orden de pago -->
                                <div class="space-y-3">
                                    <template x-for="(reporte, index) in modalReporteData.detalleR" :key="reporte.id">
                                        <div class="border border-gray-300 rounded-lg bg-white overflow-hidden shadow-sm">
                                            <button @click="openAccordion = (openAccordion === index ? null : index)"
                                                    class="flex justify-between items-center w-full p-4 hover:bg-gray-50 transition cursor-pointer">
                                                <span class="font-bold text-gray-800">
                                                    Reporte #<span x-text="index+1"></span>: 
                                                    <span class="font-normal uppercase" x-text="reporte.metodo"></span>
                                                </span>
                                                <div class="flex items-center">
                                                    <span :class="getStatusClasses(reporte.estado)" class="text-xs font-semibold px-2 py-0.5 rounded-full" x-text="reporte.estado"></span>
                                                    <svg class="w-4 h-4 ml-2 transition-transform" :class="{'rotate-180': openAccordion === index}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
                                                </div>
                                            </button>

                                            <div x-show="openAccordion === index" x-collapse>
                                                <div class="p-4 border-t border-gray-200 bg-gray-50 space-y-2 text-sm">
                                                    <p><b>Fecha:</b> <span x-text="`${reporte.fecha_reporte[0]} ${reporte.fecha_reporte[1]}`"></span></p>
                                                    <p><b>Tasa De Cambio:</b> <span x-text="Number(reporte.tasa).toFixed(2) + ' Bs'"></span></p>
                                                    <p><b>Monto ($):</b> <span x-text="'$' + Number(reporte.monto).toFixed(2)"></span></p>
                                                    <p><b>Monto (Bs):</b> <span x-text="Number(Number(reporte.monto).toFixed(2) * Number(reporte.tasa).toFixed(2)).toFixed(2) + ' Bs'"></span></p>
                                                    <p><b>Referencia:</b> <span x-text="reporte.referencia"></span></p>

                                                    <div class="flex flex-wrap gap-2 pt-3">
                                                        <a :href="'/ver-comprobante/' + reporte.imagen_referencia" target="_blank" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm cursor-pointer">Ver Comprobante</a>

                                                        <template x-if="modalReporteData?.detalleR?.[0]?.orden_estado === 'Por Verificar'">
                                                            <div x-show="reporte.estado === 'Pendiente'" class="space-x-1">
                                                                <!-- Boton de confirmar pago -->
                                                                <button type="button" @click="handleAction(reporte, 'confirmar')" 
                                                                    :disabled="reporte.estado !== 'Pendiente' || loadingReportId === reporte.id"
                                                                    :class="reporte.estado === 'Pendiente' ? 'bg-green-600' : 'bg-gray-400'"
                                                                    class="inline-flex text-white px-4 py-2 rounded-md text-sm cursor-pointer">

                                                                    <template x-if="loadingReportId === reporte.id">
                                                                        <svg class="animate-spin h-4 w-4 mr-2 text-white" viewBox="0 0 24 24">
                                                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                                        </svg>
                                                                    </template>

                                                                    <span x-text="loadingReportId === reporte.id ? 'Procesando...' : 'Confirmar Pago'"></span>
                                                                </button>

                                                                <!-- Boton de Rechazar pago -->
                                                                <button type="button" @click="handleAction(reporte, 'rechazar')" 
                                                                    :disabled="reporte.estado !== 'Pendiente' || loadingReportId === reporte.id"
                                                                    :class="reporte.estado === 'Pendiente' ? 'bg-red-600' : 'bg-gray-400'"
                                                                    class="inline-flex text-white px-4 py-2 rounded-md text-sm cursor-pointer">
                                                                    <template x-if="loadingReportId === reporte.id">
                                                                        <svg class="animate-spin h-4 w-4 mr-2 text-white" viewBox="0 0 24 24">
                                                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                                        </svg>
                                                                    </template>
                                                                    <span x-text="loadingReportId === reporte.id ? 'Procesando...' : 'Rechazar Pago'"></span>
                                                                </button>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                <!-- Mensaje de error -->
                                @if (session()->has('errorPayment'))
                                    <div x-data="{ show: true }"
                                        x-show="show" 
                                        x-transition:leave.duration.500ms
                                        class="p-4 mb-4 text-red-700 bg-red-100 rounded-sm flex" 
                                        role="alert">
                                        
                                        {{ session('errorPayment') ?? "Ocurrió un error. Inténtalo de nuevo." }} 
                                        
                                        <button @click="show = false" class="ml-auto focus:outline-none">&times;</button>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function gestionOrdenes() {
        return {
            modalData: {cedula: '', nombre: '', correo: '', telefono: '', direccion: ''},
            modalPedidoData: { detalle: [] },
            modalReporteData: { detalleR: [] },

            openModal(cedula, nombre, correo, telefono, direccion) {
                this.modalData = { cedula, nombre, correo, telefono, direccion };
            },

            modalPedido(data) {
                this.modalPedidoData = data; 
            },

            modalReporte(data) {
                this.modalReporteData = data; 
            }
        }
    }

    function reporteModal() {
        return {
            openAccordion: null,
            copied: false,
            loadingReportId: null, // rastrea qué reporte se está procesando


            copyToken(id) {
                navigator.clipboard.writeText(id);
                this.copied = true;
                setTimeout(() => this.copied = false, 2000);
            },

            getStatusClasses(estado) {
                return {
                    'bg-green-100 text-green-800': estado === 'Verificado',
                    'bg-yellow-100 text-yellow-800': estado === 'Pendiente',
                    'bg-red-100 text-red-800': estado === 'Rechazado'
                };
            },

            async handleAction(reporte, tipo) {
                const msj = tipo === 'confirmar' 
                    ? '¿Estás seguro de que deseas CONFIRMAR este pago?' 
                    : '¿Estás seguro de que deseas RECHAZAR este pago?';

                
                if (confirm(msj)) {
                    this.loadingReportId = reporte.id; // Bloqueamos el botón actual
                    const metodo = tipo === 'confirmar' ? 'confirmarPago' : 'rechazarPago';
                    
                    try {
                        // Llamada a Livewire
                        await this.$wire.call(metodo, reporte.id, reporte.order_id);
                    } catch (e) {
                        console.error("Error:", e);
                    } finally {
                        this.loadingReportId = null; // Liberamos el botón
                    }
                }
            }
        }
    }
</script>
