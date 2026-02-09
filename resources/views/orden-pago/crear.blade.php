<!-- Está es la Vista del formulario orden de pago -->
<!-- Utiliza el componente o plantilla base-main.blade.php y Utiliza el componente Livewire -->
<x-base-main title="Carrito De Compra" type="catalogo-carrito">
    {{-- el componente Livewire: formulario de orden pago --}}
    <div>
        @livewire('orden-pago')
    </div>
     {{-- el componente Livewire: carrito de compra --}}
    <div>
        @livewire('show-car')
    </div>
</x-base-main>
