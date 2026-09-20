<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('product_images')
            ->where('image_url', 'like', 'https://placehold.co/800x500/%')
            ->orderBy('id')
            ->each(function (object $image): void {
                $url = preg_replace(
                    '#https://placehold\.co/800x500/[^/]+/[^?]+\?text=#',
                    'https://placehold.co/800x500/7f1d1d/f9fafb?text=',
                    $image->image_url,
                );

                if ($url !== $image->image_url) {
                    DB::table('product_images')->where('id', $image->id)->update(['image_url' => $url]);
                }
            });
    }

    public function down(): void
    {
        // The previous placeholder colors are intentionally not restored.
    }
};
