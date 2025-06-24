<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('obras', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion');
            $table->string('ubicacion');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->decimal('presupuesto', 15, 2);
            $table->enum('estado', ['planificacion', 'en_progreso', 'pausada', 'finalizada', 'cancelada'])
                  ->default('planificacion');
            $table->string('cronograma_pdf')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('obras');
    }
};
