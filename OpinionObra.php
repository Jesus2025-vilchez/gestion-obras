<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpinionObra extends Model
{
    use HasFactory;

    protected $table = 'opiniones_obras';

    protected $fillable = [
        'opinion',
        'respuesta',
        'estado',
        'obra_id',
        'user_id',
        'admin_id'
    ];

    public function obra()
    {
        return $this->belongsTo(Obra::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
