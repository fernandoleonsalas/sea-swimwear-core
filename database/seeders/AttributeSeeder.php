<?php

namespace Database\Seeders; // <-- 1. Añade el namespace

use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Database\Seeder;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attributesData = [
            'Talla' => ['XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL', '2XL', '3XL', '4XL', '5XL', 'UNICA'],

            'Color' => [
                'Negro' => '#000000', 'Blanco' => '#FFFFFF', 'Gris' => '#808080', 'Rojo' => '#FF0000',
                'Azul' => '#0000FF', 'Verde' => '#008000', 'Amarillo' => '#FFFF00', 'Naranja' => '#FFA500',
                'Rosa' => '#FFC0CB', 'Violeta' => '#EE82EE', 'Marrón' => '#46220F', 'Púrpura' => '#800080',
                'Turquesa' => '#40E0D0', 'Plateado' => '#C0C0C0', 'Dorado' => '#FFD700', 'Fucsia' => '#FF00FF',
                'Lila' => '#C8A2C8', 'Beige' => '#F5F5DC', 'Celeste' => '#ADD8E6', 'Caqui' => '#F0E68C',
            ],

            'Estampado' => [
                'Ninguno',        // Para franelas de color sólido
                'Rayas',          // Estampado simple (ej. Rayas Negras)
                'Geométrico',     // Estampado más complejo
                'Oveja',          // Estampado único (el ejemplo que mencionaste)
                'Bicolor'         // Para distinguir un diseño de dos colores (ej. Rojo y Negro)
            ],
        ];

        foreach ($attributesData as $attributeName => $values) {
            $attribute = Attribute::firstOrCreate(['name' => $attributeName]);

            // Determinar si es el atributo 'Color'
            $isColorAttribute = ($attributeName === 'Color');

            // Adaptamos el bucle para manejar el array asociativo de Colores
            $dataToIterate = $isColorAttribute ? $values : array_combine($values, array_fill(0, count($values), null));

            foreach ($dataToIterate as $value => $colorCode) {
                
                // Si es el atributo 'Color', $colorCode tendrá el valor hexadecimal. Si no es 'Color', $colorCode será null.
                
                $dataToCreate = [
                    'attribute_id' => $attribute->id,
                    'value' => $value,
                    // Condición: Asigna $colorCode solo si es el atributo 'Color',
                    // de lo contrario, Laravel lo tomará como null (que ya está permitido).
                    'color_code' => $isColorAttribute ? $colorCode : null, 
                ];

                AttributeValue::firstOrCreate($dataToCreate);
            }
        }
    }
}