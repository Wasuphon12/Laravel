<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_slip_path')->nullable()->after('transaction_ref');
            $table->enum('slip_status', ['not_submitted', 'pending', 'verified', 'rejected'])->default('not_submitted')->after('payment_slip_path');
            $table->timestamp('payment_submitted_at')->nullable()->after('slip_status');
            $table->timestamp('payment_verified_at')->nullable()->after('payment_submitted_at');
            $table->foreignId('payment_verified_by')->nullable()->after('payment_verified_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['payment_verified_by']);
            $table->dropColumn(['payment_slip_path', 'slip_status', 'payment_submitted_at', 'payment_verified_at', 'payment_verified_by']);
        });
    }
};
