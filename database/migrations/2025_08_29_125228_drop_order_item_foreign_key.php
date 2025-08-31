<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_items', function($table)
        {
            // $table->dropForeign('product_id');
            $table->foreignId('product_id', 'prod_id')->nullable()->constrained('products')->nullOnDelete()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function($table)
        {
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
        });
    }
};
