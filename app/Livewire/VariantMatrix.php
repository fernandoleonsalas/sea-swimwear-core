<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log; // Para el log

class VariantMatrix extends Component
{   
    public $ContenidoCargado = false; // Indica si el contenido principal ya se cargó o no.     

    // CLAVE PARA EL RE-RENDER para forzar a Livewire a recrear el componente en junto su contenido.
    public int $select_key = 0; 

    // Va identificar en que seccion del catalogo estamos (mayorista o minorista)
    public $seccionCatalogo; 
    // Propiedades públicas que se pasan desde Blade/Controller
    public $product, $productId, $productName, $initialMatrix, $colors, $colorsEstampado, $estampados, $sizes, $minPieces;
    
    // Propiedades reactivas de Livewire
    public $quantities = []; // Cantidades que el usuario está seleccionando en la tabla
    public $effectiveMatrix = []; // Stock disponible real (Original - en Carrito)
    //  Declarar la propiedad pública
    public $currentValue = 0;
    public $totalQuantity = 0; // Va almacenar la cantidad de producto (variante seleccionado)

    // AUXILIAR Método: se llamará después de que Livewire se haya inicializado en el navegador
    public function inicializarContenido()
    {
        $this->ContenidoCargado = true; // <-- El componente está listo
    }
    // AUXILIAR Método: se llama cuando se cambia una cantidad en los input tabla de compra
    public function updatedQuantities($value, $variantId)
    {
        // Obtener el valor ingresado por el usuario y Asegurar que es un número
        $qty = intval($value);
        // Obtener el id de la variante del producto
        $variantId = (int) $variantId;
        // Encontrar el stock efectiva es decir Obtener el stock máximo (max) para esta variante en la matriz
        $effectiveStock = 0;
        
        foreach ($this->effectiveMatrix as $color => $sizesData) {
            foreach ($sizesData as $size => $variantData) {
                if (isset($variantData['variant_id']) && (int)$variantData['variant_id'] === $variantId) {
                    $effectiveStock = intval($variantData['stock'] ?? 0);
                    break 2; // Salir de ambos bucles
                }
            }
        }
        
        // Validación: Asegurar que no se excede el stock efectivo y no es negativo
        if ($qty < 0) {
            $this->quantities[$variantId] = 0;
        } elseif ($qty > $effectiveStock) {
            $this->quantities[$variantId] = $effectiveStock;
        } else {
            $this->quantities[$variantId] = $qty; // Asignar el valor limpio
        }
        $this->totalQuantity = collect($this->quantities)->sum();
    }
    // AUXILIAR Método: para encontrar la info de color/talla por ID de variante
    private function findVariantInfo($variantId)
    {
        foreach ($this->initialMatrix as $color => $sizesData) {
            foreach ($sizesData as $size => $variantData) {
                if (!empty($variantData) && (int)$variantData['variant_id'] === (int)$variantId) {
                    return [
                        'color' => $color, 
                        'size' => $size, 
                        'stock' => intval($variantData['stock'] ?? 0)
                    ];
                }
            }
        }
        
        return null;
    }

    // AUXILIAR Método: calculada para saber si se puede añadir al carrito 
    public function getCanAddToCartProperty()
    {
        // Log::info('valida si la cantidad seleccionada por el usuario es mayor a la regla de negocio', []);
        return $this->totalQuantity >= intval($this->minPieces); // Devuelve un valor bool
    }
    // METODO: principal para añadir al carrito (Livewire)
    public function addToCart()
    {
        // Llama al metodo getCanAddToCartProperty que valida si la cantidad seleccionada por el usuario es mayor a la regla de negocio
        if (!$this->canAddToCart) {
            $texto = $this->minPieces > 1 ? "$this->minPieces unidades " : "$this->minPieces unidad ";
            session()->flash('error', "Debe seleccionar un mínimo de $texto en total.");
            return;
        }
        // Recuperar el carrito de la sesión el valor de la clave 'cart'
        $cart = Session::get('cart', []);
        // Contador de unidades añadidas
        $addedCount = 0;
        // Recorrer las cantidades seleccionadas por el usuario
        foreach ($this->quantities as $variantId => $qty) {
            // Convertir a enteros
            $variantId = (int) $variantId;
            // Obtener la cantidad
            $qty = intval($qty);
            // Si la cantidad es mayor a 0, proceder a añadir al carrito
            if ($qty > 0) {
                // Buscar la información completa de la variante en la matrix
                $variantInfo = $this->findVariantInfo($variantId);
                // Si se encontró la variante
                if ($variantInfo) {
                    // Asegurarse de no exceder stock actual
                    $effectiveStock = intval($this->effectiveMatrix[$variantInfo['color']][$variantInfo['size']]['stock'] ?? 0);
                    $qtyToAdd = min($qty, $effectiveStock);

                    // Actualizar la sesión del carrito
                    $cart[$variantId] = [
                        'product_id' => $this->productId,
                        'name_product' => $this->productName,
                        'variant_id' => $variantId,
                        'qty'        => ($cart[$variantId]['qty'] ?? 0) + $qtyToAdd,
                        'name_variante'       => $this->productName . ' - ' . $variantInfo['color'] . ' ' . $variantInfo['size'],
                        'piezas_minima'    => optional($this->product)->min_pieces ?? null,
                    ];
                    // Incrementar el contador de unidades añadidas
                    $addedCount += $qtyToAdd;
                }
            }
        }

        // Guardar el carrito actualizado en la sesión
        Session::put('cart', $cart);
        
        // Recargar el estado para actualizar el stock visible
        $this->loadState();
        
        // Limpiar las cantidades del formulario después de añadir
        $this->quantities = array_map(fn($v) => 0, $this->quantities); 
        // Mostra mensaje de exito
        session()->flash('success', $addedCount . ' unidades añadidas al carrito!');
        // Emite el evento de señal (sin datos adicionales)
        $this->dispatch('actualizarCarrito'); // Dispara el evento para actualizar el carrito
        // limpiar la cantidad total:
        $this->totalQuantity = 0;
        // CLAVE: Incrementa el wire:key para forzar a Livewire a recrear el div
        $this->select_key++;
    }

    // METODO: Inicializa o recalcula el estado de la tabla de compra (stock y cantidades)
    public function loadState()
    {
        // Recuperar el carrito de la sesión el valor de la clave 'cart'
        $cart = Session::get('cart', []); // Si 'cart' no existe en la sesión, devuelve un array vacío []
        // Capturar la matrix enviada desde blade
        $this->effectiveMatrix = $this->initialMatrix;
        // Inicializa el array de variantes seleccionadas 
        $this->quantities = []; 

        // Recorrer la matriz inicial y ajustar el stock basado en el carrito
        foreach ($this->initialMatrix as $color => $sizesData) {
            foreach ($sizesData as $size => $variantData) {
                // Si no hay datos de variante, saltar
                if (empty($variantData) || !isset($variantData['variant_id'])) {
                    continue;
                }
                // ID de la variante actual
                $variantId = (int) $variantData['variant_id'];
                // Buscar si esta variante ya está en el carrito y si lo está, obtener la cantidad y si no esta devuelve 0
                $qtyInCart = isset($cart[$variantId]) ? intval($cart[$variantId]['qty']) : 0;
                // calcula la cantidad de stock de un producto que está efectivamente disponible para ser comprado
                $effectiveStock = max(0, intval($variantData['stock'] ?? 0) - $qtyInCart);
                // Actualizar la matriz efectiva (stock para mostrar al usuario)
                $this->effectiveMatrix[$color][$size]['stock'] = $effectiveStock;
                $this->effectiveMatrix[$color][$size]['variant_id'] = $variantId;
                // Inicializar la cantidad seleccionada a 0 en cada variante
                $this->quantities[$variantId] = 0;
            }
        }
        // Visualizar en log de laravel el resultado:
        // Log::info("Matriz: Se ha actualizado los datos de la tabla de compra para el producto {$this->productName}", [
        //     'artículos_carrito' => array_keys($cart),
        //     'variantes_disponibles' => array_keys($this->quantities),
        // ]);
    }
    // Metodo Mount: se llama al inicializar el componente Livewire 
    public function mount($id, $matrix, $min_piezas, $productName, $listaColores, $listaEstampado, $listaColoresYEstampados, $listaTallas)
    {
        // Usamos fill para asignar múltiples propiedades de un solo golpe
        $this->fill([
            'product'         => $id,
            'productId'       => $id->id,
            'initialMatrix'   => $matrix ?? [],
            'minPieces'       => $min_piezas ?? null,
            'productName'     => $productName,
            'colors'          => $listaColores ?? [],
            'estampados'      => $listaEstampado ?? [],
            'colorsEstampado' => $listaColoresYEstampados ?? [],
            'sizes'           => $listaTallas ?? [],
        ]);
        // Cargar el estado inicial de la tabla
        $this->loadState();
    }
    // Metodo Renderiza la vista Livewire
    public function render()
    {
        return view('livewire.variant-matrix');
    }
}