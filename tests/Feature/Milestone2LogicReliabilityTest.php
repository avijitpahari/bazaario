<?php

namespace Tests\Feature;

use App\Models\Auction;
use App\Models\Category;
use App\Models\Product;
use App\Models\SellerOrder;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class Milestone2LogicReliabilityTest extends TestCase
{
    use RefreshDatabase;

    protected function createApprovedSeller(): User
    {
        $seller = User::factory()->create([
            'role'     => 'seller',
            'status'   => 'active',
            'password' => Hash::make('Password123!'),
        ]);

        SellerProfile::create([
            'user_id'             => $seller->id,
            'shop_name'           => 'Empirical Test Merchant',
            'shop_slug'           => 'empirical-merchant-' . $seller->id,
            'seller_type'         => 'Farmer',
            'city'                => 'Contai',
            'state'               => 'West Bengal',
            'country'             => 'India',
            'status'              => 'approved',
            'trust_score'         => 94.00,
            'commission_rate'     => 10.00,
            'address'             => 'Plot 55, Agro Hub, Contai',
            'postal_code'         => '721401',
            'operating_radius_km' => 25,
            'bank_account_number' => '9182374650124092',
            'bank_ifsc'           => 'HDFC0001245',
        ]);

        return $seller->fresh(['sellerProfile']);
    }

    /**
     * Test P21: Legal pages respond with HTTP 200 and contain expected content.
     */
    public function test_legal_routes_respond_with_http_200(): void
    {
        $privacyResponse = $this->get('/privacy');
        $privacyResponse->assertStatus(200);
        $privacyResponse->assertSee('Privacy Policy');
        $privacyResponse->assertSee('Information We Collect');

        $termsResponse = $this->get('/terms');
        $termsResponse->assertStatus(200);
        $termsResponse->assertSee('Terms of Service');
        $termsResponse->assertSee('Marketplace &amp; Escrow Framework', false);

        $returnResponse = $this->get('/return-policy');
        $returnResponse->assertStatus(200);
        $returnResponse->assertSee('Return &amp; Refund Policy', false);
        $returnResponse->assertSee('Escrow-Backed Buyer Protection');
    }

    /**
     * Test P20, P21, P22: Footer contains correct route bindings, Deals filter, and valid social links.
     */
    public function test_footer_renders_correct_links_and_filter(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        // P21: Verify policy links in footer
        $response->assertSee(route('pages.privacy'));
        $response->assertSee(route('pages.terms'));
        $response->assertSee(route('pages.return-policy'));

        // P22: Verify Deals link uses filter=deals
        $response->assertSee(route('products.index', ['filter' => 'deals']));

        // P20: Verify no dead href="#" in social badges
        $response->assertSee('https://instagram.com');
        $response->assertSee('https://x.com');
        $response->assertSee('https://youtube.com');
        $response->assertSee('https://github.com');

        // P7: Verify AI modal trigger and modal content
        $response->assertSee('✦ Ask Bazaario AI');
        $response->assertSee('Bazaario AI Assistant');
        $response->assertSee('Autonomous Shopping Agent • Coming Soon');
    }

    /**
     * Test P6: Homepage AI Compare CTA updated to Explore Products.
     */
    public function test_home_ai_compare_cta_is_explore_products(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Explore Products');
        $response->assertDontSee('Try AI Compare');
    }

    /**
     * Test P41: Category route handles null/empty and 'all' slugs gracefully.
     */
    public function test_category_route_handles_null_slug_gracefully(): void
    {
        $category = Category::create([
            'name'        => 'Fresh Produce',
            'slug'        => 'fresh-produce',
            'status'      => 'active',
            'description' => 'Direct from local farms',
        ]);

        $seller = $this->createApprovedSeller();

        Product::create([
            'seller_id'   => $seller->id,
            'category_id' => $category->id,
            'name'        => 'Organic Sweet Papaya',
            'slug'        => 'organic-sweet-papaya',
            'price'       => 85.00,
            'stock'       => 20,
            'unit_type'   => 'kg',
            'status'      => 'active',
        ]);

        // GET /category without slug should not 404 or show empty due to slug=null query
        $nullSlugResponse = $this->get('/category');
        $nullSlugResponse->assertStatus(200);
        $nullSlugResponse->assertSee('Organic Sweet Papaya');

        // GET /category/all
        $allSlugResponse = $this->get('/category/all');
        $allSlugResponse->assertStatus(200);
        $allSlugResponse->assertSee('Organic Sweet Papaya');

        // GET /category/{slug}
        $specificResponse = $this->get('/category/fresh-produce');
        $specificResponse->assertStatus(200);
        $specificResponse->assertSee('Organic Sweet Papaya');
    }

    /**
     * Test P9: Seller dashboard renders genuine zero metrics for seller with 0 data.
     */
    public function test_seller_dashboard_shows_genuine_zero_metrics_without_fake_fallbacks(): void
    {
        $seller = $this->createApprovedSeller();

        $response = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));
        $response->assertStatus(200);

        // Must NOT see fake numbers 248, 84520, or fake Alphonso Mango products if no orders exist
        $response->assertDontSee('235 / 248 on time');
        $response->assertDontSee('182 verified reviews');
        $response->assertSee('0 / 0 on time (No orders yet)');
        $response->assertSee('0 verified reviews');
        $response->assertSee('0 Total');
        $response->assertSee('No orders received yet');
    }

    /**
     * Test P10: Seller auction history route returns ended auctions.
     */
    public function test_seller_auction_history_route_resolves_and_filters(): void
    {
        $seller = $this->createApprovedSeller();

        $response = $this->actingAs($seller, 'seller')->get(route('seller.auctions.history'));
        $response->assertStatus(200);
        $response->assertViewHas('status', 'ended');
    }

    /**
     * Test P26 & P28: Seller notifications and settings routes resolve cleanly.
     */
    public function test_seller_notifications_and_settings_routes(): void
    {
        $seller = $this->createApprovedSeller();

        // P26: Notifications
        $notifResponse = $this->actingAs($seller, 'seller')->get(route('seller.account.notifications'));
        $notifResponse->assertStatus(200);
        $notifResponse->assertSee('Store Alerts &amp; Notifications', false);

        // P28: Settings
        $settingsResponse = $this->actingAs($seller, 'seller')->get(route('seller.account.settings'));
        $settingsResponse->assertStatus(200);
        $settingsResponse->assertSee('Store Settings &amp; Preferences', false);

        // Settings update PUT
        $updateResponse = $this->actingAs($seller, 'seller')->put(route('seller.account.settings.update'), [
            'auto_hide_perishable' => 1,
            'default_low_stock_threshold' => 15,
            'default_unit_type' => 'kg',
        ]);
        $updateResponse->assertRedirect(route('seller.account.settings'));
        $updateResponse->assertSessionHas('success');
    }

    /**
     * Test P8: Seller header search bar has valid form action and input.
     */
    public function test_seller_header_search_bar_wired(): void
    {
        $seller = $this->createApprovedSeller();

        $response = $this->actingAs($seller, 'seller')->get(route('seller.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('action="' . route('seller.products.index') . '"', false);
        $response->assertSee('name="search"', false);
        $response->assertSee(route('seller.account.notifications'), false);
    }
}
