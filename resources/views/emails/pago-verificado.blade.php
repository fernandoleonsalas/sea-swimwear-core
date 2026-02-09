<x-mail::message>
# ✅ ¡Pago verificado, <span style="text-transform: capitalize;">{{ $order->client->names }}</span>!

Te confirmamos que hemos **validado exitosamente tu pago** por un monto de <strong>${{ number_format($order->total_purchase, 2) }}</strong>.

Tu transacción ha sido aprobada y tu pedido ya se encuentra en nuestro sistema. A partir de este momento, **toda la información sobre el estado de tu envío y los detalles logísticos te serán comunicados directamente vía WhatsApp**.

<x-mail::panel>
**Estado actual:**
* **Pago:** Verificado y Liquidado.
* **Seguimiento:** Vía WhatsApp.
</x-panel>

Mantente atento a tus mensajes, pronto nos pondremos en contacto contigo por ese medio.
---

### ¿Tienes alguna duda?
Si necesitas ayuda con tu pedido, nuestro equipo está listo para atenderte:

<x-mail::button :url="'https://wa.me/573114756873'" color="success">
Contáctanos por WhatsApp
</x-mail::button>

Gracias por tu confianza,<br>
{{ config('app.name') }}

<x-slot:subcopy>
Este correo es una confirmación automática de validación de pago. Si tienes alguna duda inmediata, puedes escribirnos a nuestra línea de atención al cliente.
</x-slot:subcopy>
</x-mail::message>
