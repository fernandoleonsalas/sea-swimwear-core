<div>
    <div class="mt-10 max-w-2xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="bg-linear-to-r from-[#f8b4b4] to-[#e48383] p-6 text-white">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-white/20 rounded-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zM17 13v-2V7l-5-3-5 3v4v2l5 3 5-3z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold">Tasa de Cambio</h3>
                    <p class="text-sm font-semibold">Actualiza el valor del dólar para el cálculo de precios</p>
                </div>
            </div>
        </div>
        <div class="p-8">
            <form wire:submit.prevent="guardarTasa" id="formGuardarTasa">
                <div class="space-y-6">
                    <div>
                        <label for="tasa" class="block text-sm font-medium text-gray-700 mb-2">
                            Valor del Dólar (USD a Moneda Local)
                        </label>
                        <div class="relative mt-1 rounded-md shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                <span class="text-gray-500 sm:text-lg">Bs</span>
                            </div>
                            <input type="number" id="tasa"  step="0.01" min="0"
                                wire:model="campoTasaCambio"
                                class="block w-full rounded-xl border-gray-300 pl-10 text-2xl font-bold focus:border-[#d67e7e] focus:ring-[#efb7b7] py-4 pr-22" 
                                placeholder="0.00" 
                            >
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4">
                                <span class="text-gray-400 font-medium" id="price-currency">VES/USD</span>
                            </div>
                        </div>
                        @error('campoTasaCambio') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

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

                    <button type="submit" form="formGuardarTasa" wire:target="guardarTasa"
                        class="w-full bg-[#d67e7e] hover:bg-[#c46a6a] text-white font-bold py-4 px-6 rounded-xl transition duration-200 shadow-lg flex items-center justify-center space-x-2 cursor-pointer">
                        <svg wire:loading.remove  wire:target="guardarTasa" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <svg wire:loading.delay.shortest wire:target="guardarTasa" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span>Actualizar Tasa de Cambio</span>
                    </button>
                </div>
            </form>

            <div class="mt-6 p-4 bg-gray-100 rounded-lg border border-dashed border-gray-300">
                <p class="text-xs text-gray-600 flex items-start">
                    <svg class="w-4 h-4 mr-2 text-[#d67e7e] shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                    Al cambiar esta tasa, todos los precios calculados automáticamente en el catálogo se actualizarán de inmediato según el valor ingresado.
                </p>
            </div>
        </div>
    </div>
</div>
