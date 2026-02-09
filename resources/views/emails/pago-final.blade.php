<x-mail::message>
# ¡Hola, <span style="text-transform: capitalize;">{{ $order->client->names }}</span>!

Hemos recibido correctamente el reporte de tu **pago total (100%)**.

Tu pedido ha sido registrado exitosamente y ahora **entrará en proceso de verificación**. Una vez validada la información, procederemos con la preparación y envío de tu compra.

**Resumen de la operación:**

<x-mail::table>
| Concepto | Información |
| :--- | :--- |
| **Fecha** | {{ \Carbon\Carbon::parse($order->order_date)->locale('es')->isoFormat('LL') }} |
| **Monto Total** | **${{ number_format($order->total_purchase, 2) }}** |
| **Monto Pagado** | **${{ number_format($order->total_paid, 2) }}** |
| **Monto Pendiente** | **${{ number_format($order->remaining_amount, 2) }}** |
</x-mail::table>

<x-mail::panel>
**Estado del pedido:** En verificación.
</x-panel>

**Adjunto a este correo encontrarás tu Nota de Entrega en formato PDF** con todos los detalles de tu compra. 
> **Descarga y guarda este recibo como soporte de tu pago.**

---
### ¿Tienes alguna duda?
Si necesitas ayuda con tu pedido, nuestro equipo está listo para atenderte:

<x-mail::button :url="'https://wa.me/573114756873'" color="success">
Contáctanos por WhatsApp
</x-mail::button>

Gracias por tu confianza,<br>
{{ config('app.name') }}

<x-slot:subcopy>
Este correo confirma la recepción de tu pago. La aprobación final está sujeta a la validación de la transacción bancaria. El documento adjunto es un comprobante digital de tu operación.
</x-slot:subcopy>
</x-mail::message>
