<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('payouts');
        Schema::dropIfExists('dealer_wallets');

        Schema::table('order_items', function (Blueprint $table): void {
            $columns = ['commission_fee', 'net_amount', 'escrow_status', 'completed_at'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('order_items', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table): void {
            $table->decimal('commission_fee', 10, 2)->default(0)->after('price');
            $table->decimal('net_amount', 10, 2)->default(0)->after('commission_fee');
            $table->enum('escrow_status', ['holding', 'released', 'refunded'])->default('holding')->after('delivery_status');
            $table->timestamp('completed_at')->nullable()->after('escrow_status');
        });

        Schema::create('dealer_wallets', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('dealer_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->decimal('balance', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('payouts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('dealer_id')->constrained('users')->restrictOnDelete();
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'processing', 'completed', 'rejected'])->default('pending');
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });
    }
};
