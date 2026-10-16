<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('inscripcion_id')->constrained('inscripciones');

            $table->foreignId('metodo_pago_id')->constrained('metodos_pago');

            $table->foreignId('recibido_por')->constrained('users');

            $table->decimal('monto', 10, 2);
            $table->dateTime('fecha_pago');

            $table->string('referencia', 100)->nullable();
            $table->text('observaciones')->nullable();

            $table->enum('estado', ['confirmado', 'anulado'])->default('confirmado');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
