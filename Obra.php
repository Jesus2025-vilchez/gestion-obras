<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Obra extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'ubicacion',
        'fecha_inicio',
        'fecha_fin',
        'presupuesto',
        'estado',
        'cronograma_pdf',
        'user_id'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'presupuesto' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function seguimientos()
    {
        return $this->hasMany(Seguimiento::class);
    }

    public function activos()
    {
        return $this->hasMany(Activo::class);
    }

    public function opiniones()
    {
        return $this->hasMany(OpinionObra::class);
    }
}
