<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    // Permitir la asignación masiva para los campos  de la tabla products.
    protected $fillable = ["sku_base","name","description","price_retail","price_wholesale","min_pieces","status"];

    // 1. Relación n:m:  Un producto puede tener muchas categorías (Relación Muchos a Muchos).  usando tabla pivote category_product 
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class,'category_product'); // Tabla pivot 'category_product'
    }
    // 2. Relación 1:n: Un producto tiene muchas variantes (Relación Uno a Muchos).
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }
}