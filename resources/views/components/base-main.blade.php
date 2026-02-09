@props(['type' => 'null', 'title' => 'Sea swimwear'])

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Favicon estándar para la mayoría de navegadores -->
    <link rel="icon" type="image/x-icon" href="{{ asset('images/SEA-SWINWEAR-LOGO.ico') }}">
    <!-- Para navegadores modernos (recomendado) -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/SEA-SWINWEAR-LOGO.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/SEA-SWINWEAR-LOGO.png') }}">
    <title>{{ $title }}</title> 
    {{-- 1. Incluye los estilos y scripts de Livewire --}}
    @livewireStyles
    {{-- 2. Incluye tus assets compilados por Vite (donde está Alpine.js) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="m-0 w-full h-full flex flex-col justify-center items-center">    
    <div class="w-full max-w-[1600px] min-h-dvh grid templ grid-rows-[auto_2fr_auto] sm:grid-rows-[auto_auto_2fr_auto]">
        <!-- Encabezado-contacto de escritorio  -->
        <header class="w-full z-50">
            <div class="relative w-full bg-black text-white flex justify-center items-center">
                <div class="w-full hidden lg:flex justify-around items-center py-2 text-sm border-b border-gray-100  text-[.78rem] font-sans font-medium">
                    <p class="flex flex-row mx-6">
                        <svg class="me-1" xmlns="http://www.w3.org/2000/svg" width="11" height="16" fill="currentColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
                            <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"/>
                        </svg>
                        <span>Dirección: Valencia, Carabobo</span>
                    </p>
                    <p class="flex flex-row ">
                        <svg class="me-1" xmlns="http://www.w3.org/2000/svg" width="11" height="16" fill="currentColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
                            <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"/>
                        </svg>
                        <span>Teléfono: +57 311 475 6873</span>
                    </p>
                    <p class="flex flex-row">
                        <svg class="me-1" xmlns="http://www.w3.org/2000/svg" width="11" height="16" fill="currentColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
                            <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"/>
                        </svg>
                        <span>Correo: seaswinwear.vzla@gmail.com</span>
                    </p>
                </div>
            </div>
            <!-- logo de la empresa -->
            <div class="bg-white hidden lg:flex justify-center items-center py-2">
                <a href="https://seaswimwear.store">
                    <img src="{{ asset('images/SEA-SWINWEAR-LOGO.webp') }}" alt="Logo de la empresa" width="182">
                </a>
            </div>
        </header>
        <!-- Navegación de escritorio -->
        <nav class="hidden lg:block sticky top-0 z-50 text-red-400 bg-linear-to-t from-[#223d37] to-white from-50% to-50%">
            <div class="flex justify-center items-center gap-8 bg-black/80 p-2">
                <ul class="bg-white text-black text-[.9rem]  py-3 px-4 rounded-4xl flex justify-center items-center">
                    <li class="border-r border-gray-300 px-5 hover:text-[#efb7b7] transition-all {{ $type == "inicio" ? "text-[#efb7b7]":"text-black" }}"><a href="https://seaswimwear.store">Inicio</a></li>
                    <li class="border-r border-gray-300 px-5 hover:text-[#efb7b7] transition-all {{ $type == "pedidios" ? "text-[#efb7b7]":"text-black" }}"><a href="https://seaswimwear.store">Pedidos Personalizados</a></li>
                    <li class="border-r border-gray-300 px-5 hover:text-[#efb7b7] transition-all {{ $type == "catalogo-mayorista" ? "text-[#efb7b7]":"text-black" }}"><a href="{{ route('catalogoMay') }}">Catálogo al Mayor</a></li>
                    <li class="border-r border-gray-300 px-5 hover:text-[#efb7b7] transition-all {{ $type == "catalogo-minorista" ? "text-[#efb7b7]":"text-black" }}"><a href="{{ route('catalogoMin') }}">Catálogo al Detal</a></li>
                    <li class="border-r border-gray-300 px-5 hover:text-[#efb7b7] transition-all {{ $type == "preguntas" ? "text-[#efb7b7]":"text-black" }}"><a href="https://seaswimwear.store">Preguntas Frecuentes</a></li>
                    <li class="px-5 hover:text-[#efb7b7] transition-all {{ $type == "contacto" ? "text-[#efb7b7]":"text-black" }}"><a href="https://seaswimwear.store">Contacto</a></li>
                </ul>
                <ul class="font-bold py-3 px-5 border-3 text-white border-white rounded-4xl hover:bg-white hover:text-black transition-all group">
                    <li class="">
                        <a href="{{ route('carrito') }}" class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="w-[1.2rem] m-1 fill-white group-hover:-rotate-40 group-hover:scale-110 group-hover:fill-black transition-transform">
                                <!--!Font Awesome Free v7.1.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M256 144C256 108.7 284.7 80 320 80C355.3 80 384 108.7 384 144L384 192L256 192L256 144zM208 192L144 192C117.5 192 96 213.5 96 240L96 448C96 501 139 544 192 544L448 544C501 544 544 501 544 448L544 240C544 213.5 522.5 192 496 192L432 192L432 144C432 82.1 381.9 32 320 32C258.1 32 208 82.1 208 144L208 192zM232 240C245.3 240 256 250.7 256 264C256 277.3 245.3 288 232 288C218.7 288 208 277.3 208 264C208 250.7 218.7 240 232 240zM384 264C384 250.7 394.7 240 408 240C421.3 240 432 250.7 432 264C432 277.3 421.3 288 408 288C394.7 288 384 277.3 384 264z"/></svg>
                            ¡Pagar Ahora!
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
        <!-- Encabezado-contacto de móvil y Navegación móvil -->
        <header class="w-full bg-white sticky top-0 z-50 lg:hidden" x-data="{ open: false }">
            <div class="font-light flex justify-between items-center px-4 py-3 border-b">
                <div class="text-xs">
                    <p class="flex flex-row items-center mb-1">
                        <svg class="me-1" xmlns="http://www.w3.org/2000/svg" width="14" height="16" fill="currentColor" class="bi bi-whatsapp" viewBox="0 0 16 16">
                            <path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/>
                        </svg>
                        +58 414-419-0539
                    </p>
                    <p class="flex flex-row items-center">
                        <svg class="me-1" xmlns="http://www.w3.org/2000/svg" width="14" height="16" fill="currentColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
                            <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"/>
                        </svg>
                        Valencia, Carabobo
                    </p>
                </div>
                <!-- Logo de la empresa -->
                <div class="grow flex justify-center">
                    <a href="https://seaswimwear.store">
                        <img src="{{ asset('images/SEA-SWINWEAR-LOGO.webp') }}" alt="Logo de la empresa" width="180">
                    </a>
                </div>
                <!-- Botón del menú -->
                <button  type="button" title="Menu" class="text-2xl p-2 focus:outline-none hover:bg-black hover:text-white rounded" @click="open = !open">
                    <svg x-show="!open" class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="red" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    
                    <svg x-show="open" class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <!-- Menú desplegable -->
            <div class="w-full bg-white overflow-hidden"> 
                <nav x-show="open"
                    x-transition:enter="transition ease-out duration-400"
                    x-transition:enter-start="opacity-0 transform -translate-y-full"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform -translate-y-full"
                    class="border-t border-gray-100 pb-2 max-h-screen overflow-y-auto">
                
                    <a href="https://seaswimwear.store" class="block px-4 py-3 border-b border-gray-200 text-xs font-medium hover:bg-gray-100 {{ $type == "inicio" ? "bg-[#efb7b7]":"bg-repeat-space" }}">Inicio</a>
                    <a href="https://seaswimwear.store" class="block px-4 py-3 border-b border-gray-200 text-xs font-medium hover:bg-gray-100 {{ $type == "pedidios" ? "bg-[#efb7b7]":"bg-repeat-space" }}">Pedidos Personalizados</a>
                    <a href="{{ route('catalogoMin') }}" class="block px-4 py-3 border-b border-gray-200 text-xs font-medium hover:bg-gray-100 {{ $type == "catalogo-minorista" ? "bg-[#efb7b7]":"bg-repeat-space" }}">Catálogo al Detal</a>
                    <a href="{{ route('catalogoMay') }}" class="block px-4 py-3 border-b border-gray-200 text-xs font-medium hover:bg-gray-100 {{ $type == "catalogo-mayorista" ? "bg-[#efb7b7]":"bg-repeat-space" }}">Catálogo al Mayor</a>
                    <a href="https://seaswimwear.store" class="block px-4 py-3 border-b border-gray-200 text-xs font-medium hover:bg-gray-100 {{ $type == "preguntas" ? "bg-[#efb7b7]":"bg-repeat-space" }}">Preguntas Frecuentes</a>
                    <a href="https://seaswimwear.store" class="block px-4 py-3 border-b border-gray-200 text-xs font-medium hover:bg-gray-100 {{ $type == "contacto" ? "bg-[#efb7b7]":"bg-repeat-space" }}">Contacto</a>
                    <a href="{{ route('carrito') }}" class="block px-4 py-3 border-b border-gray-200 text-xs font-bold hover:bg-gray-100">¡Pagar Ahora!</a>
                </nav>
            </div>
        </header>
        <!-- Contenido principal -->
        <main class="bg-gray-200 pb-6">
            <div class="bg-gray-200">
                <!-- Contenido dinamico -->
                {{$slot}}
            </div>
        </main>
        <!-- footer -->    
        <footer class="bg-black font-bold text-white text-center text-sm p-4">
            <div class="mb-2">
                Copyright © 2026 Sea Swimwear. All Rights Reserved
            </div>
            <div class="flex justify-center gap-4 font-normal text-gray-400">
                <a href="{{ asset('pdf/Terminos-y-Condiciones-de-Servicio-Sea-Swimwear.pdf') }}" class="hover:text-white transition">Términos y Condiciones</a>
                <span>|</span>
                <a href="{{ asset('pdf/Politica-de-Privacidad-Sea-Swimwear.pdf') }}" class="hover:text-white transition">Política de Privacidad</a>
            </div>
        </footer>
    </div>
    {{-- 4. Incluye los scripts de Livewire al final del body --}}
    @livewireScripts
</body>
</html>
