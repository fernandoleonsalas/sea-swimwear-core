<x-mail::message>
# 📦 ¡Pedido Listo para Entregar!

La preparación del pedido ha finalizado con éxito. Los productos han sido verificados y están listos para ser entregados al cliente o despachados a la dirección correspondiente.

<x-mail::table>
| Detalle de Logística | Información |
| :--- | :--- |
| **Cliente** | <span style="text-transform: capitalize;">{{ $order->client->names }}</span> |
| **ID Pedido** | #{{ $order->id }} |
| **Estado de Pago** | {{ $order->remaining_amount <= 0 ? '✅ Pagado Total' : '⚠️ Saldo Pendiente' }} |
| **Monto a Cobrar** | **${{ number_format($order->remaining_amount, 2) }}** |
</x-mail::table>

<x-mail::panel>
**Acción Requerida:** Por favor, coordina con el cliente para la entrega final. Asegúrate de validar el estado del pago antes de entregar la mercancía si existe un saldo pendiente.
</x-panel>

<x-mail::button :url="route('ordenes')" color="primary">
    Gestionar Despacho
</x-mail::button>

<span>
    <b>Coordinar Entrega:</b>
    <br>
    Contacta al cliente ahora para avisarle que su pedido está listo:
</span>

<x-mail::button :url="'https://wa.me/58' . $order->client->phone . '?text=Hola ' . $order->client->names . ', tu pedido de ' . config('app.name') . ' ya está listo para ser entregado.'" color="success">
    Notificar al Cliente por WhatsApp
</x-mail::button>

<x-slot:subcopy>
    <span style="display: block; text-align: center; font-size: small;">
        Aviso de disponibilidad generado por el <br>
        <span style="font-weight: bolder; text-transform: uppercase;">
            Departamento de Operaciones de {{ config('app.name') }}
        </span>
    </span>
</x-slot:subcopy>

</x-mail::message>