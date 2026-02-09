<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class AttributeValueVariant extends Pivot // Usar 'Pivot' es buena práctica
{
    protected $table = 'attribute_value_variants';
    // las relaciones inversas
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
    
    public function attributeValue()
    {
        return $this->belongsTo(AttributeValue::class);
    }
}
