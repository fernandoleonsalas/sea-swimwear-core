<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class OrderItem extends Model
{
    protected $table = 'order_items';

    // Permitir asignación masiva para los campos que necesitamos crear
    protected $fillable = ['order_id', 'product_variant_id', 'quantity', 'applied_unit_price', 'created_at','updated_at'];

    // * Relación: Un artículo de pedido pertenece solo a una variante de producto.
    public function orderVariante(): BelongsTo
    {
        return $this->BelongsTo(ProductVariant::class, 'product_variant_id', 'id');
    }

    // Relacion: un artículo de pedido pertenece solo a un pedido.
    public function Order(): BelongsTo
    {
        return $this->BelongsTo(Order::class);
    }
    
}
