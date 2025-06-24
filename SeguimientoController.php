<?php

namespace App\Http\Controllers;

use App\Models\Obra;
use App\Models\Seguimiento;
use Illuminate\Http\Request;
use App\Models\User; // Assuming User model might be needed for responsable, though 'with' handles it

class SeguimientoController extends Controller
{
    public function index(Request $request)
    {
        $seguimientos = Seguimiento::with('user', 'obra')
            ->latest()
            ->get()
            ->groupBy('obra.nombre');

        $obras = Obra::orderBy('nombre')->get();
        return view('seguimientos.index', [
            'seguimientosGrouped' => $seguimientos,
            'obras' => $obras
        ]);
    }

    public function store(Request $request, Obra $obra)
    {
        $request->validate([
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after:fecha_inicio'],
            'avance' => ['required', 'numeric', 'min:0', 'max:100'],
            'descripcion_avance' => ['required', 'string'],
        ]);

        $seguimiento = new Seguimiento($request->all());
        $seguimiento->obra_id = $obra->id;
        $seguimiento->user_id = auth()->id();
        $seguimiento->nombre_obra = $obra->nombre;
        $seguimiento->save();

        return redirect()->route('obras.seguimientos.index', $obra)
            ->with('success', 'Seguimiento registrado exitosamente');
    }

    public function edit(Obra $obra, Seguimiento $seguimiento)
    {
        // This method is called via AJAX to populate the edit modal
        // Ensure the seguimiento belongs to the obra, though route model binding should handle this.
        if ($seguimiento->obra_id != $obra->id) {
            // Or handle this more gracefully, perhaps a 404 or 403 error
            return response()->json(['error' => 'Seguimiento no pertenece a la obra especificada.'], 403);
        }
        return response()->json($seguimiento);
    }

    public function update(Request $request, Obra $obra, Seguimiento $seguimiento)
    {
        $request->validate([
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after:fecha_inicio'],
            'avance' => ['required', 'numeric', 'min:0', 'max:100'],
            'descripcion_avance' => ['required', 'string'],
        ]);

        $seguimiento->update($request->all());

        return redirect()->route('obras.seguimientos.index', $obra)
            ->with('success', 'Seguimiento actualizado exitosamente');
    }

    public function destroy(Obra $obra, Seguimiento $seguimiento)
    {
        $seguimiento->delete();
        return redirect()->route('obras.seguimientos.index', $obra)
            ->with('success', 'Seguimiento eliminado exitosamente');
    }
}
