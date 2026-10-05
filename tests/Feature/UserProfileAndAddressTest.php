<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * UserProfileAndAddressTest
 *
 * Verifies Features 50 to 54 across Tiers 1, 2, and 3:
 * - Feature 50: View Profile (personal info & trust indicators)
 * - Feature 51: Edit Profile (name, phone, bio update)
 * - Feature 52: Upload Profile Image (avatar file upload & storage)
 * - Feature 53: Change Password (current password verification & complexity rules)
 * - Feature 54: Manage Delivery Addresses (Full CRUD: List, Create, Update, Delete, Set Default)
 */
class UserProfileAndAddressTest extends TestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    protected function createCustomer(array $attributes = []): User
    {
        return User::create(array_merge([
            'name'               => 'Pooja Bannerjee',
            'email'              => 'pooja_' . uniqid() . '@bazaario.com',
            'phone'              => '98300' . rand(10000, 99999),
            'password'           => Hash::make('CurrentPassword123!'),
            'role'               => 'user',
            'status'             => 'active',
            'preferred_language' => 'en',
            'email_verified_at'  => now(),
        ], $attributes));
    }

    protected function createAddress(User $user, array $attributes = []): Address
    {
        return Address::create(array_merge([
            'user_id'        => $user->id,
            'type'           => 'home',
            'full_name'      => $user->name,
            'phone'          => '9876543210',
            'address_line_1' => '24 Park Street, Suite 4A',
            'address_line_2' => 'Opposite Mall',
            'landmark'       => 'Historic Park Gate',
            'city'           => 'Kolkata',
            'state'          => 'West Bengal',
            'postal_code'    => '700016',
            'country'        => 'India',
            'is_default'     => true,
        ], $attributes));
    }

    /*
    |--------------------------------------------------------------------------
    | Feature 50: View Profile
    |--------------------------------------------------------------------------
    */

    public function test_f50_view_profile_renders_user_information()
    {
        $user = $this->createCustomer(['name' => 'Dr. Sourav Ganguly']);

        $response = $this->actingAs($user, 'user')->get('/user/profile');
        $response->assertStatus(200);
        $response->assertSee('Dr. Sourav Ganguly');
    }

    /*
    |--------------------------------------------------------------------------
    | Feature 51: Edit Profile
    |--------------------------------------------------------------------------
    */

    public function test_f51_edit_profile_form_renders_successfully()
    {
        $user = $this->createCustomer();

        $response = $this->actingAs($user, 'user')->get('/user/profile/edit');
        $response->assertStatus(200);
        $response->assertSee($user->email);
    }

    public function test_f51_update_profile_saves_new_details()
    {
        $user = $this->createCustomer([
            'name'  => 'Old Name',
            'phone' => '9999999991',
        ]);

        $response = $this->actingAs($user, 'user')->put('/user/profile', [
            'name'  => 'Updated Name',
            'phone' => '9999999992',
        ]);

        $response->assertRedirect(route('user.profile'));

        $freshUser = $user->fresh();
        $this->assertEquals('Updated Name', $freshUser->name);
        $this->assertEquals('9999999992', $freshUser->phone);
    }

    /*
    |--------------------------------------------------------------------------
    | Feature 52: Upload Profile Image
    |--------------------------------------------------------------------------
    */

    public function test_f52_upload_profile_image_stores_file_and_updates_avatar_path()
    {
        Storage::fake('public');

        $user = $this->createCustomer();
        $pngContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        $file = UploadedFile::fake()->createWithContent('avatar.png', $pngContent);

        $response = $this->actingAs($user, 'user')->post('/user/profile/photo', [
            'profile_image' => $file,
        ]);

        $response->assertRedirect();

        $freshUser = $user->fresh();
        $this->assertNotNull($freshUser->profile_image);
        Storage::disk('public')->assertExists($freshUser->profile_image);
    }

    /*
    |--------------------------------------------------------------------------
    | Feature 53: Change Password
    |--------------------------------------------------------------------------
    */

    public function test_f53_security_page_renders_successfully()
    {
        $user = $this->createCustomer();

        $response = $this->actingAs($user, 'user')->get('/user/security');
        $response->assertStatus(200);
    }

    public function test_f53_change_password_with_valid_current_password_succeeds()
    {
        $user = $this->createCustomer(['password' => Hash::make('OldPassword123!')]);

        $response = $this->actingAs($user, 'user')->post('/user/security/password', [
            'current_password'      => 'OldPassword123!',
            'password'              => 'BrandNewPassword456!',
            'password_confirmation' => 'BrandNewPassword456!',
        ]);

        $response->assertRedirect(route('user.security'));
        $this->assertTrue(Hash::check('BrandNewPassword456!', $user->fresh()->password));
    }

    /*
    |--------------------------------------------------------------------------
    | Feature 54: Manage Delivery Addresses (Full CRUD)
    |--------------------------------------------------------------------------
    */

    public function test_f54_list_addresses_renders_customer_addresses()
    {
        $user = $this->createCustomer();
        $address = $this->createAddress($user, ['address_line_1' => 'Flat 12B, Alipore Towers']);

        $response = $this->actingAs($user, 'user')->get('/user/addresses');
        $response->assertStatus(200);
        $response->assertSee('Flat 12B, Alipore Towers');
    }

    public function test_f54_create_address_stores_record_in_database()
    {
        $user = $this->createCustomer();

        $payload = [
            'type'           => 'work',
            'full_name'      => 'Corporate Office',
            'phone'          => '9876543210',
            'address_line_1' => 'Sector V Webel Bhavan',
            'address_line_2' => 'Tower II, 5th Floor',
            'landmark'       => 'Near Wipro Circle',
            'city'           => 'Kolkata',
            'state'          => 'West Bengal',
            'postal_code'    => '700091',
            'country'        => 'India',
            'is_default'     => true,
        ];

        $response = $this->actingAs($user, 'user')->post('/user/addresses', $payload);

        $response->assertRedirect(route('user.addresses.index'));

        $this->assertDatabaseHas('addresses', [
            'user_id'        => $user->id,
            'address_line_1' => 'Sector V Webel Bhavan',
            'type'           => 'work',
        ]);
    }

    public function test_f54_first_created_address_is_automatically_default()
    {
        $user = $this->createCustomer();

        $this->actingAs($user, 'user')->post('/user/addresses', [
            'type'           => 'home',
            'full_name'      => $user->name,
            'phone'          => '9876543210',
            'address_line_1' => 'First House in Village',
            'city'           => 'Contai',
            'state'          => 'West Bengal',
            'postal_code'    => '721401',
            'country'        => 'India',
        ]);

        $created = $user->addresses()->first();
        $this->assertNotNull($created);
        $this->assertTrue((bool)$created->is_default);
    }

    public function test_f54_update_address_modifies_existing_record()
    {
        $user = $this->createCustomer();
        $address = $this->createAddress($user);

        $response = $this->actingAs($user, 'user')->put("/user/addresses/{$address->id}", [
            'type'           => 'other',
            'full_name'      => 'Updated Receiver',
            'phone'          => '9123456789',
            'address_line_1' => 'Updated Street 99',
            'city'           => 'Siliguri',
            'state'          => 'West Bengal',
            'postal_code'    => '734001',
            'country'        => 'India',
        ]);

        $response->assertRedirect(route('user.addresses.index'));

        $fresh = $address->fresh();
        $this->assertEquals('Updated Street 99', $fresh->address_line_1);
        $this->assertEquals('Siliguri', $fresh->city);
        $this->assertEquals('other', $fresh->type);
    }

    public function test_f54_delete_address_removes_record()
    {
        $user = $this->createCustomer();
        $address = $this->createAddress($user);

        $response = $this->actingAs($user, 'user')->delete("/user/addresses/{$address->id}");

        $response->assertRedirect(route('user.addresses.index'));
        $this->assertDatabaseMissing('addresses', ['id' => $address->id]);
    }

    public function test_f54_set_default_address_updates_default_flag()
    {
        $user = $this->createCustomer();
        $addr1 = $this->createAddress($user, ['is_default' => true]);
        $addr2 = $this->createAddress($user, ['is_default' => false, 'address_line_1' => 'Second Address']);

        $response = $this->actingAs($user, 'user')->post("/user/addresses/{$addr2->id}/default");

        $response->assertRedirect(route('user.addresses.index'));
        $this->assertTrue((bool)$addr2->fresh()->is_default);
        $this->assertFalse((bool)$addr1->fresh()->is_default);
    }

    /*
    |--------------------------------------------------------------------------
    | Tier 2: Boundary & Edge Cases
    |--------------------------------------------------------------------------
    */

    public function test_tier2_update_profile_rejects_empty_name()
    {
        $user = $this->createCustomer();

        $response = $this->actingAs($user, 'user')->put('/user/profile', [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_tier2_update_profile_rejects_duplicate_phone_from_another_user()
    {
        $userA = $this->createCustomer(['phone' => '9800000001']);
        $userB = $this->createCustomer(['phone' => '9800000002']);

        $response = $this->actingAs($userB, 'user')->put('/user/profile', [
            'name'  => $userB->name,
            'phone' => '9800000001',
        ]);

        $response->assertSessionHasErrors('phone');
    }

    public function test_tier2_upload_photo_rejects_non_image_files()
    {
        Storage::fake('public');

        $user = $this->createCustomer();
        $file = UploadedFile::fake()->create('malicious.php', 100, 'text/plain');

        $response = $this->actingAs($user, 'user')->post('/user/profile/photo', [
            'profile_image' => $file,
        ]);

        $response->assertSessionHasErrors('profile_image');
    }

    public function test_tier2_change_password_rejects_incorrect_current_password()
    {
        $user = $this->createCustomer(['password' => Hash::make('ActualPass123!')]);

        $response = $this->actingAs($user, 'user')->post('/user/security/password', [
            'current_password'      => 'WrongCurrentPassword!',
            'password'              => 'BrandNewPass123!',
            'password_confirmation' => 'BrandNewPass123!',
        ]);

        $response->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('ActualPass123!', $user->fresh()->password));
    }

    public function test_tier2_change_password_rejects_weak_new_password()
    {
        $user = $this->createCustomer(['password' => Hash::make('ActualPass123!')]);

        $response = $this->actingAs($user, 'user')->post('/user/security/password', [
            'current_password'      => 'ActualPass123!',
            'password'              => 'simple',
            'password_confirmation' => 'simple',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_tier2_user_cannot_update_another_users_address()
    {
        $userA = $this->createCustomer();
        $userB = $this->createCustomer();
        $addrA = $this->createAddress($userA);

        $response = $this->actingAs($userB, 'user')->put("/user/addresses/{$addrA->id}", [
            'type'           => 'home',
            'full_name'      => 'Hijacker',
            'phone'          => '9876543210',
            'address_line_1' => 'Hijacked Line',
            'city'           => 'Kolkata',
            'state'          => 'WB',
            'postal_code'    => '700001',
            'country'        => 'India',
        ]);

        $response->assertStatus(403);
    }

    public function test_tier2_user_cannot_delete_another_users_address()
    {
        $userA = $this->createCustomer();
        $userB = $this->createCustomer();
        $addrA = $this->createAddress($userA);

        $response = $this->actingAs($userB, 'user')->delete("/user/addresses/{$addrA->id}");
        $response->assertStatus(403);

        $this->assertDatabaseHas('addresses', ['id' => $addrA->id]);
    }

    public function test_tier2_user_cannot_set_default_on_another_users_address()
    {
        $userA = $this->createCustomer();
        $userB = $this->createCustomer();
        $addrA = $this->createAddress($userA);

        $response = $this->actingAs($userB, 'user')->post("/user/addresses/{$addrA->id}/default");
        $response->assertStatus(403);
    }

    /*
    |--------------------------------------------------------------------------
    | Tier 3: Combinatorial & Cross-Feature Interactions
    |--------------------------------------------------------------------------
    */

    public function test_tier3_change_password_followed_by_login_with_new_credentials()
    {
        $user = $this->createCustomer(['password' => Hash::make('FirstPass123!')]);

        // 1. Change password
        $this->actingAs($user, 'user')->post('/user/security/password', [
            'current_password'      => 'FirstPass123!',
            'password'              => 'NextComplexPass789!',
            'password_confirmation' => 'NextComplexPass789!',
        ]);

        // 2. Logout
        $this->actingAs($user, 'user')->post('/logout');

        // 3. Login with new credentials
        $loginResponse = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'NextComplexPass789!',
        ]);

        $loginResponse->assertRedirect();
        $this->assertTrue(Auth::guard('user')->check());
        $this->assertEquals($user->id, Auth::guard('user')->id());
    }
}
