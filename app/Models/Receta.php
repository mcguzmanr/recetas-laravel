<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receta extends Model
{
    use HasFactory;
    /**
     * Campos que se agregaran
     *
     * @var array
     */
    protected $fillable = [
        'titulo',
        'preparacion',
        'ingredientes',
        'imagen',
        'categoria_id',
    ];

    
    // Obtiene la categoría de la receta via FK
    public function categoria()
    {
       return $this->belongsTo(CategoriaReceta::class); 
    }

    // Obtiene la información del usuario en via FK
    public function autor()
    {
        return $this->belongsTo(User::class, 'user_id'); // FK de esta tabla para el usuario
    }

    // Likes que ha recibido una receta
    public function likes()
    {
        return $this->belongsToMany(User::class, 'likes_receta');
    }
}
