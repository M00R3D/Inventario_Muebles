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

    public function create()
    {
        return view('categorias.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:150|unique:categorias,nombre',
            'descripcion' => 'nullable|string|max:500',
            'ruta_img' => 'nullable|string|max:200',
        ]);

        $c = Categoria::create($request->only('nombre','descripcion','ruta_img'));

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($c, 201);
        }
        return redirect()->route('categorias.index')->with('success', 'Categoría creada correctamente');
    }

    public function show(Categoria $categoria)
    {
        return response()->json($categoria);
    }

    public function edit(Categoria $categoria)
    {
        return view('categorias.edit', compact('categoria'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $request->validate([
            'nombre' => 'required|string|max:150|unique:categorias,nombre,'.$categoria->id,
            'descripcion' => 'nullable|string|max:500',
            'ruta_img' => 'nullable|string|max:200',
        ]);

        $categoria->update($request->only('nombre','descripcion','ruta_img'));

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($categoria);
        }
        return redirect()->route('categorias.index')->with('success', 'Categoría actualizada correctamente');
    }

    public function destroy(Request $request, Categoria $categoria)
    {
        $categoria->delete();
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Categoría eliminada']);
        }
        return redirect()->route('categorias.index')->with('success', 'Categoría eliminada');
    }
}