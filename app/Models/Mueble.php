<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Mueble extends Model
{
    protected $table = 'muebles';
    protected $fillable = ['codigo', 'descripcion', 'fecha_registro', 'monto_unitario', 'nota', 'ruta_img', 'persona_id', 'estado'];
        public function usuario()
        {return $this->belongsTo(Usuario::class, 'persona_id');}
    public function solicitudes()
    {return $this->hasMany(Solicitud::class, 'mueble_id');}
}
