<!-- Está es la Vista de variantes de un producto -->
<!-- Utiliza el componente o plantilla base-main.blade.php -->
<x-base-main title="{{ $id->name ?? 'Producto Variante' }}" type="catalogo-{{ $cat }}">
    <!-- Validar si existe variantes del producto -->
    @if (!blank($id) && !blank($consultarTasa))
        <!-- Contenedor principal para el carrusel de imágenes -->
        <div x-data="{ 
            // Cargamos las diapositivas usando los datos de las variantes
            'slides': {{json_encode($slides)}}, 
            currentSlideIndex: 1,
            // Variable para controlar el zoom
            isZoomed: false,
            // Función para ir a la diapositiva anterior
            previous() {                
                if (this.currentSlideIndex > 1) {                    
                    this.currentSlideIndex = this.currentSlideIndex - 1                
                } else {   
                    // Si es la primera, ve a la última           
                    this.currentSlideIndex = this.slides.length                
                }            
            },            
            // Función para ir a la siguiente diapositiva
            next() {                
                if (this.currentSlideIndex < this.slides.length) {                    
                    this.currentSlideIndex = this.currentSlideIndex + 1                
                } else {                 
                    // Si es la última, ve a la primera    
                    this.currentSlideIndex = 1                
                }            
            },    
            // Función para obtner la imagen actual (Necesaria para el zoom Overlay)
            get currentImage() {
                return this.slides[this.currentSlideIndex - 1] || {};
            },
            // Agregar boton de mostrar tabla de compras
            estadoBotonTabla: false,
        }" 
        class="w-full bg-gray-200  mt-8 p-0 sm:p-4 font-sans antialiased grid grid-cols-1 md:grid-cols-2 gap-5 rounded-xl shadow-2xl">
            <!-- Boton de regresar a la página anterior -->
            <div class="px-4 col-span-2" x-data="{volverACatalogo() { window.history.back();}}">
                <button @click="volverACatalogo()" title="Regresar" class="inline-flex items-center justify-center p-2 rounded-full text-white bg-black transition duration-300 ease-in-out shadow-lg hover:bg-white hover:text-black focus:outline-none focus:ring-4 focus:ring-gray-500 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-10">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 9-3 3m0 0 3 3m-3-3h7.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </button>
            </div>

            <!-- CARRUSEL PRINCIPAL Y BOTONES -->
            <div class="w-full overflow-hidden flex flex-col gap-5 col-span-2 md:col-span-1">
                <div class="relative">
                    <!-- Botón Anterior -->
                    <button type="button" title="Anterior" class="absolute left-5 top-1/2 z-20 flex rounded-full -translate-y-1/2 items-center justify-center bg-white/40 p-2 text-neutral-600 transition hover:bg-white/60 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black active:outline-offset-0 dark:bg-neutral-950/40 dark:text-neutral-300 dark:hover:bg-neutral-950/60 dark:focus-visible:outline-white cursor-pointer" aria-label="previous slide" x-on:click="previous()">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="3" class="size-5 md:size-6 pr-0.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                        </svg>
                    </button>
                    <!-- Botón Siguiente -->
                    <button type="button" title="Siguiente" class="absolute right-5 top-1/2 z-20 flex rounded-full -translate-y-1/2 items-center justify-center bg-white/40 p-2 text-neutral-600 transition hover:bg-white/60 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black active:outline-offset-0 dark:bg-neutral-950/40 dark:text-neutral-300 dark:hover:bg-neutral-950/60 dark:focus-visible:outline-white cursor-pointer" aria-label="next slide" x-on:click="next()">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="3" class="size-5 md:size-6 pl-0.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </button>
                    <!-- Área de las Diapositivas -->
                    <div class="relative min-h-[50vh] w-full">
                        <template x-for="(slide, index) in slides" :key="index">
                            <!-- Contenedor individual de la diapositiva -->
                            <div x-show="currentSlideIndex == index + 1" 
                                class="absolute inset-0" 
                                x-transition.opacity.duration.500ms
                            >
                                <!-- IMAGEN: APLICAMOS EL EVENTO CLICK PARA ACTIVAR EL ZOOM -->
                                <img 
                                    @click="isZoomed = true"
                                    class="absolute w-full aspect-square object-contain h-full inset-0 text-neutral-600 dark:text-neutral-300 
                                        cursor-zoom-in rounded-lg transition-transform duration-300 ease-in-out hover:scale-[1.05]" 
                                    :src="slide.imgSrc" 
                                    :alt="slide.imgAlt" 
                                />
                            </div>
                        </template>
                    </div>
                    <!-- Indicadores de puntos -->
                    <div class="absolute rounded-sm bottom-3 md:bottom-5 left-1/2 z-20 flex -translate-x-1/2 gap-4 md:gap-3  px-1.5 py-1 md:px-2 bg-neutral-950/75" role="group" aria-label="slides" >
                        <template x-for="(slide, index) in slides" :key="index">
                            <button class="size-2 rounded-full transition" 
                                x-on:click="currentSlideIndex = index + 1" 
                                :class="[currentSlideIndex === index + 1 ? 'bg-red-400' : 'bg-neutral-600/50 dark:bg-neutral-300/50 hover:bg-red-400']" 
                                :aria-label="'slide ' + (index + 1)">
                            </button>
                        </template>
                    </div>
                    <p class="text-center text-xs text-gray-600 mt-2 pt-2">Haz clic en la imagen para ver el zoom completo.</p>
                </div>
                <!-- CONTROLES EXTERNOS -->
                <div class="p-2  bg-neutral-500 flex justify-center flex-wrap gap-4 rounded-sm" role="group" aria-label="Diapositivas por nombre">
                    <template x-for="(slide, index) in slides">
                        <button 
                            class="rounded-sm px-3 py-3 text-sm font-medium transition cursor-pointer"
                            x-on:click="currentSlideIndex = index + 1" 
                            x-bind:class="{ 
                                // Clase para el botón SELECCIONADO (la diapositiva actual)
                                'bg-transparent outline-red-400 outline-4 text-white border border-transparent': currentSlideIndex === index + 1,
                                
                                // Clase para el botón NO SELECCIONADO (aplica texto y hover genéricos)
                                'text-neutral-900 hover:opacity-80 outline-white outline-2 border border-black': currentSlideIndex !== index + 1
                            }"
                            x-bind:style="{ 'background-color': slide.codigo }"
                            x-bind:title="slide.label"
                            x-bind:aria-label="'Ir a ' + (slide.label || ('Imagen ' + (index + 1)))"
                        ></button>
                    </template>
                </div>
            </div>

            <!-- MODAL DE ZOOM DE PANTALLA COMPLETA (OVERLAY) -->
            <div 
                x-cloak 
                x-show="isZoomed" 
                x-transition.opacity.duration.300ms 
                class="fixed inset-0 bg-[#000000e6] z-50 flex items-center justify-center p-4">
                <!-- Contenedor del Modal -->
                <div class="relative flex flex-col items-center justify-center w-full h-full">
                    <!-- Imagen en Zoom -->
                    <img 
                        :src="currentImage.imgSrc" 
                        :alt="currentImage.imgAlt" 
                        class="max-w-[90vw] max-h-[90vh] rounded-lg shadow-2xl transition-transform duration-[0.5s] ease-in-out" 
                        @click="isZoomed = false"
                    />
                    <!-- Botón de Cerrar (X) -->
                    <button 
                        type="button" 
                        class="absolute top-4 right-4 bg-white/20 p-2 rounded-full text-white hover:bg-white/40 transition cursor-pointer" 
                        @click="isZoomed = false"
                        aria-label="Cerrar zoom"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                    
                    <p class="mt-4 text-white text-sm">Haz clic en la imagen o en la 'X' para cerrar.</p>
                </div>
            </div>

            <!-- Seccion información del producto -->
            <div class="px-4 select-none col-span-2 md:col-span-1">
                @php
                    $precioDolar = number_format($cat == "mayorista" ? $id->price_wholesale : $id->price_retail, 2);
                    $precioBs = number_format($precioDolar * (float) $consultarTasa->value);
                @endphp

                <span class="text-[#d18787] text-xs font-bold uppercase tracking-widest mb-2">Colección</span>

                <h1 class="text-4xl font-black text-gray-900 leading-none mb-4">
                    <span class="text-transparent bg-clip-text bg-linear-to-r to-gray-700 from-black">{{ $id->name }}</span>
                </h1>
                
                <p class="text-gray-600 leading-relaxed mb-6 border-l-2 border-black pl-4 uppercase">
                    {{ $id->description }}
                </p>

                <ul class="mb-2 space-y-1">
                    @if (!blank($listaTallas))
                        <li class="flex justify-between border-y border-white py-2 italic">
                            <span class="text-gray-600 font-normal">Tallas</span>
                            <span class="text-gray-900 font-medium">{{ implode(', ', $listaTallas) }}</span>
                        </li>
                    @endif

                    @if (!blank($listaEstampado))
                        <li class="flex justify-between border-y border-gray-100 py-2 italic">
                            <span class="text-gray-500 font-normal">Estampado</span>
                            <span class="text-gray-900 font-medium">{{ implode(', ', $listaEstampado) }}</span>
                        </li>
                    @endif

                    @if (!blank($listaMateriales))
                        <li class="flex justify-between border-y border-gray-100 py-2 italic">
                            <span class="text-gray-500 font-normal">Material</span>
                            <span class="text-gray-900 font-medium">{{ implode(', ', $listaMateriales) }}</span>
                        </li>
                    @endif
                </ul>

                <div class="bg-gray-50 p-6 rounded-sm mb-6">
                    <div class="text-2xl font-semibold text-gray-900">
                        ${{ $precioDolar }} <span class="text-xs font-normal text-gray-400">USD</span>
                    </div>
                    <div class="text-sm text-gray-500 mt-1">Ref: {{ $precioBs }} Bs.</div>
                </div>

                
                <button type="button" 
                    @click="
                        // 1. Activamos la visibilidad de la tabla en Alpine
                        estadoBotonTabla = true; 

                        // 2. Esperamos a que el DOM se actualice (que la tabla realmente exista en la pantalla)
                        $nextTick(() => { 
                            // 3. Buscamos el elemento de la tabla por su ID
                            const elemento = document.getElementById('tabla-compras');
                            
                            // 4. Definimos un margen superior (en píxeles) para que no quede pegado al techo
                            const offset = 100; 

                            // 5. Calculamos la posición de la tabla respecto a la parte superior de la página
                            // getBoundingClientRect().top da la posición respecto a la ventana actual
                            // window.pageYOffset suma lo que ya hayamos bajado con el scroll
                            const topPosicion = elemento.getBoundingClientRect().top + window.pageYOffset;

                            // 6. Ejecutamos el scroll suave restando el margen (offset)
                            window.scrollTo({
                                top: topPosicion - offset,
                                behavior: 'smooth' // Esto hace que el scroll sea animado y no un salto
                            });
                        });
                    " 
                    :disabled="estadoBotonTabla"
                    :class="{ 'opacity-60 cursor-not-allowed': estadoBotonTabla }" 
                    class="flex w-full items-center justify-center mt-4 p-5 bg-black text-white  border rounded-xs font-medium hover:bg-white hover:text-black focus:outline-none focus:ring-4 focus:ring-gray-600 focus:bg-white focus:text-black cursor-pointer group uppercase">
                    <svg class="h-5 w-5 transition-transform duration-300 group-hover:-translate-y-1 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    Comprar ahora
                </button>
            </div>

            {{-- el componente Livewire: tabla de compras --}}
            <div class="col-span-1 sm:col-span-2">
                @livewire('variant-matrix', params: ['id'=> $id,'matrix' => $matrix,'min_piezas' => $min_piezas,'productName' => $id->name,'listaColores' => $listaColores,'listaTallas' => $listaTallas,'listaEstampado' => $listaEstampado, 'seccionCatalogo' => $cat,'listaColoresYEstampados' => $listaColoresYEstampados])
            </div>
        </div>

    @elseif (blank($consultarTasa))
        <div class="p-4 m-8 text-red-700 bg-red-100 rounded-sm border-red-500 border-l-4 text-sm"  role="alert">
            <p class="font-bold mb-1">Atención!</p>
            <p>⚠️ No se ha podido determinar la valoración de los productos debido a que no hay una tasa de cambio vigente. Por favor, intente más tarde.</p>
        </div>
    @else
        <div class="p-4 m-8 text-red-700 bg-red-100 rounded-sm border-red-500 border-l-4 text-sm"  role="alert">
            <p class="font-bold mb-1">Atención!</p>
            <p>⚠️ No hay productos disponibles.</p>
        </div>
    @endif
    {{-- el componente Livewire: carrito de compra --}}
    <div>
        @livewire('show-car')
    </div>
</x-base-main>
