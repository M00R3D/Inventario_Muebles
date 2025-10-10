<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $table = 'areas';
    protected $fillable = ['nombre'];

    public function usuarios()
    {return $this->hasMany(Usuario::class, 'area_id');}
}
