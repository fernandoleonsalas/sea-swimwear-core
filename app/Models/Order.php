<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    // Permitir asignación masiva para los campos que necesitamos crear
    protected $fillable = ['client_cedula', 'total_purchase', 'total_paid', 'remaining_amount', 'dollar_rate', 'order_status', 'deposit_status', 'token_segundo_pago', 'payment_deadline'];

    // Relacion: Un pedido puede tener muchos artículos de pedido
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
    // Relacion: Un pedido puede tener muchos reportes de pago.
    public function payments(): HasMany
    {
        return $this->hasMany(PaymentReport::class);
    }
    // Relacion: Un pedido pertenece a un solo usuario.
    public function orders(): belongsTo
    {
        return $this->belongsTo(User::class);
        
    }

    /**
     * Relación Eloquent: Una orden pertenece a un cliente.
     * @return BelongsTo
     */
    public function client(): BelongsTo
    {
        // 1. Model relacionado: Client::class
        // 2. Foreign Key en la tabla 'orders': 'client_cedula'
        // 3. Owner Key en la tabla 'clients': 'cedula' (que es la PK)
        return $this->belongsTo(Client::class, 'client_cedula', 'cedula');
    }
}
