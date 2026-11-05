<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Product;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Move any mistakenly categorized Organic Alphonso Mango to Organic Food category if available
        $organicCat = Category::where('slug', 'organic-food')->first();
        if ($organicCat) {
            Product::where('name', 'like', '%Alphonso Mango%')
                ->where('category_id', '!=', $organicCat->id)
                ->update(['category_id' => $organicCat->id]);
        }

        // 2. Clean up freshness/harvest fields for all non-organic products
        $nonOrganicCategories = Category::all()->reject(function ($cat) {
            return $cat->isOrganic();
        })->pluck('id');

        if ($nonOrganicCategories->isNotEmpty()) {
            DB::table('products')
                ->whereIn('category_id', $nonOrganicCategories)
                ->update([
                    'is_perishable'     => false,
                    'harvest_date'      => null,
                    'expiry_days'       => null,
                    'expiry_date'       => null,
                    'harvest_grade'     => null,
                    'auto_hide_expired' => false,
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse required for data cleanup
    }
};
