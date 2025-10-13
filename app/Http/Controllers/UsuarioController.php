<?php
// app/Http/Controllers/UsuarioController.php
namespace App\Http\Controllers;
use App\Models\Usuario;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $usuarios = Usuario::with('area')->orderBy('id','asc')->get();
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($usuarios);
        }
        return view('usuarios', compact('usuarios'));
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
        $usuario->update([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'rol' => $request->rol,
            'area_id' => $request->area_id,
        ]);
        if ($request->filled('password')) {
            $usuario->update(['password' => \Hash::make($request->password)]);
        }
        return response()->json($usuario);
    }
    public function destroy(Request $request, Usuario $usuario)
    {
        $usuario->delete();
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Usuario eliminado correctamente']);
        }
        return redirect('/usuarios')->with('success', 'Usuario eliminado correctamente');
    }
}
