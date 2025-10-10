<?php
// app/Http/Controllers/MuebleController.php
namespace App\Http\Controllers;
use App\Models\Mueble;
use App\Models\Usuario;
use Illuminate\Http\Request;

class MuebleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $muebles = Mueble::with('usuario')->get();
        return response()->json($muebles);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Mostrar formulario de creación
        $usuarios = Usuario::all();
        return view('muebles.create', compact('usuarios'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:50|unique:muebles,codigo',
            'descripcion' => 'nullable|string|max:500',
            'fecha_registro' => 'nullable|date',
            'monto_unitario' => 'required|numeric|min:0',
            'nota' => 'nullable|string|max:500',
            'ruta_img' => 'nullable|string|max:200',
            'persona_id' => 'required|exists:usuarios,id',
            'estado' => 'required|in:bueno,regular,malo,en_reparacion',
        ]);
        $mueble = Mueble::create($request->all());
        return response()->json($mueble, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Mueble $mueble)
    {
        $mueble->load('usuario');
        return response()->json($mueble);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mueble $mueble)
    {
        // Mostrar formulario de edición
        $usuarios = Usuario::all();
        return view('muebles.edit', compact('mueble', 'usuarios'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Mueble $mueble)
    {
        $request->validate([
            'codigo' => 'required|string|max:50|unique:muebles,codigo,' . $mueble->id,
            'descripcion' => 'nullable|string|max:500',
            'fecha_registro' => 'nullable|date',
            'monto_unitario' => 'required|numeric|min:0',
            'nota' => 'nullable|string|max:500',
            'ruta_img' => 'nullable|string|max:200',
            'persona_id' => 'required|exists:usuarios,id',
            'estado' => 'required|in:bueno,regular,malo,en_reparacion',
        ]);
        $mueble->update($request->all());
        return response()->json($mueble);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mueble $mueble)
    {
        $mueble->delete();
        return response()->json(['message' => 'Mueble eliminado correctamente']);
    }
}
