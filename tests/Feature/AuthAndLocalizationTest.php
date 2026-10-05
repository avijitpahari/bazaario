<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuthAndLocalizationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Feature 1: Registration sends OTP and verifies registration.
     */
    public function test_f1_registration_otp_and_creation(): void
    {
        $response = $this->postJson(route('register.send-otp'), [
            'name'                  => 'Arjun Sharma',
            'email'                 => 'arjun@example.com',
            'phone'                 => '+91 9876543210',
            'password'              => 'Secret1234!',
            'password_confirmation' => 'Secret1234!',
            'role'                  => 'user',
            'terms'                 => '1',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $otpRecord = DB::table('email_otps')->where('email', 'arjun@example.com')->first();
        $this->assertNotNull($otpRecord);
        $this->assertEquals(6, strlen($otpRecord->otp));

        // Submit OTP
        $verifyResponse = $this->postJson(route('register.verify-otp'), [
            'otp' => $otpRecord->otp,
        ]);

        $verifyResponse->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', [
            'email' => 'arjun@example.com',
            'role'  => 'user',
        ]);

        $this->assertTrue(Auth::guard('user')->check());
    }

    /**
     * Feature 1 & 2: Seller registration sets seller guard and redirects without loop.
     */
    public function test_f1_seller_registration_authenticates_seller_guard(): void
    {
        $this->postJson(route('register.send-otp'), [
            'name'                  => 'Ramesh Traders',
            'email'                 => 'ramesh@traders.com',
            'phone'                 => '+91 9876543211',
            'password'              => 'Secret1234!',
            'password_confirmation' => 'Secret1234!',
            'role'                  => 'seller',
            'terms'                 => '1',
        ]);

        $otpRecord = DB::table('email_otps')->where('email', 'ramesh@traders.com')->first();

        $verifyResponse = $this->postJson(route('register.verify-otp'), [
            'otp' => $otpRecord->otp,
        ]);

        $verifyResponse->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('seller_profiles', [
            'status' => 'pending',
        ]);

        $this->assertTrue(Auth::guard('seller')->check());
        $user = Auth::guard('seller')->user();
        $this->assertEquals('seller', $user->role);
    }

    /**
     * Feature 2: Seller login authenticates seller guard and prevents redirect loop.
     */
    public function test_f2_seller_login_authenticates_seller_guard_and_prevents_loop(): void
    {
        $seller = User::factory()->create([
            'email'    => 'approved.seller@bazaario.com',
            'password' => Hash::make('SellerPass123!'),
            'role'     => 'seller',
            'status'   => 'active',
        ]);

        SellerProfile::create([
            'user_id'     => $seller->id,
            'shop_name'   => 'Artisan Pottery',
            'shop_slug'   => 'artisan-pottery',
            'status'      => 'approved',
            'trust_score' => 95,
        ]);

        $response = $this->post('/login', [
            'email'    => 'approved.seller@bazaario.com',
            'password' => 'SellerPass123!',
        ]);

        $response->assertRedirect(route('seller.dashboard'));
        $this->assertTrue(Auth::guard('seller')->check());

        // Visiting dashboard must return 200 without being redirected back to login
        $dashboardResponse = $this->get(route('seller.dashboard'));
        $dashboardResponse->assertStatus(200);
    }

    /**
     * Feature 3: Logout terminates all guards.
     */
    public function test_f3_logout_terminates_all_guards(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        Auth::guard('user')->login($user);

        $this->assertTrue(Auth::guard('user')->check());

        $response = $this->post(route('logout'));
        $response->assertRedirect(route('login'));

        $this->assertFalse(Auth::guard('user')->check());
        $this->assertFalse(Auth::guard('seller')->check());
    }

    /**
     * Feature 4 & 5: Forgot Password sends reset link and Password Reset updates password.
     */
    public function test_f4_f5_forgot_password_and_reset_flow(): void
    {
        $user = User::factory()->create([
            'email'    => 'buyer.forgot@example.com',
            'password' => Hash::make('OldPassword123!'),
        ]);

        // 1. Show forgot password page
        $forgotPage = $this->get(route('password.request'));
        $forgotPage->assertStatus(200);

        // 2. Submit forgot password request
        $sendReset = $this->post(route('password.email'), [
            'email' => 'buyer.forgot@example.com',
        ]);
        $sendReset->assertRedirect();
        $sendReset->assertSessionHas('status');

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'buyer.forgot@example.com',
        ]);

        // 3. Reset password using generated token
        $token = Password::broker()->createToken($user);

        $resetPage = $this->get(route('password.reset', ['token' => $token, 'email' => $user->email]));
        $resetPage->assertStatus(200);

        $resetResponse = $this->post(route('password.update'), [
            'token'                 => $token,
            'email'                 => 'buyer.forgot@example.com',
            'password'              => 'BrandNewPass123!',
            'password_confirmation' => 'BrandNewPass123!',
        ]);

        $resetResponse->assertRedirect(route('login'));
        $resetResponse->assertSessionHas('success');

        // 4. Verify user can now log in with new password
        $user->refresh();
        $this->assertTrue(Hash::check('BrandNewPass123!', $user->password));

        $loginResponse = $this->post('/login', [
            'email'    => 'buyer.forgot@example.com',
            'password' => 'BrandNewPass123!',
        ]);
        $loginResponse->assertRedirect(route('products.index'));
    }

    /**
     * Feature 6: RBAC checks in SellerMiddleware.
     */
    public function test_f6_rbac_seller_middleware_enforcement(): void
    {
        // 1. Guest redirected to login
        $guestResponse = $this->get(route('seller.dashboard'));
        $guestResponse->assertRedirect(route('login'));

        // 2. Customer user rejected from seller dashboard
        $customer = User::factory()->create(['role' => 'user']);
        Auth::guard('user')->login($customer);
        $customerResponse = $this->get(route('seller.dashboard'));
        $customerResponse->assertRedirect(route('login'));

        // 3. Suspended seller rejected and logged out
        $suspendedSeller = User::factory()->create([
            'role'   => 'seller',
            'status' => 'suspended',
        ]);
        Auth::guard('seller')->login($suspendedSeller);
        $suspendedResponse = $this->get(route('seller.dashboard'));
        $suspendedResponse->assertRedirect(route('login'));
        $this->assertFalse(Auth::guard('seller')->check());
    }

    /**
     * Feature 7 & 8: Language Selector and Persistence across Session, Cookie, and DB.
     */
    public function test_f7_f8_language_switching_and_persistence(): void
    {
        $user = User::factory()->create([
            'preferred_language' => 'en',
        ]);

        $this->actingAs($user, 'user');

        // Switch to Hindi
        $responseHi = $this->post(route('language.switch'), [
            'locale' => 'hi',
        ]);
        $responseHi->assertRedirect();
        $responseHi->assertSessionHas('locale', 'hi');
        $responseHi->assertCookie('locale', 'hi');

        $user->refresh();
        $this->assertEquals('hi', $user->preferred_language);

        // Next request should trigger SetLocale middleware
        $homeResponse = $this->get(route('home'));
        $homeResponse->assertStatus(200);
        $this->assertEquals('hi', App::getLocale());

        // Switch to Bengali
        $responseBn = $this->postJson(route('language.switch'), [
            'locale' => 'bn',
        ]);
        $responseBn->assertStatus(200)
            ->assertJson(['success' => true, 'locale' => 'bn']);

        $user->refresh();
        $this->assertEquals('bn', $user->preferred_language);

        // Invalid locale rejected
        $responseInvalid = $this->postJson(route('language.switch'), [
            'locale' => 'fr',
        ]);
        $responseInvalid->assertStatus(422);
    }

    /**
     * Feature 50: View profile renders personal info and buyer trust rank badge.
     */
    public function test_f50_view_profile_displays_trust_rank(): void
    {
        $user = User::factory()->create([
            'name'  => 'Siddharth Roy',
            'email' => 'siddharth@example.com',
            'role'  => 'user',
        ]);

        $this->actingAs($user, 'user');

        $response = $this->get(route('user.profile'));
        $response->assertStatus(200);
        $response->assertSee('Siddharth Roy');
        $response->assertSee('siddharth@example.com');
        $response->assertSee('Tier-1 Verified Buyer');
    }

    /**
     * Feature 51: Edit profile updates name, phone, and bio.
     */
    public function test_f51_edit_profile_saves_name_phone_and_bio(): void
    {
        $user = User::factory()->create([
            'name'  => 'Original Name',
            'phone' => '+91 9999900000',
            'bio'   => null,
        ]);

        $this->actingAs($user, 'user');

        $editView = $this->get(route('user.profile.edit'));
        $editView->assertStatus(200);
        $editView->assertSee('About / Bio');

        $updateResponse = $this->put(route('user.profile.update'), [
            'name'               => 'Updated Name',
            'phone'              => '+91 9999911111',
            'bio'                => 'Passionate art collector and vintage enthusiast from Kolkata.',
            'preferred_language' => 'bn',
        ]);

        $updateResponse->assertRedirect(route('user.profile'));

        $user->refresh();
        $this->assertEquals('Updated Name', $user->name);
        $this->assertEquals('+91 9999911111', $user->phone);
        $this->assertEquals('Passionate art collector and vintage enthusiast from Kolkata.', $user->bio);
        $this->assertEquals('bn', $user->preferred_language);
    }

    /**
     * Feature 54: Address update form and PUT endpoint execution.
     */
    public function test_f54_address_update_flow(): void
    {
        $user = User::factory()->create();
        $address = Address::create([
            'user_id'        => $user->id,
            'type'           => 'home',
            'full_name'      => 'Aarav Gupta',
            'phone'          => '9876543210',
            'address_line_1' => '12 Market Lane',
            'city'           => 'Kolkata',
            'state'          => 'West Bengal',
            'postal_code'    => '700001',
            'country'        => 'India',
            'is_default'     => true,
        ]);

        $this->actingAs($user, 'user');

        $indexView = $this->get(route('user.addresses.index'));
        $indexView->assertStatus(200);
        $indexView->assertSee('Edit Address Details');

        $updateResponse = $this->put(route('user.addresses.update', $address->id), [
            'type'           => 'work',
            'full_name'      => 'Aarav Gupta Work',
            'phone'          => '9876543210',
            'address_line_1' => '88 Tech Park, Sector V',
            'city'           => 'Kolkata',
            'state'          => 'West Bengal',
            'postal_code'    => '700091',
            'country'        => 'India',
            'is_default'     => true,
        ]);

        $updateResponse->assertRedirect(route('user.addresses.index'));

        $address->refresh();
        $this->assertEquals('work', $address->type);
        $this->assertEquals('Aarav Gupta Work', $address->full_name);
        $this->assertEquals('88 Tech Park, Sector V', $address->address_line_1);
        $this->assertEquals('700091', $address->postal_code);
    }
}
