 <!-- Modal informacion del cliente -->
<div id="cliente-info-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <!-- Modal content -->
        <div class="relative bg-neutral-primary-soft border border-default rounded-lg shadow-sm p-4 md:p-6 bg-gray-200">
            <!-- Modal header -->
            <div class="flex items-center justify-between border-b border-gray-300 pb-2 md:pb-3">
                <h3 class="text-lg font-extrabold text-gray-900 dark:text-white">
                    <span class="flex text-transparent bg-clip-text bg-gradient-to-r to-gray-700 from-black">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#000" class="icon icon-tabler icons-tabler-filled icon-tabler-user mr-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 2a5 5 0 1 1 -5 5l.005 -.217a5 5 0 0 1 4.995 -4.783z" /><path d="M14 14a5 5 0 0 1 5 5v1a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-1a5 5 0 0 1 5 -5h4z" /></svg>
                        Información del cliente
                    </span>
                </h3>
                <button type="button" title="Cerrar modal" class="text-body bg-transparent hover:bg-neutral-tertiary hover:text-heading rounded-base text-sm w-9 h-9 ms-auto inline-flex justify-center items-center text-gray-600 hover:bg-gray-200 hover:text-gray-900 hover:cursor-pointer" data-modal-hide="cliente-info-modal">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/></svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <div class="space-y-2 md:space-y-6 py-4 md:py-6 ">
                <p class="py-1 my-1"><b class="font-bold">Cédula de Identidad:</b></p>
                <p class="text-gray-700" x-text="modalData.cedula"></p>
                <p class="py-1 my-1"><b class="font-bold">Nombre:</b></p>
                <p class="text-gray-700 capitalize" x-text="modalData.nombre"></p>
                <p class="py-1 my-1"><b class="font-bold">Correo Electrónico:</b></p>
                <p class="text-gray-700" x-text="modalData.correo"></p>
                <p class="py-1 my-1"><b class="font-bold">Teléfono:</b></p>
                <p class="text-gray-700" x-text="modalData.telefono"></p>
                <p class="py-1 my-1"><b class="font-bold">Dirección de Habitación:</b></p>
                <p class="text-gray-700 capitalize" x-text="modalData.direccion"></p>
            </div>
            <!-- Modal footer -->
            <div class="flex justify-center items-center border-t border-gray-300 space-x-4 pt-4 md:pt-5">
                <button  type="button"  data-modal-hide="cliente-info-modal"
                class="inline py-2.5 px-5 text-sm mb-2 
                bg-black text-white w-full sm:w-auto items-center justify-center rounded-lg border border-black font-medium  hover:bg-white hover:text-black focus:z-10 focus:outline-none focus:ring-3 focus:ring-gray-600 focus:bg-white focus:text-black cursor-pointer">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>