<?php

namespace Tests\Feature\Seller;

use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SellerMultiDocumentVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /**
     * Test: Seller completes onboarding wizard providing PAN and Bank proof documents.
     * Verifies pan_doc_path and bank_doc_path are stored and tracked in provided_documents.
     */
    public function test_seller_onboarding_wizard_with_pan_and_bank_proofs(): void
    {
        $seller = User::factory()->create([
            'role'   => 'seller',
            'status' => 'active',
        ]);

        $storefrontImage = UploadedFile::fake()->image('shop.jpg');
        $panProof = UploadedFile::fake()->create('pan_card.pdf', 300, 'application/pdf');
        $bankProof = UploadedFile::fake()->create('bank_cheque.jpg', 400, 'image/jpeg');

        $payload = [
            'seller_type'          => 'Farmer',
            'shop_name'            => 'Kolkata Mango Orchard',
            'bio'                  => 'Direct producer of organic mangoes.',
            'storefront_image'     => $storefrontImage,
            'address'              => '12 Orchard Lane',
            'city'                 => 'Kolkata',
            'state'                => 'West Bengal',
            'postal_code'          => '700001',
            'latitude'             => 22.5726,
            'longitude'            => 88.3639,
            // Provided Documents array from dynamic dropdown
            'provided_documents'   => ['pan', 'bank'],
            'pan_number'           => 'ABCDE1234F',
            'pan_doc'              => $panProof,
            'bank_account_number'  => '987654321012',
            'bank_ifsc'            => 'SBIN0000058',
            'bank_doc'             => $bankProof,
        ];

        $response = $this->actingAs($seller, 'seller')->post(route('seller.onboarding.submit'), $payload);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('seller.pending'));

        $profile = SellerProfile::where('user_id', $seller->id)->first();
        $this->assertNotNull($profile);
        $this->assertEquals('ABCDE1234F', $profile->pan_number);
        $this->assertEquals('987654321012', $profile->bank_account_number);
        $this->assertEquals('SBIN0000058', $profile->bank_ifsc);

        // Verify files were stored
        $this->assertNotNull($profile->pan_doc_path);
        $this->assertNotNull($profile->bank_doc_path);
        Storage::disk('public')->assertExists($profile->pan_doc_path);
        Storage::disk('public')->assertExists($profile->bank_doc_path);

        // Verify tracked in provided_documents
        $this->assertContains('pan', $profile->provided_documents);
        $this->assertContains('bank', $profile->provided_documents);
    }

    /**
     * Test: Admin approval screen inspects BOTH PAN and Bank proofs.
     * Verifies that the previous bug (admin only showing bank proof) is fixed.
     */
    public function test_admin_approvals_view_shows_both_pan_and_bank_proofs(): void
    {
        $seller = User::factory()->create([
            'role'   => 'seller',
            'status' => 'active',
        ]);

        $panPath = 'seller_documents/' . $seller->id . '/pan_card.pdf';
        $bankPath = 'seller_documents/' . $seller->id . '/bank_passbook.png';
        Storage::disk('public')->put($panPath, 'dummy pan content');
        Storage::disk('public')->put($bankPath, 'dummy bank content');

        $profile = SellerProfile::create([
            'user_id'              => $seller->id,
            'seller_type'          => 'Farmer',
            'shop_name'            => 'Midnapore Farm Hub',
            'shop_slug'            => 'midnapore-farm-hub',
            'status'               => 'pending',
            'city'                 => 'Contai',
            'pan_number'           => 'BKZPA1234X',
            'pan_doc_path'         => $panPath,
            'bank_account_number'  => '50100234567890',
            'bank_ifsc'            => 'HDFC0001234',
            'bank_doc_path'        => $bankPath,
            'provided_documents'   => ['pan', 'bank'],
        ]);

        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.sellers.approvals', ['selected' => $profile->id]));
        $response->assertStatus(200);

        // Verify PAN card is visible and rendered with its number & inspect proof button
        $response->assertSee('BKZPA1234X');
        $response->assertSee('Permanent Account Number (PAN)');
        $response->assertSee(Storage::url($panPath), false);

        // Verify Bank card is visible and rendered with its details & inspect proof button
        $response->assertSee('50100234567890');
        $response->assertSee('HDFC0001234');
        $response->assertSee('Escrow Bank Account & Proof');
        $response->assertSee(Storage::url($bankPath), false);

        // Verify dynamic counter reflects 2/2 documents attached
        $response->assertSee('2/2 Documents Attached');
    }

    /**
     * Test: Dynamic dropdown in seller pending dashboard allows uploading Aadhaar and Mandi license.
     */
    public function test_seller_can_upload_aadhaar_and_mandi_license_from_dropdown(): void
    {
        $seller = User::factory()->create([
            'role'   => 'seller',
            'status' => 'active',
        ]);

        $profile = SellerProfile::create([
            'user_id'            => $seller->id,
            'seller_type'        => 'Kirana Store',
            'shop_name'          => 'Kolkata Kirana',
            'shop_slug'          => 'kolkata-kirana',
            'status'             => 'pending',
            'provided_documents' => ['pan', 'bank'],
        ]);

        // Upload Aadhaar via pending view modal
        $aadhaarFile = UploadedFile::fake()->create('aadhaar_card.pdf', 300, 'application/pdf');
        $resAadhaar = $this->actingAs($seller, 'seller')->post(route('seller.pending.documents.upload'), [
            'doc_type'       => 'aadhaar',
            'aadhaar_number' => '9999 8888 7777',
            'document_file'  => $aadhaarFile,
        ]);
        $resAadhaar->assertRedirect(route('seller.pending'));
        $resAadhaar->assertSessionHas('success');

        $profile->refresh();
        $this->assertEquals('9999 8888 7777', $profile->aadhaar_number);
        $this->assertNotNull($profile->aadhaar_doc_path);
        Storage::disk('public')->assertExists($profile->aadhaar_doc_path);
        $this->assertContains('aadhaar', $profile->provided_documents);

        // Upload Mandi License via pending view modal
        $mandiFile = UploadedFile::fake()->create('apmc_license.png', 400, 'image/png');
        $resMandi = $this->actingAs($seller, 'seller')->post(route('seller.pending.documents.upload'), [
            'doc_type'             => 'mandi_license',
            'mandi_license_number' => 'APMC-KOL-2026-99',
            'document_file'        => $mandiFile,
        ]);
        $resMandi->assertRedirect(route('seller.pending'));
        $resMandi->assertSessionHas('success');

        $profile->refresh();
        $this->assertEquals('APMC-KOL-2026-99', $profile->mandi_license_number);
        $this->assertNotNull($profile->mandi_license_doc_path);
        Storage::disk('public')->assertExists($profile->mandi_license_doc_path);
        $this->assertContains('mandi_license', $profile->provided_documents);

        // Now admin approvals should show all 4 provided documents
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $adminRes = $this->actingAs($admin, 'admin')->get(route('admin.sellers.approvals', ['selected' => $profile->id]));
        $adminRes->assertStatus(200);
        $adminRes->assertSee('9999 8888 7777');
        $adminRes->assertSee('Aadhaar / Government Photo ID');
        $adminRes->assertSee('APMC-KOL-2026-99');
        $adminRes->assertSee('APMC / Mandi Market Trading License');
    }

    /**
     * Test: Admin sellers show view renders all provided KYC documents with proof links.
     */
    public function test_admin_sellers_show_view_renders_provided_documents_dossier(): void
    {
        $seller = User::factory()->create(['role' => 'seller', 'status' => 'active']);

        $panFile = 'seller_documents/' . $seller->id . '/pan.pdf';
        $bankFile = 'seller_documents/' . $seller->id . '/bank.pdf';
        $udyamFile = 'seller_documents/' . $seller->id . '/udyam.pdf';
        Storage::disk('public')->put($panFile, 'pan file content');
        Storage::disk('public')->put($bankFile, 'bank file content');
        Storage::disk('public')->put($udyamFile, 'udyam file content');

        $profile = SellerProfile::create([
            'user_id'              => $seller->id,
            'seller_type'          => 'Farmer',
            'shop_name'            => 'Sundarban Honey Co',
            'shop_slug'            => 'sundarban-honey-co',
            'status'               => 'approved',
            'pan_number'           => 'SNDRB9999P',
            'pan_doc_path'         => $panFile,
            'bank_account_number'  => '1234009876',
            'bank_ifsc'            => 'ICIC0000100',
            'bank_doc_path'        => $bankFile,
            'udyam_number'         => 'UDYAM-WB-05-998877',
            'udyam_doc_path'       => $udyamFile,
            'provided_documents'   => ['pan', 'bank', 'udyam'],
        ]);

        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $response = $this->actingAs($admin, 'admin')->get(route('admin.sellers.show', $profile->id));
        $response->assertStatus(200);

        // Verify PAN, Bank, and Udyam appear with their numbers and proof links
        $response->assertSee('SNDRB9999P');
        $response->assertSee('1234009876');
        $response->assertSee('ICIC0000100');
        $response->assertSee('UDYAM-WB-05-998877');
        $response->assertSee(Storage::url($panFile), false);
        $response->assertSee(Storage::url($bankFile), false);
        $response->assertSee(Storage::url($udyamFile), false);
        $response->assertSee('3/3 Proofs');
    }
}
