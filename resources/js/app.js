// 1. IMPORTACIONES
import './bootstrap';
// 2. Flowbite necesita ser importado si lo instalaste vía npm
import 'flowbite'; 
import { Modal } from 'flowbite'; // Importamos la clase Modal explícitamente
// 3. Importa los estilos de Tom Select
import 'tom-select/dist/css/tom-select.css'; 
// 4. Importar Alpine.js 
import Alpine from 'alpinejs'; 
import focus from '@alpinejs/focus';
import collapse from '@alpinejs/collapse' 
// 6. Importa la clase  tom-select
import TomSelect from 'tom-select';
// 7. CONFIGURACIÓN GLOBAL
Alpine.plugin(focus);
Alpine.plugin(collapse)
window.Alpine = Alpine; 
window.TomSelect = TomSelect;
// 8. LÓGICA DE LIVEWIRE/FLOWBITE para abrir los modal con flowbite  🔑
document.addEventListener('livewire:initialized', () => {
    // Obtenemos el elemento de un modal en especifico una sola vez permite saber si existe en la página actual
    const modalElement = document.getElementById('editar-modal-p');
    const modalElementRerpote = document.getElementById('reporte-pago-modal');

    // 1. Crear un array con las referencias a los posibles elementos modales
    const modalElements = [modalElement, modalElementRerpote];

    // 2. Iterar sobre el array y ejecutar la lógica de inicialización solo si el elemento existe
    modalElements.forEach(element => {
        // ⚠️ Importante: Solo ejecuta el código si el elemento actual (modal) existe en la página
        if (element) {
            // Inicializamos la instancia de Flowbite Modal
            const flowbiteModal = new Modal(element);

            // Escuchar el evento 'open-modal' emitido desde el componente PHP para ESTE modal
            // NOTA: Si necesitás abrir/cerrar modales específicos, deberías usar eventos distintos
            // (e.g., 'open-editar-modal', 'open-reporte-modal') y escucharlos aquí.
            Livewire.on('open-modal', () => {
                // Se asume que 'open-modal' abre el primer modal que se encuentra o solo uno.
                // Para la mayoría de los casos de uso, es mejor usar eventos específicos.
                flowbiteModal.show();
            });

            // Opcionalmente, escuchar el evento 'close-modal' para cerrar
            Livewire.on('close-modal', () => {
                flowbiteModal.hide();
            });
        }
    });
    // Obtenemos el elemento del modal cliente. (Se usa para saber si la página actual lo necesita)
    const modalInforCliente = document.getElementById('cliente-info-modal');
    // Importante: Solo ejecuta el código si el modal cliente existe en la página actual
    if (modalInforCliente) {
        // 2. Lógica para Reinicializar Componentes Flowbite DINÁMICOS
        Livewire.hook('morph.updated', ({ el, component }) => {
            if (typeof initFlowbite === 'function') {
                // Reinicializa Flowbite SOLO dentro del elemento (el) que Livewire actualizó.
                initFlowbite(el); 
            }
        });
    }
});

// 🚨 NO Alpine.start() aquí si Livewire lo está haciendo automáticamente
