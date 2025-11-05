<?php
// app/Database/Seeders/MuebleSeeder.php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Mueble;
use App\Models\Usuario;
use App\Models\Categoria;

class MuebleSeeder extends Seeder
{
    public function run(): void
    {
        $responsable = Usuario::where('email', 'jobmurdan@hotmail.com')->first();
        $responsableId = $responsable ? $responsable->id : null;

        // intentar obtener algunas categorías por nombre; si no existen usar null
        $catOficina = Categoria::where('nombre', 'Oficina')->first();
        $catSala = Categoria::where('nombre', 'Sala')->first();
        $catAlmacen = Categoria::where('nombre', 'Almacén')->first();

        Mueble::insert([
            [
                'codigo' => 'M001',
                'descripcion' => 'Escritorio de oficina',
                'fecha_registro' => now(),
                'monto_unitario' => 1500.00,
                'nota' => 'Buen estado',
                'ruta_img' => null,
                'persona_id' => null,
                'responsable_id' => $responsableId,
                'marca' => 'MarcaA',
                'modelo' => 'Escritorio-120',
                'categoria_id' => $catOficina ? $catOficina->id : null,
                'estado' => 'bueno',
            ],
            [
                'codigo' => 'M002',
                'descripcion' => 'Silla ergonómica',
                'fecha_registro' => now(),
                'monto_unitario' => 800.00,
                'nota' => null,
                'ruta_img' => 'sillaergonomica/1.webp',
                'persona_id' => null,
                'responsable_id' => $responsableId,
                'marca' => 'ErgoCo',
                'modelo' => 'E-200',
                'categoria_id' => $catOficina ? $catOficina->id : null,
                'estado' => 'regular',
            ],
            [
                'codigo' => 'M003',
                'descripcion' => 'Mesa de reuniones grande',
                'fecha_registro' => now(),
                'monto_unitario' => 3200.00,
                'nota' => 'Requiere limpieza',
                'ruta_img' => 'mesareuniones/1.webp',
                'persona_id' => null,
                'responsable_id' => $responsableId,
                'marca' => 'MeetingPro',
                'modelo' => 'M-3000',
                'categoria_id' => $catSala ? $catSala->id : null,
                'estado' => 'bueno',
            ],
            [
                'codigo' => 'M004',
                'descripcion' => 'Archivador metálico',
                'fecha_registro' => now(),
                'monto_unitario' => 950.00,
                'nota' => null,
                'ruta_img' => 'archivadormetalico/1.jpg',
                'persona_id' => null,
                'responsable_id' => $responsableId,
                'marca' => 'ArchiveX',
                'modelo' => 'A-90',
                'categoria_id' => $catAlmacen ? $catAlmacen->id : null,
                'estado' => 'bueno',
            ],
            [
                'codigo' => 'M005',
                'descripcion' => 'Silla de visitas',
                'fecha_registro' => now(),
                'monto_unitario' => 400.00,
                'nota' => 'Pata floja',
                'ruta_img' => 'sillavisitas/1.webp',
                'persona_id' => null,
                'responsable_id' => $responsableId,
                'marca' => 'SimpleSeat',
                'modelo' => 'VS-10',
                'categoria_id' => $catSala ? $catSala->id : null,
                'estado' => 'en_reparacion',
            ],
            [
                'codigo' => 'M006',
                'descripcion' => 'Mesa auxiliar',
                'fecha_registro' => now(),
                'monto_unitario' => 600.00,
                'nota' => null,
                'ruta_img' => 'mesaauxiliar/2.jpg',
                'persona_id' => null,
                'responsable_id' => $responsableId,
                'marca' => 'Auxi',
                'modelo' => 'AX-6',
                'categoria_id' => $catOficina ? $catOficina->id : null,
                'estado' => 'bueno',
            ],
            [
                'codigo' => 'M007',
                'descripcion' => 'Silla ejecutiva',
                'fecha_registro' => now(),
                'monto_unitario' => 1200.00,
                'nota' => 'Ruedas desgastadas',
                'ruta_img' => 'sillaejecutiva/1.jpg',
                'persona_id' => null,
                'responsable_id' => $responsableId,
                'marca' => 'BossSeat',
                'modelo' => 'BX-1',
                'categoria_id' => $catOficina ? $catOficina->id : null,
                'estado' => 'regular',
            ],
            [
                'codigo' => 'M008',
                'descripcion' => 'Estante de madera',
                'fecha_registro' => now(),
                'monto_unitario' => 700.00,
                'nota' => null,
                'ruta_img' => 'estantemadera/1.png',
                'persona_id' => null,
                'responsable_id' => $responsableId,
                'marca' => 'WoodLine',
                'modelo' => 'WL-8',
                'categoria_id' => $catAlmacen ? $catAlmacen->id : null,
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
                'responsable_id' => $responsableId,
                'marca' => 'MeetingPro',
                'modelo' => 'M-1200',
                'categoria_id' => $catSala ? $catSala->id : null,
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
                'responsable_id' => $responsableId,
                'marca' => 'Foldy',
                'modelo' => 'F-1',
                'categoria_id' => $catSala ? $catSala->id : null,
                'estado' => 'malo',
            ],
        ]);
    }
}
