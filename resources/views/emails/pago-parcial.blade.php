<x-mail::message>
# ¡Hola, <span style="text-transform: capitalize;">{{ $order->client->names }}</span>!

Hemos recibido correctamente el reporte de tu **pago inicial del 50%**.

Tu pedido ha sido registrado y entrará en **proceso de verificación**. Recuerda que para completar la entrega, debes cancelar el monto restante antes de la fecha de vencimiento.

**Resumen de la operación:**

<x-mail::table>
| Concepto | Información |
| :--- | :--- |
| **Fecha** | {{ \Carbon\Carbon::parse($order->order_date)->locale('es')->isoFormat('LL') }} |
| **Monto Total** | **${{ number_format($order->total_purchase, 2) }}** |
| **Monto Pagado** | **${{ number_format($order->total_paid, 2) }}** |
| **Monto Pendiente** | **${{ number_format($order->remaining_amount, 2) }}** |
| **Fecha Límite** | **{{ \Carbon\Carbon::parse($order->payment_deadline)->locale('es')->isoFormat('LL') }}** |
</x-mail::table>

<x-mail::button :url="route('pago', ['token' => $token])">
Pagar el 50% Restante
</x-mail::button>

Si tienes problemas con el botón, copia y pega el siguiente enlace en tu navegador:<br>
[{{ route('pago', ['token' => $token]) }}]({{ route('pago', ['token' => $token]) }})

**Adjunto a este correo encontrarás tu Nota de Entrega en formato PDF** con todos los detalles de tu compra. 
> **Descarga y guarda este recibo como soporte de tu pago.**

---
### ¿Tienes alguna duda?
Si necesitas ayuda con tu pedido, nuestro equipo está listo para atenderte:

<x-mail::button :url="'https://wa.me/573114756873'" color="success">
Contáctanos por WhatsApp
</x-mail::button>

Gracias por confiar en nosotros,<br>
{{ config('app.name') }}

<x-slot:subcopy>
    Este correo confirma la recepción de tu pago inicial del 50%. La aprobación final está sujeta a la validación de la transacción bancaria. El documento adjunto es un comprobante digital de tu operación.
</x-slot:subcopy>
</x-mail::message>
