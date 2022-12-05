<?php

namespace App\Http\Controllers;

use App\Models\CategoriaReceta;
use App\Models\Receta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;

class RecetaController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth', ['except' => 'show', 'search']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $recetas = auth()->user()->recetas;
        // Recetas con paginación

        $usuario =  auth()->user();

        $recetas = Receta::where('user_id', $usuario->id)->paginate(10);


        return view('recetas.index')
            ->with('recetas', $recetas)
            ->with('usuario', $usuario);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // DB::table('categoria_receta')->get()->pluck('nombre', 'id')->dd();
        
        // Obtener las categorias (sin modelo)
        // $categorias = DB::table('categoria_recetas')->get()->pluck('nombre', 'id');

        // Obtener las categorias por medio de un modelo
        $categorias = CategoriaReceta::all(['id', 'nombre']);


        return view('recetas.create')
            ->with('categorias', $categorias);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // dd( $request['imagen']->store('upload-recetas', 'public') );

        // Validación
        $data = $request->validate([
            'titulo' => 'required| min:6',
            'preparacion' => 'required',
            'ingredientes' => 'required',
            'imagen' => 'required|image',
            'categoria' => 'required',
        ]);

        // Obtener la ruta de la imagen
        $rutaImagen = $request['imagen']->store('upload-recetas', 'public');

        // Resize de la imagen
        $img = Image::make( public_path("storage/{$rutaImagen}"))->fit(1000, 550);
        $img->save();

        // Almacenar en la BD (sin modelo)
        // DB::table('recetas')->insert([
        //     'titulo' => $data['titulo'],
        //     'preparacion' => $data['preparacion'],
        //     'ingredientes' => $data['ingredientes'],
        //     'imagen' => $rutaImagen,
        //     'user_id' => Auth::user()->id,
        //     'categoria_id' => $data['categoria']

        // ]);

        // Almacenar en la BD (con modelo)
        auth()->user()->recetas()->create([
            'titulo' => $data['titulo'],
            'preparacion' => $data['preparacion'],
            'ingredientes' => $data['ingredientes'],
            'imagen' => $rutaImagen,
            'categoria_id' => $data['categoria']
        ]);

        // Redireccionar 
        return redirect()->action([RecetaController::class, 'index']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Receta  $receta
     * @return \Illuminate\Http\Response
     */
    public function show(Receta $receta)
    {
        // Obtener si el usuario actual le gusta la receta y está autenticado
        $like = ( auth()->user() ) ? auth()->user()->meGusta->contains($receta->id): false;

        // Pasa la cantidad de likes a la vista
        $likes = $receta->likes->count();

        return view('recetas.show', compact('receta', 'like', 'likes'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Receta  $receta
     * @return \Illuminate\Http\Response
     */
    public function edit(Receta $receta)
    {
        // Revisar el policy
        $this->authorize('view', $receta);
        // $this->authorize('edit', $receta);

        //Con Modelo
        $categorias = CategoriaReceta::all(['id', 'nombre']);
        return view('recetas.edit', compact('categorias', 'receta'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Receta  $receta
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Receta $receta)
    {
        // Revisar el policy
        $this->authorize('update', $receta);

        // Validación
        $data = $request->validate([
            'titulo' => 'required| min:6',
            'preparacion' => 'required',
            'ingredientes' => 'required',
            'categoria' => 'required',
        ]);
        // Reasignar los valores 
        $receta->titulo = $data['titulo'];
        $receta->preparacion = $data['preparacion'];
        $receta->ingredientes = $data['ingredientes'];
        $receta->categoria_id = $data['categoria'];

        // Si el usuario sube una nueva imagen 
        if(request('imagen'))
        {
            // Obtener la ruta de la imagen
            $rutaImagen = $request['imagen']->store('upload-recetas', 'public');

            // Resize de la imagen
            $img = Image::make( public_path("storage/{$rutaImagen}"))->fit(1000, 550);
            $img->save();

            // Asignar al objeto
            $receta->imagen = $rutaImagen;
        }

        $receta->save();

        // Redireccionar
        return redirect()->action([RecetaController::class, 'index']);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Receta  $receta
     * @return \Illuminate\Http\Response
     */
    public function destroy(Receta $receta)
    {
        //Ejecutar el Policy
        $this->authorize('delete', $receta);

        // Eliminar la receta
        $receta->delete();

        // Eliminar la receta
        return redirect()->action([RecetaController::class, 'index']);
    }

    public function search(Request $request)
    {
        // $busqueda = $request['buscar'];
        $busqueda = $request->get('buscar');

        $recetas = Receta::where('titulo', 'like', '%' . $busqueda . '%')->paginate(10);
        $recetas->appends(['buscar' => $busqueda]);

        return view('busquedas.show', compact('recetas', 'busqueda'));
    }
}
