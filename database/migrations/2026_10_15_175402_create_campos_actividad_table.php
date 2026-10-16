<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('campos_actividad', function (Blueprint $table) {
            $table->id();

            $table->foreignId('actividad_id')
                ->constrained('actividades')
                ->restrictOnDelete();

            $table->string('nombre', 100);
            $table->string('etiqueta', 150);

            $table->enum('tipo', [
                'texto',
                'numero',
                'fecha',
                'seleccion',
                'texto_largo',
                'booleano'
            ]);

            $table->json('opciones')->nullable();

            $table->boolean('obligatorio')->default(false);
            $table->unsignedInteger('orden')->default(0);

            $table->timestamps();

            $table->unique(['actividad_id', 'nombre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campos_actividad');
    }
};
