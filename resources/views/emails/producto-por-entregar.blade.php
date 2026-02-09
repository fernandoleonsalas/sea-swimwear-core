<x-mail::message>
# 🚀 ¡Tu pedido ya está en camino!

Hola, **<span style="text-transform: capitalize;">{{ $order->client->names }}</span>**.

¡Buenas noticias! Tu pedido ha sido preparado con éxito y ya se encuentra en la **etapa de entrega**. Queremos agradecerte por completar tu pago; ahora nosotros nos encargamos de que tus productos lleguen seguros a tus manos.

**Detalles de la Entrega:**

<x-mail::table>
| Concepto | Información |
| :--- | :--- |
| **Fecha de Envío** | {{ \Carbon\Carbon::now()->locale('es')->isoFormat('LL') }} |
| **Estado del Pago** | **Totalmente Pagado ✅** |
| **Monto Total** | **${{ number_format($order->total_purchase, 2) }}** |
| **Dirección** | {{ $order->shipping_address ?? 'Registrada en sistema' }} |
</x-mail::table>


*Nota: Los tiempos de entrega pueden variar según tu ubicación y la transportadora.*

---

### ¿Necesitas ayuda adicional?
Si tienes alguna pregunta sobre el proceso de entrega o quieres confirmar algún dato, nuestro equipo está a un clic de distancia:

<x-mail::button :url="'https://wa.me/573114756873'" color="success">
Chat de Soporte WhatsApp
</x-mail::button>

¡Disfruta tu compra!<br>
{{ config('app.name') }}

<x-slot:subcopy>
    Este correo confirma que tu pedido ha finalizado su etapa de producción y pago, y ha sido entregado a la empresa de logística para su distribución.
</x-slot:subcopy>

</x-mail::message>