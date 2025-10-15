<?php
// app/Http/Controllers/MuebleController.php
namespace App\Http\Controllers;
use App\Models\Mueble;
use App\Models\Usuario;
use Illuminate\Http\Request;
class MuebleController extends Controller
{
    public function index(Request $request)
    {
        $query = Mueble::with('usuario')->orderBy('id','desc');
        if ($request->filled('codigo')) {$query->where('codigo', 'like', '%' . $request->codigo . '%');}
        if ($request->filled('descripcion')) {$query->where('descripcion', 'like', '%' . $request->descripcion . '%');}
        if ($request->filled('estado')) {$query->where('estado', $request->estado);}
        if ($request->filled('persona_id')) {$query->where('persona_id', $request->persona_id);}
        if ($request->filled('desde')) {$query->whereDate('fecha_registro', '>=', $request->desde);}
        if ($request->filled('hasta')) {$query->whereDate('fecha_registro', '<=', $request->hasta);}
        $muebles = $query->get();
        if ($request->wantsJson() || $request->is('api/*')) {return response()->json($muebles);}
        $usuarios = Usuario::all();
        $public = public_path();
        $entries = @scandir($public) ?: [];
        $dirs = [];
        foreach ($entries as $e) {
            if ($e === '.' || $e === '..') continue;
            $path = $public . DIRECTORY_SEPARATOR . $e;
            if (is_dir($path)) $dirs[] = $e;
        }
        sort($dirs);

        return view('muebles.index', compact('muebles', 'usuarios', 'dirs'));
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
            'persona_id' => 'required|exists:usuarios,id',
            'estado' => 'required|in:bueno,regular,malo,en_reparacion',
        ]);
        $data = $request->all();
        if (empty($data['fecha_registro'])) {$data['fecha_registro'] = now()->toDateString();}
        $mueble = Mueble::create($data);
        if ($request->wantsJson() || $request->is('api/*')) {return response()->json($mueble, 201);}
        return redirect('/muebles')->with('success', 'Mueble creado correctamente');
    }
    public function show(Mueble $mueble)
    {
        $mueble->load('usuario');
        return response()->json($mueble);
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
            'persona_id' => 'required|exists:usuarios,id',
            'estado' => 'required|in:bueno,regular,malo,en_reparacion',
        ]);
        $data = $request->all();
        if (! $request->filled('fecha_registro')) {unset($data['fecha_registro']);}
        $mueble->update($data);
        if ($request->wantsJson() || $request->is('api/*')) {return response()->json($mueble);}
        return redirect('/muebles')->with('success', 'Mueble actualizado correctamente');
    }
    public function destroy(Request $request, Mueble $mueble)
    {
        $mueble->delete();
        if ($request->wantsJson() || $request->is('api/*')) {return response()->json(['message' => 'Mueble eliminado correctamente']);}
        return redirect('/muebles')->with('success', 'Mueble eliminado correctamente');
    }
}
