<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('inscripciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('estudiante_id')->constrained('estudiantes');

            $table->foreignId('actividad_id')->constrained('actividades');

            $table->decimal('costo_acordado', 10, 2);

            $table->enum('estado', ['inscrito', 'cancelado'])->default('inscrito');

            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->unique(['estudiante_id', 'actividad_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscripciones');
    }
};
