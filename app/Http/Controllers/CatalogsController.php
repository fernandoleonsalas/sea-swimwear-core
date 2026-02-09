<?php

namespace App\Http\Controllers;

use App\Models\Product; // uso del modelo Product
use App\Models\configurations; // uso del modelo configurations
use Illuminate\Support\Facades\Storage; // Importar para usar Storage::url

class CatalogsController extends Controller
{
    // 1. Metodo-Mostrar: catálogo minorista:
    public function catalogoMin(){
        // return "Bienvenido a la página principal de los productos";
        return view("catalogos.minorista");
    }
    // 2. Metodo-Mostrar: catálogo mayorista:
    public function catalogoMay(){
        // return "Bienvenido a la página principal de los productos";
        return view("catalogos.mayorista");
    }
    // 3. Metodo-Mostrar: la página de detalles de un producto, cargando todas sus variantes y transformando los datos necesarios para la vista (colores, tallas, materiales, matriz de stock).
    public function verVariantes(Product $id, $cat): mixed // Buscar un registro en la base de datos por su ID
    {
        // 1. Va indicar si el contenido principal ya se cargó o no. True = si , False = no.    
        $ContenidoCargado = false; 

        // --- VALIDAR: Sí el producto seleccionado procede del catálogo mayorista. Asegurar que dicho producto tenga un precio mayorista asignado y la cantidad mínima de compra (piezas)
        if ($cat == "mayorista" && $id->min_pieces != null && $id->price_wholesale != null) {   
            $min_piezas = $id->min_pieces;
        }else if ($cat == "minorista" && $id->price_retail != null) { // Si procede del catálogo minorista. Asegurar que tiene precio al detal 
            $min_piezas = 1;
        }else{
            return redirect()->back(); // Retorna a la URL anterior
        }

        /* 2. Carga las relaciones necesarias para el producto: variantes, valores de atributos y los atributos de esos valores (para acceder al nombre 'Color', 'Talla', etc.),
        además de la relación 'image' en cada variante (tabla images via image_id). */
        $id->load([
            'variants' => function ($query) {
                // Excluir variantes inactivas
                $query->where('status', 'active'); 
                // Excluir variantes sin stock (stock debe ser >= 1)
                $query->where('stock', '>=', 1); 
                // Carga de sub-relaciones que no necesitan filtros adicionales
                $query->with('attributeValues.attribute', 'image'); 
            }
        ]);

        // 3. Obtener la colección de TODAS las variantes visibles del producto (filtradas por load()). Si el producto no tiene variantes visibles, esta colección estará vacía.
        $variants = $id->variants; 

        // VALIDAR: Si el producto no tiene variantes visibles, redirigir a la página anterior
        if ($variants->isEmpty()) {
            return redirect()->back(); // Retorna a la URL anterior
        }

        /* --- LOGICA Extracción de Atributos Únicos ---
        // 4.1 Extraer Color: todos los valores de atributo cuyo 'attribute->name' sea 'Color'. flatMap itera sobre variantes y aplana los resultados de los valores de atributo. */
        $listaColores = $variants->flatMap(fn($v) => $v->attributeValues
            // 1. Filtrar SOLO los valores de atributo con nombre 'Color'
            ->filter(fn($av) => optional($av->attribute)->name === 'Color'))
            // 2. La colección resultante de flatMap es una mezcla de todos los AttributeValues de color
            // 3. Ahora Mapeamos para quedarnos SOLO con 'value' y 'color_code'
            ->map(function ($av) {
                return [
                    'value'      => $av->value,
                    'color_code' => $av->color_code,
                ];
            })
            // 4. Aplicamos unique() a la COLECCIÓN GLOBAL. Usamos 'value' para determinar la unicidad
            ->unique('value') 
            // 5. Ordenamos y reseteamos las claves
            ->sortBy('value')
            ->values() 
            // Convierte la colección final en un array de PHP.
            ->all();

        // 4.2 Extraer Color + Estampado:
        $listaColoresYEstampados = $variants->map(function ($variant) {
            // 1. Obtener los valores de "Color" y "Estampado"
            $color = $variant->attributeValues
                ->firstWhere(fn($av) => optional($av->attribute)->name === 'Color')['value'] ?? null;
            $estampado = $variant->attributeValues
                ->firstWhere(fn($av) => optional($av->attribute)->name === 'Estampado')['value'] ?? null;
            // 2. Determinar los elementos finales para la combinación
            $resultado = [];
            // Incluir Color si existe.
            if ($color) {
                $resultado[] = $color;
            }
            // Incluir Estampado SOLO si existe y NO es 'Ninguno'.
            if ($estampado && strtolower($estampado) !== 'ninguno') {
                $resultado[] = "(".$estampado.")";
            }
            // 3. Unir los elementos en una cadena.
            return implode(' ', $resultado);
        })
        ->filter() // Quita las variantes que resulten en cadenas vacías (sin color ni estampado)
        ->unique()
        ->values()
        ->all(); // Convierte a array PHP simple

        // 4.3 Extraer Estampado: todos los valores de atributo cuyo 'attribute->name' sea 'Estampado'.
        $listaEstampado = $variants->flatMap(fn($v) => $v->attributeValues 
            ->filter(fn($av) => optional($av->attribute)->name === 'Estampado')
            ->pluck('value') // Obtiene solo los valores (ej: ['Rayas', 'Liso', 'Rayas'])
        )
        ->filter(fn($value) => !in_array(strtolower($value), ['ninguno']))
        ->unique()->values()->all();


        // 4.4 Extraer Talla: todos los valores únicos de atributo cuyo nombre sea 'Talla'.
        $listaTallas = $variants->flatMap(fn($v) => $v->attributeValues
            ->filter(fn($av) => optional($av->attribute)->name === 'Talla')
            ->pluck('value')
        )->unique()->values()->all();
        
        // 4.5 Extraer Talla: Extrae todos los valores únicos de atributo cuyo nombre sea 'Material'.
        $listaMateriales = $variants->flatMap(fn($v) => $v->attributeValues
            ->filter(fn($av) => optional($av->attribute)->name === 'Material')
            ->pluck('value')
        )->unique()->values()->all();


        /* --- Preparación de Slides para Carrusel ---
        Agrupamos las variantes por la combinación Estampado + Color para que cada botón del carrusel muestre ambos atributos. 
        Usamos la relación 'image' para obtener la URL desde la tabla `images` (campo main_image_url). */
        $slides = $variants
            ->groupBy(function($v) {
                $est = $v->attributeValues->firstWhere('attribute.name','Estampado')->value ?? null;
                $col = $v->attributeValues->firstWhere('attribute.name','Color')->value ?? null;
                $codCol = $v->attributeValues->firstWhere('attribute.name','Color')->color_code	 ?? null;
                return $est.'|'.$col.'|'.$codCol; // clave compuesta
            })
            ->map(function ($group, $key) {
                // Tomamos la primera variante del grupo para obtener la imagen representativa
                $v = $group->first();

                // Construir label legible: "Estampado - Color"
                [$est, $col, $codCol] = explode('|', $key) + ['', '', ''];
                $label = trim(($col ?: '').' '.($est == "Ninguno" ? "" : $est));

                // Obtener la ruta de la imagen desde la relación image (fallback a placeholder)
                $imgPath = optional($v->image)->main_image_url;
                $imgSrc = $imgPath ? Storage::url($imgPath) : Storage::url('default-placeholder.jpg');

                return [
                    'imgSrc' => $imgSrc,
                    'imgAlt' => "Imagen de la variante SKU: {$v->full_sku}",
                    'label'  => $label,
                    'codigo'  => $codCol,
                ];
            })
            ->values()
            ->all();
            
        // --- Construcción de la Matriz de Variantes (Color x Talla) ---
        // Crea una matriz asociativa para mapear rápidamente la combinación Color/Talla a los datos de la variante.
        $matrix = [];
        foreach ($variants as $v) {
            // Busca el Color y la Talla de la variante actual.
            $c = $v->attributeValues->firstWhere('attribute.name','Color')->value ?? null;
            $s = $v->attributeValues->firstWhere('attribute.name','Talla')->value ?? null;
            $m = $v->attributeValues->firstWhere('attribute.name','Material')->value ?? null;
            $est = $v->attributeValues->firstWhere('attribute.name','Estampado')->value ?? null;

            // Construir la clave principal: si hay estampado válido, unirlo a color
            // Ej: "Negro (Rayas)", si no hay estampado simplemente "Negro".
            $colorKey = $c;

            if ($est && !in_array(strtolower(trim($est)), ['ninguno','sin estampado','none',''])) {
                $colorKey = trim($c . ' (' . $est . ')');
            }
            
            // Si la variante tiene ambos atributos (color y talla), se registra en la matriz.
            if ($c && $s) {
                // Estructura: $matrix[Color][Talla] = [datos de la variante]
                $matrix[$colorKey][$s] = [
                    'variant_id' => $v->id,
                    'sku' => $v->full_sku,
                    'stock' => $v->stock ?? 0,
                ];
            }
            // // Si la variante tiene ambos atributos (color y m), se registra en la matriz.
            else if ($c && $m) {
                // Usamos material como índice de fallback para la segunda dimensión cuando no hay talla.
                $index = $s ?? $m;
                $matrix[$colorKey][$index] = [
                    'variant_id' => $v->id,
                    'sku' => $v->full_sku,
                    'stock' => $v->stock ?? 0,
                ];
            }
        }

        // consultar la tasa del dolar
        $consultarTasa = configurations::where('key', 'tasaDolar')->select(['value'])->first();

        // Retorna la vista Blade 'catalogos.variantes', pasando todos los datos.
        return view('catalogos.variantes', compact('ContenidoCargado','id','slides','listaColores','listaEstampado','listaTallas','listaMateriales','listaColoresYEstampados','matrix', 'min_piezas','cat','consultarTasa'));
    }
    // 4. Metodo-Mostrar: detalle del carrito de compra:
    public function detalleCarrito(){
        // return "Bienvenido a la página principal de los productos";
        return view("catalogos.detalle-carrito");
    }
}
