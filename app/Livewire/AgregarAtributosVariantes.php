<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Image;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Carbon\Carbon;

class AgregarAtributosVariantes extends Component
{

    // PROPIEDADES DE ESTADO Y CONTROL DE PASOS: Control de la navegación: 'producto_base', 'variantes'
    public string $seccionContenido = 'producto_base'; // Seccion predeterminada del formulario
    public string $productElegido = ""; // ID del producto seleccionado en el select
    public string $varianteElegido = ""; // ID de la variante seleccionada
    public $todosProductos; // Va almacenar la Colección de productos existentes en la base de datos
    public $todosVariantes; // Va almacenas la coleccion de las variantes existentes de un producto
    public $todosImagenes;  // Va almacenar la Colección de imagenes de productos existentes en la base de datos
    public $tallasDisponibles;  // Va almacenar la Colección de imagenes de productos existentes en la base de datos
    public $tallasSelect = []; // Va almacenar las tallas seleccionado por el usuario

    // ====================================================================
    // REGLAS DE VALIDACIÓN DINÁMICA Y MENSAJES PERSONALIZADOS
    protected function reglas()
    {
        $reglas = [
            'producto_base' => ['productElegido' => 'required|string|min:1|max:100',],
            'variantes' => ['tallasSelect' => 'required|array|min:1',],
        ];

        // Se devuelve el array completo para ser usado en la navegación y validación de categorías
        return $reglas;
    }
    // Mensajes de error personalizados para cada imputs
    protected $messages = [
        /** Mensajes en producto base */
        'productElegido.required' => 'Debes seleccionar un producto.', 
        /** Mensajes en variantes */
        'tallasSelect.required' => 'Debes seleccionar al menos una opción.',
    ];

    // Metodo guardarProceso posee la Lógica para registrar el proceso completo del formulario.
    public function guardarVariante() 
    {
        // --DB::transaction() para envolver todas las operaciones de DB.
        try {  
            // INICIO DE LA TRANSACCIÓN: Usamos DB::transaction(). 
            DB::transaction(function () {
                $now = Carbon::now(); // Obtener la fecha y hora actual
                $listaAtt = [];  // Va almacenar los ids atributos color,estampado de la variante
                $dataFinal = []; // va contener los registro final. con los IDs de imagen y la URL limpia por cada variante existente
                $pivotDataToInsert = []; // Array para la inserción masiva de la tabla pivote de atributos
                $listaAttv = [];

                // Cargar la variante seleccionada con sus valores de atributos
                $variant = ProductVariant::with([
                        'attributeValues' => function ($query) {
                        $query->select('id','attribute_id','value'); // del modelo AttributeValue (la tabla 'attribute_values')
                    } 
                ])->findOrFail($this->varianteElegido)->toArray();
                // Extraer valores de atributos
                foreach ($variant["attribute_values"] as $att) {
                    $listaAtt[] = [$att["id"],$att["value"]];
                }
                // Proceso logico crear el  skud de las variantes:
                $skuEditar = explode('-', trim($variant["full_sku"] ?? '')); // Dividir el sku en un array para poder agregar la talla al sku
                $atributos = array_splice($skuEditar, -2); // Obtener las partes del SKU que corresponden a los atributos (sin importar cuántas partes tenga el SKU)
                // Ahora los unimos de nuevo si los necesitas como string
                $skuRaiz = implode("-", $skuEditar); // Resultado: "SKA-2025-001"
                $propiedades = implode("-", $atributos); // Resultado: "BLANCO-GEOMETRICA
                
                // Recorre cada talla seleccionada para crear un SKU por cada una
                foreach ($this->tallasSelect as $t) {
                    $r = explode("-", $t); // Separar el identificador y valor de la talla
                    // Creamos un nuevo registro de base de datos para cada SKU final
                    $dataFinal[] = [
                        'product_id' => $variant["product_id"],
                        'full_sku' => "$skuRaiz-$r[1]-$propiedades",
                        'stock' => 0,
                        'image_id' => $variant["image_id"],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                    $listaAttv["$skuRaiz-$r[1]-$propiedades"] = [
                        'color' => $listaAtt[1],
                        'estampado' => $listaAtt[2],
                        'talla' => [$r[0]],
                    ];
                }
                // A. INSERCIÓN MASIVA DE VARIANTES
                if (!empty($dataFinal)) {
                    ProductVariant::insert($dataFinal); // UN solo INSERT para todas las variantes de todos los grupos.
                }
                // B. RECUPERAR LAS VARIANTES INSERTADAS Y SINCRONIZAR ATRIBUTOS
                $skudConsultar = array_keys($listaAttv);

                if (!empty($skudConsultar)) {
                    // Recuperar solo los IDs de las variantes que acabamos de crear (SELECT eficiente)
                    $newVariants = ProductVariant::whereIn('full_sku', $skudConsultar)->get(['id', 'full_sku']);
                    // Crear un mapa (SKU => ID) para búsqueda rápida
                    $variantMap = $newVariants->keyBy('full_sku')->map->id->toArray(); 
                    // Construir el array final para la tabla pivote de atributos
                    foreach ($listaAttv as $k => $attV) { 
                        $varianteID = $variantMap[$k]; // Obtener el id de la variante creada
                        foreach ($attV as $valor) {
                            $pivotDataToInsert[] = [
                                'product_variant_id' => $varianteID,
                                'attribute_value_id' => $valor[0],
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];
                        }
                    }
                }

                // C. INSERCIÓN MASIVA EN LA TABLA PIVOTE
                if (!empty($pivotDataToInsert)) {
                    // UN solo INSERT para todas las relaciones de todas las variantes.
                    DB::table('attribute_value_variants')->insert($pivotDataToInsert);
                }
            });

            // Mensaje y limpieza de estado de Livewire
            $this->productElegido = "";
            $this->varianteElegido = "";
            $this->seccionContenido = 'producto_base'; // Regresar a la sección de producto_base
            session()->flash('notificacion', ['tipo' => 'text-green-700 bg-green-100','titulo' => '¡Registro Exitoso!','mensaje' => '¡Listo! Guardado correctamente.',]);
        } catch (\Exception $e) {
            session()->flash('notificacion', ['tipo' => 'text-red-700 bg-red-100','titulo' => '¡Error!','mensaje' => 'Error al guardar los cambios.',]);
        }
    }
    // Método mostrarSeccion controla la navegación entre secciones y la validación
    public function mostrarSeccion(string $seccion, string $accion = 'Regresar')
    {
        if ($accion != "Regresar") {
            // 1. Obtiene las reglas específicas para el paso actual. Accede al array de reglas y solo toma las claves que coinciden con el paso actual.
            $reglasValidacion = $this->reglas()[$seccion];
            // 2. Ejecuta la validación. Si falla, se detiene y muestra errores.
            $validatedData = $this->validate($reglasValidacion);
            // 3. Si no ocurre ningun error Transiciona al siguiente paso si la validación es exitosa.
            if ($this->seccionContenido == 'producto_base') {
                $resultado = $this->cargarVariante(); // Carga todas las variantes disponibles de un produto
                $this->seccionContenido = $resultado ?  'lista' : 'producto_base';
            }
            // 4. Guardar variantes
            if ($this->seccionContenido == 'variantes') {
                $this->guardarVariante(); // Guardar variante
            }
        }else{
            $this->resetValidation(); // Limpiar errores de validación al regresar
            $this->seccionContenido = $seccion; // Retrocede al paso indicado
        }
    }

    // Método mostrar variante a editar 
    public function varianteMostrar(string $varianteID, $tallas)
    {
        $this->varianteElegido = $varianteID; // Almacenar variante
        $this->cargarTallas($tallas); // Carga las tallas disponibles
        $this->seccionContenido = "variantes";
        $this->tallasSelect = []; // Limpiar valor
    }
    // Método cargarVariantes es auxiliar para cargar los productos de la base de datos
    public function cargarVariante()
    {
        /* Va almacenar las variantes ya procesadas para evitar duplicados */ 
        $varianteGuardados = []; 

        // Carga el producto seleccionado junto con sus variantes e imágenes asociadas
        $variante = Product::with([
            'variants' => function ($query) {
                // Selecciona las columnas de product_variants
                $query->select('id', 'product_id', 'full_sku', 'image_id');
            }
        ])->findOrFail($this->productElegido);
        // agrupa las variantes por image_id
        $variantsByImage = $variante->variants->groupBy('image_id')->toArray();
        $variantsKeyImage = array_keys($variantsByImage);
        // Si no se cargaron variantes, muestra una notificación de error
        if (empty($variantsByImage)) {
            session()->flash('notificacion', ['tipo' => 'text-red-700 bg-red-100','titulo' => '¡Error!','mensaje' => 'No se encontraron variantes disponibles para este producto.',]);
            return false;
        }
        $this->cargarImagenes($variantsKeyImage); // Carga todas las imagenes de la variante seleccionada

        // Proceso para organizar las variantes en la vista:
        foreach ($variantsByImage as $variants) {
            $grupoImagenV = []; // Va almacenar solo una variante por grupo de imagen
            foreach ($variants as $items) {
                $fullSku = explode('-', trim($items["full_sku"] ?? '')); // Separar el identificador y valor de la talla
                $r = array_slice($fullSku, -3); // Obtener los últimos 3 elementos: color, talla, estampado
                // Entra Si el elemento NO está en el array:
                if (!in_array($items["image_id"], $grupoImagenV)) {
                    $grupoImagenV[] = $items["image_id"];
                    $varianteGuardados[$items["image_id"]] = [
                        "id" => $items["id"],
                        "product_id" => $items["product_id"],
                        "image_id" => $items["image_id"],
                        "color" => $r[1],
                        "talla" => [$r[0]],
                        "estampado" => $r[2],
                    ];
                }else{
                    $varianteGuardados[$items["image_id"]]["talla"][] = $r[0];
                }
            }
        }
        $this->todosVariantes = $varianteGuardados;
        return true;
    }
    // Método cargarProducto es auxiliar para cargar las imagenes de los productos de la base de datos
    protected function cargarImagenes(array $arrIDs)
    {
        $this->todosImagenes = Image::whereIn('id', $arrIDs)->pluck('main_image_url', 'id')->toArray();
    }
    // Método cargarProducto es auxiliar para cargar los productos de la base de datos
    protected function cargarProducto()
    {
        $this->todosProductos = Product::select('id', 'name')->get();
    }
    // Método cargarTallas es auxiliar para cargar las tallas de la base de datos
    protected function cargarTallas($tallas = [])
    {
        // Obtener el ID del atributo 'Tallas'
        $tallasId = Attribute::where('name', 'Talla')->value('id');

        // Consultar los AttributeValue, filtrando por ID y EXCLUYENDO los valores
        $this->tallasDisponibles = AttributeValue::where('attribute_id', $tallasId)
            // Excluye los valores en el array $tallasAExcluir
            ->whereNotIn('value', $tallas)
            // Formatea el resultado: id => value
            ->pluck('value', 'id') 
            ->toArray();
    }
    // Metodo mount Se ejecuta una sola vez al cargar el componente
    public function mount()
    {
        $this->cargarProducto(); // Carga todos los productos disponibles
    }
    // Metodo Renderiza la vista Livewire
    public function render()
    {
        return view('livewire.agregar-atributos-variantes');

    }
}
