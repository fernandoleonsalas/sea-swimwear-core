<x-mail::message>
# ✅ Pago Verificado Exitosamente

Se ha confirmado la recepción de un nuevo pago. La orden ha sido actualizada y los montos se han conciliado en el sistema.

<x-mail::table>
| Concepto | Información del Pago |
| :--- | :--- |
| **Fecha de Verificación** | {{ \Carbon\Carbon::now()->locale('es')->isoFormat('LL') }} |
| **Cliente** | <span style="text-transform: capitalize;">{{ $order->client->names }} </span> |
| **Monto Verificado** | **${{ number_format($order->total_paid, 2) }}** |
| **Saldo Restante** | **${{ number_format($order->remaining_amount, 2) }}** |
</x-mail::table>

<x-mail::panel>
**Estado Actual:** El pago ha sido aprobado. Si la orden tiene saldo pendiente, el sistema quedará a la espera del próximo pago; de lo contrario, está lista para despacho.
</x-panel>

<x-mail::button :url="route('ordenes')">
    Ver Orden Actualizada
</x-mail::button>

<span>
    <b>Notificar al Cliente:</b>
    <br>
    Si deseas enviar un comprobante manual o confirmar la entrega:
</span>

<x-mail::button :url="'https://wa.me/58' . $order->client->phone" color="success">
    Enviar Mensaje al Cliente
</x-mail::button>

<x-slot:subcopy>
    <span style="display: block; text-align: center; font-size: small;">
        Este es un comprobante oficial de operación generado por <br>
        <span style="font-weight: bolder; text-transform: uppercase;">
            Administración de {{ config('app.name') }}
        </span>
    </span>
</x-slot:subcopy>

</x-mail::message>
