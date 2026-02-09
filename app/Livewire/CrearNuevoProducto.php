<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CrearNuevoProducto extends Component
{
    // PROPIEDADES DE ESTADO Y CONTROL DE PASOS: Control de la navegación: 'categoria', 'producto_base', Finalizar
    public string $seccionContenido = 'categorias'; // Seccion predeterminada del formulario
    // Datos del Paso 1: Categorías
    public string $nuevaCategoria = ''; // Nombre de la nueva categoría a crear
    public array $catgElegidas = []; // IDs de categorías seleccionadas en el select
    public $todasCategorias; // Va almacenar la Colección de categorías existentes en la base de datos
    public int $select_key = 0; // CLAVE PARA EL RE-RENDER DE TOM SELECT, Es decir , para forzar a Livewire a recrear el componente select, cada vez que se agrega una nueva categoría.

    // Datos del Paso 2: Productos
    public string $skuBase = ''; // sku Base del producto a crear
    public string $nuevoProducto = ''; // Nombre del nuevo producto a crear
    public string $descriptionProducto = ''; // description del nuevo producto a crear
    public $priceRetail = 0.00; // Precio minorista al público del nuevo producto a crear
    public $priceWholesale = 0; // Precio mayorista al público del nuevo producto a crear
    public $min_piezas = 0; // mínimo de piezas al por mayor 
    public $status = ""; // Estado del nuevo producto a crear

    // ====================================================================
    // REGLAS DE VALIDACIÓN DINÁMICA Y MENSAJES PERSONALIZADOS
    // ====================================================================
    // Se Define todas las reglas de validación agrupadas por pasos del formulario.
    protected function reglas()
    {
        $reglas = [
            'categorias' => [
                'nuevaCategoria' => 'nullable|string|min:3|max:100|unique:categories,name', 
                'catgElegidas' => 'required|array|min:1',
            ],
            'producto_base' => [
                'skuBase' => 'required|string|min:3|max:100|unique:products,sku_base', 
                'nuevoProducto' => 'required|string|min:3|max:150|unique:products,name',
                'descriptionProducto' => 'required|string|max:1000',
                'priceRetail' => 'required|numeric|min:1.00',
                'min_piezas' => 'integer|min:0',
                'priceWholesale' => 'numeric|min:0.00',
                'status' => ['required', Rule::in(['active', 'inactive'])],
            ],
        ];

        // Se devuelve el array completo para ser usado en la navegación y validación de categorías
        return $reglas;
    }
    // Mensajes de error personalizados para cada imputs
    protected $messages = [
        /** Mensaje de agregar nueva categoria */
        'nuevaCategoria.min'=> 'Ingrese al menos :min caracteres de longitud.', 
        'nuevaCategoria.unique'=> 'La categoría ingresada ya existe en el sistema.', 
        /** Mensaje de seleccionar categoria */
        'catgElegidas.required' => 'Debes seleccionar al menos una categoría.', 
        'catgElegidas.min' => 'Selecciona al menos :min categoría.', 
        /** Mensajes crear un producto base */
        'skuBase.required' => 'Debes ingresar el sku base del producto.',
        'skuBase.min' => 'Ingrese al menos :min caracteres de longitud.',
        'skuBase.unique'=> 'El codigo sku ingresado ya existe en el sistema.', 
        'nuevoProducto.required' => 'Debes ingresar el nombre del producto.',
        'nuevoProducto.min' => 'Ingrese al menos :min caracteres de longitud.',
        'nuevoProducto.unique'=> 'El nombre ingresado para ese producto ya existe en el sistema.', 
        'descriptionProducto.required' => 'El campo de descripción del producto es obligatorio.',
        'priceRetail.required' => 'El campo de precio de venta minorista del producto debe ser de al menos 1,00.',
        'priceRetail.min' => 'El campo de precio de venta minorista del producto debe ser de al menos 1,00.',
        'priceWholesale.required' => 'El campo de precio de venta mayorista del producto debe ser de al menos $1.00.',
        'priceWholesale.min' => 'El campo de precio de venta mayorista del producto debe ser de al menos $1.00.',
        'status.required' => 'Debes seleccionar el estado del producto.',
        'status.in' => 'El estado seleccionado no es válido.',
        'min_piezas.required' => 'El campo mínimo piezas es obligatorio.',
        'min_piezas.min' => 'El campo mínimo de piezas debe ser al menos 1.',
    ];
    // Metodo crearProducto posee la Lógica para registrar un nuevo producto directamente desde el formulario.
    public function crearProducto($camposValidados)
    {
        // 2. Validación adicional (si el campo precio mayorista o minimo de piezas tiene datos mayores a 0)
        $dataToValidate = [ // Prepara los datos a validar
            'min_piezas' => $this->min_piezas,
            'priceWholesale' => $this->priceWholesale,
        ];

        if (!empty($this->min_piezas) || !empty($this->priceWholesale)) {
            $rules = [ // Definir las reglas específicas para esta validación
                'min_piezas' => 'required|integer|min:1', // Cambiamos a min:1
                'priceWholesale' => 'required|numeric|min:1.00', // Cambiamos a min:1.00
            ];
            $messages = [ // Define mensajes personalizados si no quieres usar los mensajes de la regla principal
                'min_piezas.min' => 'El mínimo de piezas debe ser al menos 1.',
                'priceWholesale.min' => 'El precio mayorista debe ser como mínimo $1.00.',
            ];

            // Crea y ejecuta el validador
            $validator = Validator::make($dataToValidate, $rules, $messages);

            // Lanza la excepción si falla, que Livewire manejará automáticamente
            $validator->validate();
        }
    
        // Crear el array final mapeado para la BD
        $datosParaBD = [
            // Columna DB        => Dato Validado (Clave de la Propiedad)
            'sku_base'      => $camposValidados['skuBase'],
            'name'          => $camposValidados['nuevoProducto'],
            'description'   => $camposValidados['descriptionProducto'],
            'price_retail'  => $camposValidados['priceRetail'],
            'price_wholesale' => $camposValidados['priceWholesale'] <= 0 ? null : $camposValidados['priceWholesale'],
            'min_pieces' => $camposValidados['min_piezas'] <= 0 ? null : $camposValidados['min_piezas'],
            'status'        => $camposValidados['status'],
        ];
        // // CREACIÓN: Usa Asignación Masiva. (Requiere $fillable en el modelo Product)
        $producto = Product::create($datosParaBD);
        // 7. Limpia solo las propiedades del formulario
        $this->reset([
            'nuevaCategoria',
            'skuBase', 
            'nuevoProducto', 
            'descriptionProducto', 
            'priceRetail', 
            'priceWholesale', 
            'min_piezas',
            'status',
        ]);

        // DICIÓN: SINCRONIZAR CATEGORÍAS
        // Asegúrate de que $product está disponible 
        if (!empty($this->catgElegidas) && $producto) {
            // El método sync acepta un array de IDs y se encarga de:
            // 1. Insertar las nuevas categorías.
            // 2. Eliminar las categorías que ya no están en la lista (si existían previamente).
            $producto->categories()->sync($this->catgElegidas);
            // Si solo quieres agregar y no eliminar las existentes, usa attach():
            // $product->categories()->attach($this->catgElegidas);
        }


        // Limpiar campo de categorias
        $this->catgElegidas = [];
         // Regresar a la sección de categorias
        $this->seccionContenido = 'categorias';
        // Mensaje Exito:
        session()->flash('notificacion', ['tipo' => 'text-green-700 bg-green-100','titulo' => '¡Registro Exitoso!','mensaje' => '¡Listo! Guardado correctamente.',]);
    }
    // Metodo crearCategoria posee la Lógica para registrar una nueva categoría directamente desde el formulario.
    public function crearCategoria()
    {
        // Obtiene el array completo de reglas.
        $reglasValidacion = $this->reglas();
        // Acceso seguro a la regla de validación de la nueva categoría 
        $categoriaReglas = $reglasValidacion['categorias']['nuevaCategoria'];

        if (Str::length($this->nuevaCategoria) >= 1) {
            // Valida solo el campo 'nuevaCategoria'.
            $this->validate(['nuevaCategoria' => $categoriaReglas]);
            // CREACIÓN: Usa Asignación Masiva. (Requiere $fillable en el modelo Category)
            $categor = Category::create(['name' => $this->nuevaCategoria]);
            // 1. Actualiza la lista de categorías (carga la nueva de la DB)
            $this->cargarCategoria(); 
            // 2. Selecciona la nueva categoría en el array de IDs
            $this->catgElegidas[] = (string) $categor->id; 
            // 3. Limpia el campo de texto
            $this->nuevaCategoria = '';
            // 4. CLAVE: Incrementa el wire:key para forzar a Livewire a recrear el div
            $this->select_key++; // 
            // Enviar mensaje de exito
            session()->flash('message_exitoso', 'Categoría registrada.');
        }else{
            // Si el campo está vacio, generar un mensjae de error personalizado.
            $this->addError('nuevaCategoria', 'El nombre de la categoría no puede estar vacío.');
        }
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
            $this->seccionContenido == 'categorias' ? $this->seccionContenido = 'producto_base' : $this->crearProducto($validatedData);
        }else{
            $this->resetValidation(); // Limpiar errores de validación al regresar
            $this->seccionContenido = $seccion; // Retrocede al paso indicado
        }
    }
    // Método cargarCategoria es auxiliar para cargar las categorías de la base de datos
    protected function cargarCategoria()
    {
        $this->todasCategorias = Category::all(['id', 'name']);
    }
    // Metodo mount Se ejecuta una sola vez al cargar el componente
    public function mount()
    {
        $this->cargarCategoria(); // Carga todas las categorías disponibles
    }
    // Metodo Renderiza la vista Livewire
    public function render()
    {
        return view('livewire.crear-nuevo-producto');
    }
}
