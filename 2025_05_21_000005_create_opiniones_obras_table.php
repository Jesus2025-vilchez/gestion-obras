<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('opiniones_obras', function (Blueprint $table) {
            $table->id();
            $table->text('opinion');
            $table->text('respuesta')->nullable();
            $table->enum('estado', ['pendiente', 'respondida'])->default('pendiente');
            $table->foreignId('obra_id')->constrained('obras')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('opiniones_obras');
    }
};
