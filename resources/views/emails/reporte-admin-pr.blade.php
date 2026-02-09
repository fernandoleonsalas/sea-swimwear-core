<x-mail::message>
# ⚠️ Pago Rechazado

Se ha registrado un rechazo de pago en la plataforma. Este es un comprobante de la acción realizada por el administrador/sistema.

<x-mail::table>
| Concepto | Detalles del Rechazo |
| :--- | :--- |
| **Fecha de Acción** | {{ \Carbon\Carbon::now()->locale('es')->isoFormat('LL') }} |
| **Cliente** | <span style="text-transform: capitalize;">{{ $order->client->names }}</span> |
| **ID Pedido** | #{{ $order->id }} |
| **Monto Rechazado**| **${{ number_format(($order->total_purchase - $order->total_paid), 2) }}** |
| **Motivo** | {{ $motivo_rechazo ?? 'No especificado' }} |
</x-mail::table>

<x-mail::panel>
**Nota Informativa:** El cliente ha sido notificado sobre el rechazo y se le ha solicitado revisar su comprobante o método de pago para reintentar la operación.
</x-panel>

<x-mail::button :url="route('ordenes')" color="error">
    Ver Detalles en el Panel
</x-mail::button>

<span>
    <b>Soporte al Cliente:</b>
    <br>
    Si necesitas contactar al cliente para aclarar el motivo del rechazo:
</span>

<x-mail::button :url="'https://wa.me/58' . $order->client->phone" color="success">
    Contactar por WhatsApp
</x-mail::button>

<x-slot:subcopy>
    <span style="display: block; text-align: center; font-size: small;">
        Este es un mensaje informativo generado por el <br>
        <span style="font-weight: bolder; text-transform: uppercase;">
            Sistema De Notificaciones De {{ config('app.name') }}
        </span>
    </span>
</x-slot:subcopy>

</x-mail::message>