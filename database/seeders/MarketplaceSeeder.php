<?php

namespace Database\Seeders;

use App\Models\DealerProfile;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MarketplaceSeeder extends Seeder
{
    public function run(): void
    {
        $dealer = User::updateOrCreate(
            ['email' => 'demo-dealer@secondpc.test'],
            ['name' => 'SecondPC Demo Store', 'password' => Hash::make('password'), 'role' => 'dealer'],
        );

        DealerProfile::updateOrCreate(
            ['user_id' => $dealer->id],
            ['store_name' => 'SecondPC Demo Store', 'id_card_url' => 'https://placehold.co/800x500?text=KYC+Approved', 'bank_name' => 'Kasikornbank', 'bank_account' => '123-4-56789-0', 'promptpay_id' => '0812345678', 'status' => 'approved'],
        );

        $products = [
            ['name' => 'Lenovo ThinkPad T480', 'price' => 12500, 'specs' => ['cpu' => 'Intel Core i5-8350U', 'ram' => '16GB DDR4', 'storage' => '512GB SSD', 'display' => '14-inch FHD'], 'description' => 'โน้ตบุ๊กทำงานยอดนิยม สภาพสวย แบตเตอรี่ใช้งานได้ดี พร้อมอะแดปเตอร์แท้', 'image' => 'https://placehold.co/800x500/7f1d1d/f9fafb?text=Lenovo+ThinkPad+T480'],
            ['name' => 'Dell OptiPlex 7080 SFF', 'price' => 10900, 'specs' => ['cpu' => 'Intel Core i5-10500', 'ram' => '16GB DDR4', 'storage' => '256GB NVMe SSD', 'form_factor' => 'Small Form Factor'], 'description' => 'Desktop ขนาดกะทัดรัด เหมาะสำหรับสำนักงานและทำงานทั่วไป ผ่านการทดสอบครบทุกพอร์ต', 'image' => 'https://placehold.co/800x500/7f1d1d/f9fafb?text=Dell+OptiPlex+7080'],
            ['name' => 'ASUS ROG Strix G15 Gaming', 'price' => 22900, 'specs' => ['cpu' => 'AMD Ryzen 7 4800H', 'ram' => '16GB DDR4', 'gpu' => 'NVIDIA RTX 3060', 'storage' => '1TB SSD'], 'description' => 'เกมมิ่งโน้ตบุ๊กแรงสำหรับเล่นเกมและตัดต่อ มีรอยใช้งานเล็กน้อยบริเวณฝาหลัง', 'image' => 'https://placehold.co/800x500/7f1d1d/f9fafb?text=ASUS+ROG+Strix+G15'],
            ['name' => 'Apple MacBook Air M1', 'price' => 18900, 'specs' => ['chip' => 'Apple M1', 'memory' => '8GB Unified Memory', 'storage' => '256GB SSD', 'display' => '13.3-inch Retina'], 'description' => 'MacBook Air M1 เครื่องไทย ใช้งานน้อย สี Space Gray พร้อมสายชาร์จ USB-C', 'image' => 'https://placehold.co/800x500/7f1d1d/f9fafb?text=MacBook+Air+M1'],
            ['name' => 'HP EliteDesk 800 G6 Mini', 'price' => 13900, 'specs' => ['cpu' => 'Intel Core i7-10700T', 'ram' => '16GB DDR4', 'storage' => '512GB NVMe SSD', 'form_factor' => 'Mini PC'], 'description' => 'Mini PC ประหยัดพื้นที่ ประสิทธิภาพสูง เหมาะทำงานหลายจอ มีรอยใช้งานตามอายุเล็กน้อย', 'image' => 'https://placehold.co/800x500/7f1d1d/f9fafb?text=HP+EliteDesk+800+G6'],
        ];

        $catalog = [
            ['Acer Aspire 5 A515', 8900, 'Intel Core i5-1135G7'],
            ['Lenovo IdeaPad Slim 3', 9900, 'AMD Ryzen 5 5500U'],
            ['Dell Latitude 5420', 14800, 'Intel Core i5-1145G7'],
            ['HP ProBook 440 G8', 11900, 'Intel Core i5-1135G7'],
            ['ASUS VivoBook 15', 10900, 'AMD Ryzen 5 5600H'],
            ['Microsoft Surface Laptop 4', 16900, 'AMD Ryzen 5 4680U'],
            ['Apple Mac mini M1', 15900, 'Apple M1'],
            ['Lenovo ThinkCentre M70q', 10500, 'Intel Core i5-10400T'],
            ['Dell Precision 3551', 17900, 'Intel Core i7-10850H'],
            ['HP Z2 Mini G5', 18900, 'Intel Core i7-10700'],
        ];

        foreach (range(6, 100) as $number) {
            [$model, $basePrice, $cpu] = $catalog[($number - 6) % count($catalog)];
            $products[] = [
                'name' => sprintf('%s (%02d)', $model, $number),
                'price' => $basePrice + (($number % 4) * 500),
                'specs' => ['cpu' => $cpu, 'ram' => $number % 2 === 0 ? '16GB DDR4' : '8GB DDR4', 'storage' => $number % 3 === 0 ? '512GB NVMe SSD' : '256GB NVMe SSD', 'warranty' => 'รับประกันร้าน 30 วัน'],
                'description' => sprintf('คอมพิวเตอร์มือสองผ่านการตรวจสอบการทำงานแล้ว รายการสาธิตลำดับที่ %d พร้อมอะแดปเตอร์และรับประกันร้าน 30 วัน', $number),
                'image' => sprintf('https://placehold.co/800x500/7f1d1d/f9fafb?text=%s', urlencode($model)),
            ];
        }

        foreach ($products as $item) {
            $product = $dealer->products()->updateOrCreate(
                ['name' => $item['name']],
                collect($item)->except('image')->all() + ['status' => 'available'],
            );
            ProductImage::updateOrCreate(['product_id' => $product->id, 'is_primary' => true], ['image_url' => $item['image']]);
        }
    }
}
