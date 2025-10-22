<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_admin')->nullable();
            $table->unsignedInteger('id_usuario')->nullable();
            $table->string('estado', 20)->default('cerrada');
            $table->string('tipo', 20)->default('prueba');
            $table->string('descripcion', 500)->nullable();
            $table->dateTime('fecha_creacion')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('fecha_visto')->nullable();
            $table->string('ruta', 100)->nullable();
            $table->string('audiencia', 20)->default('todos');
            $table->index('id_usuario');
            $table->index('id_admin');
            $table->index('audiencia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
