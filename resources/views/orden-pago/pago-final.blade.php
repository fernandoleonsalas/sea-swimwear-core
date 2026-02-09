<!-- Está es la Vista del formulario orden de pago -->
<!-- Utiliza el componente o plantilla base-main.blade.php y Utiliza el componente Livewire -->
<x-base-main title="Finalizar Compra - Sea Swimwear" type="Finalizar-Compra-Sea-Swimwear">
    @if (!empty($orden))
        <!-- Componente livewire que contiene formulario de token y pago final -->
        <div>
            @livewire('confirmar-pago-final',['orden' => $orden])
        </div>
    @else
        <!-- Formulario ingresar token pago -->
        <div class="flex justify-center select-none">
            <div class="mt-12 p-8 bg-white rounded-xl shadow-2xl antialiased max-w-md w-full">
                <!-- Titulo -->
                <h2 class="flex align-content-center justify-center items-center text-center text-3xl font-extrabold text-gray-900">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r to-gray-700 from-black mr-1"> Finalizar Compra</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#364153" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-cash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 15h-3a1 1 0 0 1 -1 -1v-8a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v3" /><path d="M7 9m0 1a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v8a1 1 0 0 1 -1 1h-12a1 1 0 0 1 -1 -1z" /><path d="M12 14a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /></svg>
                </h2>

                <p class="text-gray-600 text-center mt-2 mb-8">Por favor, ingresa el token que recibiste para completar el 50% restante de tu compra.</p>
                <!-- Formulario -->
                <form method="POST" action="{{ route('validarToken') }}" method="POST">
                    @csrf
                    <div class="mb-6">
                        <label for="token" class="block text-gray-700 text-base font-medium mb-3">Token de Pago:</label>
                        <input type="text" **id="token" name="token"** placeholder="Ingresa tu token aquí" required
                        class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-[#e8a3a3] focus:border-transparent">
                        @error('token')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror

                        <!-- Mensaje de error -->
                        @if (session()->has('error_token'))
                            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 20000)" x-transition:leave.duration.500ms
                            class="p-4 mt-4 m mb-4 text-red-700 bg-red-100 rounded-sm flex flex-col"  role="alert">
                                <div class="flex text-sm">
                                    {{ session('error_token') ?? "Ocurrió un error. Inténtalo de nuevo." }} 
                                    <button @click="show = false" class="ml-auto focus:outline-none text-lg cursor-pointer">&times;</button>
                                </div>
                            </div>
                        @endif

                    </div>

                    <div class="flex items-center justify-center">
                        <button
                        type="submit"
                        class="bg-[#e8a3a3] hover:bg-[#d38c8c] text-white font-bold py-3 px-6 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#e8a3a3]/50 focus:ring-opacity-50 transition duration-300 ease-in-out cursor-pointer">
                            Finalizar Compra
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</x-base-main>
