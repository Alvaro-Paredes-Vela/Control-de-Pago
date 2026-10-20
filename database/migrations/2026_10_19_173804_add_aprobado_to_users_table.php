<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Agrega el campo de autorización.
        // Los usuarios que ya existen seguirán habilitados.
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('aprobado')->default(true);
        });
    }

    public function down(): void
    {
        // Permite quitar el campo si se revierte la migración.
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('aprobado');
        });
    }
};
