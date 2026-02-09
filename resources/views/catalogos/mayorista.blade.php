<!-- Está es la Vista catalogo mayorista -->
<!-- Utiliza el componente o plantilla base-main.blade.php y Utiliza el componente Livewire catalogo-mayorista.blade.php -->
<x-base-main title="Catálogo Mayoristas" type="catalogo-mayorista">
    {{-- Contenido del SLOT POR DEFECTO va aquí --}}
    {{-- Aquí va el componente Livewire Logica: catalgoo mayorista --}}
    <div class="w-full">
        @livewire('catalogo-mayorista', ['seccionCatalogo' => "mayorista"])
    </div>
    {{-- el componente Livewire: carrito de compra --}}
    <div>
        @livewire('show-car')
    </div>
</x-base-main>
