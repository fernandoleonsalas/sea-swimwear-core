<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class configurations extends Model
{
    protected $primaryKey = 'key'; // Define cuál es tu llave primaria
    public $incrementing = false;  // Indica que no es un entero autoincremental
    protected $keyType = 'string'; // Indica que la llave es un texto
    
    protected $fillable = [
        'key',
        'text',
        'value'
    ];
}
