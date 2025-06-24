<?php

namespace App\Http\Controllers;

use App\Models\Obra;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ObraController extends Controller
{
    public function index()
    {
        $responsables= User::where('role', 'admin')->get();
        $obras = Obra::with('user')->get(); 
        return view('obras.index', compact('obras','responsables'));
    }

    public function store(Request $request)
    { 
        $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['required', 'string'],
            'ubicacion' => ['required', 'string', 'max:255'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after:fecha_inicio'],
            'presupuesto' => ['required', 'numeric', 'min:0'],
            'estado' => ['required', 'in:planificacion,en_progreso,pausada,finalizada,cancelada'],
            'cronograma_pdf' => ['nullable', 'file', 'mimes:pdf', 'max:2048']
        ]);

        $data = $request->all();
        
        if ($request->hasFile('cronograma_pdf')) {
            $path = $request->file('cronograma_pdf')->store('cronogramas', 'public');
            $data['cronograma_pdf'] = $path;
        }

        $data['user_id'] = auth()->id();
        
        try {
            $obra = Obra::create($data); 
            return redirect()->route('obras.index')
                ->with('success', 'Obra creada exitosamente.');
        } catch (\Exception $e) { 
            return redirect()->route('obras.index')
                ->with('error', 'Error al crear la obra: ' . $e->getMessage());
        }
    }

    public function edit(Obra $obra)
    {
        return response()->json($obra);
    }

    public function update(Request $request, Obra $obra)
    {
        $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['required', 'string'],
            'ubicacion' => ['required', 'string', 'max:255'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after:fecha_inicio'],
            'presupuesto' => ['required', 'numeric', 'min:0'],
            'estado' => ['required', 'in:planificacion,en_progreso,pausada,finalizada,cancelada'],
            'cronograma_pdf' => ['nullable', 'file', 'mimes:pdf', 'max:2048']
        ]);

        $data = $request->all();

        if ($request->hasFile('cronograma_pdf')) {
            if ($obra->cronograma_pdf) {
                Storage::disk('public')->delete($obra->cronograma_pdf);
            }
            $path = $request->file('cronograma_pdf')->store('cronogramas', 'public');
            $data['cronograma_pdf'] = $path;
        }

        $obra->update($data);

        return redirect()->route('obras.index')
            ->with('success', 'Obra actualizada exitosamente.');
    }

    public function destroy(Obra $obra)
    {
        if ($obra->cronograma_pdf) {
            Storage::disk('public')->delete($obra->cronograma_pdf);
        }
        
        $obra->delete();

        return redirect()->route('obras.index')
            ->with('success', 'Obra eliminada exitosamente.');
    }
}
