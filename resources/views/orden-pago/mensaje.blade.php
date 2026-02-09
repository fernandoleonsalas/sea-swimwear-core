<!-- Está es la Vista del formulario orden de pago -->
<!-- Utiliza el componente o plantilla base-main.blade.php y Utiliza el componente Livewire -->
<x-base-main title="Compra Finalizada" type="compra-finalizada">
    <div class="mt-12 flex justify-center align-items-center items-center">
        <div class="bg-white card-gradient p-10 rounded-xl max-w-lg w-full shadow-2xl">
            <div class="text-center py-5">
                <svg class="mx-auto h-22 w-22 text-yellow-700 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg> 

                <h1 class="text-4xl font-extrabold text-gray-800 mb-4 tracking-tight">¡Reporte de Pago Recibido!</h1>

                <p class="text-xl text-yellow-800 mb-6">
                    Tu transacción está pendiente de verificación.
                </p>

                <p class="text-lg text-gray-600 leading-relaxed mb-6">
                    Hemos registrado exitosamente tu <b>Reporte de Pago</b>. 
                    <br>
                    Nuestro departamento administrativo procederá a <b>validar</b> el comprobante a la mayor brevedad. 

                    @if ($aviso)
                        <br><br>
                        <b>Recuerda</b> que para completar la entrega, debes cancelar el monto restante antes de la <b>fecha de vencimiento ({{ $aviso }}).</b> 
                    @endif
                    <br><br>
                    Te enviaremos una notificación oficial a tu correo electrónico confirmando la <b>aceptación o rechazo</b> de tu pago.
                </p>

                <div class="mt-8 pt-8 border-t border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">¿Tienes alguna duda acerca de tu pago?</h3>
                    
                    <div class="flex flex-col gap-3">
                        <a href="https://wa.me/573114756873?text={{ urlencode('Hola! Tengo una duda con mi reporte de pago.') }}" 
                        target="_blank"
                        class="flex items-center justify-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg transition duration-300 shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            WhatsApp
                        </a>

                        <a href="mailto:seaswinwear.vzla@gmail.com?subject={{ urlencode('Duda sobre mi reporte de pago') }}" 
                        class="flex items-center justify-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold rounded-lg transition duration-300 shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            Enviar Correo
                        </a>
                    </div>
                </div>

                <div class="mt-8 italic text-gray-500 text-sm">
                    Agradecemos su comprensión.
                </div>
            </div>
        </div>
    </div>
</x-base-main>
