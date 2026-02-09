<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\configurations;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class DetalleDeCompra extends Component
{
    public $ContenidoCargado = false; // Indica si el contenido principal ya se cargó o no.     

    // Propiedades principales
    public $consultarV = [];


    // public $tasaImpuestos = 0.16; // 16% IVA
    // public $costoEnvio = 25.00; // Valor fijo o inicial
    public $tasaImpuestos = 0;  // 0% en este caso no pagan impuesto
    public $impuestosCalculados = 0.00;
    public $subtotalProductos = 0.00;
    public $totalEstimado = 0.00;
    public $agrupado = [];
    public $montoTotal = 0.00; 
    public $search = ''; 
    public $cartData = []; // Propiedad auxiliar que almacena los datos brutos del carrito (por ID)
    public $stockValidationErrors = []; // Almacena errores de stock para la vista
    public $tasaDolar = 0;
    
    // Propiedades de paginación manual
    public $perPage = 10; // por defecto 10 productos.
    public $currentPage = 1;
    public $totalItems = 0; 
    public $totalPages = 1; 

    // AUXILIAR Método: se llamará después de que Livewire se haya inicializado en el navegador
    public function inicializarContenido()
    {
        // sleep(4); // Descomentar esta liunea solo en produccion para ver claramente el efecto de carga 
        $this->ContenidoCargado = true; // <-- El componente está listo
    }

    /* ------------------------------------------
    // LISTENERS Y HOOKS
    // ------------------------------------------ */
    public function updatedPerPage($value)
    {
        $this->perPage = (int) $value;
        $this->currentPage = 1; 
        $this->processCartData(); 
    }

    // Se activa cuando $search cambia (input)
    public function updatedSearch()
    {
        $this->currentPage = 1; // Volver a la primera página al buscar
        $this->processCartData();
    }
    
    // Se activa cuando $cartData cambia (wire:model.live en los inputs de cantidad)
    public function updatedCartData() 
    {
        // Guardar el cambio inmediatamente en la sesión para persistencia
        Session::put('cart', $this->cartData); 
        // Recalcular precios, totales y paginación
        $this->processCartData();
    }
    
    // ------------------------------------------
    // MÉTODOS DE CONTROL
    // ------------------------------------------

    public function gotoPage($page)
    {
        if ($page > 0 && $page <= $this->totalPages) {
            $this->currentPage = $page;
            $this->processCartData(); 
        }
    }

    public function discardVariant($variantId)
    {
        if (isset($this->cartData[$variantId])) {
            unset($this->cartData[$variantId]);
            Session::put('cart', $this->cartData); 
            $this->processCartData();
            $this->dispatch('variantDiscarded', ['message' => 'Variante eliminada del carrito.']);
        }
    }

    public function checkStockAndProceed()
    {
        $this->stockValidationErrors = []; 
        $requiredVariantIds = array_keys($this->cartData);
        $hasErrors = false; 
        
        if (empty($requiredVariantIds)) {
            session()->flash('error', 'El carrito está vacío. Agregue productos para continuar.');
            return;
        }

        // Obtener stock actual de la base de datos
        $dbStock = ProductVariant::whereIn('id', $requiredVariantIds)->pluck('stock','id')->toArray();

        // 2. Comparar y ajustar cantidades
        foreach ($this->cartData as $variantId => $item) {
            $cantidadSeleccionada = (int) $item['qty'];
            $cantidadStock = $dbStock[$variantId] ?? 0; 

            if ($cantidadSeleccionada > $cantidadStock) {
                $hasErrors = true;
                
                // Ajustar la cantidad en el carrito a la cantidad disponible
                // $this->cartData[$variantId]['qty'] = $cantidadStock;

                $this->stockValidationErrors[$variantId] = [
                    'variant_name' => $item['name_variante'],
                    'required' => $cantidadSeleccionada,
                    'available' => $cantidadStock,
                ];
            }
        }

        if ($hasErrors) {
            // Recalcular totales y refrescar la vista
            $this->processCartData();
            
            // Notificar al usuario (Alerta amarilla de Alpine)
            $this->dispatch('stock-issue');
            session()->flash('error', '⚠️ Hay productos con cantidades superiores al stock disponible. Por favor, ajuste su carrito.');
        } else {
            // Stock suficiente. Lógica de pago.
            // Log::info("Compra:", [$cantidadSeleccionada,$cantidadStock]);
            $this->dispatch('paymentReady', ['message' => 'Stock confirmado. Procediendo al pago.']);
            return redirect()->route('ordenForm'); 
        }
    }
    // Metodo LÓGICA DE PROCESAMIENTO CENTRAL
    public function processCartData()
    {
        $listaVariantes = []; // Va almacenar los IDs de las variantes
        $agrupadoBruto = []; // Inicializar el array agrupado
        $montoTotalAcumulado = 0.00; // Inicializar el acumulador de monto total
        $this->cartData = Session::get('cart', []); // Asegurarse de tener los datos más recientes del carrito

        // 1. Filtrado, campo busqueda y Agrupación inicial (Primera pasada)
        $filteredCartData = collect($this->cartData)->filter(function ($item) {
            $searchTerm = strtolower($this->search);
            return  str_contains(strtolower($item['name_product']), $searchTerm) || 
                    str_contains(strtolower($item['name_variante']), $searchTerm);
        })->all();

        foreach ($filteredCartData as $item) {
            $listaVariantes[] = $item["variant_id"];
            [$pID,$cant] = [$item['product_id'],(int) $item['qty']];

            if (!isset($agrupadoBruto[$pID])) { // Entra si el producto no existe en el array
                $agrupadoBruto[$pID] = [
                    'name_product' => $item['name_product'],
                    'Cantidad Total' => 0,
                    'variantes' => [],
                ];
            }
            // Actualizo los datos de ese producto existente en el array
            $agrupadoBruto[$pID]['Cantidad Total'] += $cant;
            $agrupadoBruto[$pID]['variantes'][] = [
                'id_variante' => $item['variant_id'],
                'name_variante' => $item['name_variante'],
                'qty' => $cant,
            ];

        }
        // 2. Obtener las claves de los productos agrupados para realizar en la consulta y asi obtener precios y piezas minima aztualizada
        $clavesProductos = array_keys($agrupadoBruto);
        // 2.1 Consulta producto eficiente:
        $consultarP = Product::whereIn('id', $clavesProductos)->select(['id', 'price_retail', 'price_wholesale', 'min_pieces'])->get();
        // 2.3 Consulta variante eficiente:
        $this->consultarV = ProductVariant::whereIn('product_variants.id', $listaVariantes)->join('images', 'images.id', '=', 'product_variants.image_id')
        ->select([
            'product_variants.id',
            'product_variants.stock',
            'images.main_image_url as foto'
        ])
        ->get()
        ->keyBy('id')
        ->toArray();

        // 2. Aplicar Precios y Cálculos Finales (Segunda pasada)
        $agrupadoFinal = [];
        foreach ($agrupadoBruto as $productId => $producto) {
            $pEncontrado = $consultarP->find($productId); // Obtener el producto correspondiente
            $cantidadTotal = $producto['Cantidad Total']; // Obtener la cantidad total del producto actual

            if ($pEncontrado["price_wholesale"] != null && $pEncontrado["min_pieces"] != null) { // Entra si el producto tiene precio mayorista y tiene definido piezas minima
                if ($cantidadTotal < $pEncontrado["min_pieces"]) {
                    $precioUnitario = $pEncontrado['price_retail'];
                    $tipoTarifa = 'Minorista';
                }else {
                    $precioUnitario = $pEncontrado['price_wholesale'];
                    $tipoTarifa = 'Mayorista';
                }
            }else{
                $precioUnitario = $pEncontrado['price_retail'];
                $tipoTarifa = 'Minorista';
            }
            
            // Calcular el Subtotal del Producto Principal
            $subtotalProducto = $cantidadTotal * $precioUnitario;
            // Acumular al Monto Total Acumulado (ahora es el Subtotal General de Productos)
            $montoTotalAcumulado += $subtotalProducto;

            $variantesFinales = [];
            foreach ($producto['variantes'] as $variante) {
                $variantesFinales[] = [
                    'id_variante' => $variante['id_variante'],
                    'name_variante' => $variante['name_variante'],
                    'qty' => $variante['qty'],
                    'foto' => $this->consultarV[$variante['id_variante']]["foto"]
                ];
            }
            
            $agrupadoFinal[$productId] = [
                'name_product' => $producto['name_product'],
                'P.U. Aplicado' => number_format($precioUnitario, 2, '.', ''), // Formatear solo para mostrar
                'Tarifa Aplicada' => $tipoTarifa,
                'Cantidad Total' => $cantidadTotal,
                'Subtotal' => number_format($subtotalProducto, 2, '.', ''),
                'variantes' => $variantesFinales,
            ];
        }

        // 3. Calcular el RESUMEN de la orden
        $this->subtotalProductos = $montoTotalAcumulado; // Subtotal sin descuentos ni impuestos

        // Cálculo de Impuestos (16% sobre el subtotal)
        $this->impuestosCalculados = $this->subtotalProductos * $this->tasaImpuestos;
        
        // Cálculo del Total Estimado
        $this->totalEstimado = $this->subtotalProductos 
                            + $this->impuestosCalculados;

        // 4. Lógica de Paginación Manual
        $this->totalItems = count($agrupadoFinal);
        $this->totalPages = ceil($this->totalItems / $this->perPage);

        if ($this->currentPage > $this->totalPages && $this->totalPages > 0) {
            $this->currentPage = 1;
        }

        $offset = ($this->currentPage - 1) * $this->perPage;
        $agrupadoPaginated = array_slice($agrupadoFinal, $offset, $this->perPage, true);

        // Asignar a propiedades públicas
        $this->agrupado = $agrupadoPaginated;
        $this->montoTotal = number_format($this->totalEstimado, 2, '.', '');
    }
    // Metodo para consultar la tasa del dolar
    public function consultarTasa()
    {
        $consultarTasa = configurations::where('key', 'tasaDolar')->select(['value'])->first();
        return $consultarTasa ? $consultarTasa->value : false;
    }
    /* Metodo se ejecuta una vez cuando el componente se inicializa */
    public function mount()
    {
        $this->tasaDolar = $this->consultarTasa();
        $this->tasaDolar ? $this->processCartData() : session()->flash('errorTasa', '⚠️ No se ha podido determinar la valoración de los productos debido a que no hay una tasa de cambio vigente. Por favor, intente más tarde.');
    }
    /* Metodo Renderiza la vista del componente */
    public function render()
    {
        return view('livewire.detalle-de-compra'); // Se eliminaron variables innecesarias
    }
}