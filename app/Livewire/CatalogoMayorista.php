<?php

namespace App\Livewire;

use Livewire\Component;      // Importa la clase base de Livewire para componentes
use Livewire\WithPagination; // Importa el trait de paginación para usar el método 'paginate()'
use App\Models\Category;     // El namespace correcto para el modelo Category (Aunque no se usa directamente)
use App\Models\Product;      // El namespace correcto para el modelo Product

class CatalogoMayorista extends Component
{
    use WithPagination; // Usa el trait para habilitar la paginación de los resultados de la consulta.
    public $ContenidoCargado = false; // Indica si el contenido principal ya se cargó o no.     

    // --- Propiedad que define cuántos productos se mostrarán por página:
    public $perPage = 20; // Se puede ajustar según sea necesario.
    
    // --- Filtros de seleccion, vinculados a la interfaz (checkboxes). Va permitir al usuario filtrar los productos:
    public $selectedColors = [], $selectedSizes = [], $selectedMaterials = [], $selectedEstampado = [], $selectedCategory = [];
    public $ordenProducto = "desc"; // Orden de los productos: 'asc' o 'desc' (por fecha de creación)

    // --- Propiedades que almacenan los valores disponibles para cada filtro (cargados en mount)
    public $availableColors = [], $availableSizes = [], $availableMaterials = [], $availableEstampado = [];

    // --- Propiedad que almacenará los productos a mostrar en el catálogo
    protected $listaProductos = []; 

    // --- Propiedad que Va permitir identificar en que seccion del catalogo estamos (mayorista o minorista)
    public $seccionCatalogo;

    // --- Propiedad que Va almacenar la Colección de categorías existentes en la base de datos
    public $todasCategorias;

    // Listeners (útil para depurar o forzar refresh desde botones)
    protected $listeners = [
        'refreshComponent' => '$refresh',
    ];
    
    // AUXILIAR Método: se llamará después de que Livewire se haya inicializado en el navegador
    public function inicializarContenido()
    {
        // sleep(4); // Descomentar esta liunea solo en produccion para ver claramente el efecto de carga 
        $this->ContenidoCargado = true; // <-- El componente está listo
    }
    // AUXILIAR Método: Es llamado desde el botón "Aplicar filtros" de los modales.
    public function applyFilters()
    {
        $this->resetPage(); // Resetea la paginación y forzar re-render (render se ejecutará después del request)
    }
    //  AUXILIAR Método: para resetear filtros desde el botón Reset de los modales.
    public function resetFilters($opcion)
    {   
        // Limpia los arrays de selección (desmarca checkboxes)
        $opcion == "filtros" ? $this->reset(['selectedColors', 'selectedSizes', 'selectedMaterials', 'selectedEstampado']) : $this->reset(['selectedCategory']);
        // Resetea la paginación para volver a la primera página
        $this->resetPage();
    }
    // AUXILIAR Método: Es llamado desde el botón "ordenar por" de filtros.
    public function ordenarProducto($opcion)
    {
        $this->ordenProducto = $opcion;
    }
    // Método cargarCategoria es auxiliar para cargar las categorías de la base de datos
    protected function cargarCategoria()
    {
        $this->todasCategorias = Category::all(['id', 'name']);
    }
    // El método principal que construye y ejecuta la consulta de productos con todos los filtros aplicados.
    public function consultarProductos() {
        // 1. Crear un loop que va a iterar sobre los filtros disponibles, La clave está en crear un mapa que relacione las propiedades del componente con el nombre del atributo en la base de datos:
        // --------------------------------------------------------------------------------
        $attributeFilters = [
            // PropiedadComponente => nombreAtributo
            'selectedColors'       => 'Color',
            'selectedSizes'        => 'Talla',
            'selectedMaterials'    => 'Material',
            'selectedEstampado'    => 'Estampado',
        ];
        // 2. Construcción de la Consulta Base y Carga de Relaciones
        // --------------------------------------------------------------------------------
        $query = Product::where('status', 'active') // Filtra productos que estén marcados como 'activos'.
            // Cargar la relación con 'variants' (variantes del producto). CON FILTROS CONDICIONALES. se define qué variantes se van a cargar para cada producto.
            ->with([
                'variants' => function($q) use ($attributeFilters) {
                    // 1.1 Solo variantes activas
                    $q->where('status', 'active');

                    // 1.2 Filtro base de stock (SIEMPRE aplicado a la carga de variantes)
                    $q->where('stock', '>=', 1); // Dentro de la relación 'variants', solo obtener las variantes con 'stock' mayor que 0.

                    // 2. Aplicar filtros condicionales a la CARGA (`with`). Esto asegura que solo se carguen las variantes que coinciden con los filtros.
                    foreach ($attributeFilters as $propertyKey => $attributeName) {
                        if (!empty($this->$propertyKey)) {
                            $selectedValues = $this->$propertyKey;
                            // Aplicamos restricción 'whereHas' 
                            $q->whereHas('attributeValues', function($q2) use ($attributeName, $selectedValues) {
                                $q2->whereHas('attribute', fn($q3) => $q3->where('name', $attributeName))
                                ->whereIn('value', $selectedValues);
                            });
                        }
                    }

                    // 3. Carga de sub-relaciones anidadas (AttributeValue, Image). Pero SÓLO las variantes  que pasaron el filtro 'whereHas'
                    $q->with([
                        'attributeValues'  => fn($q) => $q->select('attribute_values.id', 'attribute_values.attribute_id', 'attribute_values.value', 'attribute_values.color_code')
                            // Cargar la sub-relación con 'attribute'.
                            ->with([
                                'attribute' => fn($q) => $q->select('id', 'name') 
                            ]),
                        'image' => fn($q) => $q->select('id','main_image_url'),
                    ]);
                }
            ])
            /* Asegurar que el producto principal tenga al menos una variante con 'stock' mayor que 0. 
            Esto es necesario porque el `with` solo carga la relación filtrada, pero `whereHas` filtra el modelo principal (`Product`). 
            Es decir, es crucial para no mostrar productos cuyas variantes visibles (activas y con stock) hayan sido eliminadas por los filtros. */
            ->whereHas('variants', function ($q) {
                // 5.1. La variante debe estar activa
                $q->where('status', 'active');

                // 5.2. La variante debe tener stock
                $q->where('stock', '>', 0);
            });


        // 3. APLICAR FILTRO ESPECÍFICO para (catalogo MAYORISTA).
        if ($this->seccionCatalogo == "mayorista") { // Entra si el usuario esta consultando por catalogo mayorista
            // Debe tener un precio mayorista Y piezas mínimas definidas (no null)
            $query->whereNotNull('price_wholesale')->whereNotNull('min_pieces');
        } 

        // 4. Aplicar filtro por Categoría seleccionada (si el usuario ingrese alguna)
        // --------------------------------------------------------------------------------
        if (!empty($this->selectedCategory)) {
            $query->whereHas('categories', function ($q) {
                $q->whereIn('categories.id', (array) $this->selectedCategory);
            });
        }
        // 5. Aplicar todos los Filtros de Atributo (Refactorizado)
        // --------------------------------------------------------------------------------
        foreach ($attributeFilters as $propertyKey => $attributeName) {
            if (!empty($this->$propertyKey)) {
                $selectedValues = $this->$propertyKey;
                // El producto debe tener una variante que coincida con el atributo y valor. Y debe tener una variante que cumpla: [Stock>0] AND [Status='active']
                $query->whereHas('variants', function($q) use ($attributeName, $selectedValues) {
                    // Status y Stock para asegurar que solo contamos variantes visibles
                    $q->where('status', 'active')->where('stock', '>', 0);

                    $q->whereHas('attributeValues', function($q2) use ($attributeName, $selectedValues) {
                        // Filtrar por el nombre del atributo ('Color', 'Talla', etc.)
                        $q2->whereHas('attribute', callback: fn($q3) => $q3->where('name', $attributeName))
                        ->whereIn('value', $selectedValues);
                    });
                });
            }
        }

        // 6. APLICAR ORDENAMIENTO de los productos por precio o fecha
        // --------------------------------------------------------------------------------
        $secuencia = $this->ordenProducto ?? 'desc';
        if (in_array(strtolower($secuencia), ['precioasc', 'preciodesc'])) {
            $columnaFiltrar = $this->seccionCatalogo == "mayorista" ? "price_wholesale" : "price_retail"; 
            $ordenFiltrar = $secuencia == "precioASC" ? 'asc' : "desc";
            $query->orderBy($columnaFiltrar, $ordenFiltrar);
        }else{
            // Aplica el ordenamiento solo si la dirección es válida. // Si no se especifica o es inválido, va ordenar por el más nuevo (latest())
            in_array(strtolower($secuencia), ['asc', 'desc']) ? $query->orderBy('created_at', $secuencia) : $query->latest(); 
        }
        
        // 7. Devolver el Query Builder listo para paginar/ejecutar
        return $query;
    }
    /* El métdo mount Se ejecuta SOLO una vez, al inicializar el componente. Aquí cargamos los filtros globales disponibles, 
    que no cambian con la selección del usuario, para evitar recargar datos pesados en cada render. */
    public function mount()
    {
        // ---------------------------------------------------------------------
        // CONSULTA #1: Obtener TODOS los atributos únicos para los filtros. Se ejecuta UNA SOLA VEZ para rellenar los filtros de la interfaz.
        // Se Inicia una consulta para obtener TODOS los productos globalmente que cumplen con las condiciones
        // para ser utilizados en los filtros del modal (como los filtros de color/talla/entre otros).
        // ---------------------------------------------------------------------
        // Construcción de la Consulta Base y Carga de Relaciones
        $query = Product::where('status', 'active'); // 1. Filtrar productos que estén marcados como 'activos'.

        // APLICAR FILTRO ESPECÍFICO para (catalogo MAYORISTA).
        if ($this->seccionCatalogo == "mayorista") { // Entra si el usuario esta consultando por catalogo mayorista
            // Debe tener un precio mayorista Y piezas mínimas definidas (no null)
            $query->whereNotNull('price_wholesale')->whereNotNull('min_pieces');
        } 

        // Cargar la relación con 'variants' (variantes del producto). se define qué variantes se van a cargar para cada producto. para evitar el problema de N+1 consultas.
        $globalProducts = $query->with([
                'variants' => fn($q) => $q
                    ->where('stock', '>=', 1)  // Dentro de la relación 'variants', solo obtener las variantes con 'stock' mayor que 0.
                    // Carga de sub-relaciones anidadas (AttributeValue).
                    ->with([
                        'attributeValues'  => fn($q) => $q->select('attribute_values.id', 'attribute_values.attribute_id', 'attribute_values.value', 'attribute_values.color_code')
                            // Cargar la sub-relación con 'attribute'.
                            ->with([
                                'attribute' => fn($q) => $q->select('id', 'name') 
                            ]),
                        ])
            ])
            /* Asegurar que el producto principal tenga al menos una variante con 'stock' mayor que 0. 
            Es decir, evitará mostrar productos y atributos que no tengan variantes o que sus stock sean menor a 1 */
            ->whereHas('variants', fn($q) => $q->where('stock', '>', 0))
            ->get(); // Ejecuta la consulta y obtiene todos los productos en una colección.

        // 2. Aplana todas las attributeValues de todas las variantes de todos los productos globales
        $allAttributeValues = $globalProducts->flatMap(fn($product) => $product->variants->flatMap(fn($v) => $v->attributeValues));

        // 3 Extracción de valores únicos por tipo de atributo (Color, Talla, ....)
        // 3.1 Determinar los colores disponibles para mostrar en la interfaz de usuario (Modal).
        $this->availableColors = $allAttributeValues
            // Filtra para mantener solo los valores de atributo cuyo nombre es 'Color'.
            ->filter(fn($av) => optional($av->attribute)->name === 'Color')
            // Extrae 'value' y 'code_color' de cada elemento (variante)
            ->map(function ($av) {
                return [
                    'value'      => $av->value,
                    'color_code' => $av->color_code,
                ];
            })
            // Elimina valores duplicados.
            ->unique()
            // Ordena alfabéticamente/numéricamente.
            ->sortBy('value')
            // Reindexa la colección (quita claves no secuenciales).
            ->values()
            // Convierte la colección final en un array de PHP.
            ->all();

        // 3.2 Determinar las tallas disponibles para mostrar en la interfaz de usuario (Modal).
        $this->availableSizes = $allAttributeValues
            // Filtra para mantener solo los valores de atributo cuyo nombre es 'Talla'.
            ->filter(fn($av) => optional($av->attribute)->name === 'Talla')
            // Extrae 'value' de cada elemento (variante)
            ->pluck('value')
            // Se realiza los mismo metodos
            ->unique()->sort()->values()->all();

        // 3.3 Determinar los materiales disponibles para mostrar en la interfaz de usuario (Modal).
        $this->availableMaterials = $allAttributeValues
            // Filtra para mantener solo los valores de atributo cuyo nombre es 'Material'.
            ->filter(fn($av) => optional($av->attribute)->name === 'Material')
            // Se realiza los mismo metodos
            ->pluck('value')->unique()->sort()->values()->all();


        // 3.4 Determinar los estampado disponibles para mostrar en la interfaz de usuario (Modal).
        $this->availableEstampado = $allAttributeValues
            // Filtra para mantener solo los valores de atributo cuyo nombre es 'Estampado'.
            ->filter(fn($av) => optional($av->attribute)->name === 'Estampado')
            // Extrae 'value' de cada elemento (variante)
            ->pluck('value')
            // Se Excluye los valores 'Ninguno' o 'none', independientemente de mayúsculas/minúsculas.
            ->filter(fn($va) => !in_array($va, ['Ninguno', 'none']))
            // Se realiza los mismo metodos
            ->unique()->sort()->values()->all();

        // 4. Carga todas las categorías disponibles para el modal filtrado
        $this->cargarCategoria(); 
    }
    // El método 'render' es el corazón de cualquier componente Livewire. Se ejecuta para generar la vista y proveerla con datos.
    public function render()
    {
        // Usamos (consultarProductos) y aplicamos paginate
        $productosPaginados = $this->consultarProductos()->paginate($this->perPage);
        // Retorna la vista de Livewire, inyectando la colección de productos.
        return view('livewire.catalogo-mayorista', [
            'products' => $productosPaginados ?? [], // Pasamos el Paginator a la vista
        ]);
    }
}
