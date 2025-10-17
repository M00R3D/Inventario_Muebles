<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notificacion;
use Carbon\Carbon;

class NotificacionSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        Notificacion::insert([
            [
                'id_admin' => 1,
                'id_usuario' => 2,
                'estado' => 'cerrada',
                'tipo' => 'prueba',
                'descripcion' => 'Bienvenida al sistema — configuración inicial.',
                'fecha_creacion' => $now->copy()->subDays(6)->toDateTimeString(),
                'fecha_visto' => null,
                'ruta' => null,
            ],
            [
                'id_admin' => 1,
                'id_usuario' => 3,
                'estado' => 'cerrada',
                'tipo' => 'prueba',
                'descripcion' => 'Tu solicitud fue aprobada. Revisa detalles en solicitudes.',
                'fecha_creacion' => $now->copy()->subDays(3)->toDateTimeString(),
                'fecha_visto' => null,
                'ruta' => null,
            ],
            [
                'id_admin' => null,
                'id_usuario' => 2,
                'estado' => 'cerrada',
                'tipo' => 'prueba',
                'descripcion' => 'Recordatorio: entrega de mueble programada.',
                'fecha_creacion' => $now->copy()->subDay()->toDateTimeString(),
                'fecha_visto' => null,
                'ruta' => 'notificaciones/recordatorio.pdf',
            ],
            [
                'id_admin' => 2,
                'id_usuario' => null,
                'estado' => 'cerrada',
                'tipo' => 'prueba',
                'descripcion' => 'Notificación interna: revisión completada.',
                'fecha_creacion' => $now->copy()->subDays(10)->toDateTimeString(),
                'fecha_visto' => null,
                'ruta' => null,
            ],
            [
                'id_admin' => 1,
                'id_usuario' => 1,
                'estado' => 'cerrada',
                'tipo' => 'prueba',
                'descripcion' => 'Prueba de notificaciones: esto es un mensaje de prueba.',
                'fecha_creacion' => $now->copy()->subHours(6)->toDateTimeString(),
                'fecha_visto' => null,
                'ruta' => 'notificaciones/prueba.txt',
            ],
        ]);
    }
}