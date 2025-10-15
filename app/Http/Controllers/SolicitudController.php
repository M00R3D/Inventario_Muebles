<?php
// app/Http/Controllers/SolicitudController.php
namespace App\Http\Controllers;
use App\Models\Solicitud;
use App\Models\Mueble;
use App\Models\Usuario;
use Illuminate\Http\Request;
class SolicitudController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = session()->has('usuario_id') ? Usuario::find(session('usuario_id')) : null;
        $isAdmin = $currentUser && ($currentUser->rol === 'admin');
        $query = Solicitud::with(['mueble', 'usuario'])->orderBy('id','desc');
        if (! $isAdmin && $currentUser) {$query->where('persona_id', $currentUser->id);}
        if ($request->filled('persona_id') && $isAdmin) {$query->where('persona_id', $request->input('persona_id'));}
        if ($request->filled('estado')) {$query->where('estado', $request->input('estado'));}
        if ($request->filled('codigo')) {
            $q = $request->input('codigo');
            $query->whereHas('mueble', function($qb) use ($q) {
                $qb->where('codigo', 'like', "%{$q}%")
                   ->orWhere('descripcion', 'like', "%{$q}%");
            });
        }
        $solicitudes = $query->get();
        if ($request->wantsJson() || $request->is('api/*')) {return response()->json($solicitudes);}
        $muebles = Mueble::all();
        $usuarios = Usuario::all();
        return view('solicitudes.index', compact('solicitudes','muebles','usuarios','currentUser','isAdmin'));
    }
    public function create(Request $request)
    {
        $mueble = null;
        $muebleId = $request->query('mueble_id');
        if ($muebleId) {$mueble = Mueble::find($muebleId);}
        $usuarios = Usuario::all();
        $currentUser = session()->has('usuario_id') ? Usuario::find(session('usuario_id')) : null;
        return view('solicitudes.create', compact('mueble', 'usuarios', 'currentUser'));
    }
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

        $currentUser = session()->has('usuario_id') ? Usuario::find(session('usuario_id')) : null;
        $isAdmin = $currentUser && ($currentUser->rol === 'admin');
        $data = $request->all();
        if ($currentUser && ! $isAdmin) {
            $data['persona_id'] = $currentUser->id;
            $data['estado'] = 'pendiente';
        }
        $solicitud = Solicitud::create($data);
        if ($request->wantsJson() || $request->is('api/*')) {return response()->json($solicitud, 201);}
        return redirect('/solicitudes')->with('success', 'Solicitud creada correctamente');
    }
    public function show(Solicitud $solicitud)
    {
        $solicitud->load(['mueble', 'usuario']);
        return response()->json($solicitud);
    }
    public function edit(Solicitud $solicitud)
    {
        $muebles = Mueble::all();
        $usuarios = Usuario::all();
        return view('solicitudes.edit', compact('solicitud', 'muebles', 'usuarios'));
    }
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
        if ($request->wantsJson() || $request->is('api/*')) {return response()->json($solicitud);}
        return redirect('/solicitudes')->with('success', 'Solicitud actualizada correctamente');
    }
    public function destroy(Request $request, Solicitud $solicitud)
    {
        $solicitud->delete();
        if ($request->wantsJson() || $request->is('api/*')) {return response()->json(['message' => 'Solicitud eliminada correctamente']);}
        return redirect('/solicitudes')->with('success', 'Solicitud eliminada correctamente');
    }
    public function changeEstado(Request $request, Solicitud $solicitud)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,aprobada,rechazada',
        ]);
        $solicitud->update(['estado' => $request->estado]);
        if ($request->wantsJson() || $request->is('api/*')) {return response()->json(['message' => 'Estado actualizado', 'solicitud' => $solicitud]);}
        return redirect('/solicitudes')->with('success', 'Estado actualizado correctamente');
    }
}
