<?php
// app/Http/Controllers/UsuarioController.php
namespace App\Http\Controllers;
use App\Models\Usuario;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $usuarios = Usuario::with('area')->orderBy('id','asc')->get();

        // si la petición es API/JSON, devolver JSON (mantener compatibilidad apiResource)
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($usuarios);
        }

        // petición web: devolver la vista usuarios.blade.php con los datos
        return view('usuarios', compact('usuarios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Mostrar formulario de creación
        $areas = Area::all();
        return view('usuarios.create', compact('areas'));
    }

    /**
     * Store a newly created resource in storage.
     */
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

    /**
     * Display the specified resource.
     */
    public function show(Usuario $usuario)
    {
        $usuario->load('area');
        return response()->json($usuario);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Usuario $usuario)
    {
        // Mostrar formulario de edición
        $areas = Area::all();
        return view('usuarios.edit', compact('usuario', 'areas'));
    }

    /**
     * Update the specified resource in storage.
     */
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Usuario $usuario)
    {
        $usuario->delete();
        return response()->json(['message' => 'Usuario eliminado correctamente']);
    }
}
