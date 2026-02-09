<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Image extends Model
{
    protected $table = 'images';
    protected $fillable = ["main_image_url"];

    // 2. Relación 1:n: Una imagen representa muchas variantes (Relación Uno a Muchos).
    public function imagenVariante(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }
}
    