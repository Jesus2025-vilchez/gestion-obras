<?php

namespace App\View\Components;

use App\Models\Obra;
use Illuminate\View\Component;

class ComentariosObra extends Component
{
    public $obra;

    public function __construct(Obra $obra)
    {
        $this->obra = $obra;
    }

    public function render()
    {
        return view('components.comentarios-obra');
    }
}
