<?php

namespace App\Http\Controllers;

use App\Models\Receta;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CategoriaReceta;

class InicioController extends Controller
{

    public function index()
    {
        // Mostrar las recetas por cantidad de votos
        // $votadas = Receta::has('likes', '>', 1)->get();
        $votadas = Receta::withCount('likes')->orderBy('likes_count', 'desc')->take(3)->get();

        // Obtener las recetas más nuevas
        $nuevas = Receta::latest()->take(5)->get();

        // Obtener todas las categorías 
        $categorias = CategoriaReceta::all();

        // Agrupar las recetas por categorías
        $recetas = [];

        foreach($categorias as $categoria)
        {
            $recetas[ Str::slug($categoria->nombre) ][] = Receta::where('categoria_id', $categoria->id )->take(3)->get();
        }

        return view('inicio.index', compact('nuevas', 'recetas', 'votadas'));
    }
}
