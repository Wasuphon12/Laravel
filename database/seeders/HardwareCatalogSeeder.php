<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Database\Seeder;

class HardwareCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $dealer = User::where('email', 'demo-dealer@secondpc.test')->firstOrFail();
        $items = [
            ['cpu', 'AMD Ryzen 5 5600X', 4300, ['cpu' => 'AMD Ryzen 5 5600X', 'socket' => 'AM4', 'cores' => '6 Cores / 12 Threads']],
            ['gpu', 'NVIDIA GeForce RTX 3060 12GB', 7500, ['gpu' => 'NVIDIA RTX 3060', 'vram' => '12GB GDDR6', 'interface' => 'PCIe 4.0']],
            ['ram', 'Corsair Vengeance 16GB DDR4', 1800, ['ram' => '16GB DDR4', 'speed' => '3200MHz', 'module' => '2 x 8GB']],
            ['storage', 'Samsung 980 Pro 1TB NVMe SSD', 2600, ['storage' => '1TB NVMe SSD', 'interface' => 'PCIe 4.0', 'form_factor' => 'M.2 2280']],
            ['storage', 'Seagate Barracuda 2TB HDD', 1200, ['storage' => '2TB HDD', 'interface' => 'SATA III', 'speed' => '7200 RPM']],
            ['motherboard', 'MSI B550M PRO-VDH WIFI', 2400, ['chipset' => 'AMD B550', 'socket' => 'AM4', 'form_factor' => 'Micro ATX']],
            ['psu', 'Corsair CX650 650W', 1900, ['power' => '650W', 'rating' => '80 Plus Bronze', 'modular' => 'Semi Modular']],
            ['case', 'NZXT H5 Flow', 2100, ['form_factor' => 'ATX Mid Tower', 'color' => 'Black', 'side_panel' => 'Tempered Glass']],
            ['monitor', 'LG UltraGear 24GN600 144Hz', 3900, ['display' => '24-inch IPS', 'refresh_rate' => '144Hz', 'resolution' => 'FHD']],
            ['accessory', 'Logitech G Pro X Superlight', 2200, ['type' => 'Wireless Mouse', 'sensor' => 'HERO 25K', 'color' => 'Black']],
            ['gpu', 'NVIDIA GeForce RTX 5070 12GB', 24500, ['gpu' => 'NVIDIA RTX 5070', 'vram' => '12GB GDDR7', 'interface' => 'PCIe 5.0']],
            ['cpu', 'Intel Core i5-14600K', 9800, ['cpu' => 'Intel Core i5-14600K', 'socket' => 'LGA1700', 'cores' => '14 Cores / 20 Threads']],
            ['cpu', 'AMD Ryzen 7 9700X', 12200, ['cpu' => 'AMD Ryzen 7 9700X', 'socket' => 'AM5', 'cores' => '8 Cores / 16 Threads']],
        ];

        foreach ($items as $index => [$category, $name, $price, $specs]) {
            $product = $dealer->products()->updateOrCreate(
                ['name' => $name],
                [
                    'name' => $name, 'category' => $category, 'price' => $price,
                    'specs' => $specs, 'description' => 'อุปกรณ์คอมพิวเตอร์มือสอง ผ่านการตรวจสอบการใช้งานแล้ว รับประกันร้าน 30 วัน',
                    'status' => 'available',
                ],
            );
            ProductImage::updateOrCreate(
                ['product_id' => $product->id, 'is_primary' => true],
                ['image_url' => 'https://placehold.co/800x500/7f1d1d/f9fafb?text='.urlencode($name)],
            );
        }
    }
}
