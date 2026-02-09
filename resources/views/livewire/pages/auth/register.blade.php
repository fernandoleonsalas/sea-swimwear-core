<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Illuminate\Validation\Rules\Password; // para requerir mayúsculas, minúsculas y símbolo.

new #[Layout('layouts.guest', ['title' => 'Crear una cuenta'])] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    // Mensajes personalizado
    protected $messages = [
        // Mensajes para 'name'
        'name.required' => 'El nombre es obligatorio.',
        'name.max'      => 'El nombre no debe exceder los 100 caracteres.',
        'name.regex'    => 'El nombre solo puede contener letras, espacios y guiones.', // Mensaje personalizado

        // Mensajes para 'email'
        'email.required' => 'El correo electrónico es obligatorio.',
        'email.email'    => 'El correo electrónico debe tener un formato válido (ej. usuario@dominio.com).', // Cubre el email:rfc,dns
        'email.unique'   => 'Este correo electrónico ya está registrado.',

        // Mensajes para 'password'
        'password.required'   => 'La contraseña es obligatoria.',
        'password.confirmed'  => 'Las contraseñas no coinciden.',
        // Laravel genera automáticamente mensajes para las sub-reglas de Password::defaults(),
        // pero puedes personalizar el de 'uncompromised' así:
        'password.uncompromised' => 'Esta contraseña es insegura. Por favor, elige otra.',
    ];

    /**
     * Handle an incoming registration request.
     */
    public function register()
    {
        // 1. Definición y ejecución de la validación
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:100', 'regex:/^[\pL\s\-]+$/u'], // 'regex:/^[\pL\s\-]+$/u' aceptar solo letras, espacios y guiones
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'email:rfc,dns', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()->uncompromised()], // Password::defaults() Min 8, letra, número,  ->uncompromised(), Verifica que la contraseña no esté en bases de datos de contraseñas filtradas (Pwned Passwords)
        ]);

        // 2. Agregar el valor de roles_id al array validado
        $validated['role_id'] = 1; // En este caso es administrador

        // 3. Hashing de la contraseña es decir convertir la contraseña en una cadena segura
        $validated['password'] = Hash::make($validated['password']); 

        // 4. Crear el usuario
        event(new Registered($user = User::create($validated)));

        // 5. Iniciar sesión con el usuario recién creado
        Auth::login($user);
        
        // 6. Redirigir al usuario al dashboard
        return $this->redirect(route('dashboard', absolute: false), navigate: false);
    }
}; 
?>

<div>
    <form wire:submit="register">
        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input wire:model="name" id="name" class="block mt-1 w-full capitalize" type="text" name="name"  autocomplete="name" autofocus required/>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" autocomplete="off" autofocus required/>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input wire:model="password" id="password" class="block mt-1 w-full" type="password" name="password" autocomplete="new-password" autofocus required/>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" autocomplete="new-password" autofocus required/>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600  hover:text-black  rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2" href="{{ route('login') }}" wire:navigate>
                {{ __('Already registered?') }}
            </a>

            {{-- BOTÓN DE REGISTRAR --}}
            <x-primary-button class="ms-4" wire:loading.attr="disabled" wire:target="registro">
                {{-- Contenido normal del botón (visible cuando NO está cargando) --}}
                <span wire:loading.remove> 
                    {{ __('Register') }}
                </span>
                {{-- Indicador de carga (visible cuando SÍ está cargando) --}}
                <span wire:loading.delay.shortest class="items-center hidden">
                    <div class="flex row">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ __('Procesando...') }}
                    </div>
                </span>
            </x-primary-button>
        </div>
    </form>
</div>
