<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AttributeValue extends Model 
{
    // Si tu tabla en la base de datos se llama 'attribute_values' (snake_case),
    // Laravel la inferirá automáticamente, pero es buena práctica especificarla si usas un nombre no convencional.
    protected $table = 'attribute_values';
    protected $fillable = ['attribute_id', 'value', 'color_code'];

    // 1. Relación 1-N (BelongsTo)
    // MÉTODO CORREGIDO: Se usa el singular 'attribute()' para una relación BelongsTo
    public function attribute(): BelongsTo 
    {
        // NOTA: También debes asegurarte de que tu modelo 'attributes' se llame 'Attribute'
        return $this->belongsTo(Attribute::class); 
    }

    // 2. Relación N-M (BelongsToMany)
    public function variants(): BelongsToMany
    {
        // Se asume que product_variants es ahora ProductVariant::class
        return $this->belongsToMany(
            ProductVariant::class,
            'attribute_value_variants',     // Nombre de la tabla pivot
            'product_variant_id',           // Clave foránea local (en la tabla pivot)
            'attribute_value_id'            // Clave foránea relacionada (en la tabla pivot)
        );
    }
}