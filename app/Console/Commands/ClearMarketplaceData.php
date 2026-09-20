<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ClearMarketplaceData extends Command
{
    protected $signature = 'marketplace:clear-data {--force : Clear non-admin accounts and all marketplace data}';

    protected $description = 'Clear marketplace demo data while preserving administrator accounts';

    public function handle(): int
    {
        if (! $this->option('force')) {
            $this->error('กรุณาระบุ --force เพื่อยืนยันการล้างข้อมูล');

            return self::FAILURE;
        }

        if (DB::table('users')->where('role', 'admin')->doesntExist()) {
            $this->error('ไม่พบผู้ดูแลระบบ จึงยกเลิกเพื่อป้องกันการลบบัญชีทั้งหมด');

            return self::FAILURE;
        }

        $nonAdminEmails = DB::table('users')->where('role', '!=', 'admin')->pluck('email');

        DB::transaction(function () use ($nonAdminEmails): void {
            DB::table('messages')->delete();
            DB::table('dealer_reviews')->delete();
            DB::table('disputes')->delete();
            DB::table('order_items')->delete();
            DB::table('orders')->delete();
            DB::table('product_images')->delete();
            DB::table('products')->delete();
            DB::table('dealer_profiles')->delete();
            DB::table('password_reset_tokens')->whereIn('email', $nonAdminEmails)->delete();
            DB::table('users')->where('role', '!=', 'admin')->delete();
        });

        foreach ([
            storage_path('app/public/products'),
            storage_path('app/public/payment-slips'),
            storage_path('app/public/kyc-documents'),
            storage_path('app/public/dispute-evidence'),
        ] as $directory) {
            if (File::isDirectory($directory)) {
                File::cleanDirectory($directory);
            }
        }

        $this->info('ล้างข้อมูลมาร์เก็ตเพลสและบัญชีที่ไม่ใช่ผู้ดูแลระบบแล้ว');

        return self::SUCCESS;
    }
}
