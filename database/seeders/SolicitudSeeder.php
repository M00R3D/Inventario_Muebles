<?php
// app/Database/Seeders/SolicitudSeeder.php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Solicitud;
class SolicitudSeeder extends Seeder
{
    public function run(): void
    {
        Solicitud::insert([
            [
                'fecha_inicio' => now(),
                'fecha_fin' => null,
                'nota' => 'Solicito reparación de escritorio',
                'mueble_id' => 1,
                'persona_id' => 1,
                'estado' => 'pendiente',
            ],
            [
                'fecha_inicio' => now(),
                'fecha_fin' => now()->addDays(2),
                'nota' => 'Cambio de silla',
                'mueble_id' => 2,
                'persona_id' => 2,
                'estado' => 'aprobada',
            ],
        ]);
    }
}
