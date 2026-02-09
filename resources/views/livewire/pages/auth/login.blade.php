<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest', ['title' => 'Iniciar Sesión'])] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login()
    {
        // 1. LA VALIDACIÓN DE CAMPOS VACÍOS (frontend)
        $this->validate();
        // 2. LA LÓGICA CRÍTICA: Autenticación
        $this->form->authenticate();
        // 3. ÉXITO (Si llegamos aquí)
        Session::regenerate();
        // 4. Redirigir al usuario al dashboard
        return $this->redirect(route('dashboard', absolute: false), navigate: false);
    }
}; 
?>

<div>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login">
        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="form.email" id="email" class="block mt-1 w-full" type="email" name="email" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input wire:model="form.password" id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember" class="inline-flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox" class="rounded  border-gray-900 text-black shadow-sm focus:ring-gray-600" name="remember">
                <span class="ms-2 text-sm text-black">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-700  hover:text-black rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black" href="{{ route('password.request') }}" wire:navigate>
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            {{-- BOTÓN DE INICIO DE SESIÓN CON CARGADOR --}}
            <x-primary-button class="ms-3" wire:loading.attr="disabled" wire:target="login">
                {{-- Mantiene 'wire:target="login"' SOLO en el botón padre --}}

                {{-- Contenido normal del botón (visible cuando NO está cargando) --}}
                <span wire:loading.remove> 
                    {{ __('Log in') }}
                </span>
                
                {{-- Indicador de carga (visible cuando SÍ está cargando) --}}
                {{-- Añadimos 'hidden' por defecto para evitar que se muestre al inicio --}}
                <span wire:loading.delay.shortest class="items-center hidden">
                    <div class="flex row">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ __('Cargando...') }}
                    </div>
                </span>
            </x-primary-button>
        </div>
    </form>
</div>
