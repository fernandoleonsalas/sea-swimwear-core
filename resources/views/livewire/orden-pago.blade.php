<div class="bg-white p-6 max-w-4xl mt-10 mx-3 lg:mx-auto shadow-xl rounded-lg">     
    @if ($tasaDolar != false && (!blank($pagoMovilMetodo) || !blank($zelleMetodo))) 
        @if(!empty($cartData))
            <!-- Titulo -->
            <h1 class="mb-6 pl-3 text-2xl font-extrabold text-gray-900 dark:text-white md:text-3xl lg:text-3xl">
                <span class="text-transparent bg-clip-text bg-linear-to-r to-gray-700 from-black"> Finalizar Compra</span>
                🛒 
            </h1>  

            <!-- Seccion de consultar cedula -->
            <div class="w-full mx-auto mb-10 p-4 border border-[#ffa2a2] rounded-xl bg-[#ffa2a22e] shadow-lg">
                <div class="inline-block px-4 px py-1.5 mb-4 bg-[#ffa2a2] text-black font-bold rounded-lg shadow-md">
                    <span class="text-xl">⚡</span> COMPRA RÁPIDA
                </div>

                <p class="text-lg font-semibold text-black mb-4">
                    ¿Ya has comprado antes?
                </p>
                
                <div class="flex items-center space-x-2">
                    <input 
                        type="text" wire:model.defer="buscarCedula"
                        placeholder="Ingresa tu Cédula para autocompletar" 
                        class="flex-1 p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ffa2a2] transition duration-150"
                    >

                    <button wire:click.prevent="consultarCedula" wire:target="consultarCedula" class="flex items-center justify-center bg-black text-white font-bold py-3 px-5 rounded-lg hover:bg-[#151515] transition duration-150 shadow-md cursor-pointer" title="Buscar">
                        <span wire:loading.remove wire:target="consultarCedula" class="flex row"> 
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        <span wire:loading.delay.shortest wire:target="consultarCedula" class="hidden">
                            <svg class="animate-spin  h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-100" fill="#fff" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>
                </div>

                @error('buscarCedula') <p class="mt-1 text-red-500 text-sm">{{ $message }}</p> @enderror

                @if(Session::has('flash_message_success'))
                    <div class="mt-4 text-sm font-semibold text-green-800">
                        {{ Session::get('flash_message_success') }}
                    </div>
                @elseif(Session::has('flash_message_error'))
                    <div class="mt-4 text-sm font-semibold text-red-800">
                        {{ Session::get('flash_message_error') }}
                    </div>
                @else
                    <p class="mt-4 text-sm text-gray-700">
                        Tus datos se cargarán de forma segura y enmascarada.
                    </p>
                @endif
            </div>

            <!-- Formulario -->
            <form wire:submit.prevent="confirmarPedido" enctype="multipart/form-data" x-data="{ isProcessing: false }" x-on:submit="isProcessing = true" class="mb-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Seccion formulario -->
                    <div class="col-span-3 md:col-span-2 space-y-6">
                        <!-- Seccion datos del cliente -->
                        <h3 class="text-xl font-semibold border-b pb-2 mb-4">1. Datos del Comprador</h3>
                        <div class="space-y-4" wire:key="item-{{ $buscarCedula }}">
                            <!-- campo cedula cliente -->
                            <input wire:model.defer="customer_cedula" type="text" placeholder="Cédula" class="w-full p-3 border border-gray-300 rounded focus:ring-gray-500 focus:border-gray-800 
                            {{ !empty($buscarCedula)  ? 'normal-case bg-gray-200' : 'capitalize bg-white' }}" 
                            {{ !empty($buscarCedula) ? 'disabled' : '' }}>
                            @error('customer_cedula') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror

                            <!-- campo nombre cliente -->
                            <input wire:model.defer="customer_name" type="text" placeholder="Nombre Completo" class="w-full p-3 border border-gray-300 rounded focus:ring-gray-500 focus:border-gray-800 {{ $datosEncontrados ? 'normal-case bg-gray-200' : 'capitalize bg-white' }}" {{ $datosEncontrados ? 'disabled' : '' }}>
                            @error('customer_name') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror

                            <!-- Campo correo Electrónico -->
                            <input wire:model.defer="client_email" type="email" placeholder="Correo Electrónico" class="w-full p-3 border border-gray-300 rounded focus:ring-gray-500 focus:border-gray-800  {{ $datosEncontrados ? 'normal-case bg-gray-200' : ' bg-white' }}" {{ $datosEncontrados ? 'disabled' : '' }}>
                            @error('client_email') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                            <!-- Campo telefono -->
                            <input wire:model.defer="customer_phone" type="text" placeholder="Teléfono" class="w-full p-3 border border-gray-300 rounded focus:ring-gray-500 focus:border-gray-800  {{ $datosEncontrados ? 'normal-case bg-gray-200' : ' bg-white' }}" {{ $datosEncontrados ? 'disabled' : '' }}>
                            @error('customer_phone') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                            <!-- Campo dirección de la habitación -->
                            <input wire:model.defer="customer_adress" type="text" placeholder="Dirección de la habitación" class="w-full p-3 border border-gray-300 rounded focus:ring-gray-500 focus:border-gray-800 capitalize {{ $datosEncontrados ? 'normal-case bg-gray-200' : 'capitalize bg-white' }}" {{ $datosEncontrados ? 'disabled' : '' }}>
                            @error('customer_adress') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                        </div>

                        <!-- Seccion Mostrar datos de la empresa -->
                        <div class="space-y-4">
                            <h3 class="text-xl font-semibold border-b pb-2 mb-4">2. Método de Pago</h3>

                            {{-- Selector de Métodos de Pago --}}
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-8">
                                {{-- Opción Pago Móvil --}}
                                @if (!blank($pagoMovilMetodo))
                                    <button
                                        type="button"
                                        wire:click="selectMethod('pago-movil')"
                                        class="flex flex-col items-center justify-center p-4 rounded-lg transition duration-200 border-2 cursor-pointer {{
                                            $selectedMethod === 'pago-movil'
                                                ? 'bg-[#ffa2a22e] border-[#ffa2a2] text-black font-semibold'
                                                : 'bg-gray-50 border-gray-300 text-gray-600 hover:bg-[#ffa2a22e]'
                                        }}">
                                    
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                                        </svg>

                                        <span class="mt-1">P. Móvil</span>
                                    </button>
                                @endif

                                {{-- Opción Zelle --}}
                                @if (!blank($zelleMetodo))
                                    <button
                                        type="button"
                                        wire:click="selectMethod('zelle')"
                                        class="flex flex-col items-center justify-center p-4 rounded-lg transition duration-200 border-2 cursor-pointer {{
                                            $selectedMethod === 'zelle'
                                                ? 'bg-blue-100 border-blue-500 text-blue-700 font-semibold fill-blue-700'
                                                : 'bg-gray-50 border-gray-300 text-gray-600 hover:bg-blue-50 fill-gray-600'
                                        }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill=""><path d="M200-280v-280h80v280h-80Zm240 0v-280h80v280h-80ZM80-120v-80h800v80H80Zm600-160v-280h80v280h-80ZM80-640v-80l400-200 400 200v80H80Zm178-80h444-444Zm0 0h444L480-830 258-720Z"/></svg>
                                        Zelle
                                    </button>
                                @endif
                            </div>

                            <div class="space-y-6">
                                @if ($selectedMethod === 'pago-movil' && !blank($pagoMovilMetodo))
                                    {{-- Datos para Pago Móvil --}}
                                    <div class="bg-[#ffa2a22e] border-[#ffa2a2] border-l-4 p-4 rounded">
                                        <h3 class="font-semibold text-lg mb-3 text-black flex flex-row space-x-1 items-center capitalize">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                                            </svg>
                                            <span>Datos para Pago Móvil</span>
                                        </h3>
                                        <!-- Campo tipo banco -->
                                        <div>
                                            <div 
                                            x-data="{ 
                                                copied: false,
                                                copyValue(id) {
                                                    navigator.clipboard.writeText(id).then(() => {
                                                        this.copied = true;
                                                        // Mostrar el check de copiado por 2 segundos
                                                        setTimeout(() => { this.copied = false }, 2000);
                                                    })
                                                    .catch(err => {
                                                        console.error('Error al intentar copiar:', err);
                                                    });
                                                }
                                            }"
                                            class="my-5 border border-gray-300 rounded-lg overflow-hidden shadow-sm"
                                            >
                                                <div class="flex items-stretch bg-white">
                                                    <div class="w-20 px-4 py-3 bg-gray-100 text-sm font-medium text-gray-700 whitespace-nowrap border-r border-gray-300 flex items-center">
                                                        Banco
                                                    </div>

                                                    <div class="grow p-0">
                                                        <input type="text" readonly class="w-full h-full p-3 text-sm text-gray-900 border-none focus:ring-0 bg-transparent" aria-label="ID de Pago"
                                                        :value="$wire.banco" {{-- Aquí Alpine lee directamente la propiedad de Livewire --}} >
                                                    </div>

                                                    <button type="button" class="flex items-center justify-center p-3 transition duration-150 border-l border-gray-300 relative cursor-pointer" aria-label="Copiar ID de Pago" 
                                                    @click.stop="copyValue($wire.banco)" {{-- Pasamos la propiedad de Livewire como argumento --}}
                                                    :class="{'text-green-500 bg-green-50 hover:bg-green-100': copied, 'text-gray-500 hover:bg-gray-200 hover:text-gray-700': !copied}"
                                                    >
                                                        <svg x-show="!copied" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                            <path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" />
                                                            <path d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z" />
                                                        </svg>
                                                        <svg x-show="copied" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 13.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Campo de cedula -->
                                        <div>
                                            <div 
                                                x-data="{ 
                                                    copied: false,
                                                    copyValue(id) {
                                                    navigator.clipboard.writeText(id)
                                                        .then(() => {
                                                        this.copied = true;
                                                        // Mostrar el check de copiado por 2 segundos
                                                        setTimeout(() => { this.copied = false }, 2000);
                                                        })
                                                        .catch(err => {
                                                            console.error('Error al intentar copiar:', err);
                                                        });
                                                    }
                                                }"
                                                class="my-5 border border-gray-300 rounded-lg overflow-hidden shadow-sm"
                                                >
                                                <div class="flex items-stretch bg-white">
                                                    <div class="w-20 px-4 py-3 bg-gray-100 text-sm font-medium text-gray-700 whitespace-nowrap border-r border-gray-300 flex items-center">
                                                        (Rif/CI)
                                                    </div>

                                                    <div class="grow p-0">
                                                        <input type="text" readonly class="w-full h-full p-3 text-sm text-gray-900 border-none focus:ring-0 bg-transparent" aria-label="C.I de Pago"
                                                        :value="$wire.cedulaBanco" {{-- Aquí Alpine lee directamente la propiedad de Livewire --}} >
                                                    </div>

                                                    <button type="button" class="flex items-center justify-center p-3 transition duration-150 border-l border-gray-300 relative cursor-pointer" aria-label="Copiar C.I de Pago" 
                                                    @click.stop="copyValue($wire.cedulaBanco)" {{-- Pasamos la propiedad de Livewire como argumento --}}
                                                    :class="{'text-green-500 bg-green-50 hover:bg-green-100': copied, 'text-gray-500 hover:bg-gray-200 hover:text-gray-700': !copied}"
                                                    >
                                                        <svg x-show="!copied" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                            <path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" />
                                                            <path d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z" />
                                                        </svg>
                                                        <svg x-show="copied" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 13.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Campo de telefono -->
                                        <div>
                                            <div 
                                                x-data="{ 
                                                    copied: false,
                                                    copyValue(id) {
                                                    navigator.clipboard.writeText(id)
                                                        .then(() => {
                                                        this.copied = true;
                                                        // Mostrar el check de copiado por 2 segundos
                                                        setTimeout(() => { this.copied = false }, 2000);
                                                        })
                                                        .catch(err => {
                                                            console.error('Error al intentar copiar:', err);
                                                        });
                                                    }
                                                }"
                                                class="my-5 border border-gray-300 rounded-lg overflow-hidden shadow-sm"
                                                >
                                                <div class="flex items-stretch bg-white">
                                                    <div class="w-20 px-4 py-3 bg-gray-100 text-sm font-medium text-gray-700 whitespace-nowrap border-r border-gray-300 flex items-center">
                                                        Teléfono
                                                    </div>

                                                    <div class="grow p-0">
                                                        <input type="text" readonly class="w-full h-full p-3 text-sm text-gray-900 border-none focus:ring-0 bg-transparent" aria-label="teléfono de Pago"
                                                        :value="$wire.telefonoBanco" {{-- Aquí Alpine lee directamente la propiedad de Livewire --}} >
                                                    </div>

                                                    <button type="button" class="flex items-center justify-center p-3 transition duration-150 border-l border-gray-300 relative cursor-pointer" aria-label="Copiar teléfono de Pago" 
                                                    @click.stop="copyValue($wire.telefonoBanco)" {{-- Pasamos la propiedad de Livewire como argumento --}}
                                                    :class="{'text-green-500 bg-green-50 hover:bg-green-100': copied, 'text-gray-500 hover:bg-gray-200 hover:text-gray-700': !copied}"
                                                    >
                                                        <svg x-show="!copied" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                            <path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" />
                                                            <path d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z" />
                                                        </svg>
                                                        <svg x-show="copied" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 13.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Campo de nombre titular -->
                                        <div>
                                            <div class="my-5 border border-gray-300 rounded-lg overflow-hidden shadow-sm">
                                                <div class="flex items-stretch bg-white">
                                                    <div class="w-20 px-4 py-3 bg-gray-100 text-sm font-medium text-gray-700 whitespace-nowrap border-r border-gray-300 flex items-center">
                                                        Titular
                                                    </div>
                                                    <span class="flex  items-center text-sm pl-3 text-gray-700 capitalize">{{ $titularBanco }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                @elseif ($selectedMethod === 'zelle' && !blank($zelleMetodo))
                                    {{-- Datos para Zelle --}}
                                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                                        <h3 class="font-semibold text-lg mb-3 text-blue-700 flex flex-row space-x-1 items-center capitalize">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1447e6"><path d="M200-280v-280h80v280h-80Zm240 0v-280h80v280h-80ZM80-120v-80h800v80H80Zm600-160v-280h80v280h-80ZM80-640v-80l400-200 400 200v80H80Zm178-80h444-444Zm0 0h444L480-830 258-720Z"/></svg>
                                            <span>Datos para Zelle</span>
                                        </h3>
                                        <!-- Campo de email -->
                                        <div>
                                            <div 
                                                x-data="{ 
                                                    copied: false,
                                                    copyToClipboardEmail(id) {
                                                    navigator.clipboard.writeText(id)
                                                        .then(() => {
                                                        this.copied = true;
                                                        // Mostrar el check de copiado por 2 segundos
                                                        setTimeout(() => { this.copied = false }, 2000);
                                                        })
                                                        .catch(err => {
                                                            console.error('Error al intentar copiar:', err);
                                                        });
                                                    }
                                                }"
                                                class="my-5 border border-gray-300 rounded-lg overflow-hidden shadow-sm"
                                                >
                                                <div class="flex items-stretch bg-white">
                                                    <div class="w-20 px-4 py-3 bg-gray-100 text-sm font-medium text-gray-700 whitespace-nowrap border-r border-gray-300 flex items-center">
                                                        Email
                                                    </div>

                                                    <div class="grow p-0">
                                                        <input type="text" readonly class="w-full h-full p-3 text-sm text-gray-900 border-none focus:ring-0 bg-transparent" aria-label="Email de Pago"
                                                        :value="$wire.emailZelle" {{-- Aquí Alpine lee directamente la propiedad de Livewire --}} >
                                                    </div>

                                                    <button type="button" class="flex items-center justify-center p-3 transition duration-150 border-l border-gray-300 relative cursor-pointer" aria-label="Copiar Email de Pago" 
                                                    @click.stop="copyToClipboardEmail($wire.emailZelle)" {{-- Pasamos la propiedad de Livewire como argumento --}}
                                                    :class="{'text-green-500 bg-green-50 hover:bg-green-100': copied, 'text-gray-500 hover:bg-gray-200 hover:text-gray-700': !copied}"
                                                    >
                                                        <svg x-show="!copied" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                            <path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" />
                                                            <path d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z" />
                                                        </svg>
                                                        <svg x-show="copied" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 13.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Campo de nombre titular -->
                                        <div>
                                            <div class="my-5 border border-gray-300 rounded-lg overflow-hidden shadow-sm">
                                                <div class="flex items-stretch bg-white">
                                                    <div class="w-20 px-4 py-3 bg-gray-100 text-sm font-medium text-gray-700 whitespace-nowrap border-r border-gray-300 flex items-center">
                                                        Titular
                                                    </div>
                                                    <span class="flex  items-center text-sm pl-3 text-gray-700 capitalize">{{ $titularBancoZelle }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <hr>

                            <label class="block text-gray-700 font-medium mb-2">Monto a Pagar Ahora:</label>

                            <div class="flex space-x-4">
                                <label class="inline-flex items-center">
                                    <input wire:model.live.debounce.300ms="payment_intention" type="radio" value="50" name="payment_intention" class="form-radio text-gray-900 focus:border-gray-800 focus:ring-gray-500 cursor-pointer">
                                    <span class="ml-2">50% de Abono</span>
                                </label>

                                <label class="inline-flex items-center">
                                    <input wire:model.live.debounce.300ms="payment_intention" type="radio" value="100" name="payment_intention" class="form-radio text-gray-900 focus:border-gray-800 focus:ring-gray-500 cursor-pointer">
                                    <span class="ml-2">100% Total</span>
                                </label>
                            </div>

                            <!-- Seccion metodo de pago y comprobante -->
                            <div class="space-y-4" wire:key="item-{{ $payment_method }}">
                                <label for="payment_method" class="block text-gray-700 font-medium mb-2">Método de pago:</label>

                                <select wire:model.defer="payment_method" id="payment_method" class="w-full p-3 border border-gray-300 focus:border-[#ffa2a2] focus:ring-[#ffa2a2] rounded">
                                    <!-- <option value="Transferencia">Transferencia Bancaria</option> -->
                                    <option value="pago-movil">Pago Móvil</option>
                                    <option value="zelle">Zelle</option>
                                </select>
                                
                                @error('payment_method') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror


                                <label for="reference_number" class="block font-medium text-gray-700">
                                    Número de Referencia / ID Transacción
                                </label>
                                <input wire:model.defer="reference_number" id="reference_number" type="text" placeholder="Número de Referencia del Pago" class="w-full p-3 border border-gray-300 rounded focus:border-[#ffa2a2] focus:ring-[#ffa2a2]">
                                @error('reference_number') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror

                                <label class="block text-gray-700 font-medium">Adjuntar Captura de Pago (Imagen):</label>
                                <input wire:model="comprobante" type="file" class="w-full p-3 border border-dashed border-gray-400 rounded-md bg-gray-50 text-gray-500">
                                @error('comprobante') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                            </div>

                            <hr>

                            {{-- Verificación de Seguridad --}}
                            <div class="pt-4">
                                <h3 class="font-semibold text-lg mb-3 text-primary">🛡️ Verificación de Seguridad</h3>
                                <div class="flex items-center space-x-3">
                                    <label for="securityAnswer" class="text-gray-700">¿Cuánto es <b>{{ $numero1 }}</b> + <b>{{ $numero2 }}</b>?</label>
                                    <input
                                        type="text"
                                        id="securityAnswer"
                                        wire:model.defer="securityAnswer"
                                        class="w-20 rounded-md border-gray-300 shadow-sm focus:border-[#ffa2a2] focus:ring-[#ffa2a2] @error('securityAnswer') border-red-500 @enderror"
                                        placeholder="?"
                                    >
                                </div>
                                @error('securityAnswer') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>


                            {{-- Términos y Condiciones --}}
                            <div class="flex items-start pt-4">
                                <input
                                    type="checkbox"
                                    id="termsAccepted"
                                    wire:model="termsAccepted"
                                    class="mt-1 h-4 w-4 text-[#ffa2a2] border-gray-300 rounded focus:ring-[#ffa2a2] @error('termsAccepted') border-red-500 @enderror"
                                >
                                <label for="termsAccepted" class="ml-2 text-sm text-gray-600">
                                    He leído y acepto los 
                                    <a href="{{ asset('pdf/Terminos-y-Condiciones-de-Servicio-Sea-Swimwear.pdf') }}" class="text-blue-600 hover:underline">Términos y Condiciones</a> y las 
                                    <a href="{{ asset('pdf/Politica-de-Privacidad-Sea-Swimwear.pdf') }}" class="text-blue-600 hover:underline">Políticas de Privacidad</a>.
                                </label>

                            </div>
                            @error('termsAccepted') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Seccion resumen del pago -->
                    <div class="col-span-3 md:col-span-1 bg-gray-50 p-6 rounded-lg shadow-inner sticky top-20 self-start">
                        <h3 class="font-bold mb-4 border-b-2 pb-1 border-l-3 border-[#ea9b9b] uppercase text-base text-center ">Resumen Final</h3>

                        <div class="space-y-3 mb-2">
                            <div class="flex justify-between text-base text-gray-600 my-1">
                                <span class="text-sm">Total <b>({{ $payment_intention }}%)</b></span>
                                <span class="font-semibold">${{ number_format($paid_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-base text-gray-600">
                                <span class="text-sm">Ref.</span>
                                <span class="text-sm font-semibold">{{ number_format($paid_amount * $tasaDolar, 2) }} Bs</span>
                            </div>
                            <div class="flex justify-between text-red-500 py-2 border-y border-dashed">
                                <span class="text-sm italic font-semibold">Saldo Pendiente:</span>
                                <span class="text-sm font-bold">${{ number_format($totalPurchase - $paid_amount, 2) }}</span>
                            </div>


                            <button type="submit" wire:target="confirmarPedido" wire:loading.attr="disabled" wire:loading.class="opacity-75"
                            class="w-full swpace-y-1 text-center py-4 rounded-2xl border border-white/10 bg-black cursor-pointer hover:bg-black/85">
                                <span wire:loading.remove wire:target="confirmarPedido">
                                    <p class="text-[#ea9b9b] text-xs font-black uppercase tracking-[0.2em]">Pagar Pedido</p>
                                    <p class="text-2xl font-black text-white">${{ number_format($paid_amount, 2) }}</p>
                                    <p class="text-gray-300 font-medium text-sm">{{ number_format($paid_amount * $tasaDolar, 2) }} Bs.</p>
                                </span>
                                <span wire:loading wire:target="confirmarPedido" class="text-white">Procesando...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Mensajes Genearales -->
            @if (session()->has('message_exitoso'))
                <div x-data="{ show: true }"
                    x-show="show" 
                    x-init="setTimeout(() => show = false, 7000)" 
                    x-transition:leave.duration.500ms
                    class="p-4 mt-2 mb-4 text-green-700 bg-green-100 rounded-sm flex" 
                    role="alert">
                    
                    {{ session('message_exitoso') ?? "Operación exitosa."}}
                    
                    <button @click="show = false" class="ml-auto focus:outline-none">&times;</button>
                </div>
            @endif

            @if (session()->has('error_form_orden'))
                <div x-data="{ show: true }"
                    x-show="show" 
                    x-init="setTimeout(() => show = false, 20000)"
                    x-transition:leave.duration.500ms
                    class="p-4 mt-2 m mb-4 text-red-700 bg-red-100 rounded-sm flex flex-col" 
                    role="alert"
                >
                    <div class="flex">
                        {{ session('error_form_orden') ?? "Ocurrió un error. Inténtalo de nuevo." }} 
                        <button @click="show = false" class="ml-auto focus:outline-none">&times;</button>
                    </div>

                    @if($hasErrors)
                        <div class="mt-2">
                            @foreach ($varianteSinStock as $v)
                                <p class="text-[.85rem] font-bold">{{ $v }}</p>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        @else
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded" role="alert">
                <p class="font-bold">Carrito Vacío</p>
                <p>Tu carrito de compras está vacío. Por favor, agrega productos antes de proceder con el pago.</p>
            </div>
        @endif
    @else
        <!-- MENSAJES DE ERROR GENERAL -->
        @if (session()->has('errorTasaOPago'))
            <div class="p-4 m-8 text-red-700 bg-red-100 rounded-sm border-red-500 border-l-4 text-sm"  role="alert">
                <p class="font-bold mb-1">Atención!</p>
                <p>{{ session('errorTasaOPago') }}</p>
            </div>
        @endif
    @endif
</div>
