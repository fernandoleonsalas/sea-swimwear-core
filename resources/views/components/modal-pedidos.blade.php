<!-- Modal informacion del pedido -->
<div id="pedido-info-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-lg max-h-full">
        <!-- Modal content -->
        <div class="relative bg-neutral-primary-soft border border-default rounded-lg shadow-sm p-4 md:p-6 bg-gray-200">
            <!-- Modal header -->
            <div class="flex items-center justify-between border-b border-gray-300 pb-2 md:pb-3">
                <h3 class="text-lg font-extrabold text-gray-900 dark:text-white">
                    <span class="flex text-transparent bg-clip-text bg-gradient-to-r to-gray-700 from-black">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#000" class="icon icon-tabler icons-tabler-filled icon-tabler-user mr-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 2a5 5 0 1 1 -5 5l.005 -.217a5 5 0 0 1 4.995 -4.783z" /><path d="M14 14a5 5 0 0 1 5 5v1a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-1a5 5 0 0 1 5 -5h4z" /></svg>
                        Detalle del Pedido
                    </span>
                </h3>
                <button type="button" title="Cerrar modal" class="text-body bg-transparent hover:bg-neutral-tertiary hover:text-heading rounded-base text-sm w-9 h-9 ms-auto inline-flex justify-center items-center text-gray-600 hover:bg-gray-200 hover:text-gray-900 hover:cursor-pointer" data-modal-hide="pedido-info-modal">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/></svg>
                    <span class="sr-only">Cerrar modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <div class="space-y-2 md:space-y-6 py-4 md:py-6">
                <!-- 1. FECHA Y HORA DEL PEDIDO -->
                <div class="border-b pb-4">
                    <h2 class="text-xl font-semibold text-gray-700 mb-2">Información General</h2>
                    <div class="flex flex-col sm:flex-row sm:justify-between text-gray-600">
                        <!-- Accede al índice 0 para la fecha -->
                        <p>
                            <span class="font-medium text-gray-900">Fecha:</span> 
                            <span x-text="modalPedidoData.detalle.fecha_pedido?.[0] ?? ''"></span>
                        </p>
                        <!-- Accede  al índice 1 y limpia el prefijo "Hora: " -->
                        <p>
                            <span class="font-medium text-gray-900">Hora:</span> 
                            <span x-text="modalPedidoData.detalle.fecha_pedido?.[1].replace('Hora: ', '') ?? ''"></span>
                        </p>
                    </div>
                </div>
                <!-- 2. PRODUCTOS DETALLE POR GRUPO PADRE Y VARIANTES (Bucle Anidado) -->
                <div class="space-y-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-2">Productos:</h2>
                    <template x-for="([grupoNombre, variantesArray], indexGrupo) in Object.entries(modalPedidoData.detalle?.listaVariantes ?? {})" :key="indexGrupo">
                        <div class="bg-gray-50 p-4 rounded-lg shadow-sm border border-gray-200">
                            <!-- Muestra el Nombre del Grupo Padre -->
                            <h3 class="text-lg font-bold text-indigo-600 mb-3 border-b pb-2" x-text="grupoNombre"></h3>
                            <!-- Muestra detalle de los productos -->
                            <div class="space-y-3">
                                <!-- BUCLE ANIDADO (VARIANTES): Itera sobre el array de variantes dentro del grupo actual -->
                                <template x-for="(variante, indexVariante) in variantesArray" :key="`${indexGrupo}-${indexVariante}`">
                                    <div class="flex justify-between items-center bg-white p-3 rounded-md border">
                                        <!-- Columna de Detalles: Cantidad, Nombre y SKU -->
                                        <div class="flex flex-col">
                                            <span class="text-base font-medium text-gray-900" x-text="`${variante.cantidad}x $${variante.precio_unitario_aplicado}`"></span>
                                            <span class="text-xs text-gray-500 font-mono" x-text="`SKU: ${variante.sku_completo}`"></span>
                                        </div>
                                        <!-- Columna de Precio calculado -->
                                        <span class="text-base font-semibold text-gray-800" 
                                        x-text="`$${(variante.cantidad * variante.precio_unitario_aplicado).toFixed(2)}`"></span>
                                    </div>
                                </template>
                            </div> 
                        </div>
                    </template>
                </div>
                <!-- 3. TOTALES: Subtotal, Envío y Monto Total -->
                <div class="pt-6 mt-6 border-t border-gray-300 space-y-2">
                    
                    <!-- Subtotal -->
                    <div class="flex justify-between text-lg text-gray-700">
                        <span>Subtotal:</span>
                        <!-- Acceso al subtotal dentro del objeto totales, que está dentro de detalle -->
                        <span class="font-medium" x-text="`$${Number(modalPedidoData.detalle?.Subtotal ?? 0).toFixed(2)}`"></span>
                    </div>

                    <!-- Precio del Envío -->
                    <div class="flex justify-between text-lg text-gray-700">
                        <span>Costo de Envío:</span>
                        <!-- Acceso al envío dentro del objeto totales, que está dentro de detalle -->
                        <span class="font-medium" x-text="`$${Number(modalPedidoData.detalle?.costo_envio ?? 0).toFixed(2)}`"></span>
                    </div>

                    <!-- Monto Total -->
                    <div class="flex justify-between border-t-2 border-indigo-100 text-xl font-extrabold text-[#3b5998e6]">
                        <span>Monto Total:</span>
                        <!-- Acceso al monto total dentro del objeto totales, que está dentro de detalle -->
                        <span x-text="`$${Number(modalPedidoData.detalle?.compra_total ?? 0).toFixed(2)}`"></span>
                    </div>
                </div>
            </div>
            <!-- Modal footer -->
            <div class="flex justify-center items-center border-t border-gray-300 space-x-4 pt-4 md:pt-5">
                <button  type="button"  data-modal-hide="pedido-info-modal"
                class="inline py-2.5 px-5 text-sm mb-2 
                bg-black text-white w-full sm:w-auto items-center justify-center rounded-lg border border-black font-medium  hover:bg-white hover:text-black focus:z-10 focus:outline-none focus:ring-3 focus:ring-gray-600 focus:bg-white focus:text-black cursor-pointer">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>