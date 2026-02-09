<?php

namespace App\Livewire;

use Livewire\Component;

class FormCrearProducto extends Component
{
   // Propiedad para almacenar la opción seleccionada ('product' o 'variant')
    public $selectedOption = null;
    
    // Se Define las opciones disponibles. Para cada opción, se asigna un label descriptivo.
    public $options = [
        'producto' => 'Crear Producto',
        'variante' => 'Crear Variante',
        'variante_nueva' => 'Nueva',
        'variante_editar' => 'Existente',
    ];


    // Método para establecer la opción seleccionada cuando el usuario hace clic.
    public function selectOption($option)
    {
        // Solo actualiza si es una opción válida
        if (array_key_exists($option, $this->options)) {
            $this->selectedOption = $option;
        }
    }
    // Metodo Renderiza la vista Livewire
    public function render()
    {
        return view('livewire.form-crear-producto');
    }
}
