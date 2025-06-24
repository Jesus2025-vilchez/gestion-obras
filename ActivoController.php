<?php

namespace App\Http\Controllers;

use App\Models\Obra;
use App\Models\Activo;
use App\Models\User;
use Illuminate\Http\Request;

class ActivoController extends Controller
{
    public function index()
    {
        $activos = Activo::with('user', 'obra')->latest()->get(); // Eager load obra and order by latest
        $responsables = User::where('role', 'admin')->orderBy('name')->get();
        $obras = Obra::orderBy('nombre')->get();  
        return view('activos.index', compact('activos', 'responsables', 'obras')); // Pass obras to view
    }

    public function store(Request $request)
    {
        $request->validate([ 
            'tipo_activo' => ['required', 'string', 'max:100'],
            'descripcion' => ['required', 'string'],
            'fecha_registro' => ['required', 'date'],
            'estado' => ['required', 'in:disponible,en_uso,mantenimiento,inactivo'],
            'user_id' => ['required', 'exists:users,id'],
            'obra_id' => ['nullable', 'exists:obras,id'] // Validate obra_id
        ]);

        $activo = new Activo($request->all());
        $activo->save();

        return redirect()->route('activos.index')
            ->with('success', 'Activo registrado exitosamente');
    }

    public function edit(Activo $activo)
    { 
        return response()->json($activo);  
    }

    public function update(Request $request, Activo $activo)
    {
        $request->validate([ 
            'tipo_activo' => ['required', 'string', 'max:100'],
            'descripcion' => ['required', 'string'],
            'fecha_registro' => ['required', 'date'],
            'estado' => ['required', 'in:disponible,en_uso,mantenimiento,inactivo'],
            'user_id' => ['required', 'exists:users,id'],
            'obra_id' => ['nullable', 'exists:obras,id'] // Validate obra_id
        ]);

        $activo->update($request->all());

        return redirect()->route('activos.index')
            ->with('success', 'Activo actualizado exitosamente');
    }

    public function destroy(Activo $activo)
    {
        $activo->delete();
        return redirect()->route('activos.index')
            ->with('success', 'Activo eliminado exitosamente');
    }
}
