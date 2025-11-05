<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('muebles', function (Blueprint $table) {
            $table->string('marca', 200)->nullable()->after('ruta_img');
            $table->string('modelo', 200)->nullable()->after('marca');
            $table->unsignedInteger('categoria_id')->nullable()->after('modelo');
            $table->foreign('categoria_id')->references('id')->on('categorias')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('muebles', function (Blueprint $table) {
            $table->dropForeign(['categoria_id']);
            $table->dropColumn(['categoria_id', 'modelo', 'marca']);
        });
    }
};