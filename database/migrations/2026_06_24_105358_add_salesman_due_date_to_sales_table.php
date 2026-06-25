<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('salesman_id')->nullable()->after('customer_id');
            $table->date('due_date')->nullable()->after('invoice_date');
            $table->foreign('salesman_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['salesman_id']);
            $table->dropColumn(['salesman_id', 'due_date']);
        });
    }
};