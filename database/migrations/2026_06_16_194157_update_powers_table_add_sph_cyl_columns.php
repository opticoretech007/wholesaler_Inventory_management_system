<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('powers', function (Blueprint $table) {
            $table->dropColumn('power');
            $table->string('sph')->after('id');
            $table->string('cyl')->nullable()->after('sph');
        });
    }

    public function down(): void
    {
        Schema::table('powers', function (Blueprint $table) {
            $table->dropColumn(['sph', 'cyl']);
            $table->string('power')->after('id');
        });
    }
};