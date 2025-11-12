<?php
// app/Http/Controllers/UsuarioController.php
namespace App\Http\Controllers;
use App\Models\Usuario;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Notificacion;
use Carbon\Carbon;
class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Usuario::with('area')->orderBy('id','asc');
        if ($request->filled('nombre')) {$query->where('nombre', 'like', '%' . $request->nombre . '%');}
        if ($request->filled('apellido')) {$query->where('apellido', 'like', '%' . $request->apellido . '%');}
        if ($request->filled('email')) {$query->where('email', 'like', '%' . $request->email . '%');}
        if ($request->filled('rol')) {$query->where('rol', $request->rol);}
        if ($request->filled('area_id')) {$query->where('area_id', $request->area_id);}
        $usuarios = $query->get();
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($usuarios);
        }

        $areas = Area::all();
        return view('usuarios', compact('usuarios', 'areas'));
    }
    public function create()
    {
        $areas = Area::all();
        return view('usuarios.create', compact('areas'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|string|min:6',
            'rol' => 'required|in:admin,empleado,tecnico',
            'area_id' => 'required|exists:areas,id',
        ]);

        $usuario = Usuario::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'password' => \Hash::make($request->password),
            'rol' => $request->rol,
            'area_id' => $request->area_id,
        ]);

        try {
            $actorId = session('usuario_id') ?? null;
            $actor = $actorId ? Usuario::find($actorId) : null;
            $actorName = $actor ? ($actor->nombre . ' ' . $actor->apellido) : 'Sistema';
            $adminNotif = Notificacion::create([
                'id_admin' => $actorId,
                'id_usuario' => null,
                'audiencia' => 'admins',
                'estado' => 'cerrada',
                'tipo' => 'otra',
                'descripcion' => "Usuario creado: {$usuario->nombre} {$usuario->apellido} (ID {$usuario->id}). Creado por: {$actorName}",
                'fecha_creacion' => Carbon::now()->toDateTimeString(),
                'fecha_visto' => null,
                'ruta' => null,
            ]);
            if ($adminNotif) {
                $adminNotif->ruta = url("/notificaciones/{$adminNotif->id}");
                $adminNotif->save();
            }

            $userNotif = Notificacion::create([
                'id_admin' => $actorId,
                'id_usuario' => $usuario->id,
                'audiencia' => 'usuarios',
                'estado' => 'cerrada',
                'tipo' => 'prueba',
                'descripcion' => "Bienvenido {$usuario->nombre}. Tu cuenta fue creada por: {$actorName}",
                'fecha_creacion' => Carbon::now()->toDateTimeString(),
                'fecha_visto' => null,
                'ruta' => url("/login")
            ]);
        } catch (\Throwable $e) {
            \Log::error('Error al crear notificaciones en UsuarioController@store: ' . $e->getMessage());
        }

        return response()->json($usuario, 201);
    }
    public function show(Usuario $usuario)
    {
        $usuario->load('area');
        return response()->json($usuario);
    }
    public function edit(Usuario $usuario)
    {
        $areas = Area::all();
        return view('usuarios.edit', compact('usuario', 'areas'));
    }
    public function update(Request $request, Usuario $usuario)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email' => 'required|email|unique:usuarios,email,' . $usuario->id,
            'rol' => 'required|in:admin,empleado,tecnico',
            'area_id' => 'required|exists:areas,id',
        ]);
        $original = $usuario->only(['nombre','apellido','email','rol','area_id']);

        $usuario->update([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'rol' => $request->rol,
            'area_id' => $request->area_id,
        ]);
        $changed = [];
        foreach ($original as $key => $old) {
            $new = $usuario->{$key} ?? null;
            if ((string)$old !== (string)$new) {
                $label = ucfirst(str_replace('_',' ',$key));
                $changed[] = "{$label}: \"{$old}\" → \"{$new}\"";
            }
        }
        try {
            $actorId = session('usuario_id') ?? null;
            $actor = $actorId ? Usuario::find($actorId) : null;
            $actorName = $actor ? ($actor->nombre . ' ' . $actor->apellido) : 'Sistema';
            if (!empty($changed)) {
                $adminNotif = Notificacion::create([
                    'id_admin' => $actorId,
                    'id_usuario' => null,
                    'audiencia' => 'admins',
                    'estado' => 'cerrada',
                    'tipo' => 'otra',
                    'descripcion' => "Usuario actualizado: {$usuario->nombre} {$usuario->apellido} (ID {$usuario->id}). Realizado por: {$actorName}. Cambios: " . implode('; ', $changed),
                    'fecha_creacion' => Carbon::now()->toDateTimeString(),
                    'fecha_visto' => null,
                    'ruta' => null,
                ]);
                if ($adminNotif) {
                    $adminNotif->ruta = url("/notificaciones/{$adminNotif->id}");
                    $adminNotif->save();
                }

                $userNotif = Notificacion::create([
                    'id_admin' => $actorId,
                    'id_usuario' => $usuario->id,
                    'audiencia' => 'usuarios',
                    'estado' => 'cerrada',
                    'tipo' => 'otra',
                    'descripcion' => "Tus datos fueron actualizados por: {$actorName}. Cambios: " . implode('; ', $changed),
                    'fecha_creacion' => Carbon::now()->toDateTimeString(),
                    'fecha_visto' => null,
                    'ruta' => null,
                ]);
                if ($userNotif) {
                    $userNotif->ruta = url("/notificaciones/{$userNotif->id}");
                    $userNotif->save();
                }
            }
        } catch (\Throwable $e) {
            \Log::error('Error creando notificaciones en UsuarioController@update: ' . $e->getMessage());
        }

        return response()->json($usuario);
    }
    public function destroy(Request $request, Usuario $usuario)
    {
        $usuarioNombre = trim(($usuario->nombre ?? '') . ' ' . ($usuario->apellido ?? ''));
        $usuarioId = $usuario->id;
        $usuario->delete();
        try {
            $actorId = session('usuario_id') ?? null;
            $actor = $actorId ? Usuario::find($actorId) : null;
            $actorName = $actor ? ($actor->nombre . ' ' . $actor->apellido) : 'Sistema';
            Notificacion::create([
                'id_admin' => $actorId,
                'id_usuario' => null,
                'audiencia' => 'admins',
                'estado' => 'cerrada',
                'tipo' => 'otra',
                'descripcion' => "Usuario eliminado: {$usuarioNombre} (ID {$usuarioId}). Eliminado por: {$actorName}",
                'fecha_creacion' => Carbon::now()->toDateTimeString(),
                'fecha_visto' => null,
                'ruta' => url("/usuarios")
            ]);
        } catch (\Throwable $e) {
            \Log::error('Error creando notificación en UsuarioController@destroy: ' . $e->getMessage());
        }

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Usuario eliminado correctamente']);
        }
        return redirect('/usuarios')->with('success', 'Usuario eliminado correctamente');
    }
}
