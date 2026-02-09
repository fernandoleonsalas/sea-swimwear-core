<?php

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password; // para requerir mayúsculas, minúsculas y símbolo.
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;

new #[Layout('layouts.guest', ['title' => 'Restablecer Contraseña'])] class extends Component
{
    #[Locked]
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Mount the component.
     */
    public function mount(string $token): void
    {
        $this->token = $token;

        $this->email = request()->string('email');
    }
    // Mensajes personalizado
    public function messages(): array
    {
        return [

            // Mensajes para 'email'
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email'    => 'El correo electrónico debe tener un formato válido (ej. usuario@dominio.com).', // Cubre el email:rfc,dns
            
            // Mensajes para 'password'
            'password.required'   => 'La contraseña es obligatoria.',
            'password.confirmed' => 'La nueva contraseña y su confirmación no coinciden. Por favor, asegúrate de que sean idénticas.',
            // Laravel genera automáticamente mensajes para las sub-reglas de Password::defaults(),
            // pero puedes personalizar el de 'uncompromised' así:
            'password.uncompromised' => 'Esta contraseña es insegura. Por favor, elige otra.',
        ];
    }

    /**
     * Reset the password for the given user.
     */
    public function resetPassword()
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'email:rfc,dns'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()->uncompromised()], // Password::defaults() Min 8, letra, número,  ->uncompromised(), Verifica que la contraseña no esté en bases de datos de contraseñas filtradas (Pwned Passwords)
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) {
                $user->forceFill([
                    'password' => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        if ($status != Password::PASSWORD_RESET) {
            $this->addError('email', __($status));

            return;
        }

        Session::flash('status', __($status));

        return $this->redirect(route('login', absolute: false), navigate: false);
    }
}; ?>

<div>
    <form wire:submit="resetPassword">
        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autofocus autocomplete="username" disabled/>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input wire:model="password" id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full"
                        type="password"
                        name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            {{-- BOTÓN DE RESTABLECER CONTRASEÑA CON CARGADOR --}}
            <x-primary-button class="ms-3" wire:loading.attr="disabled" wire:target="restablecer">
                {{-- Contenido normal del botón (visible cuando NO está cargando) --}}
                <span wire:loading.remove> 
                    {{ __('Reset Password') }}
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
