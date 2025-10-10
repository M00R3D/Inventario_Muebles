<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('muebles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('codigo', 50)->default('mueble_default')->unique();
            $table->string('descripcion', 500)->nullable();
            $table->date('fecha_registro')->default(DB::raw('CURRENT_DATE'));
            $table->decimal('monto_unitario', 10, 2)->default(0.00);
            $table->string('nota', 500)->nullable();
            $table->string('ruta_img', 200)->nullable();
            $table->unsignedInteger('persona_id'); // referencia a usuarios
            $table->enum('estado', ['bueno', 'regular', 'malo', 'en_reparacion'])->default('bueno');
            $table->timestamps();

            // Foreign key
            $table->foreign('persona_id')->references('id')->on('usuarios')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('muebles');
    }
};
