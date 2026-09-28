<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('products', 'stock_quantity')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedInteger('stock_quantity')->default(1)->after('price');
            });
        }

        // MySQL uses the old unique index to support the foreign key. Add a
        // normal index first, then remove the one-item-only restriction.
        Schema::table('order_items', function (Blueprint $table) {
            $table->index('product_id', 'order_items_product_id_foreign_index');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropUnique(['product_id']);
        });

        // Products from the former single-item model are exhausted if already
        // reserved or sold; all other existing listings start with one unit.
        DB::table('products')
            ->whereIn('status', ['reserved', 'sold'])
            ->update(['stock_quantity' => 0]);
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->unique('product_id');
            $table->dropIndex('order_items_product_id_foreign_index');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('stock_quantity');
        });
    }
};
