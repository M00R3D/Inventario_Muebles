<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Configuracion;
class ConfiguracionController extends Controller
{
    public function index(Request $request)
    {
        $defaults = [
            ['clave' => 'icon_dashboard',     'nombre' => 'Dashboard',       'ruta_img' => 'imgs/icons/dashboard.svg'],
            ['clave' => 'icon_muebles',       'nombre' => 'Muebles',         'ruta_img' => 'imgs/icons/muebles.svg'],
            ['clave' => 'icon_categorias',    'nombre' => 'Categorías',      'ruta_img' => 'imgs/icons/categorias.svg'],
            ['clave' => 'icon_usuarios',      'nombre' => 'Usuarios',        'ruta_img' => 'imgs/icons/usuarios.svg'],
            ['clave' => 'icon_solicitudes',   'nombre' => 'Solicitudes',     'ruta_img' => 'imgs/icons/solicitudes.svg'],
            ['clave' => 'icon_notificaciones','nombre' => 'Notificaciones',  'ruta_img' => 'imgs/icons/notificaciones.svg'],
            ['clave' => 'icon_configuracion', 'nombre' => 'Configuración',   'ruta_img' => 'imgs/icons/configuracion.svg'],
        ];
        foreach ($defaults as $d) {Configuracion::firstOrCreate(['clave' => $d['clave']], $d);}
        $configuraciones = Configuracion::orderBy('clave')->get();
        if ($request->wantsJson() || $request->is('api/*')) { return response()->json($configuraciones); }
        return view('configuracion.index', compact('configuraciones'));
    }
    public function edit(Configuracion $configuracion){return view('configuracion.edit', compact('configuracion'));}
    public function update(Request $request, Configuracion $configuracion)
    {
        $data = $request->validate(['nombre' => 'nullable|string|max:150','ruta_img' => 'nullable|string|max:255',]);
        $configuracion->update($data);
        if ($request->wantsJson() || $request->is('api/*')) {return response()->json($configuracion);}
        return redirect()->route('configuracion.index')->with('success', 'Configuración actualizada');
    }
}