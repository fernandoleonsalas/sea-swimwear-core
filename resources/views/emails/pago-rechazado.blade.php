<x-mail::message>
# ⚠️ ¡Atención, <span style="text-transform: capitalize;">{{ $order->client->names }}</span>!

Te informamos que el reporte de pago asociado a tu pedido **no ha podido ser validado** y ha sido **rechazado**.

**Resumen de la operación:**

<x-mail::table>
| Concepto | Información |
| :--- | :--- |
| **Fecha** | {{ \Carbon\Carbon::parse($order->order_date)->locale('es')->isoFormat('LL') }} |
| **Monto Total** | **${{ number_format($order->total_purchase, 2) }}** |
| **Monto Pendiente** | **${{ number_format($order->remaining_amount, 2) }}** |
</x-mail::table>

<x-mail::panel>
**Estado del pedido:** En verificación.
</x-panel>

Para poder procesar tu compra, es necesario que realices un **nuevo reporte de pago** con la información correcta. Puedes hacerlo directamente a través del siguiente enlace:

<x-mail::button :url="route('pago', ['token' => $order->token_segundo_pago ?? 'new'])">
Reportar pago nuevamente
</x-mail::button>

**Adjunto a este correo encontrarás tu Nota de Entrega en formato PDF** con todos los detalles de tu compra. 

---
### ¿Tienes alguna duda?
Si necesitas ayuda con tu pedido, nuestro equipo está listo para atenderte:

<x-mail::button :url="'https://wa.me/573114756873'" color="success">
Contáctanos por WhatsApp
</x-mail::button>

Gracias por tu comprensión,<br>
{{ config('app.name') }}

<x-slot:subcopy>
Si ya realizaste un nuevo reporte o consideras que esto es un error, por favor omite este correo o contáctanos para asistirte personalmente.
</x-slot:subcopy>
</x-mail::message>
