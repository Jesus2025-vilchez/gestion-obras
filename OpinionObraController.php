<?php

namespace App\Http\Controllers;

use App\Models\OpinionObra;
use App\Models\Obra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OpinionObraController extends Controller
{
    public function store(Request $request, $obra_id)
    {
        $request->validate([
            'opinion' => 'required|string'
        ]);

        $opinion = OpinionObra::create([
            'opinion' => $request->opinion,
            'obra_id' => $obra_id,
            'user_id' => Auth::id(),
            'estado' => 'pendiente'
        ]);

        return response()->json([
            'id' => $opinion->id,
            'opinion' => $opinion->opinion,
            'created_at' => $opinion->created_at->diffForHumans(),
            'user' => [
                'id' => $opinion->user->id,
                'name' => $opinion->user->name
            ]
        ]);
    }

    public function responder(Request $request, $obra_id, OpinionObra $opinion)
    {
        $request->validate([
            'respuesta' => 'required|string'
        ]);
        
        \Log::info('Respuesta enviada correctamente', [
            'obra_id' => $obra_id,
            'opinion_id' => $opinion->id
        ]);

        $opinion->respuesta = $request->respuesta;
        $opinion->admin_id = Auth::id();
        $opinion->estado = 'respondida';
        $result = $opinion->save();

        return response()->json([
            'success' => true,
            'message' => 'Respuesta enviada correctamente',
            'respuesta' => $opinion->respuesta,
            'created_at' => now()->diffForHumans(),
            'admin' => [
                'id' => Auth::id(),
                'name' => Auth::user()->name
            ]
        ]);
    }

    public function getComments($obra_id)
    {
        $comments = OpinionObra::where('obra_id', $obra_id)
            ->with(['user', 'admin'])
            ->orderBy('created_at', 'desc')
            ->get();

        $formattedComments = $comments->map(function($comment) { 
            $respuestasCount = $comment->respuesta ? 1 : 0;
            
           
            return [
                    'id' => $comment->id,
                    'opinion' => $comment->opinion,
                    'respuesta' => $comment->respuesta,
                    'estado' => $comment->estado,
                    'created_at' => $comment->created_at->diffForHumans(),
                    'respuestas_count' => $respuestasCount,
                    'user' => [
                        'id' => $comment->user->id,
                        'name' => $comment->user->name
                    ],
                    'admin' => $comment->admin ? [
                        'id' => $comment->admin->id,
                        'name' => $comment->admin->name
                    ] : null
                ];
            }); 
        return response()->json($formattedComments);
    }
}
