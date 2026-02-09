<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Client extends Model
{
    use HasFactory;

    // 1. Especificar la clave primaria no convencional
    protected $primaryKey = 'cedula';

    // 2. Indicar que la clave primaria NO es un entero autoincremental
    public $incrementing = false;

    // 3. La clave primaria es un string
    protected $keyType = 'string';


    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'cedula', 
        'names', 
        'email', 
        'phone', 
        'address'
    ];


    /**
     * Relación Eloquent: Un cliente puede tener muchas órdenes.
     * @return HasMany
     */
    public function ordersCedula(): HasMany
    {
        // 1. Model relacionado: Order::class
        // 2. Foreign Key en la tabla 'orders': 'client_cedula'
        // 3. Local Key en la tabla 'clients': 'cedula' (que es la PK)
        return $this->hasMany(Order::class, 'client_cedula', 'cedula');
    }




}
