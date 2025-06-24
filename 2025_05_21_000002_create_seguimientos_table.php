<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('seguimientos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_obra');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->decimal('avance', 5, 2)->default(0.00);
            $table->text('descripcion_avance');
            $table->foreignId('obra_id')->constrained('obras')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('seguimientos');
    }
};
