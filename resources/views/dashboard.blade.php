<x-app-layout title="Panel de Administración">
    <x-slot name="header">
        <h2 class="pl-1 font-semibold text-2xl text-black  leading-tight flex items-center space-x-1">
            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-adjustments-cog"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 10a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M6 4v4" /><path d="M6 12v8" /><path d="M13.199 14.399a2 2 0 1 0 -1.199 3.601" /><path d="M12 4v10" /><path d="M12 18v2" /><path d="M16 7a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M18 4v1" /><path d="M18 9v2.5" /><path d="M19.001 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M19.001 15.5v1.5" /><path d="M19.001 21v1.5" /><path d="M22.032 17.25l-1.299 .75" /><path d="M17.27 20l-1.3 .75" /><path d="M15.97 17.25l1.3 .75" /><path d="M20.733 20l1.3 .75" /></svg>
            <span class="text-transparent bg-clip-text bg-linear-to-r to-black/70 from-black">
                {{ __('Dashboard') }}
            </span>
        </h2>
    </x-slot>

    <div class="py-12 px-4 md:px-0">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 my-2">
            <a href="crearProducto" class="block py-2">
                <div class="bg-black overflow-hidden shadow-sm rounded-lg hover:scale-102 hover:bg-black/90 duration-500 transition-all">
                    <div class="p-6 text-gray-100 capitalize flex justify-between">
                        Crear Nuevo Producto/Variantes
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="h-6"><!--!Font Awesome Free v7.1.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M352 128C352 110.3 337.7 96 320 96C302.3 96 288 110.3 288 128L288 288L128 288C110.3 288 96 302.3 96 320C96 337.7 110.3 352 128 352L288 352L288 512C288 529.7 302.3 544 320 544C337.7 544 352 529.7 352 512L352 352L512 352C529.7 352 544 337.7 544 320C544 302.3 529.7 288 512 288L352 288L352 128z"/></svg>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 my-2">
            <a href="listadoP"class="block py-2"> 
                <div class="bg-black overflow-hidden shadow-sm rounded-lg hover:scale-102 hover:bg-black/90 duration-500 transition-all">
                    <div class="p-6 text-gray-100 capitalize flex justify-between">
                        Lista de productos
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M320-280q17 0 28.5-11.5T360-320q0-17-11.5-28.5T320-360q-17 0-28.5 11.5T280-320q0 17 11.5 28.5T320-280Zm0-160q17 0 28.5-11.5T360-480q0-17-11.5-28.5T320-520q-17 0-28.5 11.5T280-480q0 17 11.5 28.5T320-440Zm0-160q17 0 28.5-11.5T360-640q0-17-11.5-28.5T320-680q-17 0-28.5 11.5T280-640q0 17 11.5 28.5T320-600Zm120 320h240v-80H440v80Zm0-160h240v-80H440v80Zm0-160h240v-80H440v80ZM200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm0-80h560v-560H200v560Zm0-560v560-560Z"/></svg>
                    </div>
                </div>
            </a>
        </div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 my-2">
            <a href="ordenes" class="block py-2">
                <div class="bg-black overflow-hidden shadow-sm rounded-lg hover:scale-102 hover:bg-black/90 duration-500 transition-all">
                    <div class="p-6 text-gray-100 capitalize flex justify-between">
                        Lista de ordenes/pago
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M560-440q-50 0-85-35t-35-85q0-50 35-85t85-35q50 0 85 35t35 85q0 50-35 85t-85 35ZM280-320q-33 0-56.5-23.5T200-400v-320q0-33 23.5-56.5T280-800h560q33 0 56.5 23.5T920-720v320q0 33-23.5 56.5T840-320H280Zm80-80h400q0-33 23.5-56.5T840-480v-160q-33 0-56.5-23.5T760-720H360q0 33-23.5 56.5T280-640v160q33 0 56.5 23.5T360-400Zm440 240H120q-33 0-56.5-23.5T40-240v-440h80v440h680v80ZM280-400v-320 320Z"/></svg>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 my-2">
            <a href="metodo" class="block py-2">
                <div class="bg-black overflow-hidden shadow-sm rounded-lg hover:scale-102 hover:bg-black/90 duration-500 transition-all">
                    <div class="p-6 text-gray-100 capitalize flex justify-between">
                        Configuración de Pagos
                        <svg xmlns="http://www.w3.org/2000/svg" height="26px" viewBox="0 -960 960 960" width="26px" fill="#e3e3e3"><path d="M200-200v-560 560Zm0 80q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v100h-80v-100H200v560h560v-100h80v100q0 33-23.5 56.5T760-120H200Zm320-160q-33 0-56.5-23.5T440-360v-240q0-33 23.5-56.5T520-680h280q33 0 56.5 23.5T880-600v240q0 33-23.5 56.5T800-280H520Zm280-80v-240H520v240h280Zm-160-60q25 0 42.5-17.5T700-480q0-25-17.5-42.5T640-540q-25 0-42.5 17.5T580-480q0 25 17.5 42.5T640-420Z"/></svg>
                    </div>
                </div>
            </a>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 my-2">
            <a href="tasa" class="block py-2">
                <div class="bg-black overflow-hidden shadow-sm rounded-lg hover:scale-102 hover:bg-black/90 duration-500 transition-all">
                    <div class="p-6 text-gray-100 capitalize flex justify-between">
                        Tasa De Cambio (Bs)
                        <svg xmlns="http://www.w3.org/2000/svg" height="26px" viewBox="0 -960 960 960" width="26px" fill="#e3e3e3"><path d="M80-160v-640h800v640H80Zm80-80h640v-480H160v480Zm0 0v-480 480Zm160-40h80v-40h40q17 0 28.5-11.5T480-360v-120q0-17-11.5-28.5T440-520H320v-40h160v-80h-80v-40h-80v40h-40q-17 0-28.5 11.5T240-600v120q0 17 11.5 28.5T280-440h120v40H240v80h80v40Zm320-30 80-80H560l80 80Zm-80-250h160l-80-80-80 80Z"/></svg>
                    </div>
                </div>
            </a>
        </div>
    </div>
</x-app-layout>

