<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $table = 'notificaciones';
    protected $fillable = ['id_admin','id_usuario','estado','tipo','descripcion','fecha_creacion','fecha_visto','ruta',];

    public function admin()
    {return $this->belongsTo(Usuario::class, 'id_admin');}
    public function usuario()
    {return $this->belongsTo(Usuario::class, 'id_usuario');}
}
