<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attribute extends Model
{
    // Relación 1:N:  Un atributo tiene muchos valores (ej: Atributo -> 'Color' tiene valores --> 'Rojo', 'Azul', 'Verde').
    public function attributeValues(): HasMany
    {
        return $this->hasMany(AttributeValue::class);
    }
}
