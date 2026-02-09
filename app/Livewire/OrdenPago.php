<?php

namespace App\Livewire;

use Livewire\Component;        // IMPORTAR la clase base 'Component' de Livewire, de la que debe heredar todo componente.
use Livewire\WithFileUploads;  // IMPORTAR Trait de Livewire que habilita la subida de archivos seguros dentro del componente.
use App\Mail\PagoParcial;      // CORREO.
use App\Mail\PagoFinal;        // CORREO.
use App\Mail\ReportePago;      // CORREO.
use App\Models\User;           // MODELO. 
use App\Models\Client;         // MODELO. 
use App\Models\Product;        // MODELO. 
use App\Models\ProductVariant; // MODELO.
use App\Models\Order;          // MODELO.
use App\Models\OrderItem;      // MODELO.
use App\Models\PaymentReport;  // MODELO.
use App\Models\configurations; // MODELO.
use App\Models\BankAccount;    // MODELO.
use Illuminate\Support\Arr;     // IMPORTAR Arr para manipulación de arrays.
use Illuminate\Support\Str;     // IMPORTAR para generar tokens
use Illuminate\Support\Facades\Session;     // IMPORTAR el 'Facade' de Session de Laravel, usado para acceder y manipular los datos de la sesión.
use Illuminate\Support\Facades\DB;          // IMPORTAR el 'Facade' de la Base de Datos (DB), que permite ejecutar consultas directas y operaciones transacciones con la base de datos.
use Illuminate\Support\Facades\Storage;     // IMPORTAR el 'Facade' de Storage de Laravel, esencial para interactuar con los sistemas de archivos (discos), como el almacenamiento local o servicios en la nube (S3).
use Illuminate\Support\Facades\Validator;   // IMPORTAR el 'Facade' de Validator de Laravel, que proporciona una forma sencilla de validar datos entrantes contra un conjunto definido de reglas.
use Illuminate\Support\Facades\Mail;    // IMPORTAR el 'Facade' de Mail de Laravel, utilizado para enviar correos electrónicos desde la aplicación.
use Illuminate\Validation\Rule;         // IMPORTAR la clase 'Rule' para poder usarla en las reglas de validación.

class OrdenPago extends Component
{
    use WithFileUploads; // Habilita la carga de archivos en Livewire
    // Propiedades importantes para la consultas, y arrays que van almacenar los registros solicitados:
    public $tasaDolar = 0, $pagoMovilMetodo = [], $zelleMetodo = [];
    // Propiedades consultar clientes (). Va almacenar la cedula a consultar del cliente y Va indicar si se encontraron datos del cliente.
    public $buscarCedula = "", $datosEncontrados = false;
    // Propiedades para los datos del banco y Zelle (estos valores viene de la configuración o base de datos)
    public $banco,$cedulaBanco, $telefonoBanco, $titularBanco; // cuenta de Pago movil
    public $titularBancoZelle, $emailZelle; // cuenta de zelle
    // Propiedades del Formulario orden (datos del cliente)
    public $info_cliente, $customer_cedula , $customer_name , $client_email , $customer_phone , $customer_adress;
    public $selectedMethod = 'pago-movil'; // Establecer 'pago-movil' como predeterminado
    public $payment_intention = '100'; // '50' o '100'
    
    public $payment_method = 'pago-movil'; // Valor predeterminado
    public $reference_number;
    public $comprobante; // Para el archivo subido por el usuario
    public $securityAnswer;
    public $numero1 = 1;
    public $numero2 = 2;
    public $termsAccepted = false;

    // --- otras propiedades ---
    public $cartData = []; // Propiedad auxiliar que almacena los datos brutos del carrito (por ID)
    public $paid_amount = 0; 
    public $totalPurchase = 0;
    public $varianteSinStock = [];  
    public $hasErrors = false;  

    // REGLAS DE VALIDACIÓN DINÁMICA Y MENSAJES PERSONALIZADOS
    protected function reglas(){
        $reglas = [
            // Asegúrate de validar el comprobante y otros datos
            'customer_cedula' => 'required|min:7|max:9|regex:/^\d{7,9}$/i',
            'customer_name' => ['required', 'string', 'max:100', 'regex:/^[\pL\s\-]+$/u'], // 'regex:/^[\pL\s\-]+$/u' aceptar solo letras, espacios y guiones
            'client_email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'email:rfc,dns','unique:clients,email'],
            'customer_phone' => 'required|digits_between:10,12',
            'customer_adress' => 'required|string|min:5|max:150',
            'payment_intention' => 'required|in:50,100',
            'reference_number' => 'required|string|max:100',
            'comprobante' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048', // Subida de hasta 2MB, añadido 'webp'
            'selectedMethod' => 'required|in:pago-movil,zelle',
            'securityAnswer' => 'required|integer',
            'termsAccepted' => 'accepted',
            'payment_method' => ['required', Rule::in(['pago-movil', 'zelle'])],
        ];

        // Se devuelve el array completo para ser usado en el método $this->validate()
        return $reglas;
    }
    protected $messages = [
        // 1. Mensajes Generales (para todas las reglas de un campo)
        'customer_cedula.required' => 'El campo de cédula es obligatorio.',
        'customer_cedula.min' => 'La cédula es demasiado corta. Debe tener al menos 7 dígitos.',
        'customer_cedula.max' => 'La cédula es demasiado larga. El máximo es de 9 dígitos.',
        'customer_cedula.regex' => 'El formato de cédula no es válido. Debe ser 12345678.',

        'customer_name.required' => 'Por favor, introduce tu Nombre Completo.',
        'customer_name.string'   => 'El nombre debe ser texto válido.',
        'customer_name.min'      => 'El nombre debe tener al menos :min caracteres.',
        'customer_name.max'      => 'El nombre no debe exceder los :max caracteres.',
        'customer_name.regex'    => 'El nombre solo puede contener letras, espacios y guiones.',

        'client_email.required'  => 'El campo Correo Electrónico es obligatorio.',
        'client_email.email'     => 'Introduce una dirección de correo electrónico válida (ej: ejemplo@dominio.com).',
        'client_email.max'       => 'El correo electrónico es demasiado largo.',
        'client_email.unique'    => 'Este correo electrónico ya está registrado.',

        'customer_phone.required' => 'El campo Número de Teléfono es obligatorio.',
        'customer_phone.digits_between' => 'El número de teléfono debe tener entre :min y :max dígitos (sin espacios ni guiones).',

        'customer_adress.required' => 'La dirección de la habitación es obligatoria.',
        'customer_adress.string' => 'La dirección debe ser texto.',
        'customer_adress.min' => 'La dirección debe tener al menos :min caracteres.',
        'customer_adress.max' => 'La dirección no debe superar los :max caracteres.',

        'payment_intention.required' => 'Debes seleccionar el Porcentaje de Pago (50% o 100%).',
        'payment_intention.in'       => 'La intención de pago seleccionada no es válida.',

        'reference_number.required' => 'El Número de Referencia/Operación es obligatorio.',
        'reference_number.max'      => 'El número de referencia es demasiado largo.',

        // 2. Mensajes para la Subida del Comprobante (Archivo)
        'comprobante.required' => 'Debes adjuntar el Comprobante de Pago (foto o captura).',
        'comprobante.image'    => 'El archivo debe ser una imagen válida.',
        'comprobante.mimes'    => 'El formato de la imagen debe ser JPG, PNG, WEBP o JPEG.',
        'comprobante.max'      => 'El comprobante no debe superar los 2MB de tamaño.',

        // 3. Mensajes para Métodos de Pago
        'payment_method.required' => 'Debes seleccionar un Método de Pago.',
        'payment_method.in'       => 'El método de pago seleccionado no es válido.',

        'selectedMethod.required' => 'Por favor, seleccione un método de pago para continuar.',
        'selectedMethod.in' => 'El método de pago seleccionado no es válido. Por favor, elija entre Pago Móvil o Zelle.',
        'securityAnswer.required' => 'Por favor, ingrese la respuesta de seguridad.',
        'securityAnswer.integer' => 'La respuesta de seguridad debe ser un número entero.',
        'termsAccepted.accepted' => 'Debe aceptar los términos y condiciones para completar la operación.',
    ];
    // Auxiliar Método para cambiar el método de pago
    public function selectMethod($method)
    {
        $this->selectedMethod = $method;
        $this->payment_method = $method;
    }
    // Auxiliar metodo: Permite enmascarar datos sensibles
    public function enmascaramiento(string $p) {
        $palabras = explode(' ', $p);
        $p_enmascaradas = [];

        foreach ($palabras as $palabra) {   
            $longitud = strlen($palabra);
            $letras_visibles = $longitud <= 3 ? 1 : 3;
            // Calcular cuántos caracteres deben ser reemplazados por asteriscos
            $asteriscos_a_poner = $longitud - $letras_visibles;
            // Asegurarse de que no haya un número negativo de asteriscos
            $asteriscos_a_poner = max(0, $asteriscos_a_poner);
            // Obtener la parte de la palabra que se mantendrá visible
            $parte_visible = substr($palabra, $asteriscos_a_poner);
            // Crear la parte enmascarada con asteriscos
            $parte_enmascarada = str_repeat('*', $asteriscos_a_poner);
            // Concatenar y añadir al nuevo array
            $p_enmascaradas[] = $parte_enmascarada . $parte_visible;
        }
            
        return implode(' ', $p_enmascaradas);
    }
    // Auxiliar Metodo: Permite consultar informacion del cliente en base a su cedula identidad
    public function consultarCedula(){
        // Limpiar datos previos
        $this->info_cliente = null;
        $this->datosEncontrados = false; 
        $this->customer_cedula = !empty($this->buscarCedula) ?  $this->buscarCedula : "";
        $this->customer_name = "";
        $this->client_email =  "";
        $this->customer_phone =  "";
        $this->customer_adress =  "";
        // Limpia los errores de validación anteriores
        $this->resetValidation();
        $dataToValidate = [ // Prepara los datos a validar
            'buscarCedula' => $this->buscarCedula,
        ];
        $rules = [ // Definir las reglas específicas para esta validación
            'buscarCedula' => 'required|min:7|max:9|regex:/^\d{7,9}$/i',
        ];
        $messages = [ 
            // Mensajes para la cédula
            'buscarCedula.required' => 'El campo de cédula es obligatorio.',
            'buscarCedula.min' => 'La cédula es demasiado corta. Debe tener al menos 7 dígitos.',
            'buscarCedula.max' => 'La cédula es demasiado larga. El máximo es de 9 dígitos.',
            'buscarCedula.regex' => 'El formato de cédula no es válido. Debe ser 12345678.',
        ];
        // Crea y ejecuta el validador
        $validator = Validator::make($dataToValidate, $rules, $messages);
        // Lanza la excepción si falla, que Livewire manejará automáticamente
        $validator->validate();
        // Consultar informacion del cliente:
        $client = Client::find($this->buscarCedula); // Buscar por cédula
        // Verificar si el cliente existe
        if (!empty($client)) {
            //   --- Lógica de Enmascaramiento ---
            // 1. Enmascarar el nombre (dejando solo los 4 últimos dígitos). str_pad para reemplazar los primeros caracteres por asteriscos.
            $nombres = $this->enmascaramiento((string)$client->names);
            $telefono = $this->enmascaramiento((string)$client->phone);
            $direccion =  $this->enmascaramiento((string)$client->address);
            // 2. Enmascarar el Correo Electrónico (ej. dejando la primera letra y el dominio) usuario@dominio.com -> u*******@dominio.com
            $email = $client->email;
            $emailMostrar = "";
            if (strpos($email, '@') !== false) {
                list($user, $domain) = explode('@', $email, 2);
                $maskedUser = substr($user, 0, 1) . str_repeat('*', max(0, strlen($user) - 1));
                $emailMostrar = $maskedUser . '@' . $domain;
            } else {
                $emailMostrar = $email;
            }
            $this->customer_cedula = $client->cedula;
            $this->customer_name = $nombres;
            $this->client_email =  $emailMostrar;
            $this->customer_phone =  $telefono;
            $this->customer_adress =  $direccion;
            $this->datosEncontrados = true; 
            $this->info_cliente = $client;
            // Establecer Mensaje Flash de Éxito
            Session::flash('flash_message_success', 'Cliente encontrado. Datos cargados.');
        }else{
            // Manejar caso donde el cliente no existe
            Session::flash('flash_message_error', 'Cliente no encontrado.');
        }
    }

    // Auxiliar Metodo: permite verificar si hay stock suficiente para los productos en el carrito.
    public function validarStock()
    {
        $this->cartData = Session::get('cart', []); // Capturar los item del carrito.
        $requiredVariantIds = array_keys($this->cartData);
        $this->varianteSinStock = [];  // Almacena errores de stock para la vista
        $this->hasErrors = false;

        if (empty($requiredVariantIds)) {
            session()->flash('error_form_orden', 'El carrito está vacío. Agregue productos para continuar.');
            return;
        }

        // Obtener stock actual de la base de datos
        $dbStock = ProductVariant::whereIn('id', $requiredVariantIds) 
        ->pluck('stock','id') 
        ->toArray();

        // Comparar y validar si existe stock suficiente
        foreach ($this->cartData as $variantId => $item) {
            $cantidadSeleccionada = (int) $item['qty'];
            $cantidadStock = $dbStock[$variantId] ?? 0; 
            if ($cantidadSeleccionada > $cantidadStock) {
                $this->hasErrors = true;
                $this->varianteSinStock[] = "- " . $item['name_variante'] . ". Solicitado: " . $cantidadSeleccionada . "  |  Disponible: " . $cantidadStock;
            }
        }
        if ($this->hasErrors) {
            session()->flash('error_form_orden', '⚠️ Hay productos con cantidades superiores al stock disponible. Por favor, ajuste su carrito.');
        }
    }
    // Método principal que maneja la validación, la transacción de BD y la finalización del pedido.
    public function confirmarPedido(){
        // Va almacenar la fecha limite de pago
        $fechaVencimiento = null;
        // Captura la cédula del cliente para uso posterior
        $cedulaCliente = $this->customer_cedula; 
        // 1. Obtiene el array completo de reglas.
        $reglasValidacion = $this->reglas();
        // 2. Verificar si se encontraron datos del cliente
        if ($this->info_cliente !== null && $this->datosEncontrados) { // Entra SI Se encontraron datos del cliente
            // Excluye algunos campos de validacion para este caso
            $reglasTemporales = Arr::except($reglasValidacion, [
                'customer_name',
                'client_email',
                'customer_phone',
                'customer_adress',
            ]);
            // Llama a $this->validate() con las reglas y las propiedades publica. se ejecuta la validación modificada:
            $this->validate($reglasTemporales);
        }else{
            // Llama a $this->validate() con las reglas y las propiedades publica. se ejecuta la validación:
            $this->validate($reglasValidacion);
        }
        // 3. Validar la verificación de seguridad
        if ((int)$this->securityAnswer) {
            $respuestaCorrecta = ($this->numero1 + $this->numero2) - $this->securityAnswer;
            if ($respuestaCorrecta != 0) {
                session()->flash('error', 'Respuesta de seguridad incorrecta.');
                $this->addError('securityAnswer', 'Respuesta incorrecta.');
                return;
            }
        }
        // 4. Validar stock de las variantes:
        $this->validarStock();
        if ($this->hasErrors) {
            return; 
        }
        // 5. Volver a calcular el total de la compra (por si Livewire no lo hizo en tiempo real)
        $resultado = $this->montoPagar();
        // 6. Verificar si la cedula ingresada por el usuario ya existe en la base de datos:
        $client = Client::find($this->customer_cedula); // Buscar por cédula
        // 7. Crear Cliente si no existe en la base de datos.
        if ($client === null) {
            try {
                // Crear el nuevo cliente en la base de datos
                $client = Client::create(attributes: [
                    'cedula' => $this->customer_cedula,
                    'names' => $this->customer_name,
                    'email' => $this->client_email,
                    'phone' => $this->customer_phone,
                    'address' => $this->customer_adress ?? 'No Especificada', // Usar la dirección ingresada
                ]);
                $cedulaCliente = $client->cedula; // Actualiza la cédula del cliente para uso posterior
            } catch (\Exception $e) {
                // Manejo de error si la creación del cliente falla (ej. duplicidad de email/ID)
                session()->flash('flash_message_error', 'Error al procesar el pago. Por favor, inténtelo más tarde');
                return; 
            }
        }else{
            $cedulaCliente = $client->cedula; // Actualiza la cédula del cliente para uso posterior
        }
        // 8. PREPARACIÓN DE DATOS FINALES
        $order = null;
        $proofPath = null;
        $remainingAmount = $this->totalPurchase - $this->paid_amount;
        $token = ($this->payment_intention != "100") ? Str::random(40) : null;

        // Usamos DB::transaction() para envolver todas las operaciones de DB.  Si ocurre un error, se hará rollback automáticamente.
        try {
            // A. Subir el Comprobante (dentro del try para manejar el borrado si falla el commit)
            $proofPath = $this->comprobante->store('comprobantes', 'local');
            // B. Iniciar la transacción
            DB::transaction(function () use ($proofPath, $cedulaCliente, $remainingAmount, $token, $resultado, $order, $client, &$fechaVencimiento) {
                // // B. CREAR LA ORDEN (CABECERA)
                $order = Order::create([
                    // Datos del Cliente
                    'client_cedula' => $cedulaCliente,
                    // Datos de Compra (calculados en montoPagar)
                    'total_purchase' => $this->totalPurchase,
                    // Datos de Pago Inicial
                    'total_paid' => $this->paid_amount,
                    'remaining_amount' => $remainingAmount,
                    'dollar_rate' => $this->tasaDolar,
                    'order_status' => 'Por Verificar',
                    'deposit_status' => ($this->payment_intention == "100") ? 'Por Verificar' : 'Pendiente 50%',
                    'token_segundo_pago' => $token, // Enlace seguro para el pago final
                    'payment_deadline' => ($token) ? now()->addDays(5) : null,
                ]);
                $orderItems = [];
                $variantIds = array_keys($this->cartData); // IDs de las variantes en el carrito
                // C. BLOQUEO Y DESCUENTO DE STOCK + CREACIÓN DE ORDER_ITEMS
                // Bloqueamos las filas para concurrencia segura
                $variants = ProductVariant::whereIn('id', $variantIds)->lockForUpdate()->get()->keyBy('id');
                // Descuento de stock y preparación de OrderItems
                foreach ($resultado as $producto) {
                    foreach ($producto['variantes'] as $item) {
                        $variantDB = $variants->get($item["id_variante"]); // Obtener la variante bloqueada
                        // Re-validación final antes de descontar 
                        if (!$variantDB || $variantDB->stock < (int) $item['qty']) {
                            throw new \Exception("Error de concurrencia: stock insuficiente para {$item['name_variante']}.");
                        }
                        // Descuento de Stock
                        $variantDB->decrement('stock', $item['qty']); 
                        // Preparar datos para OrderItems
                        $orderItems[] = [
                            'order_id' => $order->id,
                            'product_variant_id' => $item['id_variante'],
                            'quantity' => $item['qty'],
                            'applied_unit_price' => $producto['precio_aplicado'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
                // Inserción masiva de OrderItems   
                OrderItem::insert($orderItems);
    
                // D. REGISTRAR EL REPORTE DE PAGO
                PaymentReport::create([
                    'order_id' => $order->id,
                    'amount' => $this->paid_amount,
                    'dollar_rate' => $this->tasaDolar,
                    'method' => $this->payment_method,
                    'reference' => $this->reference_number,
                    'reference_img' => $proofPath, // Ruta del archivo subido
                    'status' => 'Pendiente', 
                ]);

                // Consultar el usuario administrador para enviarle el reporte de pago
                $usuarioAdmin = User::select('id','email')
                ->where(function ($query) {
                    $query->where('name', 'Administrador')
                    ->orWhere('id', 1);
                })
                ->first(); // Obtiene solo el primer registro (objeto), no una colección

                // ACCIONES POST-TRANSACCIÓN, eliminar carrito de compra
                Session::forget('cart');
                //  Enviar correos electrónicos según el tipo de pago
                if ($token && $this->payment_intention == '50' && $order !== null) {
                    $fechaVencimiento = $order->payment_deadline->format('dmY');
                    // Al usar send() con un Mailable que "implements ShouldQueue",  el trabajo se despacha a la tabla 'jobs' instantáneamente.
                    Mail::to($client->email)->send(new PagoParcial($order, $token, $client->names));
                } else if ($this->payment_intention == '100' && $order !== null) {
                    Mail::to($client->email)->send(new PagoFinal($order));
                }
                // Notificar al administrador
                Mail::to($usuarioAdmin->email)->send(new ReportePago($order));
            });
            // Redireccionar a una página de éxito
            return redirect()->route('msmPago', ['msm' => $fechaVencimiento ?? "nulls"]); 

        } catch (\Exception $e) {
            // Si la transacción falló, el ROLLBACK ya se ejecutó.
            if (isset($proofPath) && $proofPath) { // Borrar el archivo si se subió pero la BD falló
                Storage::disk('local')->delete($proofPath); 
            }
            session()->flash('error_form_orden', 'Error al procesar el pago. Por favor, revise el stock o inténtelo más tarde');
        }
    }

    // Metodo permite calcular el precio total que debe pagar el cliente segun vaya a pagar en dolares.
    public function updatedPaymentIntention()
    {
        $this->paid_amount = $this->totalPurchase * (float)($this->payment_intention / 100);
    }
    // Metodo permite calcular el precio total de la compra.
    public function montoPagar()
    {   
        $grupoV = []; // Va almacenar las variantes por grupo
        $consultarP = collect(); // Inicializar colección vacía
        $montoTotalAcumulado = 0.00; // Limpiar campo total a pagar.
        $this->cartData = Session::get('cart', []); // Capturar los item del carrito.

        if (!empty($this->cartData)) {
            // 1. Agrupar variantes segun su producto principal
            foreach ($this->cartData as $item) {
                $productId = $item['product_id']; // ID del producto padre
                $cantidad = (int) $item['qty']; // Cantidad seleccionada de una variante

                if (!isset($grupoV[$productId])) { // Si no existe el producto en el grupo, inicializar 
                    $grupoV[$productId] = [
                        'name_product' => $item['name_product'],
                        'Cantidad Total' => 0,
                        'variantes' => [],
                    ];
                }
                $grupoV[$productId]['Cantidad Total'] += $cantidad;
                $grupoV[$productId]['variantes'][] = [
                    'id_variante' => $item['variant_id'],
                    'name_variante' => $item['name_variante'],
                    'qty' => $cantidad,
                ];
            }
            // 2. Obtener las claves de los productos agrupados para realizar en la consulta y asi obtener precios y piezas minima aztualizada
            $clavesProductos = array_keys($grupoV);
            
            // 2.1 Consulta eficiente:
            $consultarP = Product::whereIn('id', $clavesProductos)
                        ->select(['id', 'price_retail', 'price_wholesale', 'min_pieces']) // Solo estos campos
                        ->get();

            // 3. Aplicar Precios y Cálculos Finales (Segunda pasada)
            foreach ($grupoV as $productId => $producto) {
                $pEncontrado = $consultarP->find($productId); // Obtener el producto correspondiente, find no realiza otra consulta sino que busca en la colección ya obtenida
                $cantidadTotal = $producto['Cantidad Total']; // Obtener la cantidad total de todas las variantes de un producto.
                $precioUnitario = $pEncontrado['price_retail']; // Obtener el precio al detal. Para usarlo Como valor por defecto.
                $grupoV[$productId]['tarifa_aplicada'] = 'Detal'; // Valor por defecto
                $grupoV[$productId]['precio_aplicado'] = $precioUnitario; // Valor por defecto

                if ($pEncontrado["price_wholesale"] != null && $pEncontrado["min_pieces"] != null) { // Entra si el producto tiene precio mayorista y tiene definido piezas minima
                    if ($cantidadTotal >= $pEncontrado["min_pieces"]) { // Si la cantidad total es mayor o igual a la piezas minima
                        $precioUnitario = $pEncontrado['price_wholesale']; // Precio al mayor
                        $grupoV[$productId]['tarifa_aplicada'] = 'Mayor';
                        $grupoV[$productId]['precio_aplicado'] = $precioUnitario;
                    }
                }
                // Calcular el Subtotal del Producto Principal
                $subtotalProducto = $cantidadTotal * $precioUnitario;
                // Acumular al Monto Total Acumulado (ahora es el Subtotal General de Productos)
                $montoTotalAcumulado += $subtotalProducto;
            }
        }
        // Calcular el total de la compra:
        $this->totalPurchase = $montoTotalAcumulado;
        // Calcular el total de la compra:
        $this->updatedPaymentIntention();
        //  Devolver los resultados para uso interno
        return $grupoV;
    }
    // Metodo para consultar el metodo de pago
    public function consultarBanco($metodo)
    {
        $consultarBanco = BankAccount::where('type', $metodo)
        ->select(['type', 'bank_name', 'holder_name', 'holder_id', 'phone_number', 'email', 'account_number', 'is_active'])
        ->first()
        ?->toArray();
        
        if (!blank($consultarBanco)) {
            if ($metodo == 'pago_movil') {
                $this->banco = $consultarBanco["bank_name"];
                $this->cedulaBanco = $consultarBanco["holder_id"];
                $this->telefonoBanco = $consultarBanco["phone_number"];
                $this->titularBanco = $consultarBanco["holder_name"];
            } elseif ($metodo == 'zelle') {
                $this->emailZelle = $consultarBanco["email"];
                $this->titularBancoZelle = $consultarBanco["holder_name"];
            }
        }

        return $consultarBanco;
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
        // Generar números aleatorios para la verificación de seguridad
        $this->numero1 = random_int(1, 9);
        $this->numero2 = random_int(1, 9);
        // Obtener tasa del dolar
        $this->tasaDolar = $this->consultarTasa();
        // Obtener metodos de pagos
        $this->pagoMovilMetodo = $this->consultarBanco("pago_movil");
        $this->zelleMetodo =  $this->consultarBanco("zelle");

        if (blank($this->pagoMovilMetodo) && blank($this->zelleMetodo)) {
            session()->flash('errorTasaOPago', '⚠️ No hay métodos de pago disponibles en este momento. Por favor, contacte al administrador o intente más tarde.');
        }else if (!$this->tasaDolar) {
            session()->flash('errorTasaOPago', '⚠️ No se ha podido determinar la valoración de los productos debido a que no hay una tasa de cambio vigente. Por favor, intente más tarde.');
        }else{
            $this->montoPagar();
        }
    }
    // Renderiza la vista Livewire
    public function render()
    {
        return view('livewire.orden-pago');
    }
}
