<x-mail::message>
# 🏁 ¡Pedido Entregado!

Se ha confirmado la entrega satisfactoria de la orden. Con esta acción, el proceso de venta se considera finalizado y el inventario ha sido actualizado.

<x-mail::table>
| Resumen de Finalización | Detalle |
| :--- | :--- |
| **Fecha de Entrega** | {{ \Carbon\Carbon::now()->locale('es')->isoFormat('LLLL') }} |
| **Cliente** | <span style="text-transform: capitalize;">{{ $order->client->names }}</span> |
| **ID de Orden** | #{{ $order->id }} |
| **Total Venta** | **${{ number_format($order->total_purchase, 2) }}** |
| **Estado Final** | <span style="color: #2d3748; font-weight: bold;">Completado</span> |
</x-mail::table>

<x-mail::panel>
**Información:** Esta notificación confirma que el cliente ya tiene el producto en su poder. La orden ha sido movida al histórico de ventas finalizadas.
</x-panel>

<span>
    <b>Fidelización:</b>
    <br>
    ¿Quieres agradecer al cliente o solicitar una reseña?
</span>

<x-mail::button :url="'https://wa.me/58' . $order->client->phone . '?text=Hola ' . $order->client->names . ', gracias por confiar en ' . config('app.name') . '. Esperamos que disfrutes tu compra.'" color="success">
    Enviar Mensaje de Agradecimiento
</x-mail::button>

<x-slot:subcopy>
    <span style="display: block; text-align: center; font-size: small;">
        Comprobante de cierre generado automáticamente por <br>
        <span style="font-weight: bolder; text-transform: uppercase;">
            {{ config('app.name') }} - Gestión de Ventas
        </span>
    </span>
</x-slot:subcopy>

</x-mail::message>