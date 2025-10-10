<?php
// app/Database/Seeders/AreaSeeder.php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Area;
class AreaSeeder extends Seeder
{
    public function run(): void
    {
        Area::insert([
            ['nombre' => 'Administración'],
            ['nombre' => 'Recursos Humanos'],
            ['nombre' => 'Tecnología'],
            ['nombre' => 'Mantenimiento'],
        ]);
    }
}
