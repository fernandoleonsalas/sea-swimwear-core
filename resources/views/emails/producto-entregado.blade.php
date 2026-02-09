<x-mail::message>
# 📦 ¡Pedido Entregado con Éxito!

Hola, **<span style="text-transform: capitalize;">{{ $order->client->names }}</span>**.

¡Es un gusto saludarte! El sistema nos informa que **tu pedido ha sido entregado** correctamente en su destino final. Esperamos que disfrutes de tu compra y que el producto supere tus expectativas.

---

### ¿Algo no está bien?
Si el paquete presenta alguna novedad, no dudes en contactarnos de inmediato para darte una solución:

<x-mail::button :url="'https://wa.me/573114756873'" color="success">
Soporte Post-Venta WhatsApp
</x-mail::button>

Gracias por elegirnos y confiar en nuestro trabajo,<br>
El equipo de **{{ config('app.name') }}**

<x-slot:subcopy>
    Has recibido este correo porque el sistema de mensajería ha marcado tu pedido como entregado. Si no has recibido tu paquete, por favor contáctanos de inmediato.
</x-slot:subcopy>

</x-mail::message>
