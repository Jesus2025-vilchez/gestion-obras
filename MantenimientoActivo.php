<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MantenimientoActivo extends Model
{
    use HasFactory;

    protected $table = 'mantenimientos_activos';

    protected $fillable = [
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'activo_id'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date'
    ];

    public function activo()
    {
        return $this->belongsTo(Activo::class);
    }
}
