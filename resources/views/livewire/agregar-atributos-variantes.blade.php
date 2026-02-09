<div>
    <!-- Botones de Navegación -->
    <div class="mb-8 flex justify-center space-x-4 flex-wrap">
        <button type="button" wire:click="mostrarSeccion('producto_base')"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors cursor-pointer
            {{ $seccionContenido == 'producto_base' ? 'bg-[#e8a3a3] text-black' : 'bg-gray-300 text-black hover:bg-[#e8a3a3a7]' }}">
            Producto Base
        </button>
        <button type="button" wire:click="mostrarSeccion('lista')" {{ empty($productElegido) ? 'disabled' : '' }}
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors cursor-pointer
            {{ $seccionContenido == 'lista' ? 'bg-[#e8a3a3] text-black' : 'bg-gray-300 text-black hover:bg-[#e8a3a3a7]' }}">
            Lista
        </button>
        <button type="button" wire:click="mostrarSeccion('variantes')" {{ empty($productElegido) || empty($varianteElegido)  ? 'disabled' : '' }}
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors cursor-pointer 
            {{ $seccionContenido == 'variantes' ? 'bg-[#e8a3a3] text-black' : 'bg-gray-300 text-black hover:bg-[#e8a3a3a7]' }}">
            Variante
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
        <!-- Seccion Lista de variantes -->
        @if ($seccionContenido == 'lista')
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="col-span-2">
                    <h3 class="col-span-2 text-justify text-lg font-medium text-gray-900 mb-2">Datos de la Variante</h3>    
                    <p class="col-span-2 text-justify mb-4 text-gray-900 border-b pb-2">Por favor, seleccione la variante que desea añadir.</p>
                </div>

                {{-- Itera sobre cada grupo de variantes (agrupadas por image_id) --}}
                @foreach ($todosVariantes as $variants)
                    <!-- Contenedor de imagenes -->
                    <div class="col-span-1 bg-white border border-gray-200 rounded-xl shadow-lg hover:shadow-xl hover:scale-101 transition duration-300 overflow-hidden flex flex-col md:flex-row">
                        {{-- Sección 1: Imagen del Grupo (Ocupa 1/3 del ancho en escritorio) --}}
                        <div class="w-full md:w-1/3 p-4 bg-gray-50 flex items-center justify-center">
                            <img src="{{ asset('storage').'/'.$todosImagenes[$variants["image_id"]]}}" alt="Imagen de la variante" class="object-contain max-h-60 rounded-lg shadow-md"/>
                        </div>

                        {{-- Sección 2: Detalles de Variantes (Ocupa 2/3 del ancho en escritorio) --}}
                        <div class="md:w-2/3 p-6 flex flex-col justify-between">
                            <h3 class="text-xl font-bold text-gray-900 mb-4 border-b pb-2">
                                Detalles
                            </h3>

                            {{-- Informacion --}}
                            <div class="flex flex-wrap gap-3 mb-6">
                                <div class="px-4 py-2 text-sm font-medium rounded-full border-2 border-gray-300 text-gray-700">
                                    <b>Color.</b> {{  $variants["color"] }}
                                </div>
                                <div class="px-4 py-2 text-sm font-medium rounded-full border-2 border-gray-300 text-gray-700">
                                    <b>Estampados.</b> {{  $variants["estampado"] }}
                                </div>
                                <div class="px-4 py-2 text-sm font-medium rounded-full border-2 border-gray-300 text-gray-700">
                                    <b>Tallas: </b> 
                                    @foreach ($variants["talla"] as $talla)
                                        {{  $talla }},
                                    @endforeach
                                </div>

                            </div>


                            <button type="button" 
                                wire:click="varianteMostrar('{{ $variants['id'] }}', JSON.parse('{{ json_encode($variants["talla"]) }}'))"
                                wire:loading.attr="disabled" 
                                wire:target="varianteMostrar('{{ $variants['id'] }}', JSON.parse('{{ json_encode($variants["talla"]) }}'))"
                                class="mt-auto w-full md:w-1/2 self-end px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-black hover:bg-black/80 focus:ring-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 transition duration-150 cursor-pointer">
                                
                                <span wire:loading.remove wire:target="varianteMostrar('{{ $variants['id'] }}', JSON.parse('{{ json_encode($variants["talla"]) }}'))"> 
                                    Añadir
                                </span>

                                <span wire:loading.delay.shortest wire:target="varianteMostrar('{{ $variants['id'] }}', JSON.parse('{{ json_encode($variants["talla"]) }}'))" class="items-center hidden">
                                    <svg class="animate-spin -ml-1 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-100" fill="#fff" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </div>  
                @endforeach
            </div>
        @endif
        <!-- Seccion variantes editar -->
        @if ($seccionContenido == 'variantes')
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="col-span-2">
                    <h3 class="col-span-2 text-justify text-lg font-medium text-gray-900 mb-2">Datos de la Variante</h3>    
                    <p class="col-span-2 text-justify mb-4 text-gray-900 border-b pb-2">Por favor, seleccione las tallas que desea añadir.</p>
                </div>

                <!-- Campo de seleccionar talla -->
                <div class="col-span-2">
                    <div
                        wire:ignore
                        x-data="{
                            // 1. INYECTAR EL VALOR: Pasa la propiedad de Livewire a Alpine.js
                            // Usamos 'js' para convertir el array PHP a un array JavaScript.
                            initialTallas: @js($tallasSelect), 
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
                        <label for="my_select_talla" class="block mb-2.5 text-sm font-medium text-heading">Tallas:</label>

                        <select multiple wire:model="tallasSelect" x-ref="selectMultipleTallas" id="my_select_talla" class="mt-2 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value=""> </option>
                            
                            @foreach($tallasDisponibles as $clave => $valor)
                                <option value="{{ $clave  }}-{{ $valor  }}">{{ $valor }}</option>
                            @endforeach
                        </select>
                    </div>

                    @error('tallasSelect') 
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                    @enderror 
                </div>
            </div>
        @endif
    </form>

    <!-- Mensajes generales -->
    <div class="mt-8 mb-2 w-full">
        @if (session()->has('notificacion'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 10000)" x-transition:leave.duration.500ms class="flex p-4 mb-4 {{ session('notificacion')['tipo'] }} rounded-sm" role="alert">
                {{ session('notificacion')['mensaje']  ?? ""}}
                <button @click="show = false" class="ml-auto focus:outline-none">&times;</button>
            </div>
        @endif
    </div>
    <!-- BOTONES DE ACCIÓN (Atras/Siguiente/Guardar) -->
    <div class="flex justify-center py-2 mt-8 border-t border-gray-200">
        <!-- Boton atras -->
        @if ($seccionContenido != 'producto_base')
            <button type="button" title="Atras" wire:click="mostrarSeccion('{{ $seccionContenido == "lista" ? 'producto_base' : 'lista' }}')" wire:target="mostrarSeccion('{{ $seccionContenido == "lista" ? 'producto_base' : 'lista' }}')"
            class="p-2 mx-2  bg-black text-sm text-white rounded-lg border border-black font-medium  hover:bg-white hover:text-black focus:z-10 focus:outline-none focus:ring-3 focus:ring-gray-600 focus:bg-white focus:text-black cursor-pointer">
                <span wire:loading.remove  wire:target="mostrarSeccion('{{ $seccionContenido == "lista" ? 'producto_base' : 'lista' }}')"> 
                    Atrás
                </span>

                <span wire:loading.delay.shortest wire:target="mostrarSeccion('{{ $seccionContenido == "lista" ? 'producto_base' : 'lista' }}')" class="hidden">
                    <svg class="animate-spin  h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-100" fill="#000" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
            </button>
        @endif

        <!-- Boton Siguiente -->
        @if ($seccionContenido == 'producto_base')
            <button type="submit" title="Siguiente" form="formRegistrar" wire:target="mostrarSeccion('{{ $seccionContenido }}','Guardar')" class="p-2 mx-2  bg-black text-sm text-white rounded-lg border border-black font-medium  hover:bg-white hover:text-black focus:z-10 focus:outline-none focus:ring-3 focus:ring-gray-600 focus:bg-white focus:text-black cursor-pointer">
                <span wire:loading.remove  wire:target="mostrarSeccion('{{ $seccionContenido }}','Guardar')"> 
                    Siguiente
                </span>
                <span wire:loading.delay.shortest wire:target="mostrarSeccion('{{ $seccionContenido }}','Guardar')" class="hidden">
                    <svg class="animate-spin  h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-100" fill="#000" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
            </button>
        @endif

        <!-- Boton de guardar proceso --> 
        @if ($seccionContenido == 'variantes')
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
</div>
