<?php
// app/Database/Seeders/UsuarioSeeder.php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        Usuario::insert([
            [
                'nombre' => 'Juan',
                'apellido' => 'Pérez',
                'email' => 'juan@correo.com',
                'password' => Hash::make('password'),
                'rol' => 'admin',
                'area_id' => 1,
            ],
            [
                'nombre' => 'Ana',
                'apellido' => 'García',
                'email' => 'ana@correo.com',
                'password' => Hash::make('password'),
                'rol' => 'empleado',
                'area_id' => 2,
            ],
            [
                'nombre' => 'Job',
                'apellido' => 'Moore',
                'email' => 'jobmurdan@hotmail.com',
                'password' => 'secret',
                'rol' => 'admin',
                'area_id' => 3,
            ],
            [
                'nombre' => 'Jason',
                'apellido' => 'Jhonson',
                'email' => 'ad@mail.com',
                'password' => 'secret',
                'rol' => 'admin',
                'area_id' => 3,
            ],
        ]);
    }
}
