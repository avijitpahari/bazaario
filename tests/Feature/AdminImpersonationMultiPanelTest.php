<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\SellerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminImpersonationMultiPanelTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $sellerUser;
    protected SellerProfile $sellerProfile;
    protected User $customerUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->sellerUser = User::factory()->create([
            'role' => 'seller',
            'status' => 'active',
        ]);

        $this->sellerProfile = SellerProfile::create([
            'user_id' => $this->sellerUser->id,
            'shop_name' => 'Test Green Market',
            'shop_slug' => 'test-green-market',
            'city' => 'Kolkata',
            'status' => 'approved',
        ]);

        $this->customerUser = User::factory()->create([
            'role' => 'user',
            'status' => 'active',
        ]);
    }

    public function test_admin_impersonate_seller_can_access_seller_user_and_admin_panels_seamlessly(): void
    {
        // 1. Admin logs in and triggers impersonation of seller
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.impersonate', $this->sellerUser->id));

        $response->assertRedirect(route('seller.dashboard'));
        $response->assertSessionHas('impersonating');

        // 2. Access Seller Dashboard
        $sellerDashboardResponse = $this->get(route('seller.dashboard'));
        $sellerDashboardResponse->assertStatus(200);

        // 3. Access User Dashboard while impersonating seller (NO conflict or redirect loop!)
        $userDashboardResponse = $this->get(route('user.dashboard'));
        $userDashboardResponse->assertStatus(200);

        // 4. Access Admin Dashboard while impersonating
        $adminDashboardResponse = $this->get(route('admin.dashboard'));
        $adminDashboardResponse->assertStatus(200);

        // 5. Exit impersonation
        $stopResponse = $this->post(route('admin.impersonate.stop'));
        $stopResponse->assertRedirect(route('admin.dashboard'));
        $stopResponse->assertSessionMissing('impersonating');
    }

    public function test_admin_impersonate_customer_can_access_user_and_admin_panels(): void
    {
        // 1. Admin impersonates customer
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.impersonate', $this->customerUser->id));

        $response->assertRedirect(route('user.dashboard'));

        // 2. Access User Dashboard
        $userDashboardResponse = $this->get(route('user.dashboard'));
        $userDashboardResponse->assertStatus(200);

        // 3. Access User Profile
        $profileResponse = $this->get(route('user.profile'));
        $profileResponse->assertStatus(200);

        // 4. Access Admin Dashboard
        $adminDashboardResponse = $this->get(route('admin.dashboard'));
        $adminDashboardResponse->assertStatus(200);
    }
}
