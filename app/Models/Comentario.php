<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    protected $table = 'comentarios';
    protected $fillable = ['usuario_id','mueble_id','comentario'];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function mueble()
    {
        return $this->belongsTo(Mueble::class, 'mueble_id');
    }
}