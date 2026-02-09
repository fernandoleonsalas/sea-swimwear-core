<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Image;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Closure;

class CrearNuevaVariante extends Component
{
    use WithFileUploads; // Habilita la carga de archivos en Livewire
    public int $select_key = 0; // CLAVE PARA EL RE-RENDER DE TOM SELECT, Es decir , para forzar a Livewire a recrear el componente select, cada vez que se agrega una nueva categoría.

    // PROPIEDADES DE ESTADO Y CONTROL DE PASOS: Control de la navegación: 'producto_base', 'variantes'
    public string $seccionContenido = 'producto_base'; // Seccion predeterminada del formulario
    public string $productElegido = ""; // IDs del producto seleccionado en el select
    public $todosProductos; // Va almacenar la Colección de productos existentes en la base de datos

    // Datos del Paso 2: Variantes
    public array $variants = [];  // arreglo dinámico para las variantes del producto
    public $allAttributes; // Atributos con sus valores para poblar selects en la vista
    public $listaColoresHex; // Va almacenar la lista de colores id, codigo hexadecimal  

    // ====================================================================
    // REGLAS DE VALIDACIÓN DINÁMICA Y MENSAJES PERSONALIZADOS
    // ====================================================================
    // Se Define todas las reglas de validación agrupadas por pasos del formulario.
    protected function reglas()
    {
        $reglas = [
            'producto_base' => [
                'productElegido' => 'required|string|min:1|max:100',
            ],
            'variantes' => [

                // 'variants.*.sku' aplica a cada elemento en el array $variants
                'variants.*.sku' => 'required|string|distinct|unique:product_variants,full_sku', 
                'variants.*.stock' => 'required|integer|min:0',
                'variants.*.main_image_url'=> 'required|image|mimes:jpeg,png,jpg,gif|max:1024',
                // Valida que los valores de atributos seleccionados existan en la tabla attribute_values
                'variants.*.atributos.talla' => 'required|array|min:1',
                'variants.*.atributos.color' => [
                    'required',
                    'regex:#^[^\\-]+$#', // <--- Nueva regla: No permite guiones ni barras
                    function (string $attribute, $value, Closure $fail) {
                        if (is_string($value)) {
                            // Si es un string, verifica que no esté vacío (el 'required' ya ayuda).
                            if (strlen($value) < 1) { 
                                $fail("El nombre del color debe tener al menos 3 caracteres.");
                            }
                            return;
                        }
                        if (is_array($value)) {
                            // Si es un array, verifica que tenga al menos un elemento.
                            if (count($value) < 1) {
                                $fail("El array del campo $attribute debe contener al menos un elemento.");
                            }
                            return;
                        }
                        // Fallo si no es string ni array
                        $fail("El campo $attribute debe ser un string o un array con al menos un elemento.");
                    },
                ],
                'variants.*.atributos.color_code' => ['required','string','min:5','max:7',
                    // 1. Aplica la regla REGEX para asegurar el formato #RRGGBB
                    'regex:/^#[0-9a-fA-F]{5,7}$/',
                    
                    // 2. Aplica una closure personalizada para detectar el valor de relleno
                    function (string $attribute, $value, Closure $fail) {
                        // Define el valor predeterminado que deseas bloquear
                        $VALOR_RELLENO = '#000001'; 
                        
                        if ($value === $VALOR_RELLENO) {
                            $fail('El color seleccionado es el valor predeterminado y debe ser cambiado.');
                        }
                    },
                ],
                'variants.*.atributos.estampado' => [
                    'required',
                    'regex:#^[^\\-]+$#', // <--- Nueva regla: No permite guiones ni barras
                    function (string $attribute, $value, Closure $fail) {
                        if (is_string($value)) {
                            // Si es un string, verifica que no esté vacío (el 'required' ya ayuda).
                            if (strlen($value) < 1) { 
                                $fail("El nombre del color debe tener al menos 3 caracteres.");
                            }
                            return;
                        }
                        if (is_array($value)) {
                            // Si es un array, verifica que tenga al menos un elemento.
                            if (count($value) < 1) {
                                $fail("El array del campo $attribute debe contener al menos un elemento.");
                            }
                            return;
                        }
                        // Fallo si no es string ni array
                        $fail("El campo $attribute debe ser un string o un array con al menos un elemento.");
                    },
                ],
            ],
        ];

        // Se devuelve el array completo para ser usado en la navegación y validación de categorías
        return $reglas;
    }
    // Mensajes de error personalizados para cada imputs
    protected $messages = [
        /** Mensajes en producto base */
        'productElegido.required' => 'Debes seleccionar un producto.', 
        /** Mensajes en variantes */
        'variants.*.sku.required' => 'La SKU de la variante es obligatoria.',
        'variants.*.sku.unique' => 'Este Variante del producto ya existe en el sistema.',
        'variants.*.sku.distinct' => 'El SKU de esta variante está duplicado dentro de las variantes que intentas crear.',
        'variants.*.main_image_url.required' => 'Debes agregar una imagen para la variante.',
        'variants.*.main_image_url.image' => 'El archivo debe ser una imagen válida.',
        'variants.*.main_image_url.max' => 'La imagen de la variante no debe superar 1MB.',
        'variants.*.stock.required' => 'El campo de stock es obligatorio.',
        'variants.*.stock.min' => 'El campo de stock debe ser de al menos 0.',
        'variants.*.atributos.talla.required' => 'Debes seleccionar al menos una opción.',
        'variants.*.atributos.color.required' => 'Debes seleccionar al menos una opción.',
        'variants.*.atributos.color.regex' => 'El nombre del color no puede contener guiones (-).',
        'variants.*.atributos.color.min' => 'El color debe tener al menos 3 caracteres.',
        'variants.*.atributos.estampado.required' => 'Debes seleccionar al menos una opción.',
        'variants.*.atributos.estampado.regex' => 'El estampado no puede contener guiones (-).',
        'variants.*.atributos.estampado.min' => 'El color debe tener al menos 3 caracteres.',
        'variants.*.atributos.color_code.required' => 'Debe seleccionar un valor para el código de color.',
        'variants.*.atributos.color_code.regex' => 'El código de color debe ser un valor hexadecimal válido en el formato #RRGGBB (Ej: #FF0000).',
    ];
    // Metodo guardarProceso posee la Lógica para registrar el proceso completo del formulario.
    public function crearVariante() 
    {
        $now = Carbon::now();
        $dataFinal = []; // va contener los registro final. con los IDs de imagen y la URL limpia por cada variante existente
        $attTemporales = []; // Va almacenar temporalmente los atributos por variante
        $pivotDataToInsert = []; // Array para la inserción masiva de la tabla pivote de atributos
        // 1. Realizar otra Validación de los campos 
        $reglasVariantes = $this->reglas()['variantes'];
        $this->validate($reglasVariantes); 
        // --DB::transaction() para envolver todas las operaciones de DB.
        try {  
            // 2. verificación de producto
            $product = Product::find($this->productElegido);
            if (!$product) {
                $this->addError('productElegido', 'Producto no encontrado.');
                return;
            }
            // 3. Obtener los IDs de los atributos que necesitas (ej. 'Color','Talla','Estampado')
            $attributes = Attribute::whereIn('name', ['Color', 'Talla', 'Estampado'])->get()->keyBy('name');
            // 3.1. Extraer los IDs para fácil acceso
            $colorAttributeId = $attributes->get('Color')->id ?? null;
            $estampadoAttributeId = $attributes->get('Estampado')->id ?? null;
            // INICIO DE LA TRANSACCIÓN: Usamos DB::transaction(). 
            // Pasar todas las variables necesarias por referencia con 'use (&)'
            DB::transaction(function () use ($product,$dataFinal,$pivotDataToInsert,$attTemporales,$colorAttributeId,$estampadoAttributeId,$now){
                // 4. Recorre variantes y las persiste
                foreach ($this->variants as $variantData) {
                    // Array va almacenar la lista de sku x variantes:
                    $listaSku = [];
                    $listaAttv = [];
                    // 4.1 Extraer IDs de cada variante
                    [$colorId,$stampId,$tallaIds,$colorCode] = [$variantData['atributos']['color'],$variantData['atributos']['estampado'],$variantData['atributos']['talla'],$variantData['atributos']['color_code']]; 
                    // 4.2. verificar el valor del Color (crear nuevo color si es un string)
                    if (!is_numeric($colorId)) {
                        $color = AttributeValue::firstOrCreate(
                            // Array 1: Criterios de Búsqueda
                            ['value' => ucwords(strtolower($colorId)), 'attribute_id' => $colorAttributeId],
                            // Array 2: Valores a insertar SI el registro NO se encuentra (Si el registro no existe, Laravel fusiona el Array de Búsqueda y el Array de Creación)
                            ['color_code' => $colorCode]
                        );
                        $colorId = $color->id;
                    }
                    // 4.3. Verificar el valor de Estampado (crear nuevo estampado si es un string)
                    if (!is_numeric($stampId)) { 
                        $estampado = AttributeValue::firstOrCreate(
                            ['value' => ucwords(strtolower($stampId)), 'attribute_id' => $estampadoAttributeId]
                        );
                        $stampId = $estampado->id;
                    }

                    // 4.4 Consultar si la combinación si el color y estampado de esa variante ya posee una imagen en la base de datos
                    $existeVariante = $product->variants()
                        ->whereNotNull('image_id')
                        ->whereHas('attributeValueVariants', function (Builder $query) use ($colorId, $stampId) {
                            $query->whereIn('attribute_value_id', [$colorId, $stampId]);
                        }, '=', 2) // Asume que solo se usan Color y Estampado para la imagen
                        ->first();

                    if ($existeVariante) { // Entra si la variante existe con la imagen
                        $imageId = $existeVariante->image_id; // Capturar imagen
                    } else {
                        // Variante NO existe, DEBEMOS CREAR UNA NUEVA IMAGEN
                        $uploadedFile = $variantData['main_image_url'];
                            
                        // Guardar la imagen en el storage y DB
                        $path = $uploadedFile->storePublicly('uploads', 'public'); 
                        // Registrar en la tabla de imágenes
                        $image = Image::create([
                            'main_image_url' => $path,
                        ]);
                        // Capturar el ID de la nueva imagen
                        $imageId = $image->id;
                    }

                    // 5 Procesar logicas de las tallas y crear el objecto de las variantes:
                    $skuEditar = explode('-', trim($variantData["sku"] ?? '')); // Dividir el sku en un array para poder agregar la talla al sku
                    $atributos = array_splice($skuEditar, -2); // Obtener las partes del SKU que corresponden a los atributos (sin importar cuántas partes tenga el SKU)
                    // Ahora los unimos de nuevo si los necesitas como string
                    $skuRaiz = implode("-", $skuEditar); // Resultado: "SKA-2025-001"
                    $propiedades = implode("-", $atributos); // Resultado: "BLANCO-GEOMETRICA

                    // Recorre cada talla seleccionada para crear un SKU por cada una
                    foreach ($tallaIds as $t) {
                        $r = explode("--", $t); // Separar el identificador y valor de la talla
                        // Creamos un nuevo registro de base de datos para cada SKU final
                        $listaSku[] = [
                            'product_id' => $product->id,
                            'full_sku' => "$skuRaiz-$r[1]-$propiedades",
                            'stock' => $variantData["stock"],
                            'image_id' => $imageId,
                            'created_at' => $now,
                            'updated_at' => $now,
                            
                        ];
                        $listaAttv["$skuRaiz-$r[1]-$propiedades"] = [
                            'color' => $colorId,
                            'estampado' => $stampId,
                            'talla' => $r[0],
                        ];
                    }
                    // 6. Preparar la variante para la persistencia final (fuera del loop de variantes)
                    $dataFinal = array_merge($dataFinal, $listaSku);
                    $attTemporales = array_merge($attTemporales, $listaAttv);
                }

                // A. INSERCIÓN MASIVA DE VARIANTES
                if (!empty($dataFinal)) {
                    ProductVariant::insert($dataFinal); // UN solo INSERT para todas las variantes de todos los grupos.
                }
                // B. RECUPERAR LAS VARIANTES INSERTADAS Y SINCRONIZAR ATRIBUTOS
                $skudConsultar = array_keys($attTemporales);
                
                if (!empty($skudConsultar)) {
                    // Recuperar solo los IDs de las variantes que acabamos de crear (SELECT eficiente)
                    $newVariants = ProductVariant::whereIn('full_sku', $skudConsultar)->get(['id', 'full_sku']);
                    // Crear un mapa (SKU => ID) para búsqueda rápida
                    $variantMap = $newVariants->keyBy('full_sku')->map->id->toArray(); 
                    // Construir el array final para la tabla pivote de atributos
                    foreach ($attTemporales as $k => $attV) { 
                        $varianteID = $variantMap[$k]; // Obtener el id de la variante creada
                        foreach ($attV as $valor) {
                            $pivotDataToInsert[] = [
                                'product_variant_id' => $varianteID,
                                'attribute_value_id' => $valor,
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
            // 8. Mensaje y limpieza de estado de Livewire
            $this->variants = [];
            $this->productElegido = "";
            $this->agregarVariante(); // Reiniciar con una variante vacía
            $this->seccionContenido = 'producto_base'; // Regresar a la sección de producto_base
            $this->cargarAtributos(); // Carga todos los atributos de nuevo y sus valores para llenar los selectores en la vista.
            session()->flash('notificacion', ['tipo' => 'text-green-700 bg-green-100','titulo' => '¡Registro Exitoso!','mensaje' => '¡Listo! Guardado correctamente.',]);
        } catch (\Exception $e) {
            session()->flash('notificacion', ['tipo' => 'text-red-700 bg-red-100','titulo' => '¡Error!','mensaje' => 'Error al guardar los cambios.',]);
            
        }
    }
   // Metodos hock
    public function updatedVariants($valor,$key){
        // 1. Verificar si la clave contiene la palabra "estampado"
        if (str_contains($key, 'estampado')) {
            if (!is_numeric($valor)) {
                $this->allAttributes["Estampado"][] = [
                    'value' => $valor, // O el valor que necesites
                    'text' => $valor // El texto de la nueva opción
                ];
            }
        }

        // 2. Verificar si la clave contiene la palabra "color"
        if (str_contains($key, 'color') && !str_contains($key, 'color_code')) {
            if (!is_numeric($valor)) {
                $this->allAttributes["Color"][] = [
                    'value' => $valor, // O el valor que necesites
                    'text' => $valor // El texto de la nueva opción
                ];
            }
        }
    }
    /**
     * Construye un SKU para la variante $index combinando el sku base y valores seleccionados.
     * Orden preferente de atributos: Color, Talla, Material, Complemento, Estampado
     */
    protected function buildVariantSku(int $index): string
    {
        
        // 1. Aplicar el filtro a la colección existente de productos
        $productoPadre = $this->todosProductos->find($this->productElegido);

        // Determinar sku base: preferir la propiedad skuBase; si está vacía, tratar de obtener del producto elegido
        if (empty($this->productElegido)) {
            $productoPadre = Product::find($this->productElegido);
        }

        // Optener el sku base
        $skuBase = trim($productoPadre->sku_base);

        // Construir el sku de la variante:
        $attributes = $this->variants[$index]["atributos"] ?? [];
        $order = ['color', 'material', 'complemento', 'estampado'];
        $parts = [];

        // Tomar valores en el orden preferido.
        foreach ($order as $name) {

            if (!empty($attributes[$name])) {
                $inputValue = $attributes[$name];
                $valueToSlug = null;

                // 1. Verificar si el valor es un ID numérico (debe consultar la BD)
                if (is_numeric($inputValue)) {
                    // Es un ID: Buscar en la BD y obtener el valor real.
                    $av = AttributeValue::find($inputValue);
                    if ($av) {
                        $valueToSlug = $av->value;
                    }
                } else {
                    // Es un String (valor directo): Usar el valor tal cual.
                    $valueToSlug = $inputValue;
                }
                // 2. Si se obtuvo un valor (ya sea de la BD o el string directo), se formatea y agrega
                if ($valueToSlug) {
                    $parts[] = Str::upper($valueToSlug);
                }
            }
        }

        return $skuBase . '-' . implode('-', $parts);
    }
    /**
     * Método invocado desde la vista para forzar la reconstrucción del SKU
     * cuando un select cambia. Llamado con wire:change desde cada select.
     */
    public function rebuildSku(int $index,string $opcion = "ninguno")
    {
        if (! isset($this->variants[$index])) {
            return;
        }
        // Crear y almacenar el sku de la variante
        $this->variants[$index]['sku'] = $this->buildVariantSku($index);
        // Crear y almacenar el codigo de la variante
        $opcion == "color" && $this->validarColor($index);
    }
    // Metodo de Elimina una variante por índice y reindexa el array
    public function removeVariant($index)
    {
        if (isset($this->variants[$index])) {
            unset($this->variants[$index]);
            $this->variants = array_values($this->variants);
        }
    }
    // Metodo Agrega una nueva variante vacía al arreglo $variants
    public function agregarVariante()
    {
        $this->variants[] = [
            'sku' => '',
            'stock' => 0,
            'main_image_url' => '',
            'atributos' => [
                'color' => '',
                'color_code' => '#000001',
                'talla' => [],
                'estampado' => '',
            ],
        ];
    }
    // Metodo para validar el color seleccionado por el usuario
    public function validarColor(int $i)  {
        $colorSelect = $this->variants[$i]["atributos"]["color"]; // Capturar el color seleccionado por el usuario para una variante
        // Captura si el color se encuentra en la lista de colores, si no esta es porque es nuevo y debe seleccionarlo el usuario
        $this->variants[$i]["atributos"]["color_code"] = is_numeric($colorSelect) ?  $this->listaColoresHex[$colorSelect]["color_code"] : "#000001";
    }
    // Navegación directa (usado para el paso de retroceso o para ir a un paso específico)
    public function mostrarSeccion(string $seccion, string $accion = 'Regresar')
    {
        if ($accion != "Regresar") {
            // 1. Obtiene las reglas específicas para el paso actual. Accede al array de reglas y solo toma las claves que coinciden con el paso actual.
            $reglasValidacion = $this->reglas()[$seccion];
            // 2. Ejecuta la validación. Si falla, se detiene y muestra errores.
            $validatedData = $this->validate($reglasValidacion);
            // 3. Si no ocurre ningun error Transiciona al siguiente paso si la validación es exitosa.
            $this->seccionContenido == 'producto_base' ? $this->seccionContenido = 'variantes' : $this->crearVariante($validatedData);
        }else{
            $this->resetValidation(); // Limpiar errores de validación al regresar
            $this->seccionContenido = $seccion; // Retrocede al paso indicado
        }
    }
    // Método cargarAtributos es auxiliar para cargar los valores que va tener los productos de la base de datos
    protected function cargarAtributos()
    {
        $consultar = Attribute::with('attributeValues')->get(); 

        foreach ($consultar as $atributo) {
            $this->allAttributes[$atributo->name] = $atributo->attributeValues->map(function ($c) {
                return [
                    'value' => $c->id, 
                    'text' => $c->value
                ];
            })->toArray(); // Convertimos el resultado mapeado a un array de PHP

            if ($atributo->name == "Color") {
                // 2. Array 'value', 'text' y 'codigo' (asumiendo que el campo se llama 'code')
                $this->listaColoresHex = $atributo->attributeValues->map(function ($c) {
                    return [
                        'id' => $c->id, 
                        'color' => $c->value,
                        'color_code' => $c->color_code 
                    ];
                })->keyBy('id') // reindexa todo el array, utilizando el valor de la clave 'value'
                ->toArray();
            }
        }
    }
    // Método cargarProducto es auxiliar para cargar los productos de la base de datos
    protected function cargarProducto()
    {
        $this->todosProductos = Product::all(['id', 'name', 'sku_base']);
    }
    // Metodo mount Se ejecuta una sola vez al cargar el componente
    public function mount()
    {
        $this->cargarProducto(); // Carga todos los productos disponibles
        $this->cargarAtributos(); // Carga todos los atributos y sus valores para llenar los selectores en la vista.
        // Inicializa la primera variante para que el usuario pueda empezar a llenar datos
        if (empty($this->variants)) {
            $this->agregarVariante(); // Agrega una variante vacía al iniciar el componente
        }
    }
    // Metodo Renderiza la vista Livewire
    public function render()
    {
        return view('livewire.crear-nueva-variante');
    }
}
