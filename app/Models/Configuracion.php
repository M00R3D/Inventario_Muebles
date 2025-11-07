<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $table = 'configuracion';
    protected $fillable = ['clave', 'nombre', 'ruta_img'];
    public static function getRuta(string $clave, ?string $fallback = null): ?string
    {
        $r = static::where('clave', $clave)->first();
        return $r ? $r->ruta_img : $fallback;
    }
    public static function url(string $clave, ?string $fallback = null): ?string
    {
        $ruta = static::getRuta($clave, $fallback);
        return $ruta ? asset($ruta) : ($fallback ? asset($fallback) : null);
    }
}