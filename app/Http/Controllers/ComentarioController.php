<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Comentario;
use App\Models\Mueble;

class ComentarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Comentario::with('usuario')->orderBy('created_at','desc');
        if ($request->filled('mueble_id')) { $query->where('mueble_id', $request->mueble_id); }
        $comentarios = $query->get();
        return response()->json($comentarios);
    }

    public function store(Request $request)
    {
        $request->validate([
            'mueble_id' => 'required|exists:muebles,id',
            'usuario_id' => 'nullable|exists:usuarios,id',
            'comentario' => 'required|string'
        ]);
        $c = Comentario::create($request->only('mueble_id','usuario_id','comentario'));
        return response()->json($c, 201);
    }

    public function show(Comentario $comentario)
    {
        $comentario->load('usuario','mueble');
        return response()->json($comentario);
    }

    public function update(Request $request, Comentario $comentario)
    {
        $request->validate(['comentario' => 'required|string']);
        $comentario->update($request->only('comentario'));
        return response()->json($comentario);
    }

    public function destroy(Comentario $comentario)
    {
        $comentario->delete();
        return response()->json(['message' => 'Comentario eliminado']);
    }
}