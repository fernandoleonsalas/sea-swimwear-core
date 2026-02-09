<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str; // Importar la clase de ayuda para Slugs

class Category extends Model
{
    // Permitir la asignación masiva para los campos 'name' y 'slug' de la tabla categories.
    protected $fillable = [ "name","slug"];

    // Mutator de Evento 'creating' (Método que se ejecuta antes de guardar)
    protected static function booted()
    {
        // El evento 'creating' se dispara antes de que el modelo se inserte por primera vez (Category::create)
        static::creating(function ($category) {
            // Verifica que el nombre exista y que el slug no se haya asignado manualmente.
            if (empty($category->slug) && !empty($category->name)) {
            // Genera el slug a partir del nombre
                $category->slug = Str::slug($category->name);
            }
        });
        // El evento 'updating' se dispara antes de que el modelo se actualice (Category::update)... el slug cambia si el nombre de la categoria cambia
        static::updating(function (Category $category) {
            if ($category->isDirty('name')) { // Si el campo 'name' ha sido modificado
                $category->slug = Str::slug($category->name);
            }
        });
    }

    // Relación N:M:  Una categoría puede tener muchos productos (Relación Muchos a Muchos
    // a través de la tabla pivot category_product).
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'category_product', 'category_id', 'product_id');
    }
}
