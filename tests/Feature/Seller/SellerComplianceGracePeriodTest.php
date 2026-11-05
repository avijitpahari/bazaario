<?php

namespace Tests\Feature\Seller;

use App\Models\SellerProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SellerComplianceGracePeriodTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Admin approves seller without documents; seller is activated with 2-week grace period.
     */
    public function test_seller_can_be_approved_without_documents_and_sees_2_week_notice(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $sellerUser = User::factory()->create(['role' => 'user', 'status' => 'active']);

        $profile = SellerProfile::create([
            'user_id'     => $sellerUser->id,
            'shop_name'   => 'Green Valley Farms',
            'shop_slug'   => 'green-valley-farms',
            'seller_type' => 'Farmer',
            'city'        => 'Kolkata',
            'state'       => 'West Bengal',
            'latitude'    => 22.5726,
            'longitude'   => 88.3639,
            'status'      => 'pending',
            // No bank or pan documents uploaded
            'bank_account_number' => null,
            'bank_ifsc' => null,
            'bank_doc_path' => null,
            'pan_number' => null,
            'pan_doc_path' => null,
        ]);

        // 1. Admin approves seller without documents
        $response = $this->actingAs($admin, 'admin')->post(route('admin.sellers.approve', $profile->id));
        $response->assertRedirect();
        
        $profile->refresh();
        $this->assertEquals('approved', $profile->status);
        $this->assertNotNull($profile->verified_at);
        $this->assertFalse($profile->hasMandatoryComplianceDocuments());
        $this->assertFalse($profile->hasUploadedBank());
        $this->assertFalse($profile->hasUploadedPan());
        $this->assertEquals(14, $profile->compliance_days_remaining);

        // 2. Seller visits dashboard and sees mandatory notice with countdown
        $sellerUser->refresh();
        $dashResponse = $this->actingAs($sellerUser, 'seller')->get(route('seller.dashboard'));
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Mandatory Action: Upload Bank');
        $dashResponse->assertSee('Within 2 Weeks');
        $dashResponse->assertSee('14 days remaining');
        $dashResponse->assertSee('Bank Details (with Passbook / Cancelled Cheque proof)');
        $dashResponse->assertSee('Missing (Action Needed)');
    }

    /**
     * Test: Seller uploads Bank details and PAN details successfully.
     */
    public function test_seller_can_upload_bank_and_pan_details(): void
    {
        Storage::fake('public');

        $sellerUser = User::factory()->create(['role' => 'seller', 'status' => 'active']);
        $profile = SellerProfile::create([
            'user_id'     => $sellerUser->id,
            'shop_name'   => 'Sundarban Pure Honey',
            'shop_slug'   => 'sundarban-pure-honey',
            'seller_type' => 'Farmer',
            'city'        => 'Kolkata',
            'state'       => 'West Bengal',
            'latitude'    => 22.5726,
            'longitude'   => 88.3639,
            'status'      => 'approved',
            'verified_at' => now(),
        ]);

        $bankDoc = UploadedFile::fake()->create('cheque.pdf', 300, 'application/pdf');
        $panDoc = UploadedFile::fake()->image('pancard.jpg');

        $uploadResponse = $this->actingAs($sellerUser, 'seller')->post(route('seller.account.compliance.upload'), [
            'bank_account_number' => '12345678901234',
            'bank_ifsc'           => 'SBIN0001234',
            'bank_doc'            => $bankDoc,
            'pan_number'          => 'ABCDE1234F',
            'pan_doc'             => $panDoc,
        ]);

        $uploadResponse->assertSessionHas('success');
        
        $profile->refresh();
        $this->assertTrue($profile->hasUploadedBank());
        $this->assertTrue($profile->hasUploadedPan());
        $this->assertTrue($profile->hasMandatoryComplianceDocuments());
        $this->assertNotNull($profile->bank_doc_path);
        $this->assertNotNull($profile->pan_doc_path);

        Storage::disk('public')->assertExists($profile->bank_doc_path);
        Storage::disk('public')->assertExists($profile->pan_doc_path);

        // Once uploaded, dashboard no longer displays the notice
        $dashResponse = $this->actingAs($sellerUser, 'seller')->get(route('seller.dashboard'));
        $dashResponse->assertStatus(200);
        $dashResponse->assertDontSee('Mandatory Action: Upload Bank &amp; PAN Details (Within 2 Weeks)', false);
    }

    /**
     * Test: Overdue grace period shows expired alert if > 14 days without documents.
     */
    public function test_expired_grace_period_shows_overdue_warning(): void
    {
        $sellerUser = User::factory()->create(['role' => 'seller', 'status' => 'active']);
        $profile = SellerProfile::create([
            'user_id'     => $sellerUser->id,
            'shop_name'   => 'Artisan Pottery',
            'shop_slug'   => 'artisan-pottery',
            'seller_type' => 'Kirana Store',
            'city'        => 'Kolkata',
            'state'       => 'West Bengal',
            'latitude'    => 22.5726,
            'longitude'   => 88.3639,
            'status'      => 'approved',
            'verified_at' => Carbon::now()->subDays(15), // Approved 15 days ago
        ]);

        $this->assertTrue($profile->isComplianceGraceExpired());
        $this->assertEquals(0, $profile->compliance_days_remaining);

        $dashResponse = $this->actingAs($sellerUser, 'seller')->get(route('seller.dashboard'));
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Action Required: Mandatory Compliance Grace Period Expired');
        $dashResponse->assertSee('Overdue (2-Week Grace Ended)');
    }

    /**
     * Test: Seller can add and update bank details directly from settings.
     */
    public function test_seller_can_update_bank_details_via_settings(): void
    {
        $sellerUser = User::factory()->create(['role' => 'seller', 'status' => 'active']);
        $profile = SellerProfile::create([
            'user_id'     => $sellerUser->id,
            'shop_name'   => 'Green Farm Co',
            'shop_slug'   => 'green-farm-co',
            'seller_type' => 'Farmer',
            'city'        => 'Contai',
            'state'       => 'West Bengal',
            'latitude'    => 21.778,
            'longitude'   => 87.751,
            'status'      => 'approved',
            'verified_at' => now(),
        ]);

        $response = $this->actingAs($sellerUser, 'seller')->post(route('seller.account.bank.update'), [
            'bank_account_number' => '987654321012',
            'bank_ifsc'           => 'SBIN0000058',
        ]);

        $response->assertSessionHas('success');
        $profile->refresh();
        $this->assertEquals('987654321012', $profile->bank_account_number);
        $this->assertEquals('SBIN0000058', $profile->bank_ifsc);
    }

    /**
     * Test: Seller can click test bypass button to instantly auto-verify sandbox bank details.
     */
    public function test_seller_can_use_test_bypass_button_to_auto_verify_bank_account(): void
    {
        $sellerUser = User::factory()->create(['role' => 'seller', 'status' => 'active']);
        $profile = SellerProfile::create([
            'user_id'     => $sellerUser->id,
            'shop_name'   => 'Eco Organics',
            'shop_slug'   => 'eco-organics',
            'seller_type' => 'Farmer',
            'city'        => 'Contai',
            'state'       => 'West Bengal',
            'latitude'    => 21.778,
            'longitude'   => 87.751,
            'status'      => 'approved',
            'verified_at' => now(),
        ]);

        $this->assertFalse($profile->hasUploadedBank());

        $response = $this->actingAs($sellerUser, 'seller')->post(route('seller.account.bank.bypass'));

        $response->assertSessionHas('success');
        $profile->refresh();

        $this->assertEquals('918010023456789', $profile->bank_account_number);
        $this->assertEquals('HDFC0001234', $profile->bank_ifsc);
        $this->assertTrue($profile->hasUploadedBank());
        $this->assertTrue($profile->hasMandatoryComplianceDocuments());
        Storage::disk('public')->assertExists($profile->bank_doc_path);
    }
}
