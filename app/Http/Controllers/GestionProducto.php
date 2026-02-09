<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GestionProducto extends Controller
{
    // Metodo-Mostrar: formulario de creación de producto:
    public function crearProducto(){
        return view("productos.crear");
    }
     // Metodo-Mostrar: vista de lista de producto:
    public function listaProducto(){
        return view("productos.lista");
    }
}
