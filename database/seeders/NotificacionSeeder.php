<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notificacion;
use App\Models\Usuario;
use Carbon\Carbon;
class NotificacionSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $tipos = ['prueba', 'aprobada', 'rechazada', 'otra'];
        $mensajes = [
            'Bienvenida al sistema — configuración inicial.',
            'Tu solicitud fue aprobada. Revisa detalles en solicitudes.',
            'Recordatorio: entrega de mueble programada.',
            'Notificación interna: revisión completada.',
            'Aviso: actualiza tu perfil cuando puedas.',
            'Resumen semanal: revisa las tareas pendientes.'
        ];

        $rows = [];
        $usuarios = Usuario::all();
        foreach ($usuarios as $uIndex => $u) {
            for ($i = 0; $i < 6; $i++) {
                $tipo = $tipos[$i % count($tipos)];
                $estado = ($i === 4) ? 'vista' : (($i % 2 === 0) ? 'cerrada' : 'abierta');
                $fecha_creacion = $now->copy()->subDays(($u->id * 6) + $i)->toDateTimeString();
                $fecha_visto = $estado === 'vista' ? $now->copy()->subDays($i)->toDateTimeString() : null;
                $rows[] = [
                    'id_admin' => 1,
                    'id_usuario' => $u->id,
                    'estado' => $estado,
                    'tipo' => $tipo,
                    'descripcion' => $mensajes[$i % count($mensajes)] . " (para {$u->nombre} {$u->apellido})",
                    'fecha_creacion' => $fecha_creacion,
                    'fecha_visto' => $fecha_visto,
                    'ruta' => null,
                ];
            }
        }

        if (!empty($rows)) {
            Notificacion::insert($rows);
        }
    }
}