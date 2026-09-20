# SecondPC Marketplace

Marketplace สำหรับซื้อขายคอมพิวเตอร์มือสอง สร้างด้วย Laravel 12 + Breeze (Blade/Tailwind)

## ความสามารถ

- สมัคร/เข้าสู่ระบบ พร้อมบทบาท `customer`, `dealer`, `admin`
- Dealer KYC, จัดการสินค้า, รูปภาพ, สเปก JSON และสถานะสินค้า
- สั่งซื้อสินค้าจากหลายร้านใน Order เดียว พร้อม `order_items` แยก Dealer
- Escrow: เงินถูกพักเมื่อชำระ และปล่อยให้ร้านค้าหลังลูกค้ายืนยันรับสินค้า
- กระเป๋าเงิน/คำขอถอนเงินของ Dealer, ข้อพิพาท และข้อความระหว่างผู้ใช้
- หน้าผู้ดูแลสำหรับอนุมัติ KYC, พิจารณาการถอนเงินและข้อพิพาท

## เริ่มใช้งาน

```powershell
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
php artisan serve
```

เปิด `http://127.0.0.1:8000` แล้วสมัครสมาชิก โดยเลือก **ลูกค้า** หรือ **ร้านค้า / Dealer**

สร้างผู้ดูแลผ่าน Tinker หลังจากสมัครสมาชิกแล้ว:

```powershell
php artisan tinker
>>> App\Models\User::where('email', 'admin@example.com')->update(['role' => 'admin']);
```

## ข้อควรทำก่อนขึ้นระบบจริง

ปุ่มชำระเงินในหน้าคำสั่งซื้อเป็น `demo-payment` สำหรับสาธิต flow เท่านั้น ต้องแทนที่ด้วย payment gateway และเรียก `EscrowService::capturePayment()` จาก webhook ที่ตรวจสอบลายเซ็นแล้ว ห้ามเชื่อถือคำขอจาก browser สำหรับยืนยันการชำระเงินจริง
