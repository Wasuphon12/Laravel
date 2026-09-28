<?php

namespace App\Console\Commands;

use App\Models\ProductImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Throwable;

class LocalizeProductImages extends Command
{
    protected $signature = 'marketplace:localize-product-images {--force : Download existing external product images into local storage}';

    protected $description = 'Move existing product images from external URLs into local storage';

    public function handle(): int
    {
        if (! $this->option('force')) {
            $this->error('กรุณาระบุ --force เพื่อยืนยันการย้ายรูปสินค้าจากภายนอกเข้าสู่ระบบ');

            return self::FAILURE;
        }

        $images = ProductImage::query()
            ->where('image_url', 'not like', '/storage/%')
            ->get();

        $migrated = 0;
        $failed = 0;

        foreach ($images as $image) {
            try {
                $response = Http::timeout(20)->get($image->image_url);

                if (! $response->successful() || ! str_starts_with((string) $response->header('Content-Type'), 'image/')) {
                    $failed++;
                    $this->warn("ข้ามรูป #{$image->id}: ไม่พบไฟล์รูปที่ใช้งานได้");

                    continue;
                }

                $extension = match (strtolower(explode(';', (string) $response->header('Content-Type'))[0])) {
                    'image/png' => 'png',
                    'image/webp' => 'webp',
                    default => 'jpg',
                };
                $path = "products/{$image->product_id}/image-{$image->id}.{$extension}";
                Storage::disk('public')->put($path, $response->body());
                $image->update(['image_url' => Storage::url($path)]);
                $migrated++;
            } catch (Throwable) {
                $failed++;
                $this->warn("ข้ามรูป #{$image->id}: ดาวน์โหลดไม่สำเร็จ");
            }
        }

        $this->info("ย้ายรูปเข้าในระบบแล้ว {$migrated} รูป; ไม่สำเร็จ {$failed} รูป");

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
