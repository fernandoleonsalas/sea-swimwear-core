<!-- Está es la Vista catalogo minorista -->
<!-- Utiliza el componente o plantilla base-main.blade.php y Utiliza el componente Livewire catalogo-mayorista.blade.php -->
<x-base-main title="Carrito De Compra" type="catalogo-carrito">
    {{-- el componente Livewire: detalle de la compra del usuario --}}
    <div>
        @livewire('detalle-de-compra')
    </div>
</x-base-main>
