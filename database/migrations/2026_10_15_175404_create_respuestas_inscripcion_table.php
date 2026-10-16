<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('respuestas_inscripcion', function (Blueprint $table) {
            $table->id();

            $table->foreignId('inscripcion_id')
                ->constrained('inscripciones')
                ->restrictOnDelete();

            $table->foreignId('campo_id')
                ->constrained('campos_actividad')
                ->restrictOnDelete();

            $table->text('valor')->nullable();

            $table->timestamps();

            // Una respuesta por campo y por inscripción
            $table->unique(['inscripcion_id', 'campo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('respuestas_inscripcion');
    }
};
