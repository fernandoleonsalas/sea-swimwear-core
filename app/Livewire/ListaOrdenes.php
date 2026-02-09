<?php

namespace App\Livewire;

use Livewire\Component;       // Importa la clase base 'Component' de Livewire, de la que debe heredar todo componente.
use Livewire\WithFileUploads; // Importa Trait de Livewire que habilita la subida de archivos seguros dentro del componente.
use Illuminate\Support\Str;   // Importar para generar tokens
use Illuminate\Support\Facades\DB;      // Importa el 'Facade' de la Base de Datos (DB), que permite ejecutar consultas directas y operaciones transacciones con la base de datos.
use Illuminate\Support\Facades\Session; // Importa el 'Facade' de Session de Laravel, usado para acceder y manipular los datos de la sesión.
use Illuminate\Support\Facades\Mail;    // Importa el 'Facade' de Mail de Laravel, utilizado para enviar correos electrónicos desde la aplicación.
use App\Models\User;           //Importa del Modelo de la User
use App\Models\Order;         // Importa del Modelo de la Orden
use App\Models\PaymentReport; // Importa del Modelo del Reporte de Pago
use App\Mail\PagoRechazado;   // Importa la clase Mailable para el correo
use App\Mail\PagoVerificado;  // Importa la clase Mailable para el correo
use App\Mail\AbonoProcesado;  // Importa la clase Mailable para el correo
use App\Mail\ProductoPorEntregar;  // Importa la clase Mailable para el correo
use App\Mail\ProductoEntregado;    // Importa la clase Mailable para el correo
use App\Mail\ReporteAdminPR;  // Importa la clase Mailable para el correo
use App\Mail\ReporteAdminPV;  // Importa la clase Mailable para el correo
use App\Mail\ReporteAdminPE;  // Importa la clase Mailable para el correo
use App\Mail\ReporteAdminPPE; // Importa la clase Mailable para el correo
use Carbon\Carbon;            // Importa Carbon para la manipulación de fechas
use DateTime;                 // Importar DateTime para el manejo de fechas

class ListaOrdenes extends Component
{
    // Habilitar la carga de archivos en Livewire
    use WithFileUploads; 
    // Propiedades para las Operaciones de estadistica
    public $totalOrdenes = 0, $ordenesEstados = [];
    // Propiedades para los filtros de fecha
    public $filterYear = '', $filterMonth = '', $filterDayStart = '1', $filterDayEnd = '';
    // Propiedades para los filtros de método de pago y estado
    public $metodoPagoFiltro = '';
    // Propiedad para los almacenar los datos de admin
    public $userAdmin = [];
    public $estadoFiltro = ''; // Estado por el que se filtra: 'Verificado', 'Rechazado', 'Pendiente'
    /* ------------------------------------------
    // Propiedades pública de paginación manual
    // ------------------------------------------ */
    public $ordenMostrar = 10; // Cantidad de GRUPOS (ordenes) a mostrar por página
    public $currentPage = 1; // Página actual.
    public $totalItems = 0; // Total de GRUPOS de ordenes únicos encontrados.
    public $totalPages = 1; // Total de páginas calculadas.
    public $campoBusqueda = ""; // Campo de búsqueda para filtrar numero de referencia o monto.

    // ------------------------------------------
    // Propiedades pública para el tabla
    // ------------------------------------------
    public $listaOrdenes; // Almacenará los GRUPOS de variantes paginados (el output final).

    // Propiedad pública para la fecha
    private array $arrMes = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"]; // Arreglo que contiene los meses del año (0 = Enero, 11 = Diciembre):
    private array $arrDia = ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"];  // Arreglo que contiene los días de la semana (0 = Domingo, 6 = Sábado):

    // Auxiliar: Método para filtrar las órdenes por fecha (Año, Mes, Rango de Días)
    public function filtrarOrdensPorFecha($year, $month, $dayStart, $dayEnd)
    {
        // 1. Limpiar cualquier mensaje de error anterior de filtro
        Session::forget('errorFiltro');
        
        // A. Validar si hay un año o mes invalido.
        if ((empty($year) || empty($month)) || strlen((string)$year) != 4) {
            Session::flash('errorFiltro', 'Por favor, ingrese un mes y un año válidos.');
            return; // Detener la ejecución si hay un error
        }

        // B. Validar el Rango de Días (si se ingresa alguno).
        if (empty($dayStart) || empty($dayEnd)) {
            Session::flash('errorFiltro', 'Para un filtro por rango, debe ingresar tanto el Día de inicio como el Día de fin.');
            return; // Detener la ejecución si hay un error
        }

        // C. Validar que el día de inicio o fin no exceda los 31 días de un mes.
        if ($dayStart > 31 || $dayEnd > 31) {
            Session::flash('errorFiltro', 'El día de inicio o de fin no puede exceder el día 31.');
            return; // Detener la ejecución
        }

        // D. Validar que el día de inicio o fin no sea cero o un número negativo.
        if ($dayStart <= 0 || $dayEnd <= 0) {
            // El texto mejorado:
            Session::flash('errorFiltro', 'El día de inicio o de fin no puede ser cero o un número negativo.');
            return; // Detener la ejecución
        }

        // E. Validar que el Día de inicio no sea mayor al Día de fin.
        if ((int)$dayStart > (int)$dayEnd) {
            Session::flash('errorFiltro', "El Día de inicio ($dayStart) no puede ser mayor que el Día de fin ($dayEnd).");
            return;
        }

        // 2. Almacenar los valores recibidos
        [$this->filterYear,$this->filterMonth,$this->filterDayStart,$this->filterDayEnd] = [$year,$month,$dayStart,$dayEnd];

        // 3. Carga las ordenes y prepara la paginación.
        $this->cargarOrdenes(); 
        // 4. Carga la estadistica
        $this->cargarEstadisticas(); 
    }
    /* ------------------------------------------
    // METODOS DEL filtros
    // ------------------------------------------*/
    public function ordenarPagos($value,$proceso){
        if ($proceso == "estado") {
            $this->estadoFiltro = $value;
        }else{
            $this->metodoPagoFiltro = $value;
        }
        $this->cargarOrdenes(); // Carga las ordenes y prepara la paginación.
    }

    /* ------------------------------------------
    // LISTENERS Y NAVEGACIÓN
    // ------------------------------------------ 
    // Se activa cuando $ordenMostrar cambia (select)*/
    public function updatedOrdenMostrar($value)
    {
        $this->ordenMostrar = (int) $value; // Asegura que es un entero
        $this->currentPage = 1; // Reinicia a la primera página
        $this->cargarOrdenes(); // Recarga las ordenes
    }
    // Se activa cuando $campoBusqueda cambia (input)
    public function updatedCampoBusqueda()
    {
        $this->currentPage = 1; // Reinicia a la primera página al buscar
        $this->cargarOrdenes(); // Recarga las ordenes
    }
    // Método para cambiar de página (desde los botones de navegación, o campo de busqueda)
    public function mostrarPagina(int $pagina)
    {
        if ($pagina > 0 && $pagina <= $this->totalPages) {
            $this->currentPage = $pagina;
            $this->cargarOrdenes();
        }
    }
    /* Auxiliar Metodo: Lógica para formatear la fecha de la propiedad $this->fechaEntrada */
    public function formatearFecha(string $fecha)
    {
        try {
            // Se utiliza el objeto DateTime para manejar la fecha de entrada
            $fecha_objeto = new DateTime($fecha);
            
            // Obtener los valores numéricos de la fecha de entrada
            $dia_semana_num = $fecha_objeto->format('w'); // 0-6 (Día de la semana)
            $mes_num = $fecha_objeto->format('m');       // 1-12 (Mes)
            $dia_mes = $fecha_objeto->format('d');       // Día del mes
            $anio = $fecha_objeto->format('Y');          // Año
            $hora_min = $fecha_objeto->format('h:i A');    // Hora:Minutos

            // Usar los números para acceder a los arreglos personalizados
            $nombre_dia = $this->arrDia[$dia_semana_num];
            $nombre_mes = $this->arrMes[$mes_num - 1]; // Restamos 1 al mes (1 a 12) para índice 0 a 11

            // Construir la cadena de salida
            return [$nombre_dia . ", " . $dia_mes . " de " . $nombre_mes . " del " . $anio . ".", "Hora: " . $hora_min];

        } catch (\Exception $e) {
            // Manejo de error si la fecha de entrada no es válida
            return $fecha;
        }
    }

    // Método para CANCELAR el pedido
    public function editarPedido(string $orderId, string $accion)
    {
        // Limpiar cualquier mensaje de error anterior de filtro
        Session::forget('errorPedido');
        // 1. Iniciar una transacción
        DB::beginTransaction();
        try {
                // 2. Encontrar la orden
                $order = Order::with('client:cedula,names,email')->findOrFail($orderId);
                // 3. Actualizar el estado de la ORDEN 
                $order->order_status = $accion; 
                // Guardar los cambios en la orden
                $order->save();
                // Confirmar la transacción 
                DB::commit(); 

                // Notificar al cliente y al admin según la acción
                if ($accion == "Listo para entregar") {
                    // Notificar al cliente
                    Mail::to($order->client->email)->send(new ProductoPorEntregar($order));
                    // Notificar al administrador
                    Mail::to($this->userAdmin["email"])->send(new ReporteAdminPPE($order));
                }else if($accion == "Entregado"){
                     // Notificar al cliente
                    Mail::to($order->client->email)->send(new ProductoEntregado($order));
                    // Notificar al administrador
                    Mail::to($this->userAdmin["email"])->send(new ReporteAdminPE($order));
                }

                // 4. Recargar la tabla y enviar evento para actualizar el modal
                $this->cargarOrdenes();
                // 5. Carga la estadistica
                $this->cargarEstadisticas();
                session()->flash('paymentConfirmed', "El pedido ha sido $accion exitosamente.");
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('errorPedido', "Error al $accion el pedido. Inténtalo de nuevo.");
        }
    }

    // Metodo apertura de modal */
    public function verModalReporte($key)
    {
        $this->dispatch('open-modal'); // Emite el evento que JavaScript
    }
    // Método para cerrar el modal
    public function closeModal()
    {
        // Emite el evento que JavaScript está esperando para cerrar Flowbite
        $this->dispatch('close-modal');
    }
    // Método para rechazar el pago desde el modal
    public function rechazarPago(string $reporteID, string $order_id)
    {
        // // Lógica para marcar el reporte de pago como 'Rechazado'
        DB::beginTransaction();
        try {
            // 1. Generar token
            $token =  Str::random(40);
            // 2. Encontrar el reporte de pago y la orden
            $reporte = PaymentReport::findOrFail($reporteID);
            $order = Order::with('client:cedula,names,email')->findOrFail($order_id);

            // --- LÓGICA DE DIFERENCIACIÓN ---

            // Si el total pagado es igual o menor al monto del reporte que estamos rechazando, significa que no hay otros pagos previos válidos.
            if ($order->total_paid <= $reporte->amount) {
                $order->total_paid = 0;
                $order->remaining_amount = $order->total_purchase;
            }else{ // CASO: Se rechaza el pago inicial (Abono o 100%)
                $order->total_paid = $order->total_purchase - $reporte->amount;
                $order->remaining_amount = $order->total_paid;
            }

            // // 3. Actualizar estados comunes
            $order->order_status = "Por Verificar";
            $order->deposit_status = "Por Verificar"; 
            $order->token_segundo_pago = $token;
            $order->payment_deadline = now()->addDays(5);
            $reporte->status = 'Rechazado';
            $order->save();
            $reporte->save();

            DB::commit();

            // // Enviar por correo electronico
            Mail::to($order->client->email)->send(new PagoRechazado($order, $reporte->amount));
            // // Notificar al administrador
            Mail::to($this->userAdmin["email"])->send(new ReporteAdminPR($order));

            $this->cargarOrdenes(); // Carga las ordenes y prepara la paginación.
            session()->flash('paymentConfirmed', 'El reporte de pago ha sido Rechazado.');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('errorPayment', 'Error al rechazar el pago. Inténtalo de nuevo..');
        }
    }
    // Método para confirmar el pago desde el modal
    public function confirmarPago(string $reporteID, string $order_id)
    {
        // // 1. Iniciar una transacción para asegurar que todas las actualizaciones se completen
        DB::beginTransaction();

        try {
            // 2. Encontrar el reporte de pago y la orden
            $reporte = PaymentReport::findOrFail($reporteID);
            $order = Order::with('client:cedula,names,email')->findOrFail($order_id);

            // Marcar el reporte como verificado
            $reporte->status = 'Verificado';
            $reporte->save();

            // Ejecutar la lógica de montos (Abono vs Total)
            $this->actualizarEstadoDeOrden($order);
        
            DB::commit();

            // Refrescar el modelo para obtener los nuevos valores de la DB
            $order->refresh();

            // 4. Decidir qué correo enviar según el estado resultante
            if ($order->deposit_status === '100%') {
                Mail::to($order->client->email)->send(new PagoVerificado($order,$reporte));
            } else {
                Mail::to($order->client->email)->send(new AbonoProcesado($order,$reporte));
            }

            // // Notificar al administrador
            Mail::to($this->userAdmin["email"])->send(new ReporteAdminPV($order));

            $this->cargarOrdenes(); // Carga las ordenes y prepara la paginación.
            // Mostrar una notificación de éxito
            session()->flash('paymentConfirmed', 'El pago y el estado de la orden se han actualizado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            // Mostrar una notificación de error
            session()->flash('errorPayment', 'Error al confirmar el pago. Inténtalo de nuevo..');
        }
    }
    // Auxiliar: Método para actualizar el estado de la orden según los pagos verificados
    protected function actualizarEstadoDeOrden(Order $order)
    {
        // Sumar el total de pagos verificados para esta orden
        $pagosVerificados = PaymentReport::where('order_id', $order->id)
                                        ->where('status', 'Verificado')
                                        ->sum('amount');
        
        $montoTotalOrden = $order->total_purchase; // monto total que el cliente debe pagar

        // Usar una pequeña tolerancia para evitar problemas de punto flotante
        $tolerance = 0.01; 

        // Verificar si la suma de pagos verificados cubre el total de la orden
        if ($pagosVerificados >= $montoTotalOrden - $tolerance) {
                // Lógica Pago Total
            $order->total_paid = $montoTotalOrden;
            $order->remaining_amount = 0;
            $order->order_status = 'Confirmado';
            $order->deposit_status = '100%';
            $order->token_segundo_pago = null;
            $order->payment_deadline = null;
        }else{
            // Lógica Pago Parcial
            $order->total_paid = $pagosVerificados;
            $order->remaining_amount = $montoTotalOrden - $pagosVerificados;
            $order->order_status = 'Por Verificar'; 
            $order->deposit_status = 'Pendiente 50%';
        }
        $order->save();
    }
    // Metodo: Cargar la informacion del administrador para extraer el correo
    protected function cargarDatosAdmin()
    {
        // Consultar el usuario administrador para enviarle msm al correo
        $this->userAdmin = User::select('id','email')
        ->where(function ($query) {
            $query->where('name', 'Administrador')
            ->orWhere('id', 1);
        })
        ->first(); // Obtiene solo el primer registro (objeto), no una colección
    }
    // Metodo: Cargar las ordenes con filtros y paginación manual
    protected function cargarOrdenes()
    {   
        // 1. CONSTRUCCIÓN DE LA CONSULTA BASE CON FILTROS APLICADOS
        $ordenQuery = Order::query()
            // 1. Carga ansiosa: Incluir la relación 'payment_reports'
            // Esta consulta se ejecuta para traer todos los reportes de pago  de todas las órdenes que resultaron despues del filtro y la informacion del cliente.
            ->with(['client', 'payments'])

            // 2. APLICAR FILTRO CONDICIONALMENTE
            ->when($this->campoBusqueda, function ($q, $buscar) {
                $likeSearch = '%' . $buscar . '%';  // Convertir el texto de búsqueda a patrón LIKE
                $isDate = false;
                try {
                    $fecha_objeto = Carbon::createFromFormat('Y-m-d', $buscar); // formato Y-m-d para la validación de eficiencia
                    // 2.1 validar si la búsqueda es una fecha válida (YYYY-MM-DD)
                    $isDate = $fecha_objeto && $fecha_objeto->format('Y-m-d') === $buscar ? true : false;
                } catch (\Throwable $e) {
                    $isDate = false;
                }
                // 2.2. Aplicar condiciones de búsqueda
                $q->where(function ($query) use ($likeSearch,$buscar, $isDate) {
                    // A. Si es una fecha, solo se aplica este filtro y termina.
                    if ($isDate) {
                        // Si el texto es una fecha, se usa la cláusula eficiente whereDate
                        // whereDate es la opción más rápida para campos de fecha indexados
                        $query->whereDate('orders.order_date', $buscar);
                        return; // Si es fecha, no aplica los filtros LIKE en la tabla orders.
                    }
                    // B. Si NO es una fecha, se aplica una gran OR-group que incluye:
                    // B.1. Campos locales de la orden. Búsqueda en la tabla 'orders' (order_status)
                    $query->where('orders.order_status', 'LIKE', $likeSearch);
                    // B.2. Búsqueda en la relación 'client' (nombre o cédula)
                    // Se usa orWhereHas para incluir órdenes donde el cliente asociado cumpla la condición.
                    $query->orWhereHas('client', function ($c) use ($likeSearch, $buscar) {
                        // Asumo que la cédula/identificación es un número que puede ser buscado como texto LIKE o exacto
                        $c->where('names', 'LIKE', $likeSearch)
                        ->orWhere('cedula', 'LIKE', $likeSearch); 
                    });
                    // B.3. Búsqueda en la relación 'payments' (número de referencia o método)
                    // Esto incluye órdenes donde los pagos asociados cumplen la condición.
                    $query->orWhereHas('payments', function ($r) use ($likeSearch) {
                        $r->where('reference', 'LIKE', $likeSearch)
                        ->orWhere('method', 'LIKE', $likeSearch);
                    });
                });
            })
            // 3. APLICAR FILTRO POR ESTADO DE PAGO (Usando la columna 'order_status' de la tabla ORDERS)
            ->when($this->estadoFiltro, function ($q, $estado) {
                $q->where('orders.order_status', $estado);
            })
            // 4. APLICAR FILTRO POR MÉTODO DE PAGO ESPECÍFICO (NUEVO)
            ->when($this->metodoPagoFiltro, function ($q, $metodo) {
                // Filtra solo las órdenes que tienen al menos un pago con el método seleccionado
                $q->whereHas('payments', function ($r) use ($metodo) {
                    $r->where('method', $metodo);
                });
            })
            // 5. APLICAR FILTRO POR RANGO DE FECHA (AÑO, MES, DÍAS). Se aplica el filtro solo si al menos el Año y el Mes están establecidos.
            ->when(!empty($this->filterYear) && !empty($this->filterMonth) && !empty($this->filterDayStart) && !empty($this->filterDayEnd), function ($q) {
                // Asegurar que el día tenga 2 dígitos (ej: 5 -> 05)
                $mesFormateado = str_pad($this->filterMonth, 2, '0', STR_PAD_LEFT); // Asegura "05" en vez de "5"
                $diaInicioFormateado = str_pad($this->filterDayStart, 2, '0', STR_PAD_LEFT);
                $diaFinalFormateado = str_pad($this->filterDayEnd, 2, '0', STR_PAD_LEFT);
                // Construir las fechas completas para la comparación
                $fechaInicio = "{$this->filterYear}-{$mesFormateado}-{$diaInicioFormateado}";
                $fechaFinal = "{$this->filterYear}-{$mesFormateado}-{$diaFinalFormateado}";
                // Nota: Si 'order_date' incluye tiempo, whereDate es más seguro para asegurar el día completo.
                $q->whereDate('orders.order_date', '>=', $fechaInicio)
                ->whereDate('orders.order_date', '<=', $fechaFinal);
            })
            // 6. ORDENAR
            ->orderBy('orders.order_date', 'desc');

        // 2. PAGINACIÓN EFICIENTE DE GRUPOS (solo IDs). Obtenemos solo los ID de registros que cumplen los filtros.
        $allUniqueOrdenIds = $ordenQuery->select('orders.id', 'orders.order_date') // Selecciona solo la columna orden id
                                        ->distinct() // Asegura que son únicos
                                        ->pluck('orders.id') //Ejecutar la consulta construida previamente ($variantsQuery).
                                        ->toArray(); // Convertimos a array para manipulación en memoria

        // Calculamos el total de grupos y el total de páginas en PHP (en memoria).
        $this->totalItems = count($allUniqueOrdenIds); // Total de grupos únicos
        $this->totalPages = ceil($this->totalItems / $this->ordenMostrar); // Calcula el total de páginas

        // Ajustamos la página actual si el filtro la dejó fuera o si no hay resultados
        if ($this->currentPage > $this->totalPages && $this->totalPages > 0) { // Si la página actual excede el total
            $this->currentPage = 1;
        } elseif ($this->totalPages === 0) { // No hay resultados
            $this->listaOrdenes = collect([]);
            return; // No hay resultados, terminamos aquí
        }

        // Aplicamos Paginación Manual (SLICE) a la lista de IDs de imagen (lista pequeña en memoria).
        $offset = ($this->currentPage - 1) * $this->ordenMostrar;
        $paginatedOrdenIds = array_slice($allUniqueOrdenIds, $offset, $this->ordenMostrar);
        
        // 3. CARGA DE DATOS COMPLETOS (SOLO PARA LA PÁGINA ACTUAL)
        // Usamos whereIn para cargar SÓLO las ordebes que tienen los IDs paginados.
        $ordenesPorPagina = Order::whereIn('orders.id', $paginatedOrdenIds)
            ->orderBy('orders.order_date', 'desc')
            // Seleccionar campos de datos NECESARIOS Y RELACIONES
            ->select('id', 'order_date', 'client_cedula', 'total_purchase', 'total_paid', 'remaining_amount', 'order_status', 'deposit_status', 'token_segundo_pago','payment_deadline') 
            ->with([
                // Cargar los producto de esa orden
                'items' => fn($q) => $q
                    ->select('order_items.id','order_id','product_variant_id','quantity','applied_unit_price') 
                    ->with([
                        'orderVariante' => fn($v) => $v
                        ->select('product_variants.id','product_id','full_sku') 
                        ->with([
                            'product'=> fn($v) => $v->select('products.id','name') 
                        ])
                    ]),
                // Cargar los reportes de esa orden
                'payments' => fn($p) => $p->select('payment_reports.id','order_id','amount','dollar_rate','method','reference','reference_img','report_date','status'),
                'client' => fn($c) => $c->select('cedula','names','email','phone','address'),
            ])
            ->get();

        // 4. Ordenar datos PARA LA VISTA
        $ordenesOrganizadas = [];
        foreach ($ordenesPorPagina as $orden) {
            $listaVariantes = [];  
            $precioTotal = 0; // Va imcrementar segun el precio unitaorio de cada variante y segundo su producto padre
            // 1. Información visual en la tabla:
            $infoGeneral = [
                'fecha_pedido'   => (new DateTime($orden->order_date))->format('Y-m-d h:i A'),
                'cedula_cliente' => $orden->client_cedula,
                'nombre_cliente' => $orden->client->names,
                'total_compra'   => $orden->total_purchase,
                'total_pagado'   => $orden->total_paid,
                'importe_restante' => $orden->remaining_amount,
                'estado_deposito'  => $orden->deposit_status,
                'estado_pedido'    => $orden->order_status,
            ];

            // 2. Informacion del cliente:
            $infoClienteOrden = [
                'cedula_cliente' => $orden->client->cedula,
                'nombre_cliente' => $orden->client->names,
                'email_cliente' =>  $orden->client->email,
                'telefono_cliente' => $orden->client->phone,
                'direccion_cliente' => $orden->client->address,
            ];

            // 3. Informacion del pedido Y Detalles de los Items/Variantes (Productos):
            foreach ($orden->items as $item) {
                $variante = $item->orderVariante;
                $producto = $variante->product ?? null; 
                $precioTotal += (double)$item->applied_unit_price * (int)$item->quantity;

                if ($producto) {
                    $listaVariantes[$producto->name][] = [
                        'sku_completo' => $variante->full_sku,
                        'cantidad' => $item->quantity,
                        'precio_unitario_aplicado' => $item->applied_unit_price,
                    ];
                }
            }
            $infoPedido = [
                'orden_id'      => $orden->id,
                'fecha_pedido'   => $this->formatearFecha($orden->order_date),
                "listaVariantes" => $listaVariantes,
                "Subtotal" => $precioTotal,
                "costo_envio" => max(0, (double)$orden->total_purchase - $precioTotal),
                'compra_total' =>  $orden->total_purchase,
            ];

            
            // 4. Informacion de Reportes de Pago
            $reportesPago = $orden->payments->map(function ($payment) use ($orden) {
                return [
                    'id' => $payment->id,
                    'order_id' => $payment->order_id,
                    'monto' => $payment->amount,
                    'tasa' => $payment->dollar_rate,
                    'metodo' => $payment->method,
                    'referencia' => $payment->reference,
                    'imagen_referencia' => $payment->reference_img,
                    'fecha_reporte' => $this->formatearFecha($payment->report_date),
                    'estado' => $payment->status,
                    'compra_total' =>  $orden->total_purchase,
                    'token' =>  $orden->token_segundo_pago,
                    'fecha_limite_pago' =>  $orden->payment_deadline,
                    'orden_estado' =>  $orden->order_status,
                ];
            })->toArray();

            // 5. Estructura Final
            $ordenesOrganizadas[] = [
                'infoGeneral' => $infoGeneral,
                'cliente_y_orden' => $infoClienteOrden,
                'detalle_pedido'=> $infoPedido,
                'reportes_pago' => $reportesPago,
            ];
        }
        // 5. Asignamos los resultados (La asignación final que sí convierte la Collection de arrays a array simple)
        $this->listaOrdenes = $ordenesOrganizadas;
    }
    // Metodo: Formatear fecha a formato legible en estadistica
    protected function cargarEstadisticas()
    {
        // Estados de órdenes a considerar
        $estadosOrdenes = ['Entregado','Listo para entregar','Por Verificar', 'Confirmado', 'Cancelado'];
        // Simplificamos la creación de fechas y asignación múltiple
        $fechaActual = Carbon::create($this->filterYear, (int)$this->filterMonth, 1);
        // Obtener el mes y año anterior
        $fechaAnterior = $fechaActual->copy()->subMonth();
        // Obtener el mes y año desde las propiedades de filtro 
        [$mes,$anio] = [(int)$this->filterMonth,(int)$this->filterYear];
        // Obtener el mes y año anterior
        [$mes_a,$anio_a] = [(int)$fechaAnterior->month,(int)$fechaAnterior->year];
        // Consulta optimizada
        $ordenesRaw = Order::select('order_status') // Agrupamos por el estado de la orden
            // Contar total de órdenes para el primer mes (actual)
            ->selectRaw("COUNT(CASE WHEN MONTH(created_at) = ? AND YEAR(created_at) = ? THEN 1 END) AS primer_mes", [$mes_a, $anio_a])
            // Contar total de órdenes para el segundo mes (anterior)
            ->selectRaw("COUNT(CASE WHEN MONTH(created_at) = ? AND YEAR(created_at) = ? THEN 1 END) AS segundo_mes", [$mes, $anio])
            // Filtramos solo los estados relevantes
            ->whereIn('order_status', $estadosOrdenes)
            // Filtramos solo el rango de fechas que cubre ambos meses (Opcional, pero bueno para índices)
            ->groupBy('order_status')
            ->get()
            ->keyBy('order_status');

        // Usamos colecciones para transformar los datos sin tanto IF/ELSE
        $this->ordenesEstados = collect($estadosOrdenes)->mapWithKeys(function ($estado) use ($ordenesRaw) {
            $datos = $ordenesRaw->get($estado) ?? (object)['primer_mes' => 0, 'segundo_mes' => 0];
            
            $prev = (int)$datos->primer_mes;
            $curr = (int)$datos->segundo_mes;

            // PHP 8 Match: Reemplaza los if/elseif de tendencia
            [$tendencia, $porcentaje] = match (true) {
                $prev < $curr => ['subida', ($curr - $prev) / $curr * 100],
                $prev > $curr => ['caida', ($prev - $curr) / $prev * 100],
                default       => ['estable', 0],
            };

            return [$estado => [
                'order_status' => $estado,
                'primer_mes'   => $prev,
                'segundo_mes'  => $curr,
                'tendencia'    => $tendencia,
                'porcentaje'   => $porcentaje,
            ]];
        })->all();
        // Sumamos el total del mes actual usando la colección recién creada
        $this->totalOrdenes = collect($this->ordenesEstados)->sum('segundo_mes');
    }
    // Metodo: Se ejecuta una sola vez al cargar el componente
    public function mount()
    {
        $now = Carbon::now(); // Obtener la fecha actual
        $this->filterYear = $now->year;                 // Inicializar las propiedades de filtro con el año actual
        $this->filterMonth = $now->format('m'); // Inicializar las propiedades de filtro con el mes actual
        $this->filterDayEnd = $now->day;                // Inicializar las propiedades de filtro con el dia actual
        $this->cargarEstadisticas(); // Cargar las estadísticas iniciales
        $this->cargarOrdenes(); // Carga las ordenes y prepara la paginación.
        $this->cargarDatosAdmin(); // Carga los datos del admin
    }
    // Renderiza la vista Livewire
    public function render()
    {
        return view('livewire.lista-ordenes');
    }
}

