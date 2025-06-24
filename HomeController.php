<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Obra;
use App\Models\Activo; // Though not directly used for querying here, good for context if needed later

class HomeController extends Controller
{
    public function index()
    {
        $obras = Obra::with(['user', 'seguimientos'])->get();

        $obras->each(function ($obra) {
            $activosStatusCounts = $obra->activos->groupBy('estado')->map->count();
            $obra->activos_summary = $activosStatusCounts; 
            $obra->total_activos = $obra->activos->count();
        });

        return view('landing', compact('obras'));
    }

    public function show($id)
    {
        $obra = Obra::with(['user', 'seguimientos' => function($query) {
            $query->orderBy('fecha_inicio', 'desc');
        }, 'activos'])->findOrFail($id);

        $activosStatusCounts = $obra->activos->groupBy('estado')->map->count();
        $obra->activos_summary = $activosStatusCounts;
        $obra->total_activos = $obra->activos->count();

        return view('show', compact('obra'));
    }
}
