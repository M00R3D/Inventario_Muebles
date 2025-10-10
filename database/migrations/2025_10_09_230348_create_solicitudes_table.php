<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('fecha_inicio')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('fecha_fin')->nullable();
            $table->string('nota', 500)->nullable();
            $table->unsignedInteger('mueble_id');
            $table->unsignedInteger('persona_id');
            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada'])->default('pendiente');
            $table->timestamps();

            // Foreign keys
            $table->foreign('mueble_id')->references('id')->on('muebles')->onDelete('cascade');
            $table->foreign('persona_id')->references('id')->on('usuarios')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};
