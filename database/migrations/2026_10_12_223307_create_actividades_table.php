<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('actividades', function (Blueprint $table) {
            $table->id();

            $table->foreignId('categoria_id')
                ->constrained('categorias');

            $table->foreignId('creado_por')
                ->constrained('users');

            $table->string('nombre', 200);
            $table->text('descripcion')->nullable();
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin')->nullable();
            $table->string('lugar', 200)->nullable();
            $table->decimal('costo', 10, 2)->default(0);
            $table->unsignedInteger('cupo_maximo')->nullable();

            $table->enum('estado', [
                'planificada',
                'inscripciones_abiertas',
                'finalizada',
                'cancelada'
            ])->default('planificada');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actividades');
    }
};
