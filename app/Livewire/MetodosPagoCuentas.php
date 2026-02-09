<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\BankAccount;

class MetodosPagoCuentas extends Component
{
    public $selectedMethod = null; // Controlador qué seccion mostrar

    // Arrays para agrupar los datos
    public $pagoMovil = [
        'banco' => '',
        'telefono' => '',
        'rif' => '',
        'titular' => ''
    ];

    public $zelle = [
        'email' => '',
        'titular' => ''
    ];

    // Reglas de validación centralizadas
    protected function rules()
    {
        return [
            'pagoMovil.banco'    => 'required|string|min:3|max:100|regex:/^[\pL\pN\s\-]+$/u', // 'regex:/^[\pL\s\-]+$/u' aceptar solo letras, espacios y guiones
            'pagoMovil.titular'  => 'required|string|min:3|max:100|regex:/^[\pL\s\-]+$/u',
            'pagoMovil.telefono' => 'required|numeric|digits_between:10,11',
            'pagoMovil.rif' => 'required|string|regex:/^[VJGvjg]-[0-9]{8}(-[0-9])?$/',
            'zelle.email'   => 'required|string|lowercase|email|max:255|email:rfc,dns',
            'zelle.titular' => 'required|string|min:3|max:100|regex:/^[\pL\s\-]+$/u',
        ];
    }
    // Mensajes personalizados por campo
    protected function messages()
    {
        return [
            // Mensajes para Pago Móvil
            'pagoMovil.banco.required'    => 'El nombre del banco es obligatorio.',
            'pagoMovil.banco.regex'       => 'El nombre del banco solo debe contener letras.',
            'pagoMovil.titular.required'  => 'El nombre del titular es obligatorio.',
            'pagoMovil.titular.regex'     => 'El nombre del titular solo debe contener letras.',
            'pagoMovil.telefono.required' => 'El número de teléfono es requerido.',
            'pagoMovil.telefono.numeric'  => 'El teléfono solo debe contener números.',
            'pagoMovil.telefono.digits_between' => 'El teléfono debe tener entre 10 y 11 dígitos.',
            'pagoMovil.rif.required'      => 'El RIF es obligatorio.',
            'pagoMovil.rif.regex'         => 'Formato inválido (Ej: V-12345678-9).',

            // Mensajes para Zelle
            'zelle.email.required'        => 'El correo de Zelle es obligatorio.',
            'zelle.email.email'           => 'El formato de correo no es válido.',
            'zelle.email.email:rfc,dns'   => 'El dominio del correo no parece existir.',
            'zelle.titular.required'      => 'El nombre del titular de la cuenta Zelle es obligatorio.',
            'zelle.titular.regex'         => 'El nombre del titular solo debe contener letras.',
            
            // Mensajes genéricos de tamaño
            'min' => 'Este campo debe tener al menos :min caracteres.',
            'max' => 'Este campo no debe exceder los :max caracteres.',
        ];
    }
    // Metodo para guardar el pago movil
    public function savePagoMovil()
    {
        // Validamos solo los campos pertenecientes a Pago Móvil
        $this->validate([
            'pagoMovil.banco'   => $this->rules()['pagoMovil.banco'],
            'pagoMovil.titular' => $this->rules()['pagoMovil.titular'],
            'pagoMovil.telefono'=> $this->rules()['pagoMovil.telefono'],
            'pagoMovil.rif'     => $this->rules()['pagoMovil.rif'],
        ], $this->messages());

        try {
            // 2. Guardar o Actualizar
            // Usamos 'type' => 'pago_movil' como clave para saber que estamos editando ese registro
            BankAccount::updateOrCreate(
                ['type' => 'pago_movil'], 
                [
                    'bank_name'    => $this->pagoMovil['banco'],
                    'holder_name'  => $this->pagoMovil['titular'],
                    'holder_id'    => $this->pagoMovil['rif'],
                    'phone_number' => $this->pagoMovil['telefono'],
                    'is_active'    => 1
                ]
            );

            session()->flash('exitoGuardar', 'Pago Móvil actualizado con éxito.');
            $this->selectedMethod = null;

        } catch (\Exception $e) {
            session()->flash('errorGuardar', 'Error al procesar la solicitud.');
        }
    }
    // Metodo para guardar el zelle
    public function saveZelle()
    {
        $this->validate([
            'zelle.email'   => $this->rules()['zelle.email'],
            'zelle.titular' => $this->rules()['zelle.titular'],
        ], $this->messages());


        try {
            BankAccount::updateOrCreate(
                ['type' => 'zelle'], // Busca por el tipo 'zelle'
                [
                    'email'       => $this->zelle['email'],
                    'holder_name' => $this->zelle['titular'],
                    'is_active'   => 1
                ]
            );

            session()->flash('exitoGuardar', 'Zelle actualizado con éxito.');
            $this->selectedMethod = null;
        } catch (\Exception $e) {
            session()->flash('errorGuardar', 'Error al procesar la solicitud.');
        }
    }
    // Método cargarTasaDolar es auxiliar para cargar la tasa del dolar
    public function cargarDatosPagoMovil()
    {
        $resultado = BankAccount::where('type', 'pago_movil')->first();
        // Si existe, llenamos el array que usa el formulario (wire:model)
        if ($resultado) {
            $this->pagoMovil = [
                'banco'    => $resultado->bank_name,
                'telefono' => $resultado->phone_number,
                'rif'      => $resultado->holder_id,
                'titular'  => $resultado->holder_name,
            ];
        }
    }

    // Método cargarTasaDolar es auxiliar para cargar la tasa del dolar
    public function cargarDatosZelle()
    {
        $resultado = BankAccount::where('type', 'zelle')->first();
        if ($resultado) {
            $this->zelle = [
                'email'   => $resultado->email,
                'titular' => $resultado->holder_name,
            ];
        }
    }
    // Metodo mount Se ejecuta una sola vez al cargar el componente
    public function mount()
    {
        $this->cargarDatosPagoMovil();
        $this->cargarDatosZelle();
    }
    // Metodo Renderiza la vista Livewire
    public function render()
    {
        return view('livewire.metodos-pago-cuentas');
    }
}

