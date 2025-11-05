<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $cats = [
            ['nombre' => 'Oficina', 'descripcion' => 'Muebles para oficinas y escritorios'],
            ['nombre' => 'Sala', 'descripcion' => 'Muebles para salas y reuniones'],
            ['nombre' => 'Almacén', 'descripcion' => 'Muebles para almacenaje y archivo'],
            ['nombre' => 'Exterior', 'descripcion' => 'Mobiliario para exterior'],
        ];
        foreach ($cats as $c) {
            Categoria::firstOrCreate(['nombre' => $c['nombre']], $c);
        }
    }
}