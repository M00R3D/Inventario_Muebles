<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Configuracion;
class ConfiguracionController extends Controller
{
    public function index(Request $request)
    {
        $defaults = [
            ['clave' => 'icon_dashboard',     'nombre' => 'Dashboard',       'ruta_img' => 'icons/home.png', 'normal_color' => '#5e5492', 'hover_color' => '#383569'],
            ['clave' => 'icon_muebles',       'nombre' => 'Muebles',         'ruta_img' => 'icons/muebles.png', 'normal_color' => '#5e5492', 'hover_color' => '#383569'],
            ['clave' => 'icon_categorias',    'nombre' => 'Categorías',      'ruta_img' => 'icons/categorias.png', 'normal_color' => '#5e5492', 'hover_color' => '#383569'],
            ['clave' => 'icon_usuarios',      'nombre' => 'Usuarios',        'ruta_img' => 'icons/usuarios.png', 'normal_color' => '#5e5492', 'hover_color' => '#383569'],
            ['clave' => 'icon_solicitudes',   'nombre' => 'Solicitudes',     'ruta_img' => 'icons/solicitudes.png', 'normal_color' => '#5e5492', 'hover_color' => '#383569'],
            ['clave' => 'icon_notificaciones','nombre' => 'Notificaciones',  'ruta_img' => 'icons/notificaciones.png', 'normal_color' => '#5e5492', 'hover_color' => '#383569'],
            ['clave' => 'icon_configuracion', 'nombre' => 'Configuración',   'ruta_img' => 'icons/configuracion.png', 'normal_color' => '#5e5492', 'hover_color' => '#383569'],
            ['clave' => 'icon_imagenes',      'nombre' => 'Imágenes',        'ruta_img' => 'icons/imagenes.png', 'normal_color' => '#5e5492', 'hover_color' => '#383569'],
        ];
        foreach ($defaults as $d) {
            Configuracion::firstOrCreate(['clave' => $d['clave']], $d);
        }

        $configuraciones = Configuracion::orderBy('clave')->get();
        if ($request->wantsJson() || $request->is('api/*')) { return response()->json($configuraciones); }
        return view('configuracion.index', compact('configuraciones'));
    }

    public function edit(Configuracion $configuracion){return view('configuracion.edit', compact('configuracion'));}

    public function update(Request $request, Configuracion $configuracion)
    {
        $data = $request->validate([
            'nombre' => 'nullable|string|max:150',
            'ruta_img' => 'nullable|string|max:255',
            'normal_color' => ['nullable','regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
            'hover_color' => ['nullable','regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
        ]);
        $configuracion->update($data);
        if ($request->wantsJson() || $request->is('api/*')) {return response()->json($configuracion);}
        return redirect()->route('configuracion.index')->with('success', 'Configuración actualizada');
    }

    public function applyColors(Request $request)
    {
        $normal = $request->input('normal_color') ?? $request->json('normal_color') ?? $request->input('normal') ?? $request->json('normal');
        $hover  = $request->input('hover_color')  ?? $request->json('hover_color')  ?? $request->input('hover')  ?? $request->json('hover');
        \Log::info('Configuracion::applyColors request', ['normal' => $normal, 'hover' => $hover, 'all' => $request->all()]);
        if (! $normal || !preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $normal)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => 'normal_color inválido'], 422);
            }
            return redirect()->back()->withErrors(['normal_color' => 'Color normal inválido']);
        }
        $hover = ($hover && preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $hover)) ? $hover : $normal;
        try {
            \DB::transaction(function() use ($normal, $hover) {
                \App\Models\Configuracion::query()->update([
                    'normal_color' => $normal,
                    'hover_color' => $hover,
                    'updated_at' => now(),
                ]);
            });
        } catch (\Throwable $e) {
            \Log::error('Configuracion::applyColors update error: '.$e->getMessage());
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => 'Error aplicando colores'], 500);
            }
            return redirect()->back()->withErrors(['general' => 'No se pudieron aplicar los colores']);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Colores aplicados a todas las configuraciones', 'normal_color' => $normal, 'hover_color' => $hover]);
        }
        return redirect()->route('configuracion.index')->with('success', 'Colores aplicados a todas las configuraciones');
    }
}