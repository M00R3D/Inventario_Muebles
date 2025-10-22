<?php
namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Notificacion;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showLogin()
    {
        $areas = Area::all();
        return view('login', compact('areas'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $usuario = Usuario::where('email', $request->email)->first();

        if (!$usuario) {
            return redirect('/')->with('error', 'Credenciales inválidas');
        }

        try {
            $isValid = \Illuminate\Support\Facades\Hash::check($request->password, $usuario->password);
        } catch (\RuntimeException $e) {
            // Contraseña en DB no reconocida por el hasher (p.ej. texto plano).
            // Fallback seguro: si el valor guardado coincide exactamente con lo que
            // envía el usuario (texto plano), rehasearlo y permitir login.
            $stored = $usuario->password ?? '';
            if (trim($stored) === $request->password) {
                $usuario->password = \Illuminate\Support\Facades\Hash::make($request->password);
                $usuario->save();
                $isValid = true;
            } else {
                return redirect('/')->with('error', 'Contraseña en base de datos en formato no válido. Restablece la contraseña o actualízala con Hash::make().');
            }
        }

        if (! $isValid) {
            return redirect('/')->with('error', 'Credenciales inválidas');
        }

        if (\Illuminate\Support\Facades\Hash::needsRehash($usuario->password)) {
            $usuario->password = \Illuminate\Support\Facades\Hash::make($request->password);
            $usuario->save();
        }

        session(['usuario_id' => $usuario->id]);
        return redirect('/dashboard');
    }

    public function register(Request $request)
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
            'password' => Hash::make($request->password),
            'rol' => $request->rol,
            'area_id' => $request->area_id,
        ]);

        try {
            $actorId = session('usuario_id') ?? null;
            $actor = $actorId ? Usuario::find($actorId) : null;
            $actorName = $actor ? ($actor->nombre . ' ' . $actor->apellido) : 'Autoregistro';
            Notificacion::create([
                'id_admin' => $actorId,
                'id_usuario' => null,
                'audiencia' => 'admins',
                'estado' => 'cerrada',
                'tipo' => 'otra',
                'descripcion' => "Nuevo registro de usuario: {$usuario->nombre} {$usuario->apellido} (ID {$usuario->id}). Origen: {$actorName}",
                'fecha_creacion' => Carbon::now()->toDateTimeString(),
                'fecha_visto' => null,
                'ruta' => url("/usuarios/{$usuario->id}")
            ]);
            Notificacion::create([
                'id_admin' => $actorId,
                'id_usuario' => $usuario->id,
                'audiencia' => 'usuario',
                'estado' => 'cerrada',
                'tipo' => 'prueba',
                'descripcion' => "Bienvenido {$usuario->nombre}, tu cuenta ha sido creada.",
                'fecha_creacion' => Carbon::now()->toDateTimeString(),
                'fecha_visto' => null,
                'ruta' => url("/login")
            ]);
        } catch (\Throwable $e) {
            \Log::error('Error creando notificaciones en AuthController@register: ' . $e->getMessage());
        }

        session(['usuario_id' => $usuario->id]);
        return redirect('/dashboard');
    }

    public function dashboard()
    {
        if (!session()->has('usuario_id')) {
            return redirect('/');
        }

        $usuario = Usuario::with('area')->find(session('usuario_id'));
        return view('dashboard', compact('usuario'));
    }

    public function logout()
    {
        session()->forget('usuario_id');
        return redirect('/');
    }
}