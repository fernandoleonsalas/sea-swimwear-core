<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Rutas para GUEST (invitados, no autenticados)
Route::middleware('guest')->group(function () {
    // Volt::route('sea-register-swimwear', 'pages.auth.register')
        // ->name('register'); // <-- Esta es la ruta de registro

    Volt::route('login', 'pages.auth.login')
        ->name('login'); // <-- Esta es la ruta de login

    Volt::route('forgot-password', 'pages.auth.forgot-password')
        ->name('password.request'); // <-- Esta es la ruta para solicitar el reseteo de contraseña

    Volt::route('reset-password/{token}', 'pages.auth.reset-password')
        ->name('password.reset'); // <-- Esta es la ruta para resetear la contraseña
});

// Rutas para AUTH (autenticados)
Route::middleware('auth')->group(function () {
    Volt::route('verify-email', 'pages.auth.verify-email')
        ->name('verification.notice'); // <-- Esta es la ruta para notificar verificación de email

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify'); // <-- Esta es la ruta para verificar el email

    Volt::route('confirm-password', 'pages.auth.confirm-password')
        ->name('password.confirm'); // <-- Esta es la ruta para confirmar la contraseña
});
