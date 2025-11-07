<?php
// app/Database/Seeders/DatabaseSeeder.php
namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AreaSeeder::class,
            UsuarioSeeder::class,
            CategoriaSeeder::class,
            MuebleSeeder::class,
            ComentarioSeeder::class,
            SolicitudSeeder::class,
            NotificacionSeeder::class,
            ConfiguracionSeeder::class,
        ]);
    }
}
