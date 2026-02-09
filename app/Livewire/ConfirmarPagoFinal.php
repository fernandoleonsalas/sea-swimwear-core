<?php

namespace App\Livewire;

use Livewire\Component;        // IMPORTAR la clase base 'Component' de Livewire, de la que debe heredar todo componente.
use Livewire\WithFileUploads;  // IMPORTAR Trait de Livewire que habilita la subida de archivos seguros dentro del componente.
use App\Mail\PagoParcial;      // CORREO.
use App\Mail\PagoFinal;        // CORREO.
use App\Models\Client;         // MODELO. 
use App\Models\Product;        // MODELO. 
use App\Models\ProductVariant; // MODELO.
use App\Models\Order;          // MODELO.
use App\Models\OrderItem;      // MODELO.
use App\Models\PaymentReport;  // MODELO.
use App\Models\configurations; // MODELO.
use App\Models\BankAccount;    // MODELO
use Illuminate\Support\Arr;     // IMPORTAR Arr para manipulación de arrays.
use Illuminate\Support\Str;     // IMPORTAR para generar tokens
use Illuminate\Support\Facades\Session;     // IMPORTAR el 'Facade' de Session de Laravel, usado para acceder y manipular los datos de la sesión.
use Illuminate\Support\Facades\DB;          // IMPORTAR el 'Facade' de la Base de Datos (DB), que permite ejecutar consultas directas y operaciones transacciones con la base de datos.
use Illuminate\Support\Facades\Storage;     // IMPORTAR el 'Facade' de Storage de Laravel, esencial para interactuar con los sistemas de archivos (discos), como el almacenamiento local o servicios en la nube (S3).
use Illuminate\Support\Facades\Validator;   // IMPORTAR el 'Facade' de Validator de Laravel, que proporciona una forma sencilla de validar datos entrantes contra un conjunto definido de reglas.
use Illuminate\Support\Facades\Mail;    // IMPORTAR el 'Facade' de Mail de Laravel, utilizado para enviar correos electrónicos desde la aplicación.
use Illuminate\Validation\Rule;         // IMPORTAR la clase 'Rule' para poder usarla en las reglas de validación.

class ConfirmarPagoFinal extends Component
{
    use WithFileUploads; // Habilita la carga de archivos en Livewire
    // Propiedades importantes para la consultas, y arrays que van almacenar los registros solicitados:
    public $tasaDolar = 0, $pagoMovilMetodo = [], $zelleMetodo = [];
    // Propiedades para los datos del banco y Zelle (estos valores viene de la configuración o base de datos)
    public $banco,$cedulaBanco, $telefonoBanco, $titularBanco; // cuenta de Pago movil
    public $titularBancoZelle, $emailZelle; // cuenta de zelle
    // --- Propiedades del Formulario orden (datos del cliente) ---
    public $info_cliente, $customer_cedula , $customer_name , $client_email , $customer_phone , $customer_adress;
    public $orden; // Va almacenar La orden completa
    public $selectedMethod = 'pago-movil'; // Establecer 'pago-movil' como predeterminado
    public $payment_intention = '100';
    public $payment_method = 'pago-movil'; // Valor predeterminado
    public $reference_number;
    public $comprobante; // Para el archivo subido por el usuario
    public $securityAnswer;
    public $numero1 = 1;
    public $numero2 = 2;
    public $termsAccepted = false;
    // --- otras propiedades ---
    public $total_amount = 0; 
    public $paid_amount = 0; 
    public $remaining_amount = 0;
    public $hasErrors = false;  

    // REGLAS DE VALIDACIÓN DINÁMICA Y MENSAJES PERSONALIZADOS
    protected function reglas(){
        $reglas = [
            // Asegúrate de validar el comprobante y otros datos
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

    /**
     * Método principal que maneja la validación, la transacción de BD y la finalización del pedido.
     */

    public function confirmarPedido(){
        // 1. Obtiene el array completo de reglas.
        $reglasValidacion = $this->reglas();
        // 2. Llama a $this->validate() con las reglas y las propiedades publica. se ejecuta la validación:
        $this->validate($reglasValidacion);
        // 3. Validar la verificación de seguridad
        if ((int)$this->securityAnswer) {
            $respuestaCorrecta = ($this->numero1 + $this->numero2) - $this->securityAnswer;
            if ($respuestaCorrecta != 0) {
                session()->flash('error', 'Respuesta de seguridad incorrecta.');
                $this->addError('securityAnswer', 'Respuesta incorrecta.');
                return;
            }
        }
        // 4. PREPARACIÓN DE DATOS FINALES
        $proofPath = null;


        // Asignar valores de pago
        try {
            // A. Subir el Comprobante (dentro del try para manejar el borrado si falla el commit)
            $proofPath = $this->comprobante->store('comprobantes', 'local');
            // B. Iniciar la transacción
            DB::transaction(function () use ($proofPath) {
                $order = Order::with('client:cedula,names,email')->findOrFail($this->orden->id);
                $montoRestante = (float) $order->total_purchase  - ((float) ($order->total_paid + $this->remaining_amount));
                $totalPagar =  (float) $order->remaining_amount; // Valor total a pagar

                // C. REGISTRAR EL REPORTE DE PAGO
                PaymentReport::create([
                    'order_id' => $this->orden->id,
                    'amount' => $totalPagar,
                    'dollar_rate' => $this->tasaDolar,
                    'method' => $this->payment_method,
                    'reference' => $this->reference_number,
                    'reference_img' => $proofPath, // Ruta del archivo subido
                    'status' => 'Pendiente', 
                ]);

                // 2. Modificar el registro
                $order->total_paid  = $totalPagar + $order->total_paid;
                $order->remaining_amount  = $montoRestante;
                $order->order_status = "Por Verificar";
                $order->deposit_status = "Por Verificar";
                $order->token_segundo_pago  = null; 
                $order->payment_deadline  = null; 
                // 3. Guardar cambios
                $order->save();

                // 4. Enviar correo
                if ($order->client->email) {
                    Mail::to($order->client->email)->send(new PagoFinal($order));
                }
            });

            return redirect()->route('msmPago', ['msm' => "nulls"]); 

        } catch (\Exception $e) {
             // Si la transacción falló, el ROLLBACK ya se ejecutó.
            if (isset($proofPath) && $proofPath) { // Borrar el archivo si se subió pero la BD falló
                Storage::disk('local')->delete($proofPath); 
            }
            // Log::error("Error en el Checkout (Orden {$order->id ?? 'Nueva'}): " . $e->getMessage());
            session()->flash('error_form_orden', 'Error al procesar el pago. Por favor, inténtelo más tarde.');
        }
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
    public function mount($orden)
    {
        // Obtener La orden completa
        $this->orden = $orden; 
        // Generar números aleatorios para la verificación de seguridad
        $this->numero1 = random_int(1, 10);
        $this->numero2 = random_int(1, 10);
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
            //   --- Lógica de Enmascaramiento ---
            // 1. Enmascarar el nombre (dejando solo los 4 últimos dígitos). str_pad para reemplazar los primeros caracteres por asteriscos.
            $this->customer_cedula  = $this->enmascaramiento((string)$orden->client->cedula);
            $this->customer_name    = $this->enmascaramiento((string)$orden->client->names);
            $this->customer_phone   = $this->enmascaramiento((string)$orden->client->phone);
            $this->customer_adress  =  $this->enmascaramiento((string)$orden->client->address);

            // 2. Enmascarar el Correo Electrónico (ej. dejando la primera letra y el dominio) usuario@dominio.com -> u*******@dominio.com
            $email = $orden->client->email;
            $emailMostrar = "";
            if (strpos($email, '@') !== false) {
                list($user, $domain) = explode('@', $email, 2);
                $maskedUser = substr($user, 0, 1) . str_repeat('*', max(0, strlen($user) - 1));
                $emailMostrar = $maskedUser . '@' . $domain;
            } else {
                $emailMostrar = $email;
            }
            // Asignar los valores enmascarados a las propiedades del componente
            $this->client_email =  $emailMostrar;
            // Asignar valores de pago
            $this->total_amount = $orden["total_purchase"];
            $this->paid_amount = $orden["total_paid"];
            $this->remaining_amount = $orden["remaining_amount"];
        }
    }
    // Renderiza la vista Livewire
    public function render()
    {
        return view('livewire.confirmar-pago-final');
    }
}
