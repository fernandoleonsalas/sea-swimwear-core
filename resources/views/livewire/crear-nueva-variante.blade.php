<div class="pb-10">
    <!-- Botones de Navegación -->
    <div class="mb-8 flex justify-center space-x-4 flex-wrap">
        <button type="button" wire:click="mostrarSeccion('producto_base')"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors cursor-pointer
            {{ $seccionContenido == 'producto_base' ? 'bg-[#e8a3a3] text-black' : 'bg-gray-300 text-black hover:bg-[#e8a3a3a7]' }}">
            Producto Base
        </button>

        <button type="button" wire:click="mostrarSeccion('variantes')" {{ empty($productElegido) ? 'disabled' : '' }}
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors cursor-pointer 
            {{ $seccionContenido == 'variantes' ? 'bg-[#e8a3a3] text-black' : 'bg-gray-300 text-black hover:bg-[#e8a3a3a7]' }}">
            Variantes
        </button>
    </div>

    <!-- Formulario General -->
    <form wire:submit.prevent="mostrarSeccion('{{ $seccionContenido }}','Guardar')" id="formRegistrar">
        <!-- Seccion de producto base -->
        @if ($seccionContenido == 'producto_base')
            <div class="grid grid-cols-2 gap-x-10">
                <h3 class="col-span-2 text-justify text-lg font-medium text-gray-900 mb-2">Datos del Producto Base</h3>    
                <p class="col-span-2 text-justify mb-4 text-gray-900 border-b pb-2">Por favor, seleccione el producto adecuado de la lista.</p>

                <!-- Campo de seleccionar producto -->
                <div class="col-span-2 lg:col-span-1">
                    <div 
                        wire:ignore
                        x-data="{
                            // 1. INYECTAR EL VALOR: Pasa la propiedad de Livewire a Alpine.js
                            // Usamos 'js' para convertir el array PHP a un array JavaScript.
                            initialProducto: @js($productElegido), 
                            // 2. Decralar variable Para guardar la instancia de TomSelect
                            selectInstanceProd: null, 
                            init() {
                                // 3. INICIALIZACIÓN: Crea TomSelect y usa el valor inyectado
                                this.selectInstanceProd = new TomSelect(this.$refs.selectProducto, {
                                    plugins: ['remove_button'], // Plugin para permitir eliminar etiquetas
                                    removeButton: { title: 'Eliminar esta opción' }, // Configuración el botón de eliminar
                                    items: this.initialProducto, // TomSelect se construye usando la data inyectada como su valor de inicio.
                                });
                                // 4. ESCUCHA LOS CAMBIOS  
                                // CLAVE: Cuando el valor del select cambia, forzamos a Livewire a actualizar el modelo.
                                this.selectInstanceProd.on('change', (value) => {
                                    // Envía el nuevo valor del select a la propiedad Livewire
                                    @this.set('productElegido', value);  // Alpine fuerza la actualización de la propiedad $productElegido en Livewire
                                });
                            }
                        }"
                    >
                        <label for="produtoSe" class="block font-medium text-gray-800">Producto Seleccionado</label>

                        <select wire:model.defer="productElegido" x-ref="selectProducto"  class="mt-2 block w-full rounded-md border-gray-300 shadow-sm capitalize" id="produtoSe">
                            <option value="">Seleccionar</option>
                            @foreach ($todosProductos as $producto)
                                <option value='{{ $producto->id }}'>{{ $producto->name }}</option> 
                            @endforeach
                        </select>
                    </div>
                    <!-- Mensaje de error -->
                    @error('productElegido') 
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                    @enderror
                </div>
            </div>
        @endif


        <!-- SECCION DE VARIANTES -->
        @if ($seccionContenido == 'variantes')
            <div class="space-y-4">
                <div class="">
                    <h3 class="text-justify text-lg font-medium text-gray-900 mb-2">Variantes</h3>
                    <p class=" text-justify mb-4 text-gray-900 pb-2">Agrega las variantes del producto. Puedes añadir o eliminar filas según necesites.</p>
                </div>
                
                <!-- Lista dinámica de variantes -->
                <div class="space-y-4 mt-4">
                    @foreach ($variants as $i => $variant)
                        <div wire:key="variant-{{ $i }}-{{ $variant['atributos']['color'] ?? 'x' }}-{{ $variant['atributos']['estampado'] ?? 'x' }}" class="p-4 border rounded-md bg-white">
                            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 items-end">
                                <div class="grid-cols-1 lg:col-span-2">
                                    <div class="flex row items-center">
                                        <label class="block text-sm font-medium text-gray-700 mr-2">SKU de la variante</label>
                                        <div class="relative flex group cursor-pointer">
                                            <span class="p-2 bg-gray-800 rounded-4xl">
                                                <svg class="fill-white w-3 h-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M12.432 0c1.34 0 2.01.912 2.01 1.957 0 1.305-1.164 2.512-2.679 2.512-1.269 0-2.009-.75-1.974-1.99C9.789 1.436 10.67 0 12.432 0zM8.309 20c-1.058 0-1.833-.652-1.093-3.524l1.214-5.092c.211-.814.246-1.141 0-1.141-.317 0-1.689.562-2.502 1.117l-.528-.88c2.572-2.186 5.531-3.467 6.801-3.467 1.057 0 1.233 1.273.705 3.23l-1.391 5.352c-.246.945-.141 1.271.106 1.271.317 0 1.357-.392 2.379-1.207l.6.814C12.098 19.02 9.365 20 8.309 20z"/></svg>
                                            </span>
                                            
                                            <div class="w-70 absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-1 bg-gray-800 text-white text-sm rounded-md shadow-lg hidden group-hover:block">
                                                SKU es generado en función del color y el estampado seleccionados.
                                            </div>
                                        </div>
                                    </div>

                                    <input type="text" wire:model="variants.{{ $i }}.sku" readonly class="mt-1 block w-full rounded-sm text-sm border-gray-300 p-2 shadow-sm bg-gray-50 focus:border-[#efb7b7] focus:ring-[#efb7b7]" placeholder="Ej: SKU-VAR-01">
                                    @error('variants.'.$i.'.sku') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                
                                <div class="col-span-1">
                                    <label class="block text-sm font-medium text-gray-700">Stock</label>
                                    <input type="number" step="1" min="0" wire:model.defer="variants.{{ $i }}.stock" class="mt-1 block w-full rounded-sm text-sm border-gray-300 bg-white p-2 shadow-sm focus:border-[#efb7b7] focus:ring-[#efb7b7]" placeholder="0">
                                    @error('variants.'.$i.'.stock') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div class="col-span-1">
                                    <label class="block text-sm font-medium text-gray-700">Imagen</label>
                                    <input type="file" wire:model="variants.{{ $i }}.main_image_url" accept="image/*" class="mt-1 block w-full rounded-sm  text-sm border-gray-300 bg-white p-2 shadow-sm focus:border-[#efb7b7] focus:ring-[#efb7b7]">
                                    @error('variants.'.$i.'.main_image_url') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <!-- Selects de atributos (Color, Talla, Material, etc.) -->
                            @if(!empty($allAttributes) && count($allAttributes) > 0)
                                <div class="mt-4 grid grid-cols-1 lg:grid-cols-3 gap-4" x-data="{colorSeleccionado: @js(is_numeric($variants[$i]['atributos']['color']))}">
                                    <!-- Campo de nombre color -->
                                    <div class="col-span-1">
                                        <div
                                            wire:ignore 
                                            wire:key="category-select-color{{ $select_key }}"
                                            x-data="{ 
                                                selectedOptionsColor:  @js($variants[$i]["atributos"]["color"]), 
                                                options:  @js($allAttributes["Color"]),
                                                tomSelectInstance: null // Propiedad temporal para la instancia de Tom Select
                                            }"
                                            x-init=" $nextTick(() => {
                                                const selectEl = $refs.selectElementColor; // Obtener la referencia al elemento
                                                // 1. Inicializar Tom Select con el plugin 'create','remove','change'
                                                tomSelectInstance = new TomSelect(selectEl, {
                                                    // --- Configuración de Plugins ---
                                                    plugins: ['clear_button'],
                                                    // --- Configuración de Datos ---
                                                    valueField: 'value', // El campo del objeto de opciones que se usará como valor (ID)
                                                    labelField: 'text', // El campo del objeto de opciones que se mostrará al usuario
                                                    searchField: 'text', // El campo por el cual se permite la búsqueda
                                                    options: options, // El arreglo de opciones iniciales de Alpine.js
                                                    items: selectedOptionsColor, // El arreglo de valores pre-seleccionados de Alpine.js
                                                    // --- Configuración de Creación ---
                                                    // La función se llama cuando un usuario intenta crear una opción nueva.
                                                    create: function(input, callback) {
                                                        if (input.includes('-')) {
                                                            alert('No se permiten guiones en este campo');
                                                            return false; // Bloquea la creación en el select
                                                        }
                                                        // Generar un valor temporal único para el frontend
                                                        const valorTemporal = 'temp-' + input;
                                                        // CRUCIAL: El 'value' debe ser único (aquí usamos el texto como valor).
                                                        const newOption = {
                                                            value: input, // Usar el texto escrito como valor temporal
                                                            text: input
                                                        };
                                                        // 2. Llamar al callback con el objeto para que Tom Select añada la opción a su lista y la seleccione inmediatamente.
                                                        callback(newOption);
                                                        // 3. Sincronizar el estado de Alpine manualmente
                                                        // Tom Select ya habrá actualizado el input, pero esta línea es una garantía:
                                                        selectedOptionsColor = selectEl.tomselect.getValue();
                                                        // Asegúrate de que, después del callback, también llamas a $wire.set:
                                                        setTimeout(() => {
                                                            $wire.set('variants.{{ $i }}.atributos.color', selectEl.tomselect.getValue());
                                                        }, 50);
                                                    },
                                                    
                                                    // filtro (solo permite crear si el input no está vacío)
                                                    createFilter: true, // Esto activa el botón de Add si no hay coincidencias.
                                                    // 4. Lógica de sincronización de datos de Alpine y livewire
                                                    onChange: (value) => {
                                                        // Actualiza la variable de Alpine cuando Tom Select cambia
                                                        selectedOptionsColor = [value];
                                                        // Usamos $wire.set para forzar a Livewire a actualizar
                                                        $wire.set('variants.{{ $i }}.atributos.color', value);
                                                    }
                                                });
                                            });
                                        ">
                                            <label for="my_select_color" class="block text-sm font-medium text-gray-700">Color:</label>
                                            <select x-ref="selectElementColor" id="my_select_color" wire:model="variants.{{ $i }}.atributos.color" wire:change="rebuildSku({{ $i }},'color')" class="mt-2 block w-full rounded-md border border-gray-300 focus:border-[#efb7b7] focus:ring-[#efb7b7] shadow-sm">
                                            </select>
                                        </div>
                                        @error('variants.' . $i . '.atributos.color') 
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                        @enderror   
                                    </div>
                                    <!-- Campo de codigo color -->
                                    @if (!empty($variants[$i]['atributos']['color']))
                                        <div class="col-span-1">
                                            <label for="color_code_{{ $i }}" class="block text-sm font-medium text-gray-700">Selector de Color (Código Hex):</label>
                                            <input type="color" id="color_code_{{ $i }}"  title="Seleccionar Código de Color" class="mt-1 block w-full  h-10 rounded-sm text-sm border-gray-300 p-1 shadow-sm focus:border-[#efb7b7] focus:ring-[#efb7b7] transition duration-150 ease-in-out cursor-pointer " 
                                            x-bind:disabled="colorSeleccionado" 
                                            x-bind:class="colorSeleccionado ? 'border-gray-300' : 'bg-gray-200'"
                                            wire:model.defer="variants.{{ $i }}.atributos.color_code" 
                                            >
                                            @error('variants.' . $i . '.atributos.color_code') 
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                            @enderror   
                                        </div>
                                        
                                    @else
                                        <div class="col-span-1 flex items-center select-none">
                                            <div class="flex items-center p-4 text-sm text-yellow-700 border border-yellow-300 rounded-lg bg-yellow-50" role="alert">
                                                <svg class="shrink-0 inline w-4 h-4 mr-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM10 15a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm1-4a1 1 0 0 1-2 0V6a1 1 0 1 1 2 0v5Z"/>
                                                </svg>
                                                <span class="sr-only">Advertencia</span>
                                                <p>No has seleccionado ningún color</p>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Campo de estampado -->
                                    <div class="col-span-1">
                                        <div
                                        wire:ignore 
                                        wire:key="category-select-estampado{{ $select_key }}"
                                        x-data="{ 
                                            selectedOptionsEstampado:  @js($variants[$i]["atributos"]["estampado"]), 
                                            options:  @js($allAttributes["Estampado"]),
                                            tomSelectInstance: null // Propiedad temporal para la instancia de Tom Select
                                        }"
                                        x-init=" $nextTick(() => {
                                            const selectEl = $refs.selectElementEstampado; // Obtener la referencia al elemento
                                            // 1. Inicializar Tom Select con el plugin 'create','remove','change'
                                            tomSelectInstance = new TomSelect(selectEl, {
                                                // --- Configuración de Plugins ---
                                                plugins: ['clear_button'],
                                                // --- Configuración de Datos ---
                                                valueField: 'value', // El campo del objeto de opciones que se usará como valor (ID)
                                                labelField: 'text', // El campo del objeto de opciones que se mostrará al usuario
                                                searchField: 'text', // El campo por el cual se permite la búsqueda
                                                options: options, // El arreglo de opciones iniciales de Alpine.js
                                                items: selectedOptionsEstampado, // El arreglo de valores pre-seleccionados de Alpine.js
                                                // --- Configuración de Creación ---
                                                // La función se llama cuando un usuario intenta crear una opción nueva.
                                                create: function(input, callback) {
                                                    if (input.includes('-')) {
                                                        alert('No se permiten guiones en este campo');
                                                        return false; // Bloquea la creación en el select
                                                    }
                                                    // Generar un valor temporal único para el frontend
                                                    const valorTemporal = 'temp-' + input;
                                                    // CRUCIAL: El 'value' debe ser único (aquí usamos el texto como valor).
                                                    const newOption = {
                                                        value: input, // Usar el texto escrito como valor temporal
                                                        text: input
                                                    };
                                                    // 2. Llamar al callback con el objeto para que Tom Select añada la opción a su lista y la seleccione inmediatamente.
                                                    callback(newOption);
                                                    // 3. Sincronizar el estado de Alpine manualmente
                                                    // Tom Select ya habrá actualizado el input, pero esta línea es una garantía:
                                                    selectedOptionsEstampado = selectEl.tomselect.getValue();
                                                    // Asegúrate de que, después del callback, también llamas a $wire.set:
                                                    setTimeout(() => {
                                                        $wire.set('variants.{{ $i }}.atributos.estampado', selectEl.tomselect.getValue());
                                                    }, 50);
                                                },
                                                
                                                // filtro (solo permite crear si el input no está vacío)
                                                createFilter: true, // Esto activa el botón de Add si no hay coincidencias.
                                                // 4. Lógica de sincronización de datos de Alpine y livewire
                                                onChange: (value) => {
                                                    // Actualiza la variable de Alpine cuando Tom Select cambia
                                                    selectedOptionsEstampado = [value];
                                                    // Usamos $wire.set para forzar a Livewire a actualizar
                                                    $wire.set('variants.{{ $i }}.atributos.estampado', value);
                                                }
                                            });
                                        });
                                    ">
                                        <label for="my_select_estampado" class="block text-sm font-medium text-gray-700">Estampado:</label>
                                        <select x-ref="selectElementEstampado" id="my_select_estampado" wire:model="variants.{{ $i }}.atributos.estampado" wire:change="rebuildSku({{ $i }})" class="mt-2 block w-full rounded-md border border-gray-300 focus:border-[#efb7b7] focus:ring-[#efb7b7] shadow-sm">
                                        </select>
                                        </div>
                                        @error('variants.' . $i . '.atributos.estampado') 
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                        @enderror   
                                    </div>

                                    <!-- Campo de seleccionar talla -->
                                    <div class="col-span-1">
                                        <div 
                                            wire:ignore
                                            wire:key="talla-select-{{ $select_key }}"
                                            x-data="{
                                                // 1. INYECTAR EL VALOR: Pasa la propiedad de Livewire a Alpine.js
                                                // Usamos 'js' para convertir el array PHP a un array JavaScript.
                                                initialTallas: @js($variants[$i]["atributos"]["talla"]), 
                                                // 2. Decralar variable Para guardar la instancia de TomSelect
                                                selectInstance: null, 
                                                init() {
                                                    // 3. INICIALIZACIÓN: Crea TomSelect y usa el valor inyectado
                                                    this.selectInstance = new TomSelect(this.$refs.selectMultipleTallas, {
                                                        maxItems: null, // Permite múltiples selecciones
                                                        plugins: ['remove_button'], // Plugin para permitir eliminar etiquetas
                                                        removeButton: { title: 'Eliminar esta opción' }, // Configuración el botón de eliminar
                                                        items: this.initialTallas, // TomSelect se construye usando la data inyectada como su valor de inicio.
                                                    });
                                                }
                                            }"
                                        >
                                            <label for="my_select_talla" class="block text-sm font-medium text-gray-700">Talla:</label>

                                            <select wire:model="variants.{{ $i }}.atributos.talla" x-ref="selectMultipleTallas" id="my_select_talla" class="mt-2 block w-full rounded-md border-gray-300 shadow-sm">
                                                <option value=""> </option>
                                                @foreach($allAttributes["Talla"] as $val)
                                                    <option value="{{ $val["value"].'--'.$val["text"] }}">{{ $val["text"] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <!-- Mensaje de error -->
                                        @error('variants.' . $i . '.atributos.talla') 
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                        @enderror
                                    </div>
                                </div>
                            @endif

                            <div class="mt-3 flex items-center justify-between">
                                <div class="text-sm text-gray-600">Fila #{{ $i + 1 }}</div>
                                <div class="space-x-2">
                                    <button type="button" wire:click.prevent="removeVariant({{ $i }})" class="px-3 py-1 text-xs font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 cursor-pointer">Eliminar</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Boton de añadir variante -->
                <div class="mt-4">
                    <button type="button" wire:click.prevent="agregarVariante" wire:target="agregarVariante" class="px-4 py-2 text-sm font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 cursor-pointer">
                        <span wire:loading.remove  wire:target="agregarVariante"> 
                            Añadir Variante
                        </span>
                        <span wire:loading.delay.shortest wire:target="agregarVariante" class="hidden">
                            <svg class="animate-spin  h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-100" fill="#008236" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>
                </div>
            </div>
        @endif

        <!-- Mensajes generales -->
        <div class="mt-8 mb-2 w-full">
            @if (session()->has('notificacion'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 7000)" x-transition:leave.duration.500ms class="flex p-4 mb-4 {{ session('notificacion')['tipo'] }} rounded-sm" role="alert">
                    {{ session('notificacion')['mensaje']  ?? ""}}
                    <button @click="show = false" class="ml-auto focus:outline-none">&times;</button>
                </div>
            @endif
        </div>
        <!-- BOTONES DE ACCIÓN (Atras/Siguiente/Guardar) -->
        <div class="flex justify-center py-2 mt-8 border-t border-gray-200">
            <!-- Boton atras -->
            @if ($seccionContenido != 'producto_base')
                <button type="button" title="Atras" wire:click="mostrarSeccion('producto_base')" wire:target="mostrarSeccion('producto_base')"
                class="p-2 mx-2  bg-black text-sm text-white rounded-lg border border-black font-medium  hover:bg-white hover:text-black focus:z-10 focus:outline-none focus:ring-3 focus:ring-gray-600 focus:bg-white focus:text-black cursor-pointer">
                    <span wire:loading.remove  wire:target="mostrarSeccion('producto_base')"> 
                        Atrás
                    </span>

                    <span wire:loading.delay.shortest wire:target="mostrarSeccion('producto_base')" class="hidden">
                        <svg class="animate-spin  h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-100" fill="#000" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
            @endif

            <!-- Boton Siguiente -->
            @if ($seccionContenido == 'producto_base')
                <button type="submit" title="Siguiente" form="formRegistrar" wire:target="mostrarSeccion('producto_base','Guardar')" class="p-2 mx-2  bg-black text-sm text-white rounded-lg border border-black font-medium  hover:bg-white hover:text-black focus:z-10 focus:outline-none focus:ring-3 focus:ring-gray-600 focus:bg-white focus:text-black cursor-pointer">
                    <span wire:loading.remove  wire:target="mostrarSeccion('producto_base','Guardar')"> 
                        Siguiente
                    </span>
                    <span wire:loading.delay.shortest wire:target="mostrarSeccion('producto_base','Guardar')" class="hidden">
                        <svg class="animate-spin  h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-100" fill="#000" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
            <!-- Boton de guardar producto --> 
            @else 
                <button type="submit" form="formRegistrar" wire:target="mostrarSeccion('{{ $seccionContenido }}','Guardar')" class="cursor-pointer inline-flex justify-center rounded-md border border-transparent bg-[#e8a3a3ec] py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-[#e8a3a3] focus:outline-none focus:ring-2 focus:ring-[#e59696] focus:ring-offset-2 ml-3">
                    <span wire:loading.remove  wire:target="mostrarSeccion('{{ $seccionContenido }}','Guardar')"> 
                        Guardar
                    </span>
                    
                    <span wire:loading.delay.shortest wire:target="mostrarSeccion('{{ $seccionContenido }}','Guardar')" class="hidden">
                        <svg class="animate-spin  h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-100" fill="#fff" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
            @endif
        </div>
    </form>
</div>
