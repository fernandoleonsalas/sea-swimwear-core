<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\configurations;
use Illuminate\Support\Facades\Validator;

class TasaCambioBs extends Component
{
    public $campoTasaCambio;

    // Método para guardar la tasa del dolar
    public function guardarTasa()
    {
        $this->resetValidation();
        // Seccion de validacion
        $dataToValidate = [ // Prepara los datos a validar
            'campoTasaCambio' => $this->campoTasaCambio,
        ];
        $rules = [ // Definir las reglas específicas para esta validación
            'campoTasaCambio' => 'required|numeric|min:1.00', // Cambiamos a min:1.00
        ];
        $messages = [ // Define mensajes personalizados si no quieres usar los mensajes de la regla principal
            'campoTasaCambio.required' => 'El campo precio es obligatorio.',
            'campoTasaCambio.numeric' => 'El campo precio debe ser un número válido.',
            'campoTasaCambio.min' => 'El precio debe ser como mínimo $1.00.',
        ];

        // Crear y ejecuta el validador
        $validator = Validator::make($dataToValidate, $rules, $messages);
        $validator->validate();
        $validatedData = $validator->validate();

        try {
            // 2. Ejecutar la operación dentro del bloque try
            configurations::updateOrCreate(
                ['key' => "tasaDolar"], // Condición para buscar
                ['value'     => $validatedData['campoTasaCambio']]
            );

            session()->flash('exitoGuardar', 'Configuración guardada correctamente.');
        } catch (\Exception $e) {
            // 4. Manejo del error
            session()->flash('errorGuardar', 'Hubo un error de conexión. Inténtalo más tarde.');
        }
    }
    // Método cargarTasaDolar es auxiliar para cargar la tasa del dolar
    public function cargarTasaDolar()
    {
        $resultado = configurations::where('key', 'tasaDolar')->value('value');
        $this->campoTasaCambio = !blank($resultado) ? (float) $resultado : ""; 
    }

    // Metodo mount Se ejecuta una sola vez al cargar el componente
    public function mount()
    {
        $this->cargarTasaDolar();
    }

    // Metodo Renderiza la vista Livewire
    public function render()
    {
        return view('livewire.tasa-cambio-bs');
    }
}