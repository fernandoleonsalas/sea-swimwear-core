<x-mail::message>
# ✅ ¡Abono del 50% Verificado!

Hola, **<span style="text-transform: capitalize;">{{ $order->client->names }}</span>**.

Hemos verificado correctamente tu **pago inicial**. Tu pedido ha sido registrado con éxito y ya está en proceso de preparación.

**Resumen del Abono:**

<x-mail::table>
| Concepto | Información |
| :--- | :--- |
| **Fecha** | {{ \Carbon\Carbon::parse($order->order_date)->locale('es')->isoFormat('LL') }} |
| **Monto Total** | **${{ number_format($order->total_purchase, 2) }}** |
| **Monto Pagado** | **${{ number_format($order->total_paid, 2) }}** |
| **Monto Pendiente** | **${{ number_format($order->remaining_amount, 2) }}** |
| **Fecha Límite** | **{{ \Carbon\Carbon::parse($order->payment_deadline)->locale('es')->isoFormat('LL') }}** |
</x-mail::table>

<x-mail::button :url="route('pago', ['token' => $order->token_segundo_pago])">
Pagar el 50% Restante
</x-mail::button>

Si tienes problemas con el botón, copia y pega el siguiente enlace en tu navegador:<br>
[{{ route('pago', ['token' => $order->token_segundo_pago]) }}]({{ route('pago', ['token' => $order->token_segundo_pago]) }})

*Nota: Es necesario completar el pago total para proceder con el envío final.*

---
### ¿Tienes alguna duda?
Si necesitas ayuda con tu pedido, nuestro equipo está listo para atenderte:

<x-mail::button :url="'https://wa.me/573114756873'" color="success">
Contáctanos por WhatsApp
</x-mail::button>

Gracias por confiar en nosotros,<br>
{{ config('app.name') }}

<x-slot:subcopy>
    Este correo confirma el pago inicial del 50%. La aprobación  está sujeta a la validación de la transacción bancaria.
</x-slot:subcopy>

</x-mail::message>
