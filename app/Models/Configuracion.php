<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $table = 'configuracion';
    protected $fillable = ['clave', 'nombre', 'ruta_img', 'normal_color', 'hover_color'];
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
    public static function getColor(string $clave, string $fallback = '#f59e0b'): string
    {
        $r = static::where('clave', $clave)->first();
        return $r && $r->normal_color ? $r->normal_color : $fallback;
    }
    public static function getHoverColor(string $clave, ?string $fallback = null): string
    {
        $r = static::where('clave', $clave)->first();
        if ($r && $r->hover_color) return $r->hover_color;
        return $fallback ?? static::getColor($clave, '#f59e0b');
    }
}