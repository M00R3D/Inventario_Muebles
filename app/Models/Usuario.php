<?php
// app/Models/Usuario.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Usuario extends Model
{
    protected $table = 'usuarios';
    protected $fillable = ['nombre', 'apellido', 'email', 'password', 'rol', 'area_id'];

    public function area()
    {return $this->belongsTo(Area::class, 'area_id');}
        public function muebles()
        {return $this->hasMany(Mueble::class, 'persona_id');}
    public function solicitudes()
    {return $this->hasMany(Solicitud::class, 'persona_id');}
}
