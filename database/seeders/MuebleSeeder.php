<?php
// app/Database/Seeders/MuebleSeeder.php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Mueble;

class MuebleSeeder extends Seeder
{
    public function run(): void
    {
        Mueble::insert([
            [
                'codigo' => 'M001',
                'descripcion' => 'Escritorio de oficina',
                'fecha_registro' => now(),
                'monto_unitario' => 1500.00,
                'nota' => 'Buen estado',
                'ruta_img' => null,
                'persona_id' => 1,
                'estado' => 'bueno',
            ],
            [
                'codigo' => 'M002',
                'descripcion' => 'Silla ergonómica',
                'fecha_registro' => now(),
                'monto_unitario' => 800.00,
                'nota' => null,
                'ruta_img' => 'sillaergonomica/1.webp',
                'persona_id' => 2,
                'estado' => 'regular',
            ],
            [
                'codigo' => 'M003',
                'descripcion' => 'Mesa de reuniones grande',
                'fecha_registro' => now(),
                'monto_unitario' => 3200.00,
                'nota' => 'Requiere limpieza',
                'ruta_img' => 'mesareuniones/1.webp',
                'persona_id' => 1,
                'estado' => 'bueno',
            ],
            [
                'codigo' => 'M004',
                'descripcion' => 'Archivador metálico',
                'fecha_registro' => now(),
                'monto_unitario' => 950.00,
                'nota' => null,
                'ruta_img' => 'archivadormetalico/1.jpg',
                'persona_id' => 2,
                'estado' => 'bueno',
            ],
            [
                'codigo' => 'M005',
                'descripcion' => 'Silla de visitas',
                'fecha_registro' => now(),
                'monto_unitario' => 400.00,
                'nota' => 'Pata floja',
                'ruta_img' => 'sillavisitas/1.webp',
                'persona_id' => 3,
                'estado' => 'en_reparacion',
            ],
            [
                'codigo' => 'M006',
                'descripcion' => 'Mesa auxiliar',
                'fecha_registro' => now(),
                'monto_unitario' => 600.00,
                'nota' => null,
                'ruta_img' => 'mesaauxiliar/2.jpg',
                'persona_id' => 1,
                'estado' => 'bueno',
            ],
            [
                'codigo' => 'M007',
                'descripcion' => 'Silla ejecutiva',
                'fecha_registro' => now(),
                'monto_unitario' => 1200.00,
                'nota' => 'Ruedas desgastadas',
                'ruta_img' => 'sillaejecutiva/1.jpg',
                'persona_id' => 2,
                'estado' => 'regular',
            ],
            [
                'codigo' => 'M008',
                'descripcion' => 'Estante de madera',
                'fecha_registro' => now(),
                'monto_unitario' => 700.00,
                'nota' => null,
                'ruta_img' => 'estantemadera/1.png',
                'persona_id' => 3,
                'estado' => 'bueno',
            ],
            [
                'codigo' => 'M009',
                'descripcion' => 'Mesa de juntas pequeña',
                'fecha_registro' => now(),
                'monto_unitario' => 1800.00,
                'nota' => 'Rayada en la superficie',
                'ruta_img' => 'mesajuntaspequenia/1.webp',
                'persona_id' => 1,
                'estado' => 'regular',
            ],
            [
                'codigo' => 'M010',
                'descripcion' => 'Silla plegable',
                'fecha_registro' => now(),
                'monto_unitario' => 250.00,
                'nota' => null,
                'ruta_img' => 'sillaplegable/1.webp',
                'persona_id' => 2,
                'estado' => 'malo',
            ],
        ]);
    }
}
