<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductVariant extends Model
{
    // Permitir asignación masiva para los campos que necesitamos crear
    protected $fillable = ['product_id', 'full_sku', 'stock', 'image_id', 'status'];

    // * Relacion 1:M: Una variante pertenece a un único producto principal.
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
     // * Relación: Una variante tiene muchos valores de atributo (many-to-many). Tabla pivot: attribute_value_variants
    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(
            AttributeValue::class,
            'attribute_value_variants',     // Nombre de la tabla pivot
            'product_variant_id',           // Clave foránea local (en la tabla pivot)
            'attribute_value_id'            // Clave foránea relacionada (en la tabla pivot)
        );
    }
    /**
     * Relación: Un ProductVariant tiene muchos registros en la tabla pivote/nexo.
     */
    public function attributeValueVariants()
    {
        // Esto soluciona el error 'Undefined method'
        return $this->hasMany(AttributeValueVariant::class);
    }
    // * Relación: Una variante de producto puede estar en muchos artículos de pedido, 
    public function orderItems(): HasMany 
    { 
        return $this->hasMany(OrderItem::class); 
    } 
    // Relacion 1:M: Una variante pertenece a una unica imagen principal.
    public function image()
    {
        return $this->belongsTo(Image::class, 'image_id');
    }
}

