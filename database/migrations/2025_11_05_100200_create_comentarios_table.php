<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('comentarios', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('usuario_id')->nullable();
            $table->unsignedInteger('mueble_id');
            $table->text('comentario')->nullable(false);
            $table->timestamps();

            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('set null');
            $table->foreign('mueble_id')->references('id')->on('muebles')->onDelete('cascade');
            $table->index('mueble_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comentarios');
    }
};