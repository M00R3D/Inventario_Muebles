<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comentario;
use App\Models\Mueble;
use App\Models\Usuario;

class ComentarioSeeder extends Seeder
{
    public function run(): void
    {
        $muebles = Mueble::all();
        $usuarios = Usuario::pluck('id')->all();
        if ($muebles->isEmpty() || empty($usuarios)) return;

        $rows = [];
        foreach ($muebles as $idx => $m) {
            // crear entre 0 y 3 comentarios por mueble
            $count = ($idx % 4); // varía 0..3
            for ($i = 0; $i < $count; $i++) {
                $userId = $usuarios[array_rand($usuarios)];
                $rows[] = [
                    'usuario_id' => $userId,
                    'mueble_id' => $m->id,
                    'comentario' => "Comentario {$i} sobre {$m->codigo} (generado por seeder).",
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        if (!empty($rows)) {
            Comentario::insert($rows);
        }
    }
}