<?php

namespace Database\Seeders;

use App\Models\DealerProfile;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DiversifyMarketplaceSeeder extends Seeder
{
    public function run(): void
    {
        $demoDealer = User::query()->where('email', 'demo-dealer@secondpc.test')->first();

        if ($demoDealer) {
            $keepIds = $demoDealer->products()->where('status', 'available')->latest('id')->limit(10)->pluck('id');
            $demoDealer->products()
                ->whereNotIn('id', $keepIds)
                ->whereDoesntHave('orderItem')
                ->delete();
        }

        $shops = [
            ['name' => 'แดงเดือด Gaming Gear', 'email' => 'redgear@secondpc.test', 'store' => 'RedGear Gaming', 'bank' => 'SCB', 'account' => '098-7-65432-1', 'promptpay' => '0823456789'],
            ['name' => 'ByteCraft Hardware', 'email' => 'bytecraft@secondpc.test', 'store' => 'ByteCraft Hardware', 'bank' => 'KBank', 'account' => '123-0-98765-4', 'promptpay' => '0834567890'],
            ['name' => 'Tech Renew Lab', 'email' => 'techrenew@secondpc.test', 'store' => 'Tech Renew Lab', 'bank' => 'Krungthai', 'account' => '456-2-12345-0', 'promptpay' => '0845678901'],
        ];

        $catalog = [
            ['gpu', 'NVIDIA GeForce RTX 4070 Super', 19500, ['gpu' => 'NVIDIA RTX 40 Series', 'vram' => '12GB GDDR6X', 'port' => 'HDMI / DisplayPort']],
            ['gpu', 'AMD Radeon RX 7800 XT', 14900, ['gpu' => 'AMD Radeon RX 7000 Series', 'vram' => '16GB GDDR6', 'port' => 'HDMI / DisplayPort']],
            ['cpu', 'Intel Core i5-13600K', 7800, ['cpu' => 'Intel Core i5-13600K', 'cores' => '14 Cores / 20 Threads', 'socket' => 'LGA1700']],
            ['cpu', 'AMD Ryzen 5 7600X', 6500, ['cpu' => 'AMD Ryzen 5 7600X', 'cores' => '6 Cores / 12 Threads', 'socket' => 'AM5']],
            ['ram', 'Kingston Fury Beast 32GB DDR5', 2900, ['ram' => '32GB DDR5', 'speed' => '6000MHz', 'kit' => '16GB x 2']],
            ['ram', 'Corsair Vengeance 16GB DDR4', 1300, ['ram' => '16GB DDR4', 'speed' => '3200MHz', 'kit' => '8GB x 2']],
            ['storage', 'Samsung 990 PRO 1TB NVMe SSD', 2800, ['storage' => '1TB NVMe SSD', 'interface' => 'PCIe 4.0', 'read' => '7450MB/s']],
            ['storage', 'WD Black SN850X 2TB NVMe SSD', 4500, ['storage' => '2TB NVMe SSD', 'interface' => 'PCIe 4.0', 'read' => '7300MB/s']],
            ['motherboard', 'ASUS TUF Gaming B650-PLUS', 5200, ['chipset' => 'AMD B650', 'socket' => 'AM5', 'memory' => 'DDR5']],
            ['psu', 'Corsair RM750e 750W', 2800, ['power' => '750W', 'rating' => '80+ Gold', 'modular' => 'Fully Modular']],
            ['monitor', 'AOC 24G2SP 165Hz Gaming Monitor', 3900, ['display' => '24-inch IPS', 'refresh_rate' => '165Hz', 'resolution' => 'FHD']],
            ['accessory', 'Logitech G Pro X Superlight 2', 2900, ['type' => 'Wireless Gaming Mouse', 'sensor' => 'HERO 2', 'weight' => '60g']],
        ];

        foreach ($shops as $shopIndex => $shop) {
            $dealer = User::updateOrCreate(
                ['email' => $shop['email']],
                ['name' => $shop['name'], 'password' => Hash::make('password'), 'role' => 'dealer'],
            );

            DealerProfile::updateOrCreate(
                ['user_id' => $dealer->id],
                ['store_name' => $shop['store'], 'id_card_url' => 'https://placehold.co/800x500/7f1d1d/f9fafb?text=KYC+Approved', 'bank_name' => $shop['bank'], 'bank_account' => $shop['account'], 'promptpay_id' => $shop['promptpay'], 'status' => 'approved'],
            );

            foreach ($catalog as $productIndex => [$category, $name, $price, $specs]) {
                $product = $dealer->products()->updateOrCreate(
                    ['name' => $name],
                    ['name' => $name, 'category' => $category, 'price' => $price + ($shopIndex * 150), 'specs' => $specs, 'description' => "สินค้า {$name} มือสองผ่านการทดสอบโดย {$shop['store']} พร้อมรับประกันร้าน 30 วัน", 'status' => 'available'],
                );
                ProductImage::updateOrCreate(
                    ['product_id' => $product->id, 'is_primary' => true],
                    ['image_url' => 'https://placehold.co/800x500/7f1d1d/f9fafb?text='.urlencode($name)],
                );
            }
        }
    }
}
