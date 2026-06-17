<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->after('id');
            $table->unsignedBigInteger('power_id')->after('product_id');
            $table->enum('type', ['IN', 'OUT'])->after('power_id');
            $table->integer('quantity')->after('type');

            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('power_id')->references('id')->on('powers');
        });
    }

    public function down(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['power_id']);
            $table->dropColumn(['product_id', 'power_id', 'type', 'quantity']);
        });
    }
};