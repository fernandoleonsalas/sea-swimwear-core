<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Carbon;

class GestionPago extends Controller
{
    // Metodo-Mostrar: formulario de orden:
    public function ordenForm(){
        return view("orden-pago.crear");
    }
    // Metodo-Mostrar: La lista de ordenes:
    public function listaOrdenes(){
        return view("orden-pago.listaOrdenes");
    }
    /**
     * Paso 1: Recibe el formulario, valida el token y redirige a la URL final.
     */
    public function validarToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string|min:5|max:100', // Las reglas
        ], [
            // Array de mensajes personalizados
            'token.required' => 'El campo Token de Pago es obligatorio. Por favor, ingrésalo.',
            'token.string' => 'El token debe ser texto.',
            'token.min' => 'El token debe tener al menos 5 caracteres.',
            'token.max' => 'El token no debe exceder los 100 caracteres.',
        ]);
        
        $token = $request->input('token');

        // 3. Redirigir a la ruta GET final con el token
        return redirect()->route('pago', ['token' => $token]);
    }
    /**
     * Metodo: Recibe el token en la URL y muestra el resultado final, ya sea el mismo formulario de token o formulario de pago.
     * Esta función funciona cuando el usuario entra por primera vez O cuando es redirigido.
     */
    public function formPago($token){
        $orden = null; // Va almacenar la orden del cliente

        if ($token !== "new") {
            // 1. Buscar la Orden por el token
            $orden = Order::where('token_segundo_pago', $token)
            // 2. relacionar con el cliente
            ->with('client')
            // 3. Asegurarse de que el pago no haya expirado si tienes una lógica de expiración
            ->where('payment_deadline', '>', now()) 
            ->where('remaining_amount', '>', 0) // Que aún haya saldo pendiente
            ->first();
            // 4. Crear mensaje si la orden no existe o ha caducado
            empty($orden) && session()->flash('error_token', 'El enlace de pago no es válido, ha caducado o el pedido ya fue saldado.');
        }
        // 4. Mostrar vista:
        return view("orden-pago.pago-final",compact('orden'));
    }
    // Metodo-Mostrar: mensaje de exito de pago:
    public function msmPago($msm) {
        try {
            // 1. Validamos y convertimos
            $fechaObjeto = Carbon::createFromFormat('dmY', $msm);
            // 2. Si tuvo éxito, creamos el formato amigable
            $fechaAmigable = $fechaObjeto->translatedFormat('d-m-Y');
            
            $aviso = $fechaAmigable; 
        } catch (\Exception $e) {
            // Si no es una fecha válida (es un "100" o basura), dejamos el aviso vacío
            $aviso = false;
        }
        return view("orden-pago.mensaje", compact('aviso'));
    }

}
