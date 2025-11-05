<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Categoria;

class CategoriaController extends Controller
{
    public function index(Request $request)
    {
        $categorias = Categoria::orderBy('nombre')->get();
        if ($request->wantsJson() || $request->is('api/*')) { return response()->json($categorias); }
        return view('categorias.index', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:150|unique:categorias,nombre','descripcion' => 'nullable|string|max:500']);
        $c = Categoria::create($request->only('nombre','descripcion'));
        return response()->json($c, 201);
    }

    public function show(Categoria $categoria)
    {
        return response()->json($categoria);
    }

    public function update(Request $request, Categoria $categoria)
    {
        $request->validate(['nombre' => 'required|string|max:150|unique:categorias,nombre,'.$categoria->id,'descripcion' => 'nullable|string|max:500']);
        $categoria->update($request->only('nombre','descripcion'));
        return response()->json($categoria);
    }

    public function destroy(Categoria $categoria)
    {
        $categoria->delete();
        return response()->json(['message' => 'Categoría eliminada']);
    }
}