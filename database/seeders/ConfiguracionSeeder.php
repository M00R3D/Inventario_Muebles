<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Configuracion;

class ConfiguracionSeeder extends Seeder
{
    public function run(): void
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
            Configuracion::updateOrCreate(['clave' => $d['clave']], $d);
        }
    }
}