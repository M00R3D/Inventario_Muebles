<?php
// app/Models/Mueble.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Mueble extends Model
{
    protected $table = 'muebles';
    protected $fillable = [
        'codigo',
        'descripcion',
        'fecha_registro',
        'monto_unitario',
        'nota',
        'ruta_img',
        'persona_id',
        'responsable_id',
        'estado'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'persona_id');
    }

    public function responsable()
    {
        return $this->belongsTo(Usuario::class, 'responsable_id');
    }

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'mueble_id');
    }

    protected static function booted()
    {
        static::created(function (Mueble $m) {
            if (!empty($m->codigo)) return;
            $newCodigo = 'M' . str_pad($m->id, 4, '0', STR_PAD_LEFT);
            $m->codigo = $newCodigo;
            $m->saveQuietly();
        });
    }
}
