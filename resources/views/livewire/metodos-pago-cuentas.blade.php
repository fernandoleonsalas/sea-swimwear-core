<div>
    <div class="mt-8 p-6 bg-white shadow-xl sm:rounded-lg relative">
        <div class="p-6 min-h-screen">
            <h2 class="text-3xl font-extrabold text-gray-900 mb-8 border-b pb-2">💳 Métodos De Pago</h2>

            @if (!$selectedMethod)
                <div class="text-center mb-8">
                    <p class="text-xl text-gray-600 mb-6">Selecciona el método de pago que deseas configurar:</p>
                </div>
                <div class="mb-8">
                    <!-- Mensaje de exito -->
                    @if (session()->has('exitoGuardar'))
                        <div class="mt-2 mb-4 p-4 text-sm text-green-700 bg-green-100" role="alert">
                            <span class="font-medium">Éxito:</span> 
                            {!! session('exitoGuardar') !!}
                        </div>
                    @endif

                    <!-- Mensaje de error -->
                    @if (session()->has('errorGuardar'))
                        <div class="mt-2 mb-4 p-4 text-sm text-red-800 rounded-lg bg-red-100" role="alert">
                            <span class="font-medium">Error:</span> 
                            {!! session('errorGuardar') !!}
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                    <div
                        wire:click="$set('selectedMethod', 'pago_movil')"
                        class="group bg-white rounded-xl shadow-lg hover:shadow-2xl transition duration-300 ease-in-out cursor-pointer border-4 border-transparent hover:border-[#d67e7e] p-8 flex flex-col items-center justify-center space-y-4"
                        style="--tw-ring-color: #efb7b7; --tw-shadow-color: #efb7b7; --tw-border-color: #d67e7e;"
                    >
                        <div class="p-4 bg-purple-50 rounded-full group-hover:scale-110 transition-transform">
                            <svg class="w-16 h-16 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        </div>
                        <h3 class="text-2xl font-semibold text-gray-800">Pago Móvil</h3>
                        <p class="text-gray-500 text-center text-sm">Configura banco, documento y teléfono para bolívares.</p>
                    </div>

                    <div
                        wire:click="$set('selectedMethod', 'zelle')"
                        class="group bg-white rounded-xl shadow-lg hover:shadow-2xl transition duration-300 ease-in-out cursor-pointer border-4 border-transparent hover:border-[#d67e7e] p-8 flex flex-col items-center justify-center space-y-4"
                        style="--tw-ring-color: #efb7b7; --tw-shadow-color: #efb7b7; --tw-border-color: #d67e7e;"
                    >
                        <div class="p-4 bg-blue-50 rounded-full group-hover:scale-110 transition-transform">
                            <svg class="w-16 h-16 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        </div>
                        <h3 class="text-2xl font-semibold text-gray-800">Zelle</h3>
                        <p class="text-gray-500 text-center text-sm">Configura el correo electrónico y titular para dólares.</p>
                    </div>
                </div>
            @else
                <div class="max-w-6xl mx-auto mt-10">
                    <button
                        wire:click="$set('selectedMethod', null)"
                        class="mb-6 px-4 py-2 text-sm font-medium rounded-lg hover:bg-opacity-80 transition duration-150 bg-black text-white hover:bg-black/80 focus:outline-none focus:ring-4 focus:ring-gray-300 cursor-pointer"
                    >
                        ← Volver a la Selección
                    </button>
                </div>
                
                <div class="max-w-2xl mx-auto bg-white mt-2 p-8 rounded-xl shadow-2xl border-l-4 border-l-[#efb7b7]">
                    <div class="bg-white overflow-hidden">
                        <div class=" text-xl text-gray-900">
                            <h3 class="text-xl font-bold uppercase tracking-wider">
                                Configurar {{ str_replace('_', ' ', $selectedMethod) }}
                            </h3>
                        </div>

                        <div class="p-8">
                            @if($selectedMethod === 'pago_movil')
                                <form wire:submit.prevent="savePagoMovil" class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Banco</label>
                                        <input type="text" wire:model="pagoMovil.banco" class="w-full mt-1 rounded-lg border-gray-300 focus:ring-[#efb7b7] focus:border-[#d67e7e]" placeholder="Ej: Banesco">
                                        @error('pagoMovil.banco') <span class="text-red-500 mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                                            <input type="text" wire:model="pagoMovil.telefono" class="w-full mt-1 rounded-lg border-gray-300 focus:ring-[#efb7b7] focus:border-[#d67e7e]" placeholder="04121234567">
                                            @error('pagoMovil.telefono') <span class="text-red-500 mt-1">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Documento (Rif/CI)</label>
                                            <input type="text" wire:model="pagoMovil.rif" class="w-full mt-1 rounded-lg border-gray-300 focus:ring-[#efb7b7] focus:border-[#d67e7e]" placeholder="V-12345678">
                                            @error('pagoMovil.rif') <span class="text-red-500 mt-1">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Nombre del Titular</label>
                                        <input type="text" wire:model="pagoMovil.titular" class="w-full mt-1 rounded-lg border-gray-300 focus:ring-[#efb7b7] focus:border-[#d67e7e]" placeholder="Nombre Completo">
                                        @error('pagoMovil.titular') <span class="text-red-500 mt-1">{{ $message }}</span> @enderror
                                    </div>

                                    <button type="submit" class="flex items-center justify-center mt-6 w-full bg-[#d67e7e] text-white py-3 rounded-xl font-bold hover:bg-[#c46a6a] transition shadow-md cursor-pointer">
                                        <svg wire:loading.remove  wire:target="savePagoMovil" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        <svg wire:loading.delay.shortest wire:target="savePagoMovil" class="animate-spin h-5 w-5 text-white  mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        Guardar Datos de Pago Móvil
                                    </button>
                                </form>
                            @else
                                <form wire:submit.prevent="saveZelle" class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Correo Electrónico (Zelle)</label>
                                        <input type="email" wire:model="zelle.email" class="w-full mt-1 rounded-lg border-gray-300 focus:ring-[#efb7b7] focus:border-[#d67e7e]" placeholder="pago@ejemplo.com">
                                        @error('zelle.email') <span class="text-red-500 mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Nombre del Titular</label>
                                        <input type="text" wire:model="zelle.titular" class="w-full mt-1 rounded-lg border-gray-300 focus:ring-[#efb7b7] focus:border-[#d67e7e]" placeholder="Nombre Completo">
                                        @error('zelle.titular') <span class="text-red-500 mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    <button type="submit" class="flex items-center justify-center mt-6 w-full bg-[#d67e7e] text-white py-3 rounded-xl font-bold hover:bg-[#c46a6a] transition shadow-md cursor-pointer">
                                        <svg wire:loading.remove  wire:target="saveZelle" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        <svg wire:loading.delay.shortest wire:target="saveZelle" class="animate-spin h-5 w-5 text-white  mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        Guardar Datos de Zelle
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
