<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- Titulo de la página -->
        <title>{{ $title ?? config('app.name', 'Laravel') }}</title>
        <!-- Favicon estándar para la mayoría de navegadores -->
        <link rel="icon" type="image/x-icon" href="{{ asset('images/SEA-SWINWEAR-LOGO.ico') }}">
        <!-- Para navegadores modernos (recomendado) -->
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/SEA-SWINWEAR-LOGO.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/SEA-SWINWEAR-LOGO.png') }}">
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div class="grid min-h-screen grid-rows-[auto_1fr_auto] bg-gray-200 gap-8">
            <!-- Header -->
            <div class="h-4 w-full opacity-0 sm:opacity-100">
                <div class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" class="absolute -top-2">
                        <path fill="#000" fill-opacity="1" d="M0,256L80,213.3C160,171,320,85,480,42.7C640,0,800,0,960,0C1120,0,1280,0,1360,0L1440,0L1440,0L1360,0C1280,0,1120,0,960,0C800,0,640,0,480,0C320,0,160,0,80,0L0,0Z"></path>
                    </svg>
                    <img src="" alt="">
                    <img src="{{ asset('images/icono_traje.png') }}" title="SEA SWINWEAR" alt="Logo de la empresa" width="220" class="absolute top-3 left-6 w-[calc(10%-1rem)]">
                </div>
            </div>
            <!-- Body -->
            <div class="flex flex-col sm:justify-center items-center pt-6 px-4 sm:pt-0 z-50">
                <!-- Logo de empresa -->
                <div>
                    <a href="/" wire:navigate>
                        <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                    </a>
                </div>

                <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white  shadow-md overflow-hidden rounded-lg">
                    {{ $slot }}
                </div>
            </div>
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
        @livewireScripts
    </body>
</html>
