<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On; // Importa el atributo de escucha de Livewire 3
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log; // Para el log


class ShowCar extends Component
{
    public $cartCount = 0; // Nueva propiedad pública para el conteo del carrito
    
    // "Si se escuchas el evento 'actualizarCarrito', enviado por variantMatrix.php ejecuta la función que está debajo."
    #[On('actualizarCarrito')]
    public function getTotalUnits()
    {
        $cart = Session::get('cart', []); // Captura el carrito

        // Suma la clave 'qty' de todos los ítems en el array del carrito
        $this->cartCount = collect($cart)->sum('qty');
        Log::info("FFFF: se añadieron al carrito", ["total_unidades" => $this->cartCount]);
    }
    // el metodo mount se ejecuta una vez al inicializar el componente
    public function mount()
    {
        // Inicializa el conteo del carrito al montar el componente
        $this->getTotalUnits();
    }
    // Método render que se llama en cada ciclo de vida del componente
    public function render()
    {
        return view('livewire.show-car');
    }
}
