<!-- Está es la Vista crear producto nuevo -->
<!-- Utiliza el componente o plantilla breeze y Utiliza el componente Livewire -->
<x-app-layout title="Nuevo Producto/Variante">
    <div class="pt-2 pb-8 bg-gray-200">
        <x-slot name="header">
            <h2 class="font-semibold text-2xl text-black  leading-tight flex items-center space-x-1">
                <!-- Boton de regresar a la página anterior -->
                <div class="pr-3" x-data="{volver() { window.history.back();}}">
                    <button @click="volver()" title="Regresar" class="inline-flex items-center justify-center p-1 rounded-full text-white bg-black transition duration-300 ease-in-out shadow-lg hover:bg-black/80 focus:outline-none focus:ring-4 focus:ring-gray-300 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-9">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 9-3 3m0 0 3 3m-3-3h7.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </button>
                </div>
        
                <span class="text-transparent bg-clip-text bg-linear-to-r to-black/70 from-black">
                    Crear Nuevo Producto/Variantes
                </span>
            </h2>
        </x-slot>

        {{-- el componente Livewire: crear nuevo producto --}}
        <div class="mt-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
            @livewire('form-crear-producto') 
        </div>
    </div>
</x-app-layout>
