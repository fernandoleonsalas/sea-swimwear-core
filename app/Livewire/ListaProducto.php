<?php

namespace App\Livewire;

use Livewire\Component;         // Importa la clase base de Livewire para componentes
use Livewire\WithFileUploads;   // Importa la carga de archivos en Livewire
use Illuminate\Support\Collection;          // Importa la clase Collection de Laravel
use Illuminate\Support\Facades\DB;          // Importa para las transacciones!
use Illuminate\Support\Facades\Validator;   // Importa el facade Validator para validaciones personalizadas
use Illuminate\Validation\Rule;             // Importa la clase Rule para reglas de validación avanzadas
use App\Models\Category;        // El namespace correcto para el modelo categorias
use App\Models\Product;         // El namespace correcto para el modelo Productos
use App\Models\ProductVariant;  // El namespace correcto para el modelo variantes del producto
use App\Models\Attribute;       // El namespace correcto para el modelo atributos
use App\Models\Image;           // El namespace correcto para el modelo imagenes

class ListaProducto extends Component
{
    use WithFileUploads; // Habilita la carga de archivos en Livewire

    /* ------------------------------------------
    // Propiedades pública de paginación manual
    // ------------------------------------------ */
    public $productoMostrar = 10; // Cantidad de GRUPOS (variantes) a mostrar por página
    public $currentPage = 1; // Página actual.
    public $totalItems = 0; // Total de GRUPOS de variantes únicos encontrados.
    public $totalPages = 1; // Total de páginas calculadas.
    public $campoBusqueda = ""; // Campo de búsqueda para filtrar productos por nombre o variante.

    // ------------------------------------------
    // Propiedades pública para el tabla
    // ------------------------------------------
    public $listaProductos; // Almacenará los GRUPOS de variantes paginados (el output final).
    public $listaAtributos; // Va almacenar los Atributos con sus valores para poblar selects en el modals
    public $listaCategorias; // Va almacenar todas las categorias.
    public $editingStock = []; // va almacenar el stock actualizado de una variante
    public $originalStock = []; // va almacenar el stock antiguo de una variante

    // ------------------------------------------
    // Propiedades pública para el modal
    // ------------------------------------------
    public int $campos_key = 0; // CLAVE PARA EL RE-RENDER DE TOM SELECT, o campos que estan con wire:ignore Es decir , para forzar a Livewire a recrear el componente.
    public $varianteSelec = ""; // Va almacenar temporalmente la variante a editar
    public $tituloModal = "Editar";
    public $nombreProdutoA = '';
    public $descripcionProducto = '';
    public $precioMinProducto = 0;
    public $precioMayProducto = 0;
    public $minPiezasMay = 0;
    public $estadoProducto = "";
    public $nuevaImagen = "";
    public $productoEditarID = "";
    public $imagen_url_anterior = "";
    public bool $showModalEP = false;   // va manejar el estado del modal, aunque Flowbite, lo hará Alpine lo leerá directamente desde el objeto Livewire en app.js
    public array $categoriasSelect = []; // va almacenar la categorias seleccionada por el usuario
    public $rutasImagesTemporal = []; // va almacenar las rutas temporales de la imagenes actualizar

    // ====================================================================
    // REGLAS DE VALIDACIÓN DINÁMICA Y MENSAJES PERSONALIZADOS
    // ====================================================================
    // Se Define todas las reglas de validación agrupadas por pasos del formulario.
    protected function reglas()
    {
        $reglas = [
            "modal" => [
                // --- Reglas para el Select de Categorías (Tom Select) ---
                // $this->categoriasSelect: Contiene un array de IDs existentes o nombres nuevos.
                'categoriasSelect' => 'required|array|min:1', 
                
                // --- Reglas para los campos principales del Producto ---
                // $this->nombreProdutoA: Corresponde al campo 'Nombre del Producto'
                'nombreProdutoA' => 'required|string|min:3|max:150|unique:products,name,' . $this->productoEditarID,
                
                // $this->descripcionProducto: Corresponde al campo 'Descripción'
                'descripcionProducto' => 'required|string|max:1000',
                
                // $this->precioMinProducto: Corresponde al campo 'Precio minorista'
                'precioMinProducto' => 'required|numeric|min:0.01',
                
                // $this->precioMayProducto: Corresponde al campo 'Precio mayorista'
                'precioMayProducto' => 'required|numeric|min:0.00',
                
                // $this->minPiezasMay: Corresponde al Campo de Mínimo De Piezas al por mayor
                'minPiezasMay' => 'required|numeric|min:0',
                
                // $this->estadoProducto: Corresponde al campo 'Estatus' (select)
                'estadoProducto' => ['required', Rule::in(['active', 'inactive'])],

                // --- Reglas para la Subida de Imagen ---
                // $this->nuevaImagen: Corresponde al campo 'Cambiar imagen'. Es 'nullable'
                // porque no siempre se va a cambiar.
                'nuevaImagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Subida de hasta 2MB, añadido 'webp'
            ],
            "tabla" => [
                // Validación para un elemento específico del array editingStock
                'editingStock.*' => 'required|numeric|min:0|max:999999',
            ]
        ];

        // Se devuelve el array completo para ser usado en el método $this->validate()
        return $reglas;
    }

    protected $messages = [
        // --- Mensajes para el campo de Categorías ---
        // La propiedad vinculada es 'categoriasSelect' (array)
        'categoriasSelect.required' => 'Debes seleccionar al menos una categoría.', 
        'categoriasSelect.array'    => 'El formato de selección de categorías es incorrecto.',
        'categoriasSelect.min'      => 'Selecciona al menos :min categoría.', 

        // --- Mensajes para el campo Nombre del Producto ---
        // La propiedad vinculada es 'nombreProdutoA'
        'nombreProdutoA.required' => 'Debes ingresar el nombre del producto.',
        'nombreProdutoA.string'   => 'El nombre debe ser texto.',
        'nombreProdutoA.min'      => 'Ingrese al menos :min caracteres de longitud.',
        'nombreProdutoA.max'      => 'El nombre no puede exceder los :max caracteres.',
        'nombreProdutoA.unique'   => 'El nombre ingresado para ese producto ya existe en el sistema.', 

        // --- Mensajes para el campo Descripción ---
        // La propiedad vinculada es 'descripcionProducto'
        'descripcionProducto.required' => 'El campo de descripción del producto es obligatorio.',
        'descripcionProducto.string'   => 'La descripción debe ser texto.',
        'descripcionProducto.max'      => 'La descripción no puede exceder los :max caracteres.',

        // --- Mensajes para los Precios ---
        // Propiedad vinculada: 'precioMinProducto' (Precio Minorista)
        'precioMinProducto.required' => 'El precio minorista es obligatorio.',
        'precioMinProducto.numeric'  => 'El precio minorista debe ser numérico.',
        'precioMinProducto.min'      => 'El precio minorista debe ser de al menos :min.',

        // Propiedad vinculada: 'precioMayProducto' (Precio Mayorista)
        'precioMayProducto.required' => 'El precio mayorista es obligatorio.',
        'precioMayProducto.numeric'  => 'El precio mayorista debe ser numérico.',
        'precioMayProducto.min'      => 'El precio mayorista debe ser de al menos :min.',

        // Propiedad vinculada: 'minPiezasMay' (Mínimo De Piezas)
        'minPiezasMay.required' => 'El campo mínimo piezas es obligatorio.',
        'minPiezasMay.min' => 'El campo de debe ser de al menos 1.',

        // --- Mensajes para el Estatus ---
        // Propiedad vinculada: 'estadoProducto'
        'estadoProducto.required' => 'Debes seleccionar el estado del producto.',
        'estadoProducto.in'       => 'El estado seleccionado no es válido.',
        
        // --- Mensajes para la Imagen ---
        // Propiedad vinculada: 'nuevaImagen'
        'nuevaImagen.image' => 'El archivo debe ser una imagen (jpeg, png, etc.).',
        'nuevaImagen.mimes' => 'El formato de imagen debe ser jpeg, png, jpg, o webp.',
        'nuevaImagen.max'   => 'El tamaño de la imagen no puede ser superior a :max kilobytes.',
        
        // MENSAJES PARA LA VALIDACIÓN DE STOCK (editingStock.*)
        'editingStock.*.required' => 'El stock es obligatorio.',
        'editingStock.*.numeric' => 'El stock debe ser un número entero.',
        'editingStock.*.min' => 'El stock mínimo permitido es 0.',
        'editingStock.*.max' => 'El stock no puede superar 999,999.',
    ];
    

    // Metodo para actualizar el status de una variante especfica
    public function editarEstadoV(string|int $vID, string $accion) {
        try {   
            // Actualizar el status en la base de datos (USANDO TRANSACCIÓN)
            DB::transaction(function () use ($vID, $accion) {
                // 1.1. Actualizar la variante específica
                ProductVariant::where('id', $vID)->update(['status' => $accion]);
                // 1.2. Obtener el ID del producto padre
                $variant = ProductVariant::select('product_id')->find($vID);
                if (!$variant) {
                    // Si la variante no existe, lanzamos una excepción para abortar la transacción
                    throw new \Exception("Variante ID $vID no encontrada.");
                }
                $productId = $variant->product_id;
                
                // 1.3. LÓGICA DE COHERENCIA PRODUCTO-VARIANTE
                
                if ($accion === 'active') {
                    // REGLA 1: Si una VARIANTE se ACTIVA, el PRODUCTO PADRE debe estar ACTIVO.
                    Product::where('id', $productId)
                        ->where('status', '!=', 'active') // Solo actualiza si no está activo
                        ->update(['status' => 'active']);
                }

                if ($accion === 'inactive') {
                    // REGLA 2: Si una VARIANTE se DESACTIVA, verificar si es la ÚLTIMA ACTIVA.
                    
                    // Contar cuántas variantes activas quedan para este producto
                    $activeVariantsCount = ProductVariant::where('product_id', $productId)
                                                        // NO se incluye la variante que acabamos de desactivar, 
                                                        // ya que la actualización masiva ya se ejecutó en el paso 1.1.
                                                        ->where('status', 'active')
                                                        ->count();

                    // Si no quedan variantes activas, desactivar el producto padre
                    if ($activeVariantsCount === 0) {
                        Product::where('id', $productId)->update(['status' => 'inactive']);
                    }
                }
            });

            // Opcional: Notificación de éxito
            session()->flash('exito_status_db_' . $vID, 'Estatus actualizado.');

            /**
             * Recorrer el array `$this->listaProductos` usando referencias (&)
            * para modificar el array directamente.
             */
            foreach ($this->listaProductos as &$product) {
                // Recorrer los grupos de imágenes del producto
                foreach ($product['image_groups'] as &$imageGroup) {
                    
                    // Recorrer las variantes dentro del grupo
                    foreach ($imageGroup['variants_by_image'] as &$variant) {
                        
                        // 1. **Comprobar si es la variante que buscamos**
                        if ($variant['id'] === $vID) {
                            
                            // 2. **ACTUALIZAR EL STATUS** en el array original
                            $variant['status'] = $accion;
                            
                            // 3. Opcional: Salir de todos los bucles ya que la variante fue encontrada
                            // Usamos 'break 3;' para salir de los 3 bucles anidados.
                            break 3; 
                        }
                    }
                }
            }

            // 4. Devolver el array actualizado `$this->listaProductos`
            return $this->listaProductos;
        } catch (\Exception $e) {
            // Si hay un error de DB, forzamos un mensaje de error genérico
            session()->flash('error_status_db_' . $vID, 'Error al guardar el estatus.');
        } 
    }

    // Método para actualizar el stock de una variante específica
    public function actualizarStock(string|int $vID) {
        // 1. Obtiene el array completo de reglas.
        $reglasValidacion = $this->reglas();
        // 2. Acceso al array de reglas del modal
        $tablaReglas = $reglasValidacion['tabla'];
        // 3.Llama a $this->validate() con las reglas y las propiedades publica. se ejecuta la validación:
        $validatedData = $this->validate($tablaReglas);
        // 4. Obtener el el stock de la variante a actualziar
        $ntock = (int) $this->editingStock[$vID];
        // Método para actualizar el stock
        try {   
            // 5. Actualizar el stock en la base de datos (USANDO TRANSACCIÓN)
            DB::transaction(function () use ($vID, $ntock) {
                ProductVariant::where('id', $vID)->update(['stock' => $ntock]);
            });

            // 6. Actualizar el valor original para ocultar el botón 'Guardar'
            $this->originalStock[$vID] = $ntock;
            // Opcional: Notificación de éxito
            session()->flash('exito_stock_db_' . $vID, 'Stock actualizado.');
        } catch (\Exception $e) {
            $this->editingStock[$vID] = $this->originalStock[$vID]; // Restaurar valor
            // Si hay un error de DB, forzamos un mensaje de error genérico
            session()->flash('error_stock_db_' . $vID, 'Error de servidor al guardar el stock.');
        } 
    }

    /* ------------------------------------------
    // METODOS DEL MODAL
    // ------------------------------------------
    // Metodo apertura de modal */
    public function modaleditProduct(string $productoID,string $imgID, string $rutaImg)
    {
        if ($this->varianteSelec !== $imgID) {
            // Realizar una consulta para obtener la informacion del producto padre
            $productoConsulta = Product::select('id','name','description','price_retail','price_wholesale','min_pieces','status')
            ->with([
                'categories' => fn($q) => $q->select('categories.id', 'categories.name') 
            ])
            ->where('id', $productoID)
            ->first(); 
            // Limpiar el campo de imagen
            $this->reset('nuevaImagen');
            $this->nuevaImagen = "";
            $this->imagen_url_anterior = $rutaImg;
            // Limpiar arreglo de datos en el campo de categorias
            $this->categoriasSelect = []; 
            // Indicar la variantes que se va actualizar
            $this->varianteSelec = $imgID; 
            $this->campos_key = (int)$imgID;
            // Indicar el producto que se va actualizar
            $this->productoEditarID = $productoID;
            // Insertar valores en el formulario
            $this->tituloModal = "Editar $productoConsulta->name";
            $this->nombreProdutoA = $productoConsulta->name;
            $this->descripcionProducto = $productoConsulta->description;
            $this->precioMinProducto = (float)$productoConsulta->price_retail;
            $this->precioMayProducto = (float)$productoConsulta->price_wholesale;
            $this->minPiezasMay = (int)$productoConsulta->min_pieces;
            $this->estadoProducto = $productoConsulta->status;
            foreach ($productoConsulta->categories as $c) {
                $this->categoriasSelect[] = $c->id;
            }
        }
        $this->showModalEP = true; // Opcional, pero ayuda a Alpine a saber que hay data nueva
        $this->dispatch('open-modal'); // Emite el evento que JavaScript
    }
    // Método para cerrar el modal
    public function closeModal()
    {
        // Emite el evento que JavaScript está esperando para cerrar Flowbite
        $this->dispatch('close-modal');
    }
    // Método para actualizar el producto
    public function guardarProceso() 
    {
        // 1. Obtiene el array completo de reglas.
        $reglasValidacion = $this->reglas();
        // 2. Acceso al array de reglas del modal
        $modalReglas = $reglasValidacion['modal'];
        // 3.Llama a $this->validate() con las reglas y las propiedades publica. se ejecuta la validación:
        $validatedData = $this->validate($modalReglas);
        // 4. Validación adicional (si el campo precio mayorista o minimo de piezas tiene datos mayores a 0)
         $dataToValidate = [ // Prepara los datos a validar
            'minPiezasMay' => $this->minPiezasMay,
            'precioMayProducto' => $this->precioMayProducto,
        ];

        if (!empty($this->minPiezasMay) || !empty($this->precioMayProducto)) {
            $rules = [ // Definir las reglas específicas para esta validación
                'minPiezasMay' => 'required|integer|min:1', // Cambiamos a min:1
                'precioMayProducto' => 'required|numeric|min:1.00', // Cambiamos a min:1.00
            ];

            $messages = [ // Define mensajes personalizados si no quieres usar los mensajes de la regla principal
                'minPiezasMay.min' => 'El mínimo de piezas debe ser al menos 1.',
                'precioMayProducto.min' => 'El precio mayorista debe ser como mínimo $1.00.',
            ];

            // Crea y ejecuta el validador
            $validator = Validator::make($dataToValidate, $rules, $messages);

            // Lanza la excepción si falla, que Livewire manejará automáticamente
            $validator->validate();
        }
        try {   
            DB::transaction(function () {
                // 1.1. Recuperar la instancia del Producto y su relacion con variantes 
                $product = Product::find($this->productoEditarID);

                if (!$product) {
                    // Si el producto no se encuentra, se lanza una excepción 
                    // para forzar el rollback.
                    throw new \Exception('Error: Producto no encontrado para edición.');
                }

                // ----------------------------------------------------------------------
                // 2. MANEJO Y CREACIÓN DE CATEGORÍAS
                //    Procesa $this->categoriasSelect para obtener solo IDs.
                // ----------------------------------------------------------------------
                $categoriaIdsToSync = [];
                
                foreach ($this->categoriasSelect as $item) {
                    
                    // Si es un número válido (ID existente), lo añadimos directamente
                    if (is_numeric($item) && (int) $item > 0) {
                        $categoriaIdsToSync[] = (int) $item;
                    } else {
                        // Si es una cadena, es una nueva categoría ingresada por el usuario.
                        $categoriaNombre = trim($item);
                        
                        if (!empty($categoriaNombre)) {
                            // Usamos firstOrCreate para evitar duplicados y obtener el ID.
                            $categoria = Category::firstOrCreate(
                                ['name' => ucwords(strtolower($categoriaNombre))]
                            );
                            
                            $categoriaIdsToSync[] = $categoria->id;
                        }
                    }
                }

                // ----------------------------------------------------------------------
                // 3. ACTUALIZACIÓN DE IMAGEN
                // ----------------------------------------------------------------------
                if ($this->nuevaImagen && $this->nuevaImagen instanceof \Illuminate\Http\UploadedFile) {
                    
                    /** @var \Illuminate\Http\UploadedFile $uploadedFile */
                    $uploadedFile = $this->nuevaImagen;

                    // Guarda la nueva imagen en Storage
                    $path = $uploadedFile->storePublicly('uploads', 'public'); 

                    // Actualiza la referencia en la tabla 'Image' (DB change)
                    Image::where('id', $this->varianteSelec)
                        ->update([
                            'main_image_url' => $path,
                        ]);
                }
                
                // ----------------------------------------------------------------------
                // 4. ACTUALIZAR CAMPOS PRINCIPALES DEL PRODUCTO (DB change)
                // ----------------------------------------------------------------------
                $product->name = $this->nombreProdutoA;
                $product->description = $this->descripcionProducto;
                $product->price_retail = $this->precioMinProducto;
                $product->price_wholesale = $this->precioMayProducto <= 0 ? null : $this->precioMayProducto;
                $product->min_pieces = $this->minPiezasMay <= 0 ? null : $this->minPiezasMay;
                $product->status = $this->estadoProducto;
                $product->save(); // Guarda los campos principales

                // Si el nuevo estado del producto es 'inactive', actualiza todas sus variantes al mismo estado.
                if ($product->status === 'inactive') {
                    ProductVariant::where('product_id', $product->id)->update(['status' => 'inactive']);
                }
                // ----------------------------------------------------------------------
                // 5. SINCRONIZAR LA TABLA PIVOTE CON sync() (DB change)
                // ----------------------------------------------------------------------
                $product->categories()->sync($categoriaIdsToSync);
                $this->tituloModal =  "Editar $this->nombreProdutoA";
            }); // Fin de la transacción

            // ----------------------------------------------------------------------
            // 6. ELIMINACIÓN SEGURA: Solo se ejecuta si la transacción fue exitosa (COMMIT).
            // ----------------------------------------------------------------------
            if ($this->imagen_url_anterior && $this->nuevaImagen && $this->nuevaImagen instanceof \Illuminate\Http\UploadedFile) {
                // Eliminamos la imagen anterior SÓLO después de que la DB ha sido actualizada
                \Illuminate\Support\Facades\Storage::disk('public')->delete($this->imagen_url_anterior);
            }

            session()->flash('message_exitoso_modal', 'Producto actualizado exitosamente.');
            $this->cargarProductos();

        } catch (\Exception $e) {
            // El rollback ya ocurrió automáticamente.
            session()->flash('error_mensaje_modal', 'Error al guardar los cambios.');
            // \Illuminate\Support\Facades\Log::error("Error en guardarProceso: " . $e->getMessage());
        }
    }

    /* ------------------------------------------
    // LISTENERS Y NAVEGACIÓN
    // ------------------------------------------

    // Se activa cuando $productoMostrar cambia (select) */
    public function updatedProductoMostrar($value)
    {
        $this->productoMostrar = (int) $value; // Asegura que es un entero
        $this->currentPage = 1; // Reinicia a la primera página
        $this->cargarProductos(); // Recarga
    }
    // Se activa cuando $campoBusqueda cambia (input)
    public function updatedCampoBusqueda()
    {
        $this->currentPage = 1; // Reinicia a la primera página al buscar
        $this->cargarProductos(); // Recarga
    }
    // Método para cambiar de página (desde los botones de navegación, o campo de busqueda)
    public function mostrarPagina(int $pagina)
    {
        if ($pagina > 0 && $pagina <= $this->totalPages) {
            $this->currentPage = $pagina;
            $this->cargarProductos();
        }
    }
    // Método inicializarStockParaPáginaActual va almacenar el stock de los productos que se muestran en la tabla.
    protected function inicializarStockParaPáginaActual()
    {
        // Solo recorremos los productos cargados para la página actual
        foreach ($this->listaProductos as $producto) {
            foreach ($producto["image_groups"] as $grupo) {
                foreach ($grupo["variants_by_image"] as $valor) {
                    $variantId = $valor['id'];
                    $stock = $valor['stock'];
                    
                    // Solo inicializar si aún no están en el array, o forzar re-inicialización
                    // Esto es crucial para que el botón "Guardar" se oculte al recargar la página.
                    $this->editingStock[$variantId] = $stock;
                    $this->originalStock[$variantId] = $stock;
                }
            }
        }
    }
    // Método cargarProductos */
    protected function cargarProductos()
    {
        $this->listaProductos = collect();
        // 1. CONSULTA BASE Y FILTROS (usamos ProductVariant para identificar los ítems únicos a paginar)
        $variantsQuery = ProductVariant::query()
            ->whereNotNull('image_id'); // Excluimos variantes sin imagen, ya que son los ítems o claves para agrupar.

        // 2. Aplicar el filtro condicionalmente por SKU de variante o Nombre de producto
        $variantsQuery->when($this->campoBusqueda, function ($q, $buscar) {
            $likeSearch = '%' . $buscar . '%';  // Convertir el texto de búsqueda a patrón LIKE
            // Añadir condiciones WHERE para SKU o Nombre del producto
            $q->where(function ($query) use ($likeSearch) {
                // 2.1. Filtrar por sku de la variante (cláusula WHERE principal)
                $query->where('full_sku', 'LIKE', $likeSearch); // <-- Filtra la tabla de variants
                // 2.2 Filtrar por nombre del producto (usa orWhereHas)
                $query->orWhereHas('product', function ($qProduct) use ($likeSearch) {
                    // Filtrar por nombre del producto
                    $qProduct->where('name', 'LIKE', $likeSearch); // <-- Filtra la tabla de Product
                });
            });
        });

        // 3. PAGINACIÓN EFICIENTE DE GRUPOS (solo IDs). Obtenemos solo los ID de imagen que cumplen los filtros.
        $allUniqueImageIds = $variantsQuery->select('image_id') // Selecciona solo la columna image_id
                                            ->distinct() // Asegura que son únicos
                                            ->pluck('image_id') //Ejecutar la consulta construida previamente ($variantsQuery).
                                            ->toArray(); // Convertimos a array para manipulación en memoria

        // // Calculamos el total de grupos y el total de páginas en PHP (en memoria).
        $this->totalItems = count($allUniqueImageIds); // Total de grupos únicos
        $this->totalPages = ceil($this->totalItems / $this->productoMostrar); // Calcula el total de páginas

        // Ajustamos la página actual si el filtro la dejó fuera o si no hay resultados
        if ($this->currentPage > $this->totalPages && $this->totalPages > 0) { // Si la página actual excede el total
            $this->currentPage = 1;
        } elseif ($this->totalPages === 0) { // No hay resultados
            $this->listaProductos = collect([]);
            return; // No hay resultados, terminamos aquí
        }

        // Aplicamos Paginación Manual (SLICE) a la lista de IDs de imagen (lista pequeña en memoria).
        $offset = ($this->currentPage - 1) * $this->productoMostrar;
        $paginatedImageIds = array_slice($allUniqueImageIds, $offset, $this->productoMostrar);

        // 4. CARGA DE DATOS COMPLETOS (SOLO PARA LA PÁGINA ACTUAL)
        // Usamos whereIn para cargar SÓLO las variantes que tienen los IDs de imagen paginados.
        $allVariantsForPage = ProductVariant::whereIn('image_id', $paginatedImageIds)
            // Seleccionar PK ('id'), FKs ('product_id', 'image_id') y campos de datos
            ->select('id', 'full_sku', 'stock', 'image_id', 'status', 'product_id') 
            ->with([
                // Cargar el producto padre (Necesario para nombre, precios y categorías)
                'product' => fn($q) => $q->select('id', 'name'),
                // Cargar atributos anidados (BelongsToMany -> BelongsTo)
                'attributeValues' => fn($q) => $q
                    // Select de la tabla AttributeValue, incluyendo 'attribute_id' (FK)
                    ->select('attribute_values.id', 'attribute_values.attribute_id', 'attribute_values.value')
                    ->with([
                        // Relación 'attribute' dentro del modelo AttributeValue
                        'attribute' => fn($q) => $q->select('id', 'name') 
                    ]),
                
                // Carga la imagen (FIX N+1: Asegurar que se selecciona el campo de la FK 'id' y el campo de URL)
                'image' => fn($q) => $q->select('id','main_image_url')
            ])
            ->get();

        // 5. RE-AGRUPAR por variantes por imagen (image_id)
        $paginatedImageGroups = $allVariantsForPage
            ->groupBy('image_id') 
            ->map(function ($variantsGroup) {
                $firstVariant = $variantsGroup->first();
                $product = $firstVariant->product;
                
                // Mantenemos la estructura de salida que requiere la vista Blade
                return [
                    'product_id' => $product->id, // ID del producto padre
                    'product_name' => $product->name, // Nombre del producto padre
                    'image' => $firstVariant->image->toArray(),  // Objeto Image completo y  Convertir Image a array
                    'variants_by_image' => $variantsGroup->toArray(), // Las variantes que comparten esta imagen
                ];
            })
            ->values(); // El resultado sigue siendo una Collection de arrays

        // 5. AGRUPAMIENTO FINAL POR PRODUCTO (product_id)
        $finalProductGroups = $paginatedImageGroups
            ->groupBy('product_id')
            ->map(function ($imageGroups) {
                // $imageGroups ahora contiene los grupos de imagen (punto 4) que pertenecen al mismo producto.
                
                $firstImageGroup = $imageGroups->first();

                // Extraer todas las variantes de todos los grupos de imagen de este producto
                $allVariants = $imageGroups->flatMap(fn($group) => $group['variants_by_image']);

                // Estructura final: Un grupo por producto.
                return [
                    'product_id' => $firstImageGroup['product_id'],
                    'product_name' => $firstImageGroup['product_name'],
                    'image_groups' => $imageGroups->toArray(), // Los grupos de imagen originales de este producto
                    'all_variants' => $allVariants->toArray(), // Opcional: todas las variantes del producto en una sola lista (si es necesario)
                ];
            })
            ->values(); // El resultado sigue siendo una Collection de arrays, indexada numéricamente.

        // 5. Asignamos los resultados (La asignación final que sí convierte la Collection de arrays a array simple)
        $this->listaProductos = $finalProductGroups->toArray();

        // 6. INICIALIZACIÓN DE STOCK DESPUÉS DE CARGAR PRODUCTOS
        // Inicializar los stocks para la página actual
        $this->inicializarStockParaPáginaActual();
    }
    // Método cargarCategeorias es auxiliar para cargar los valores que va tener los productos de la base de datos
    protected function cargarCategeorias()
    {
        // Consultar todas las categorias existente en la bd, optimizando la carga.
        $categorias = Category::select('id', 'name')->get(); 
        $this->listaCategorias = $categorias->map(function ($c) {
            return [
                'value' => $c->id, 
                'text' => $c->name
            ];
        })->toArray(); // Convertimos el resultado mapeado a un array de PHP
    }
    // Método cargarAtributos es auxiliar para cargar los valores que va tener los productos de la base de datos
    protected function cargarAtributos()
    {
        // Consultar todos los atributos y sus valores relacionados, optimizando la carga.
        $this->listaAtributos = Attribute::select('id', 'name')
            ->with(['attributeValues' => fn($q) => $q->select('id', 'attribute_id', 'value')])
            ->get(); 
    }
    // Metodo mount Se ejecuta una sola vez al cargar el componente
    public function mount()
    {
        $this->cargarAtributos(); // Carga todos los atributos y sus valores para llenar los selectores en la vista.
        $this->cargarCategeorias(); // Carga los productos y prepara la paginación.
        $this->cargarProductos(); // Carga los productos y prepara la paginación.
    }
    // Renderiza la vista Livewire
    public function render()
    {
        return view('livewire.lista-producto');
    }
}