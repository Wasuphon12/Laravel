<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Orders that were already paid before this release may still have
        // products marked as reserved.  They are final sales, so lock them.
        $soldProductIds = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.payment_status', 'paid')
            ->pluck('order_items.product_id');

        DB::table('products')
            ->whereIn('id', $soldProductIds)
            ->where('status', 'reserved')
            ->update(['status' => 'sold']);

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('condition_grade');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->enum('condition_grade', ['A', 'B', 'C'])->default('A')->after('price');
        });
    }
};
