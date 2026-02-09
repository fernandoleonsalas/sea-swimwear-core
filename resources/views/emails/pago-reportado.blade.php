<x-mail::message>
# 🛒 ¡Nueva Venta Registrada!

Se ha validado exitosamente un nuevo pago en la plataforma. Aquí tienes los detalles para proceder con la gestión:

<x-mail::table>
| Concepto | Información |
| :--- | :--- |
| **Fecha** | {{ \Carbon\Carbon::parse($order->order_date)->locale('es')->isoFormat('LL') }} |
| **Cliente** | <span style="text-transform: capitalize;">{{ $order->client->names }} </span> |
| **Monto Total** | **${{ number_format($order->total_purchase, 2) }}** |
| **Monto Pagado** | **${{ number_format($order->total_paid, 2) }}** |
| **Monto Pendiente** | **${{ number_format($order->remaining_amount, 2) }}** |
</x-mail::table>

<x-mail::panel>
**Estado:** El cliente ha recibido la notificación y aguarda el contacto para coordinar.
</x-panel>

<x-mail::button :url="route('ordenes')" color="primary">
    Gestionar Pedido en Panel
</x-mail::button>

<span>
    <b>Contacto Directo:</b>
    <br>
    Puedes iniciar la conversación con el cliente haciendo clic aquí:
</span>

<x-mail::button :url="'https://wa.me/58' . $order->client->phone" color="success">
    <img src="{{ asset('images/WhatsApp.png') }}" width="20" height="20" alt="WhatsApp" style="vertical-align: middle; margin-right: 8px;">
    Contactar Al Cliente
</x-mail::button>

<x-slot:subcopy>
    <span style="display: block; text-align: center; font-size: small;">
        Este es un mensaje automático generado por el <br>
        <span style="font-weight: bolder; text-transform: uppercase;">
            Sistema De Notificaciones De {{ config('app.name') }}
        </span>
    </span>
</x-slot:subcopy>

</x-mail::message>
