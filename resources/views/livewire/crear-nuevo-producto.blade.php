<div>
    <!-- Botones de Navegación -->
    <div class="mb-8 flex justify-center space-x-4 flex-wrap">
        <button type="button" wire:click="mostrarSeccion('categorias')"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors cursor-pointer
            {{ $seccionContenido == 'categorias' ? 'bg-[#e8a3a3] text-black' : 'bg-gray-300 text-black hover:bg-[#e8a3a3a7]' }}">
            Categorías
        </button>

        <button type="button" wire:click="mostrarSeccion('producto_base')" {{ empty($catgElegidas) ? 'disabled' : '' }}
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors cursor-pointer 
            {{ $seccionContenido == 'producto_base' ? 'bg-[#e8a3a3] text-black' : 'bg-gray-300 text-black hover:bg-[#e8a3a3a7]' }}">
            Producto Base
        </button>
    </div>

    <!-- Formulario General -->
    <form wire:submit.prevent="mostrarSeccion('{{ $seccionContenido }}','Guardar')" id="formRegistrar">
        <!-- SECCION DE CATEGORIA -->
        @if ($seccionContenido == 'categorias')
            <div class="grid grid-cols-2 gap-x-10">
                <!-- Titulo de la seccion -->
                <h3 class="col-span-2 text-justify text-lg font-medium text-gray-900 mb-2">Categorías</h3>    
                <!-- Descripcion -->
                <p class="col-span-2 text-justify mb-4 text-gray-900 border-b pb-2">Seleccione las categorías adecuada para su producto. Si la categoría no existe, puede crear una nueva directamente en esta sección.</p>
                <!-- Campo de seleccionar categoria -->
                <div class="col-span-2 lg:col-span-1">
                    <div 
                        wire:ignore
                        wire:key="category-select-{{ $select_key }}"
                        x-data="{
                            // 1. INYECTAR EL VALOR: Pasa la propiedad de Livewire a Alpine.js
                            // Usamos 'js' para convertir el array PHP a un array JavaScript.
                            initialCategories: @js($catgElegidas), 
                            // 2. Decralar variable Para guardar la instancia de TomSelect
                            selectInstance: null, 
                            init() {
                                // 3. INICIALIZACIÓN: Crea TomSelect y usa el valor inyectado
                                this.selectInstance = new TomSelect(this.$refs.selectMultipleCategoria, {
                                    maxItems: null, // Permite múltiples selecciones
                                    plugins: ['remove_button'], // Plugin para permitir eliminar etiquetas
                                    removeButton: { title: 'Eliminar esta opción' }, // Configuración el botón de eliminar
                                    items: this.initialCategories, // TomSelect se construye usando la data inyectada como su valor de inicio.
                                });
                                // 4. ESCUCHA LOS CAMBIOS  
                                // CLAVE: Cuando el valor del select cambia, forzamos a Livewire a actualizar el modelo.
                                this.selectInstance.on('change', (value) => {
                                    // Envía el nuevo valor del select a la propiedad Livewire
                                    @this.set('catgElegidas', value);  // Alpine fuerza la actualización de la propiedad $catgElegidas en Livewire
                                });
                            }
                        }"
                    >
                        <label for="categorias" class="block font-medium text-gray-800">Categorías Seleccionadas</label>

                        <select wire:model.defer="catgElegidas" x-ref="selectMultipleCategoria" id="categorias" class="mt-2 block w-full rounded-md border-gray-300 shadow-sm capitalize ">
                            <option value="">Seleccionar</option>
                            @foreach ($todasCategorias as $categoria)
                                <option value='{{ $categoria->id }}'>{{ $categoria->name }}</option> 
                            @endforeach
                        </select>
                        
                    </div>
                    <!-- Mensaje de error -->
                    @error('catgElegidas') 
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                    @enderror
                </div>
                <!-- campo de añadir nueva categoria -->
                <div class="col-span-2 lg:col-span-1">
                    <label for="new_categoria" class="pb-1 block font-medium text-gray-800">Registrar Nueva Categoría</label>
                    <div class="flex items-center space-x-2 mt-1" wire:key="nuevaCategoria-{{ $select_key }}">
                        <input wire:model.defer="nuevaCategoria"  type="text" id="new_categoria" placeholder="Ej: Nueva Categoria" class="text-sm block w-full rounded-sm border-gray-300 bg-white p-2 shadow-sm capitalize focus:border-[#efb7b7] focus:ring-[#efb7b7]">
                        <button type="button" wire:click="crearCategoria"  wire:loading.attr="disabled" wire:target="crearCategoria"
                        class="px-3 py-2.5 text-xs font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 cursor-pointer">
                            <span wire:loading.remove  wire:target="crearCategoria"> 
                                Añadir
                            </span>

                            <span wire:loading.delay.shortest wire:target="crearCategoria" class="items-center hidden">
                                <svg class="animate-spin -ml-1 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-100" fill="#008236" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                        </button>
                    </div>
                    <!-- Mensaje de error -->
                    @error('nuevaCategoria') 
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                    @enderror
                    <!-- Mensaje de exito -->
                    @if (session()->has('message_exitoso'))
                        <p class="mt-1 text-sm text-green-700">{{ session('message_exitoso') }}</p>
                    @endif
                </div>
            </div>
        @endif
        <!-- SECCION DE PRODUCTO -->
        @if ($seccionContenido == 'producto_base')
            <div class="grid grid-cols-2 gap-y-2 gap-x-10">
                <h3 class="col-span-2 text-justify text-lg font-medium text-gray-900 mb-2">Datos del Producto Base</h3>  
                <p class="col-span-2 text-justify mb-4 text-gray-900 border-b pb-2">Por favor, complete los campos solicitados a continuación.</p>
                <!-- Campo de sku_base -->
                <div class="col-span-2 lg:col-span-1">
                    <label for="skuBase" class="pb-1 block font-medium text-gray-800">SKU Base</label>
                    <input wire:model.defer="skuBase" type="text" id="skuBase" class="text-sm block w-full rounded-sm border-gray-300 bg-white p-2 shadow-sm focus:border-[#efb7b7] focus:ring-[#efb7b7]" placeholder="Ej: SKA-01SM" required> 
                    @error('skuBase') 
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                    @enderror 
                </div>
                <!-- Campo de NOMBRE DEL PRODUCTO -->
                <div class="col-span-2 lg:col-span-1">
                    <label for="nameProducto" class="pb-1 block font-medium text-gray-800">Nombre</label>
                    <input wire:model.defer="nuevoProducto" type="text" id="nameProducto" class="text-sm block w-full rounded-sm border-gray-300 bg-white p-2 shadow-sm focus:border-[#efb7b7] focus:ring-[#efb7b7]" placeholder="Nombre Del Producto" required> 
                    @error('nuevoProducto') 
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                    @enderror 
                </div>
                <!-- Campo de DESCRIPCION DEL PRODUCTO -->
                <div class="col-span-2 lg:col-span-1">
                    <label for="descriptionProducto" class="pb-1 block font-medium text-gray-800">Descripción</label>
                    <textarea wire:model.defer="descriptionProducto" type="text" id="descriptionProducto" class="text-sm block w-full rounded-sm border-gray-300 bg-white p-2 shadow-sm focus:border-[#efb7b7] focus:ring-[#efb7b7]" placeholder="Descripción Del Producto" required></textarea> 
                    @error('descriptionProducto') 
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                    @enderror 
                </div>
                <!-- CAMPO DE PRECIO MINORISTA -->
                <div class="col-span-2 lg:col-span-1">
                    <label for="priceRetail" class="pb-1 block font-medium text-gray-800">Precio Minorista ($)</label>
                    <input wire:model.defer="priceRetail" type="number" step="0.01" min="1.00"  id="priceRetail" class="text-sm block w-full rounded-sm border-gray-300 bg-white p-2 shadow-sm focus:border-[#efb7b7] focus:ring-[#efb7b7]" placeholder="12.33"> 
                    @error('priceRetail') 
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                    @enderror 
                </div>
                <!-- CAMPO DE PRECIO MAYORISTA -->
                <div class="col-span-2 lg:col-span-1">
                    <label for="priceWholesale" class="pb-1 block font-medium text-gray-800">Precio Mayorista ($)</label>
                    <input wire:model.defer="priceWholesale" type="number" step="0.01" id="priceWholesale" class="text-sm block w-full rounded-sm border-gray-300 bg-white p-2 shadow-sm focus:border-[#efb7b7] focus:ring-[#efb7b7]" placeholder="0"> 
                    @error('priceWholesale') 
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                    @enderror 
                </div>
                <!-- CAMPO DE MINIMO DE PIEZAS -->
                <div class="col-span-2 lg:col-span-1">
                    <label for="min_piezas" class="pb-1 block font-medium text-gray-800 capitalize">Mínimo De Piezas al por mayor</label>
                    <input wire:model.defer="min_piezas" type="number" step="0" id="min_piezas" class="text-sm block w-full rounded-sm border-gray-300 bg-white p-2 shadow-sm focus:border-[#efb7b7] focus:ring-[#efb7b7]" placeholder="0"> 
                    @error('min_piezas') 
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                    @enderror 
                </div>
                <!-- CAMPO DE ESTADO DEL PRODUCTO -->
                <div class="col-span-2 lg:col-span-1">
                    <label for="status" class="pb-1 block font-medium text-gray-800">Estado</label>
                    <select wire:model.defer="status" id="status" class="text-sm block w-full rounded-sm border-gray-300 bg-white p-2 shadow-sm focus:border-[#efb7b7] focus:ring-[#efb7b7]">
                        <option value="">Seleccionar</option>
                        <option value="active">Activo</option>
                        <option value="inactive">Inactivo</option>
                    </select>
                    @error('status') 
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                    @enderror 
                </div>
                <div class="col-span-2 mt-2">
                    <div class="bg-orange-100 border-l-4 border-orange-500 text-orange-700 p-4" role="alert">
                    <p class="font-bold">Aviso</p>
                    <p class="text-sm">Si los campos <b>Precio Mayorista</b> y <b>Mínimo De Piezas</b> están vacíos, este producto <b>NO será incluido</b> en el catálogo de ventas al por mayor.</p>
                    </div>
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
            @if ($seccionContenido != 'categorias')
                <button type="button" title="Atras" wire:click="mostrarSeccion('categorias')" wire:target="mostrarSeccion('categorias')"
                class="p-2 mx-2  bg-black text-sm text-white rounded-lg border border-black font-medium  hover:bg-white hover:text-black focus:z-10 focus:outline-none focus:ring-3 focus:ring-gray-600 focus:bg-white focus:text-black cursor-pointer">
                    <span wire:loading.remove  wire:target="mostrarSeccion('categorias')"> 
                        Atrás
                    </span>

                    <span wire:loading.delay.shortest wire:target="mostrarSeccion('categorias')" class="hidden">
                        <svg class="animate-spin  h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-100" fill="#000" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
            @endif
            <!-- Boton Siguiente -->
            @if ($seccionContenido == 'categorias')
                <button type="submit" title="Siguiente" form="formRegistrar" wire:target="mostrarSeccion('categorias','Guardar')" class="p-2 mx-2  bg-black text-sm text-white rounded-lg border border-black font-medium  hover:bg-white hover:text-black focus:z-10 focus:outline-none focus:ring-3 focus:ring-gray-600 focus:bg-white focus:text-black cursor-pointer">
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
                        Guardar Producto
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
