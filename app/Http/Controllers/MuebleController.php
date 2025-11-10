<?php
// app/Http/Controllers/MuebleController.php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Mueble;
use App\Models\Usuario;
use App\Models\Notificacion;
use Carbon\Carbon;
class MuebleController extends Controller
{
    public function index(Request $request)
    {
        $query = Mueble::with(['usuario', 'responsable', 'comentarios.usuario', 'categoria'])->orderBy('id','desc');
        if ($request->filled('codigo')) {$query->where('codigo', 'like', '%' . $request->codigo . '%');}
        if ($request->filled('descripcion')) {$query->where('descripcion', 'like', '%' . $request->descripcion . '%');}
        if ($request->filled('marca')) {$query->where('marca', 'like', '%' . $request->marca . '%');}
        if ($request->filled('modelo')) {$query->where('modelo', 'like', '%' . $request->modelo . '%');}
        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        if ($request->filled('estado')) {$query->where('estado', $request->estado);}
        if ($request->filled('persona_id')) {
            if ($request->persona_id === 'none') {
                $query->whereNull('persona_id');
            } else {
                $query->where('persona_id', $request->persona_id);
            }
        }
        if ($request->filled('desde')) {$query->whereDate('fecha_registro', '>=', $request->desde);}
        if ($request->filled('hasta')) {$query->whereDate('fecha_registro', '<=', $request->hasta);}
        $currentUser = null;
        $isAdmin = false;
        if (session()->has('usuario_id')) {
            $currentUser = Usuario::find(session('usuario_id'));
            $isAdmin = $currentUser && ($currentUser->rol === 'admin');
        }
        if (! $isAdmin) {
            $query->whereNull('persona_id');
        }

        if (! $isAdmin) {$query->where('estado', '!=', 'en_reparacion');}
        $muebles = $query->get();
        if ($request->wantsJson() || $request->is('api/*')) {return response()->json($muebles);}
        $usuarios = Usuario::all();
        $marcas = Mueble::whereNotNull('marca')->where('marca','<>','')->distinct()->orderBy('marca')->pluck('marca');
        $modelos = Mueble::whereNotNull('modelo')->where('modelo','<>','')->distinct()->orderBy('modelo')->pluck('modelo');
        $modelosPorMarca = Mueble::whereNotNull('marca')
            ->where('marca','<>','')
            ->whereNotNull('modelo')
            ->where('modelo','<>','')
            ->get(['marca','modelo'])
            ->groupBy('marca')
            ->map(function($grp){ return $grp->pluck('modelo')->unique()->sort()->values()->all(); })
            ->toArray();
        $public = public_path();
        $entries = @scandir($public) ?: [];
        $dirs = [];
        foreach ($entries as $e) {
            if ($e === '.' || $e === '..') continue;
            $path = $public . DIRECTORY_SEPARATOR . $e;
            if (is_dir($path)) $dirs[] = $e;
        }
        sort($dirs);
        return view('muebles.index', compact('muebles', 'usuarios', 'dirs', 'currentUser', 'isAdmin', 'marcas', 'modelos', 'modelosPorMarca'));
    }
    public function create()
    {
        $usuarios = Usuario::all();
        return view('muebles.create', compact('usuarios'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:50|unique:muebles,codigo',
            'descripcion' => 'nullable|string|max:500',
            'fecha_registro' => 'nullable|date',
            'monto_unitario' => 'required|numeric|min:0',
            'nota' => 'nullable|string|max:500',
            'ruta_img' => 'nullable|string|max:200',
            'persona_id' => 'nullable|exists:usuarios,id',
            'responsable_id' => 'nullable|exists:usuarios,id',
            'estado' => 'required|in:bueno,regular,malo,en_reparacion',
        ]);
        $data = $request->all();
        if (empty($data['fecha_registro'])) {$data['fecha_registro'] = now()->toDateString();}
        $mueble = Mueble::create($data);

        try {
            $adminId = session('usuario_id') ?? null;
            Notificacion::create([
                'id_admin' => $adminId,
                'id_usuario' => null,
                'audiencia' => 'admins',
                'estado' => 'cerrada',
                'tipo' => 'otra',
                'descripcion' => "Mueble creado por " . ($adminId ? (Usuario::find($adminId)->nombre . ' ' . Usuario::find($adminId)->apellido) : 'un administrador') . ": {$mueble->codigo} (ID {$mueble->id})",
                'fecha_creacion' => Carbon::now()->toDateTimeString(),
                'fecha_visto' => null,
                'ruta' => url("/muebles/{$mueble->id}")
            ]);
        } catch (\Throwable $e) {
            \Log::error('Error creando notificación de mueble: ' . $e->getMessage());
        }

        if ($request->wantsJson() || $request->is('api/*')) {return response()->json($mueble, 201);}
        return redirect('/muebles')->with('success', 'Mueble creado correctamente');
    }
    public function show(Mueble $mueble)
    {
        $mueble->load('usuario', 'responsable', 'comentarios.usuario', 'categoria');
        $currentUser = null;
        $isAdmin = false;
        if (session()->has('usuario_id')) {
            $currentUser = Usuario::find(session('usuario_id'));
            $isAdmin = $currentUser && ($currentUser->rol === 'admin');
        }
        return view('muebles.show', compact('mueble', 'isAdmin'));
    }
    public function edit(Mueble $mueble)
    {
        $usuarios = Usuario::all();
        return view('muebles.edit', compact('mueble', 'usuarios'));
    }
    public function update(Request $request, Mueble $mueble)
    {
        $request->validate([
            'codigo' => 'required|string|max:50|unique:muebles,codigo,' . $mueble->id,
            'descripcion' => 'nullable|string|max:500',
            'fecha_registro' => 'nullable|date',
            'monto_unitario' => 'required|numeric|min:0',
            'nota' => 'nullable|string|max:500',
            'ruta_img' => 'nullable|string|max:200',
            'persona_id' => 'nullable|exists:usuarios,id',
            'responsable_id' => 'nullable|exists:usuarios,id',
            'estado' => 'required|in:bueno,regular,malo,en_reparacion',
        ]);
        $original = $mueble->only(['codigo','descripcion','fecha_registro','monto_unitario','nota','ruta_img','persona_id','estado']);
        $data = $request->all();
        if (! $request->filled('fecha_registro')) {unset($data['fecha_registro']);}
        $mueble->update($data);
        $changed = [];
        foreach ($original as $key => $old) {
            $new = $mueble->{$key} ?? null;
            $oldStr = is_null($old) ? 'NULL' : (string)$old;
            $newStr = is_null($new) ? 'NULL' : (string)$new;
            if ($oldStr !== $newStr) {
                $label = match($key) {
                    'codigo' => 'Código',
                    'descripcion' => 'Descripción',
                    'fecha_registro' => 'Fecha registro',
                    'monto_unitario' => 'Monto unitario',
                    'nota' => 'Nota',
                    'ruta_img' => 'Imagen',
                    'persona_id' => 'Responsable',
                    'estado' => 'Estado',
                    default => $key,
                };
                if ($key === 'persona_id') {
                    $oldUser = $old ? Usuario::find($old) : null;
                    $newUser = $new ? Usuario::find($new) : null;
                    $oldStr = $oldUser ? ($oldUser->nombre . ' ' . $oldUser->apellido) : ($oldStr === 'NULL' ? 'NULL' : $oldStr);
                    $newStr = $newUser ? ($newUser->nombre . ' ' . $newUser->apellido) : ($newStr === 'NULL' ? 'NULL' : $newStr);
                }
                $changed[] = "{$label}: \"{$oldStr}\" → \"{$newStr}\"";
            }
        }
        if (!empty($changed)) {
            try {
                $adminId = session('usuario_id') ?? null;
                $admin = $adminId ? Usuario::find($adminId) : null;
                $adminName = $admin ? ($admin->nombre . ' ' . $admin->apellido) : 'un administrador';
                $descripcion = "Mueble actualizado por {$adminName}: {$mueble->codigo} (ID {$mueble->id}). Cambios: " . implode('; ', $changed);

                Notificacion::create([
                    'id_admin' => $adminId,
                    'id_usuario' => null,
                    'audiencia' => 'admins',
                    'estado' => 'cerrada',
                    'tipo' => 'otra',
                    'descripcion' => $descripcion,
                    'fecha_creacion' => Carbon::now()->toDateTimeString(),
                    'fecha_visto' => null,
                    'ruta' => url("/muebles/{$mueble->id}")
                ]);
            } catch (\Throwable $e) {
                \Log::error('Error creando notificación de actualización de mueble: ' . $e->getMessage());
            }
        }

        if ($request->wantsJson() || $request->is('api/*')) {return response()->json($mueble);}
        return redirect('/muebles')->with('success', 'Mueble actualizado correctamente');
    }

    public function destroy(Request $request, Mueble $mueble)
    {
        $muebleCodigo = $mueble->codigo ?? ("ID {$mueble->id}");
        $muebleId = $mueble->id;
        $muebleDescripcion = $mueble->descripcion ?? '';
        $mueble->delete();
        try {
            $adminId = session('usuario_id') ?? null;
            $admin = $adminId ? Usuario::find($adminId) : null;
            $adminName = $admin ? ($admin->nombre . ' ' . $admin->apellido) : 'un administrador';
            $descripcion = "Mueble eliminado por {$adminName}: {$muebleCodigo} (ID {$muebleId}). Descripción previa: " . ($muebleDescripcion ? \Illuminate\Support\Str::limit($muebleDescripcion,200) : '-');

            Notificacion::create([
                'id_admin' => $adminId,
                'id_usuario' => null,
                'audiencia' => 'admins',
                'estado' => 'cerrada',
                'tipo' => 'otra',
                'descripcion' => $descripcion,
                'fecha_creacion' => Carbon::now()->toDateTimeString(),
                'fecha_visto' => null,
                'ruta' => url("/muebles")
            ]);
        } catch (\Throwable $e) {
            \Log::error('Error creando notificación de eliminación de mueble: ' . $e->getMessage());
        }

        if ($request->wantsJson() || $request->is('api/*')) {return response()->json(['message' => 'Mueble eliminado correctamente']);}
        return redirect('/muebles')->with('success', 'Mueble eliminado correctamente');
    }
}
