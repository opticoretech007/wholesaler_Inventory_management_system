<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('powers', function (Blueprint $table) {
            $table->foreignId('subclass_id')->nullable()->after('category')
                  ->constrained('subclasses')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('powers', function (Blueprint $table) {
            $table->dropForeign(['subclass_id']);
            $table->dropColumn('subclass_id');
        });
    }
};