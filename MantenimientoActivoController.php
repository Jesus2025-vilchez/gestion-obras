<?php

namespace App\Http\Controllers;

use App\Models\Activo;
use App\Models\MantenimientoActivo;
use Illuminate\Http\Request;

class MantenimientoActivoController extends Controller
{
    public function index(Activo $activo)
    {
        $mantenimientos = $activo->mantenimientos()->get();
        return view('mantenimientos.index', compact('activo', 'mantenimientos'));
    }

    public function store(Request $request, Activo $activo)
    {
        $request->validate([
            'descripcion' => ['required', 'string'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'estado' => ['required', 'in:realizado,no_realizado']
        ]);

        $mantenimiento = new MantenimientoActivo($request->all());
        $mantenimiento->activo_id = $activo->id;
        $mantenimiento->save();

        return redirect()->route('activos.mantenimientos.index', $activo)
            ->with('success', 'Mantenimiento registrado exitosamente');
    }

    public function update(Request $request, Activo $activo, MantenimientoActivo $mantenimiento)
    {
        $request->validate([
            'descripcion' => ['required', 'string'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'estado' => ['required', 'in:realizado,no_realizado']
        ]);

        $mantenimiento->update($request->all());

        return redirect()->route('activos.mantenimientos.index', $activo)
            ->with('success', 'Mantenimiento actualizado exitosamente');
    }

    public function destroy(Activo $activo, MantenimientoActivo $mantenimiento)
    {
        $mantenimiento->delete();
        return redirect()->route('activos.mantenimientos.index', $activo)
            ->with('success', 'Mantenimiento eliminado exitosamente');
    }
}
