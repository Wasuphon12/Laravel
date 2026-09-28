<?php

namespace Database\Seeders;

use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Database\Seeder;

class WasuShopDemoProductsSeeder extends Seeder
{
    public function run(): void
    {
        $dealer = User::query()
            ->where('email', 'bankchannel010@gmail.com')
            ->where('role', 'dealer')
            ->firstOrFail();

        $products = [
            ['name' => 'ASUS TUF Gaming F15 i5 / RTX 3050', 'category' => 'laptop', 'price' => 18900, 'stock' => 2, 'specs' => ['cpu' => 'Intel Core i5-11400H', 'gpu' => 'NVIDIA GeForce RTX 3050', 'ram' => '16GB DDR4', 'storage' => '512GB NVMe SSD'], 'description' => 'โน้ตบุ๊กเกมมิ่งพร้อมใช้งาน จอ 144Hz เหมาะสำหรับเล่นเกมและทำงานกราฟิก', 'image' => 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?auto=format&fit=crop&w=1200&q=85'],
            ['name' => 'Lenovo ThinkPad X1 Carbon Gen 8', 'category' => 'laptop', 'price' => 15900, 'stock' => 1, 'specs' => ['cpu' => 'Intel Core i7-10510U', 'ram' => '16GB LPDDR3', 'storage' => '512GB SSD', 'display' => '14-inch FHD'], 'description' => 'โน้ตบุ๊กบางเบาสำหรับทำงาน พกพาสะดวก คีย์บอร์ดไทยแท้', 'image' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=1200&q=85'],
            ['name' => 'AMD Ryzen 7 5800X', 'category' => 'cpu', 'price' => 4900, 'stock' => 3, 'specs' => ['socket' => 'AM4', 'cores' => '8 Cores / 16 Threads', 'base_clock' => '3.8GHz'], 'description' => 'ซีพียูสำหรับเครื่องเกมและเครื่องทำงาน ผ่านการทดสอบการใช้งานแล้ว', 'image' => 'https://images.unsplash.com/photo-1555617981-dac3880eac6e?auto=format&fit=crop&w=1200&q=85'],
            ['name' => 'MSI GeForce RTX 3070 Ventus 3X', 'category' => 'gpu', 'price' => 10900, 'stock' => 2, 'specs' => ['gpu' => 'NVIDIA GeForce RTX 3070', 'vram' => '8GB GDDR6', 'interface' => 'PCIe 4.0'], 'description' => 'การ์ดจอแรงสำหรับเล่นเกมระดับ 2K ทดสอบอุณหภูมิและพอร์ตภาพเรียบร้อย', 'image' => 'https://images.unsplash.com/photo-1591488320449-011701bb6704?auto=format&fit=crop&w=1200&q=85'],
            ['name' => 'Kingston Fury Beast DDR4 32GB', 'category' => 'ram', 'price' => 1850, 'stock' => 4, 'specs' => ['capacity' => '32GB (16GB x 2)', 'speed' => '3200MHz', 'type' => 'DDR4'], 'description' => 'แรมคู่สำหรับเครื่องเกมและงานทั่วไป ทดสอบ MemTest ก่อนลงขาย', 'image' => 'https://images.unsplash.com/photo-1562976540-1502c2145186?auto=format&fit=crop&w=1200&q=85'],
            ['name' => 'WD Black SN770 NVMe SSD 1TB', 'category' => 'storage', 'price' => 2100, 'stock' => 5, 'specs' => ['capacity' => '1TB', 'interface' => 'PCIe Gen4 x4', 'form_factor' => 'M.2 2280'], 'description' => 'SSD NVMe ความเร็วสูง ตรวจสอบสุขภาพไดรฟ์และความจุแล้ว', 'image' => 'https://images.unsplash.com/photo-1591488320449-011701bb6704?auto=format&fit=crop&w=1200&q=85'],
            ['name' => 'Gigabyte B550M DS3H', 'category' => 'motherboard', 'price' => 2150, 'stock' => 2, 'specs' => ['chipset' => 'AMD B550', 'socket' => 'AM4', 'form_factor' => 'Micro ATX'], 'description' => 'เมนบอร์ด AM4 พร้อม Wi-Fi slot และพอร์ตครบสำหรับประกอบเครื่อง', 'image' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1200&q=85'],
            ['name' => 'Corsair RM750x 750W 80+ Gold', 'category' => 'psu', 'price' => 2450, 'stock' => 2, 'specs' => ['power' => '750W', 'rating' => '80 Plus Gold', 'modular' => 'Fully Modular'], 'description' => 'พาวเวอร์ซัพพลายแบบถอดสายได้ เหมาะกับคอมเกมมิ่งและเวิร์กสเตชัน', 'image' => 'https://images.unsplash.com/photo-1675893857450-783969c8922f?auto=format&fit=crop&w=1200&q=85'],
            ['name' => 'AOC 24G2SP 24 นิ้ว 165Hz', 'category' => 'monitor', 'price' => 3650, 'stock' => 3, 'specs' => ['display' => '24-inch IPS', 'refresh_rate' => '165Hz', 'resolution' => 'Full HD'], 'description' => 'จอเกมมิ่ง IPS สีสวย ลื่นไหล ตรวจสอบ dead pixel ก่อนจัดส่ง', 'image' => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?auto=format&fit=crop&w=1200&q=85'],
            ['name' => 'Logitech G Pro X Superlight', 'category' => 'accessory', 'price' => 1890, 'stock' => 4, 'specs' => ['type' => 'Wireless Gaming Mouse', 'sensor' => 'HERO 25K', 'weight' => '63g'], 'description' => 'เมาส์เกมมิ่งไร้สาย น้ำหนักเบา ทดสอบคลิกและล้อเลื่อนเรียบร้อย', 'image' => 'https://images.unsplash.com/photo-1527814050087-3793815479db?auto=format&fit=crop&w=1200&q=85'],
        ];

        foreach ($products as $item) {
            $product = $dealer->products()->updateOrCreate(
                ['name' => $item['name']],
                [
                    'category' => $item['category'],
                    'price' => $item['price'],
                    'stock_quantity' => $item['stock'],
                    'specs' => $item['specs'],
                    'description' => $item['description'],
                    'status' => 'available',
                ],
            );

            ProductImage::updateOrCreate(
                ['product_id' => $product->id, 'is_primary' => true],
                ['image_url' => $item['image']],
            );
        }
    }
}
