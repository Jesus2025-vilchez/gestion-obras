<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('activos', function (Blueprint $table) {
            $table->id(); 
            $table->string('tipo_activo', 100);
            $table->text('descripcion');
            $table->date('fecha_registro');
            $table->enum('estado', ['disponible', 'en_uso', 'mantenimiento', 'inactivo'])
                  ->default('disponible');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('obra_id')->nullable()->constrained('obras')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('activos');
    }
};
