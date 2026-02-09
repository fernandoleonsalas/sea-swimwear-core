@props(['title' => null])
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
        <div class="grid min-h-screen grid-rows-[auto_auto_1fr_auto] bg-gray-200">

            <livewire:layout.navigation />
            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif
            <!-- Page Content -->
            <main class="px-1 md:p-0">
                {{ $slot }}
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
    </body>
</html>
