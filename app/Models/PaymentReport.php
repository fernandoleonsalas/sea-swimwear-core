<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class PaymentReport extends Model
{
    protected $table = 'payment_reports';

    // Permitir asignación masiva para los campos que necesitamos crear
    protected $fillable = ['order_id', 'amount', 'dollar_rate', 'method', 'reference', 'reference_img','status'];
    
    // Relacion: un reporte de pago pertenece solo a un pedido.
    public function order(): BelongsTo 
    {
        return $this->belongsTo(Order::class);
    }
}