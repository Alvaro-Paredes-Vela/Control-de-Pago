<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('movimientos_caja', function (Blueprint $table) {
            $table->id();

            $table->foreignId('actividad_id')->nullable()->constrained('actividades');

            $table->foreignId('usuario_id')->constrained('users');

            $table->enum('tipo', ['ingreso', 'egreso']);

            $table->string('concepto', 200);
            $table->decimal('monto', 10, 2);
            $table->dateTime('fecha_movimiento');

            $table->string('comprobante', 255)->nullable();
            $table->text('observaciones')->nullable();

            $table->enum('estado', ['confirmado', 'anulado'])->default('confirmado');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_caja');
    }
};
