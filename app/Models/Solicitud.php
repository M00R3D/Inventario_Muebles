<?php
// app/Models/Solicitud.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Solicitud extends Model
{
    protected $table = 'solicitudes';
    protected $fillable = ['fecha_inicio', 'fecha_fin', 'nota', 'mueble_id', 'persona_id', 'estado'];
    public function mueble()
    {return $this->belongsTo(Mueble::class, 'mueble_id');}
    public function usuario()
    {return $this->belongsTo(Usuario::class, 'persona_id');}
}
