<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('muebles', function (Blueprint $table) {
            $table->string('codigo', 50)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('muebles', function (Blueprint $table) {
            $table->string('codigo', 50)->default('mueble_default')->change();
        });
    }
};