<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('configuracion', function (Blueprint $table) {
            $table->increments('id');
            $table->string('clave', 100)->unique()->comment('clave identificadora, p.ej. icon_muebles');
            $table->string('nombre', 150)->nullable()->comment('nombre legible del ajuste');
            $table->string('ruta_img', 255)->nullable()->comment('ruta pública de la imagen/icono');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion');
    }
};