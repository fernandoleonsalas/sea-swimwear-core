<!-- Vista componente livewire -->
<div>
    <div class="p-6 bg-white shadow-xl sm:rounded-lg relative">
        <div class="p-6 min-h-screen">
            <h2 class="text-3xl font-extrabold text-gray-900 mb-8 border-b pb-2">👗 Asistente de Catálogo de Prendas</h2>
            <!-- Seccion de seleccion: crear producto o variante -->
            @if ($selectedOption === null)
                <div class="text-center mb-8">
                    <p class="text-xl text-gray-600 mb-6">Por favor, selecciona qué tipo de artículo deseas catalogar:</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                    <div
                        wire:click="selectOption('producto')"
                        class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition duration-300 ease-in-out cursor-pointer border-4 border-transparent hover:border-[#d67e7e] p-8 flex flex-col items-center justify-center space-y-4"
                        style="--tw-ring-color: #efb7b7; --tw-shadow-color: #efb7b7; --tw-border-color: #d67e7e;"
                    >
                        <svg class="w-16 h-16" style="color: #efb7b7;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020 12h-8V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.25 13.065a3.75 3.75 0 013.75-3.75M12 9.315v3.75"></path></svg>
                        <h3 class="text-2xl font-semibold text-gray-800">{{ $options['producto'] }}</h3>
                        <p class="text-gray-500 text-center">Define un nuevo modelo de traje de baño o prenda (ej: Bikini, Enterizo, Túnica).</p>
                    </div>

                    <div
                        wire:click="selectOption('variante')"
                        class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition duration-300 ease-in-out cursor-pointer border-4 border-transparent hover:border-[#d67e7e] p-8 flex flex-col items-center justify-center space-y-4"
                        style="--tw-ring-color: #efb7b7; --tw-shadow-color: #efb7b7; --tw-border-color: #d67e7e;"
                    >
                        <svg class="w-16 h-16" style="color: #efb7b7;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h.01M17 17h.01M17 21h.01M3 10h18M3 14h18M10 3v18M14 3v18"></path></svg>
                        <h3 class="text-2xl font-semibold text-gray-800">{{ $options['variante'] }}</h3>
                        <p class="text-gray-500 text-center">Agrega una talla o color específico a un modelo ya existente.</p>
                    </div>
                </div>
            @endif

            <!-- Seccion formulario crear nuevo producto o  seleccion de variante -->
            @if ($selectedOption === "producto" || $selectedOption === "variante")
                <div class="max-w-6xl mx-auto mt-10">
                    <button
                        wire:click="$set('selectedOption', null)"
                        class="mb-6 px-4 py-2 text-sm font-medium rounded-lg hover:bg-opacity-80 transition duration-150 bg-black text-white hover:bg-black/80 focus:outline-none focus:ring-4 focus:ring-gray-300 cursor-pointer"
                    >
                        ← Volver a la Selección
                    </button>

                    <!-- Seccion de crear nuevo producto -->
                    @if ($selectedOption === 'producto')
                        <div class="bg-white mt-2 p-8 rounded-xl shadow-2xl border-l-4 border-l-[#efb7b7]">
                            <!-- Componente del formulario crear nuevo producto -->
                            @livewire('crear-nuevo-producto', [])
                        </div>
                    @endif

                    <!-- Seccion de crear o editar variante -->
                    @if ($selectedOption === 'variante')
                        <div class="text-center mb-8">
                            <p class="text-xl text-gray-600 mb-6">Por favor, selecciona cómo deseas catalogar la nueva variante:</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                            <div
                                wire:click="selectOption('variante_nueva')"
                                class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition duration-300 ease-in-out cursor-pointer border-4 border-transparent hover:border-['#d67e7e'] p-8 flex flex-col items-center justify-center space-y-4"
                                style="--tw-ring-color: #efb7b7; --tw-shadow-color: #efb7b7; --tw-border-color: #d67e7e;"
                            >
                                <svg class="w-16 h-16" style="color: #efb7b7;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <h3 class="text-2xl font-semibold text-gray-800">Crear Nueva Variante</h3>
                                <p class="text-gray-500 text-center">Define un nuevo color, talla, estampado y presentación para un producto existente.</p>
                            </div>
                            <div
                                wire:click="selectOption('variante_editar')"
                                class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition duration-300 ease-in-out cursor-pointer border-4 border-transparent hover:border-['#d67e7e'] p-8 flex flex-col items-center justify-center space-y-4"
                                style="--tw-ring-color: #efb7b7; --tw-shadow-color: #efb7b7; --tw-border-color: #d67e7e;"
                            >
                                <svg class="w-16 h-16" style="color: #efb7b7;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M10 12.5h4M12 21l-3.5-3.5m-2 2l-1.5-1.5M21 12a9 9 0 11-18 0 9 9 0 0118 0zM19 7l1.5-1.5M5 12l-1.5 1.5M7 5.5l1.5 1.5"></path></svg>
                                <h3 class="text-2xl font-semibold text-gray-800">Agregar solo Talla</h3>
                                <p class="text-gray-500 text-center">Añade una talla específica (S, M, L...) a una combinación de color/estampado/presentación ya catalogada.</p>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Seccion formulario de actualizar o crear nueva variante -->
            @if ($selectedOption === "variante_nueva" || $selectedOption === "variante_editar")
                <div class="max-w-6xl mx-auto mt-10">
                    <button
                        wire:click="$set('selectedOption', 'variante')" class="mb-6 px-4 py-2 text-sm font-medium rounded-lg hover:bg-opacity-80 transition duration-150 bg-black text-white hover:bg-black/80 focus:outline-none focus:ring-4 focus:ring-gray-300 cursor-pointer">
                        ← Volver a la Selección
                    </button>

                    <!-- Seccion de crear nueva variante -->
                    @if ($selectedOption === 'variante_nueva')
                        <div class="bg-white mt-2 p-8 rounded-xl shadow-2xl border-l-4 border-l-[#efb7b7]">
                            <!-- Componente del formulario crear nueva variante -->
                            @livewire('crear-nueva-variante', [])
                        </div>
                    @endif

                    
                    <!-- Seccion de actualizar variante -->
                    @if ($selectedOption === 'variante_editar')
                        <div class="bg-white mt-2 p-8 rounded-xl shadow-2xl border-l-4 border-l-[#efb7b7]">
                            <!-- Componente del formulario agregar atributos a una variante existente -->
                            @livewire('agregar-atributos-variantes', [])
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
