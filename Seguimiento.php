<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seguimiento extends Model
{
    use HasFactory;

    protected $table = 'seguimientos';

    protected $fillable = [
        'nombre_obra',
        'fecha_inicio',
        'fecha_fin',
        'avance',
        'descripcion_avance',
        'obra_id',
        'user_id'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'avance' => 'decimal:2'
    ];

    public function obra()
    {
        return $this->belongsTo(Obra::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
