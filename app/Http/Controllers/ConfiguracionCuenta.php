<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class configuracionCuenta extends Controller
{
    // Metodo-Mostrar: formulario de metodos de pago:
    public function metodosPagos(){
        return view("configuracion.metodo-pago");
    }

    // Metodo-Mostrar: formulario de orden:
    public function tasaCambioForm(){
        return view("configuracion.tasa-cambio");
    }
}

