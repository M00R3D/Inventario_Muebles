<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class NotificacionController extends Controller
{
    protected array $estados = ['cerrada', 'abierta', 'vista'];
    protected array $tipos = ['prueba', 'aprobada', 'rechazada', 'otra'];

    public function index(Request $request)
    {
        $query = Notificacion::with(['admin','usuario'])->orderBy('fecha_creacion','desc');
        $current = null;
        if (session()->has('usuario_id')) {$current = Usuario::find(session('usuario_id'));}
        $isAdmin = $current && ($current->rol === 'admin');
        if ($request->filled('id')) {$query->where('id', $request->input('id'));}
        if ($request->filled('id_admin') && $isAdmin) {$query->where('id_admin', $request->input('id_admin'));}
        if ($request->filled('id_usuario')) {
            if ($isAdmin) {
                if ($request->input('id_usuario') === 'none') {
                    $query->whereNull('id_usuario');
                } else {$query->where('id_usuario', $request->input('id_usuario'));}
            } else {if ($current) $query->where('id_usuario', $current->id);}
        } else {
            if (! $isAdmin && $current) {$query->where('id_usuario', $current->id);}
        }
        if ($request->filled('estado')) {
            $estado = $request->input('estado');
            if (in_array($estado, $this->estados, true)) $query->where('estado', $estado);
        }
        if ($request->filled('tipo')) {
            $tipo = $request->input('tipo');
            if (in_array($tipo, $this->tipos, true)) $query->where('tipo', $tipo);
        }
        if ($request->filled('audiencia')) {
            $aud = $request->input('audiencia');
            $query->where('audiencia', $aud);
        }
        if ($request->filled('descripcion')) {
            $desc = trim($request->input('descripcion'));
            if ($desc !== '') $query->where('descripcion', 'like', "%{$desc}%");
        }
        if ($request->filled('ruta')) {
            $ruta = trim($request->input('ruta'));
            if ($ruta !== '') $query->where('ruta', 'like', "%{$ruta}%");
        }
        if ($request->filled('fecha_creacion_from')) {$query->whereDate('fecha_creacion', '>=', $request->input('fecha_creacion_from'));}
        if ($request->filled('fecha_creacion_to')) {$query->whereDate('fecha_creacion', '<=', $request->input('fecha_creacion_to'));}
        if ($request->filled('fecha_visto_from')) {$query->whereDate('fecha_visto', '>=', $request->input('fecha_visto_from'));}
        if ($request->filled('fecha_visto_to')) {$query->whereDate('fecha_visto', '<=', $request->input('fecha_visto_to'));}
        if (!($request->wantsJson() || $request->is('api/*'))) {}

        $notificaciones = $query->get();

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($notificaciones);
        }

        $usuarios = Usuario::all();
        return view('notificaciones.index', compact('notificaciones','usuarios','current','isAdmin'));
    }

    public function create()
    {
        $usuarios = Usuario::all();
        return view('notificaciones.create', compact('usuarios'));
    }

    public function store(Request $request)
    {
        $data = $request->only(['id_admin', 'id_usuario', 'estado', 'tipo', 'descripcion', 'ruta', 'fecha_visto']);
        $validator = Validator::make($data, ['id_admin' => 'nullable|exists:usuarios,id','id_usuario' => 'nullable|exists:usuarios,id','estado' => 'nullable|in:' . implode(',', $this->estados),'tipo' => 'nullable|in:' . implode(',', $this->tipos),'descripcion' => 'nullable|string|max:500','ruta' => 'nullable|string|max:100','fecha_visto' => 'nullable|date',]);
        if ($validator->fails()) {return response()->json(['errors' => $validator->errors()], 422);}
        if (empty($data['estado'])) {$data['estado'] = 'cerrada';}
        if (empty($data['tipo'])) {$data['tipo'] = 'prueba';}
        $notificacion = Notificacion::create($data);
        return response()->json($notificacion, 201);
    }

    public function show(Notificacion $notificacion, Request $request)
    {
        $notificacion->load(['admin', 'usuario']);
        if ($request->wantsJson() || $request->is('api/*')) {return response()->json($notificacion);}
        return view('notificaciones.show', compact('notificacion'));
    }

    public function edit(Notificacion $notificacion)
    {
        $usuarios = Usuario::all();
        return view('notificaciones.edit', compact('notificacion', 'usuarios'));
    }

    public function update(Request $request, Notificacion $notificacion)
    {
        $data = $request->only(['id_admin', 'id_usuario', 'estado', 'tipo', 'descripcion', 'ruta', 'fecha_visto']);
        $validator = Validator::make($data, ['id_admin' => 'nullable|exists:usuarios,id','id_usuario' => 'nullable|exists:usuarios,id','estado' => 'nullable|in:' . implode(',', $this->estados),'tipo' => 'nullable|in:' . implode(',', $this->tipos),'descripcion' => 'nullable|string|max:500','ruta' => 'nullable|string|max:100','fecha_visto' => 'nullable|date',]);
        if ($validator->fails()) {return response()->json(['errors' => $validator->errors()], 422);}
        if (isset($data['estado']) && $data['estado'] === 'vista' && empty($data['fecha_visto'])) {$data['fecha_visto'] = Carbon::now();}
        if (isset($data['estado']) && $data['estado'] !== 'vista') {$data['fecha_visto'] = null;}
        $notificacion->update($data);
        return response()->json($notificacion);
    }

    public function destroy(Request $request, Notificacion $notificacion)
    {
        $notificacion->delete();
        if ($request->wantsJson() || $request->is('api/*')) {return response()->json(['message' => 'Notificación eliminada correctamente']);}
        return redirect('/notificaciones')->with('success', 'Notificación eliminada correctamente');
    }

    public function adminSetEstado(Request $request, Notificacion $notificacion)
    {
        $request->validate(['estado' => 'required|in:' . implode(',', $this->estados)]);
        $estado = $request->input('estado');
        $notificacion->estado = $estado;
        if ($estado === 'vista') {$notificacion->fecha_visto = Carbon::now();} elseif ($estado !== 'vista') {$notificacion->fecha_visto = null;}
        $notificacion->save();
        return response()->json($notificacion);
    }

    public function adminSetTipo(Request $request, Notificacion $notificacion)
    {
        $request->validate(['tipo' => 'required|in:' . implode(',', $this->tipos)]);
        $notificacion->tipo = $request->input('tipo');
        $notificacion->save();
        return response()->json($notificacion);
    }

    public function usuarioMarcarVisto(Notificacion $notificacion)
    {
        $notificacion->estado = 'vista';
        $notificacion->fecha_visto = Carbon::now();
        $notificacion->save();
        return response()->json($notificacion);
    }

    public function usuarioSetEstado(Request $request, Notificacion $notificacion)
    {
        $request->validate(['estado' => 'required|in:cerrada,abierta']); 
        $estado = $request->input('estado');
        $notificacion->estado = $estado;
        if ($estado === 'abierta') {$notificacion->fecha_visto = null;}
        $notificacion->save();
        return response()->json($notificacion);
    }
    public function changeEstado(Request $request, Notificacion $notificacion)
    {
        $current = null;
        if (session()->has('usuario_id')) {$current = Usuario::find(session('usuario_id'));}
        if ($current && $current->rol === 'admin') {return $this->adminSetEstado($request, $notificacion);}
        return $this->usuarioSetEstado($request, $notificacion);
    }
    public function marcarVisto(Notificacion $notificacion){return $this->usuarioMarcarVisto($notificacion);}
}
