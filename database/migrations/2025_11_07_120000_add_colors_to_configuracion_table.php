<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('configuracion', function (Blueprint $table) {
            $table->string('normal_color', 7)->nullable()->after('ruta_img')->comment('color hex para estado normal, p.ej. #ffcc00');
            $table->string('hover_color', 7)->nullable()->after('normal_color')->comment('color hex para hover');
        });
    }

    public function down(): void
    {
        Schema::table('configuracion', function (Blueprint $table) {
            $table->dropColumn(['normal_color', 'hover_color']);
        });
    }
};