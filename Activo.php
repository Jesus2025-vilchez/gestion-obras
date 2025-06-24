<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activo extends Model
{
    use HasFactory;

    protected $fillable = [  
        'tipo_activo',
        'descripcion',
        'fecha_registro',
        'estado',
        'user_id',
        'obra_id' 
    ];

    protected $casts = [
        'fecha_registro' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mantenimientos()
    {
        return $this->hasMany(MantenimientoActivo::class);
    }

    public function obra()
    {
        return $this->belongsTo(Obra::class);
    }
}
