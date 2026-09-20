<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dealer_profiles', function (Blueprint $table) {
            $table->string('promptpay_id', 15)->nullable()->unique()->after('bank_account');
        });
    }

    public function down(): void
    {
        Schema::table('dealer_profiles', function (Blueprint $table) {
            $table->dropUnique(['promptpay_id']);
            $table->dropColumn('promptpay_id');
        });
    }
};
