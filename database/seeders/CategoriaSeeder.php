<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $cats = [
            ['nombre' => 'Oficina', 'descripcion' => 'Muebles para oficinas y escritorios', 'ruta_img' => 'oficina/1.jpg'],
            ['nombre' => 'Sala', 'descripcion' => 'Muebles para salas y reuniones', 'ruta_img' => 'sala/1.webp'],
            ['nombre' => 'Almacén', 'descripcion' => 'Muebles para almacenaje y archivo', 'ruta_img' => 'almacen/1.webp'],
            ['nombre' => 'Exterior', 'descripcion' => 'Mobiliario para exterior', 'ruta_img' => 'exterior/1.jpg'],
        ];
        foreach ($cats as $c) {
            Categoria::firstOrCreate(['nombre' => $c['nombre']], $c);
        }
    }
}