<?php
// app/Http/Controllers/SolicitudController.php
namespace App\Http\Controllers;
use App\Models\Solicitud;
use App\Models\Mueble;
use App\Models\Usuario;
use Illuminate\Http\Request;

class SolicitudController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $solicitudes = Solicitud::with(['mueble', 'usuario'])->get();
        return response()->json($solicitudes);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Mostrar formulario de creación
        $muebles = Mueble::all();
        $usuarios = Usuario::all();
        return view('solicitudes.create', compact('muebles', 'usuarios'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'nota' => 'nullable|string|max:500',
            'mueble_id' => 'required|exists:muebles,id',
            'persona_id' => 'required|exists:usuarios,id',
            'estado' => 'required|in:pendiente,aprobada,rechazada',
        ]);
        $solicitud = Solicitud::create($request->all());
        return response()->json($solicitud, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Solicitud $solicitud)
    {
        $solicitud->load(['mueble', 'usuario']);
        return response()->json($solicitud);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Solicitud $solicitud)
    {
        // Mostrar formulario de edición
        $muebles = Mueble::all();
        $usuarios = Usuario::all();
        return view('solicitudes.edit', compact('solicitud', 'muebles', 'usuarios'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Solicitud $solicitud)
    {
        $request->validate([
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'nota' => 'nullable|string|max:500',
            'mueble_id' => 'required|exists:muebles,id',
            'persona_id' => 'required|exists:usuarios,id',
            'estado' => 'required|in:pendiente,aprobada,rechazada',
        ]);
        $solicitud->update($request->all());
        return response()->json($solicitud);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Solicitud $solicitud)
    {
        $solicitud->delete();
        return response()->json(['message' => 'Solicitud eliminada correctamente']);
    }
}
