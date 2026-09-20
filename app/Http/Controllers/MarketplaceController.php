<?php

namespace App\Http\Controllers;

use App\Models\DealerProfile;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class MarketplaceController extends Controller
{
    public function home(Request $request): View
    {
        $filters = $request->only(['category', 'cpu_model', 'cpu_gen', 'ram_capacity', 'ram_type', 'gpu_series', 'storage']);
        $filterOptions = [
            'category' => [
                'desktop' => 'Desktop / Mini PC', 'laptop' => 'Laptop', 'cpu' => 'CPU',
                'gpu' => 'GPU / การ์ดจอ', 'ram' => 'RAM', 'storage' => 'SSD / HDD',
                'motherboard' => 'Mainboard', 'psu' => 'Power Supply', 'case' => 'Case',
                'monitor' => 'Monitor', 'accessory' => 'อุปกรณ์เสริม',
            ],
            'cpu_model' => [
                'intel-i3' => 'Intel Core i3', 'intel-i5' => 'Intel Core i5',
                'intel-i7' => 'Intel Core i7', 'intel-i9' => 'Intel Core i9',
                'intel-ultra' => 'Intel Core Ultra', 'ryzen-3' => 'AMD Ryzen 3',
                'ryzen-5' => 'AMD Ryzen 5', 'ryzen-7' => 'AMD Ryzen 7', 'ryzen-9' => 'AMD Ryzen 9',
            ],
            'cpu_gen' => [
                'intel-14' => 'Intel Gen 14', 'intel-13' => 'Intel Gen 13', 'intel-12' => 'Intel Gen 12',
                'intel-11' => 'Intel Gen 11', 'intel-10' => 'Intel Gen 10', 'intel-9' => 'Intel Gen 9',
                'intel-8' => 'Intel Gen 8', 'ryzen-9000' => 'AMD Ryzen 9000 Series',
                'ryzen-7000' => 'AMD Ryzen 7000 Series', 'ryzen-5000' => 'AMD Ryzen 5000 Series',
                'ryzen-3000' => 'AMD Ryzen 3000 Series', 'ryzen-2000' => 'AMD Ryzen 2000 Series',
            ],
            'ram_capacity' => ['4GB', '8GB', '16GB', '32GB', '64GB', '128GB'],
            'ram_type' => ['DDR3', 'DDR4', 'DDR5'],
            'gpu_series' => [
                'rtx-50' => 'NVIDIA RTX 50 Series', 'rtx-40' => 'NVIDIA RTX 40 Series',
                'rtx-30' => 'NVIDIA RTX 30 Series', 'rtx-20' => 'NVIDIA RTX 20 Series',
                'gtx-16' => 'NVIDIA GTX 16 Series', 'gtx-10' => 'NVIDIA GTX 10 Series',
                'rx-9000' => 'AMD Radeon RX 9000 Series', 'rx-7000' => 'AMD Radeon RX 7000 Series',
                'rx-6000' => 'AMD Radeon RX 6000 Series', 'rx-5000' => 'AMD Radeon RX 5000 Series',
                'rx-500' => 'AMD Radeon RX 500 Series', 'rx-400' => 'AMD Radeon RX 400 Series',
                'radeon-vega' => 'AMD Radeon Vega Series',
            ],
            'storage' => collect(['SSD', 'HDD']),
        ];

        $products = Product::query()->where('status', 'available')->with([
            'dealer.dealerProfile' => fn ($query) => $query->withAvg('reviews', 'rating')->withCount('reviews'),
            'images',
        ])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery->where('name', 'like', "%{$search}%")
                        ->orWhereHas('dealer.dealerProfile', fn ($dealerQuery) => $dealerQuery->where('store_name', 'like', "%{$search}%"));
                });
            })
            ->when($filters['category'] ?? null, fn ($query, $category) => $query->where('category', $category))
            ->when($filters['cpu_model'] ?? null, function ($query, $model) {
                $pattern = [
                    'intel-i3' => 'Intel Core i3', 'intel-i5' => 'Intel Core i5',
                    'intel-i7' => 'Intel Core i7', 'intel-i9' => 'Intel Core i9',
                    'intel-ultra' => 'Intel Core Ultra', 'ryzen-3' => 'AMD Ryzen 3',
                    'ryzen-5' => 'AMD Ryzen 5', 'ryzen-7' => 'AMD Ryzen 7', 'ryzen-9' => 'AMD Ryzen 9',
                ][$model] ?? null;
                if ($pattern) $query->where('specs->cpu', 'like', "%{$pattern}%");
            })
            ->when($filters['cpu_gen'] ?? null, function ($query, $generation) {
                $patterns = [
                    'intel-14' => ['-14'], 'intel-13' => ['-13'], 'intel-12' => ['-12'],
                    'intel-11' => ['-11'], 'intel-10' => ['-10'], 'intel-9' => ['-9'], 'intel-8' => ['-8'],
                    'ryzen-9000' => ['Ryzen 9'], 'ryzen-7000' => ['Ryzen 7'], 'ryzen-5000' => ['Ryzen 5'],
                    'ryzen-3000' => ['Ryzen 3'], 'ryzen-2000' => ['Ryzen 2'],
                ][$generation] ?? [];
                $query->where(function ($subQuery) use ($patterns) {
                    foreach ($patterns as $pattern) $subQuery->orWhere('specs->cpu', 'like', "%{$pattern}%");
                });
            })
            ->when($filters['ram_capacity'] ?? null, fn ($query, $capacity) => $query->where('specs->ram', 'like', "%{$capacity}%"))
            ->when($filters['ram_type'] ?? null, fn ($query, $type) => $query->where('specs->ram', 'like', "%{$type}%"))
            ->when($filters['gpu_series'] ?? null, function ($query, $series) {
                $patterns = [
                    'rtx-50' => ['RTX 50'], 'rtx-40' => ['RTX 40'], 'rtx-30' => ['RTX 30'],
                    'rtx-20' => ['RTX 20'], 'gtx-16' => ['GTX 16'], 'gtx-10' => ['GTX 10'],
                    'rx-9000' => ['RX 9'], 'rx-7000' => ['RX 7'], 'rx-6000' => ['RX 6'],
                    'rx-5000' => ['RX 5'], 'rx-500' => ['RX 5'], 'rx-400' => ['RX 4'],
                    'radeon-vega' => ['Radeon Vega', 'RX Vega'],
                ][$series] ?? [];
                $query->where(function ($subQuery) use ($patterns) {
                    foreach ($patterns as $pattern) {
                        $subQuery->orWhere('specs->gpu', 'like', "%{$pattern}%");
                    }
                });
            })
            ->when($filters['storage'] ?? null, fn ($query, $storage) => $query->where('specs->storage', 'like', "%{$storage}%"))
            ->latest()->paginate(12)->withQueryString();

        return view('marketplace.home', compact('products', 'filters', 'filterOptions'));
    }

    public function show(Product $product): View
    {
        abort_unless($product->status === 'available' || auth()->id() === $product->dealer_id || auth()->user()?->isAdmin(), 404);

        return view('marketplace.show', ['product' => $product->load([
            'dealer.dealerProfile' => fn ($query) => $query->withAvg('reviews', 'rating')->withCount('reviews'),
            'images',
        ])]);
    }

    public function dealerProducts(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $products = $request->user()->products()
            ->with('images')
            ->when($search, fn ($query) => $query->where(fn ($searchQuery) => $searchQuery
                ->where('name', 'like', "%{$search}%")
                ->orWhere('serial_number', 'like', "%{$search}%")))
            ->latest()
            ->get();

        return view('dealer.products', compact('products', 'search'));
    }

    public function createProduct(): View
    {
        return view('dealer.product-form', ['product' => new Product]);
    }

    public function storeProduct(Request $request): RedirectResponse
    {
        $profile = $request->user()->dealerProfile;
        abort_unless($profile?->status === 'approved', 403, 'บัญชีร้านค้าต้องผ่าน KYC ก่อนลงสินค้า');
        $data = $this->validateProduct($request);
        $data['specs'] = array_filter($data['specs'] ?? [], fn ($value) => filled($value));
        $payload = Arr::except($data, ['images', 'image_files']);
        $product = $request->user()->products()
            ->where('serial_number', $data['serial_number'])
            ->whereDoesntHave('orderItem')
            ->first();
        if ($product) {
            $product->update($payload);
        } else {
            $product = $request->user()->products()->create($payload);
        }
        $this->saveImages($request, $product);

        return redirect()->route('home')->with('success', 'เพิ่มสินค้าเรียบร้อยแล้ว สินค้าของคุณแสดงอยู่หน้าแรก');
    }

    public function editProduct(Product $product): View
    {
        abort_unless($product->dealer_id === request()->user()->id, 403);

        return view('dealer.product-form', compact('product'));
    }

    public function updateProduct(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->dealer_id === $request->user()->id, 403);
        $data = $this->validateProduct($request);
        $data['specs'] = array_filter($data['specs'] ?? [], fn ($value) => filled($value));
        $product->update(Arr::except($data, ['images', 'image_files']));
        $this->saveImages($request, $product);

        return redirect()->route('home')->with('success', 'บันทึกการแก้ไขแล้ว');
    }

    public function kyc(): View
    {
        return view('dealer.kyc', ['profile' => request()->user()->dealerProfile]);
    }

    public function saveKyc(Request $request): RedirectResponse
    {
        $profile = $request->user()->dealerProfile;

        $data = $request->validate([
            'store_name' => ['required', 'string', 'max:255'],
            'id_card_file' => [$profile?->id_card_url ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'bank_name' => ['required', 'string', 'max:255'], 'bank_account' => ['required', 'string', 'max:50'],
            'promptpay_id' => ['required', 'regex:/^(?:\d{10}|\d{13}|\d{15})$/'],
        ]);

        if ($request->hasFile('id_card_file')) {
            $previousDocument = (string) $profile?->id_card_url;

            if (str_starts_with($previousDocument, '/storage/')) {
                Storage::disk('public')->delete(substr($previousDocument, strlen('/storage/')));
            }

            $path = Storage::disk('public')->putFile(
                "kyc-documents/{$request->user()->id}",
                $request->file('id_card_file'),
            );

            $data['id_card_url'] = Storage::url($path);
        }

        unset($data['id_card_file']);
        $data['status'] = 'pending';
        DealerProfile::updateOrCreate(['user_id' => $request->user()->id], $data);

        return back()->with('success', 'ส่งข้อมูล KYC ให้ผู้ดูแลตรวจสอบแล้ว');
    }

    public function updateShipping(Request $request, OrderItem $item): RedirectResponse
    {
        abort_unless($item->dealer_id === $request->user()->id, 403);
        $data = $request->validate(['tracking_number' => ['required', 'string', 'max:100']]);
        $item->update($data + ['delivery_status' => 'shipped']);

        return back()->with('success', 'อัปเดตเลขพัสดุแล้ว');
    }

    private function validateProduct(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'], 'category' => ['required', 'in:desktop,laptop,cpu,gpu,ram,storage,motherboard,psu,case,monitor,accessory'], 'price' => ['required', 'decimal:0,2', 'min:1'],
            'condition_grade' => ['required', 'in:A,B,C'], 'serial_number' => ['required', 'string', 'max:255'],
            'specs' => ['nullable', 'array'], 'description' => ['required', 'string'], 'status' => ['required', 'in:available,hidden'],
            'images' => ['nullable', 'array', 'max:8'], 'images.*' => ['nullable', 'url', 'max:2048'],
            'image_files' => ['nullable', 'array', 'max:8'], 'image_files.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
    }

    private function saveImages(Request $request, Product $product): void
    {
        foreach (array_values(array_filter($request->input('images', []))) as $index => $url) {
            ProductImage::create(['product_id' => $product->id, 'image_url' => $url, 'is_primary' => $index === 0 && ! $product->images()->where('is_primary', true)->exists()]);
        }

        foreach ($request->file('image_files', []) as $file) {
            $path = Storage::disk('public')->putFile("products/{$product->id}", $file);
            ProductImage::create([
                'product_id' => $product->id,
                'image_url' => Storage::url($path),
                'is_primary' => ! $product->images()->where('is_primary', true)->exists(),
            ]);
        }
    }
}
