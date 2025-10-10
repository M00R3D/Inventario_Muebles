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
    public function index()
    {
        // Listar usuarios
        $usuarios = Usuario::with('area')->get();
        return view('usuarios.index', compact('usuarios'));
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
        // Guardar nuevo usuario
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|string|min:6',
            'rol' => 'required|in:admin,empleado,tecnico',
            'area_id' => 'required|exists:areas,id',
        ]);

        Usuario::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => $request->rol,
            'area_id' => $request->area_id,
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Usuario $usuario)
    {
        // Mostrar detalles de un usuario
        return view('usuarios.show', compact('usuario'));
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
        // Actualizar usuario
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

        // Si se envía un nuevo password
        if ($request->filled('password')) {
            $usuario->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Usuario $usuario)
    {
        // Eliminar usuario
        $usuario->delete();
        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente');
    }
}
