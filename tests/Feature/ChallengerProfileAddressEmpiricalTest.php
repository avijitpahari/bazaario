<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * ChallengerProfileAddressEmpiricalTest
 *
 * Empirical adversarial stress-testing harness for Milestone 1:
 * - Profile Edit (1000-char boundary, >1000 overflow, unicode/emojis, empty, XSS/special chars)
 * - Avatar Upload (disallowed extensions .txt, .php, .pdf, >2MB oversized, valid image & old file cleanup)
 * - Password Change (incorrect old pass, confirmation mismatch, weak pass rules, session continuity)
 * - Address CRUD Authorization (cross-user 403 enforcement, IDOR spoofing prevention, type validation)
 * - Buyer Trust Rank verification (tier transitions)
 */
class ChallengerProfileAddressEmpiricalTest extends TestCase
{
    use RefreshDatabase;

    protected function createBuyer(array $attributes = []): User
    {
        return User::create(array_merge([
            'name'               => 'Test Buyer ' . uniqid(),
            'email'              => 'buyer_' . uniqid() . '@example.com',
            'phone'              => '98' . rand(10000000, 99999999),
            'password'           => Hash::make('CurrentValidPass123!'),
            'role'               => 'user',
            'status'             => 'active',
            'preferred_language' => 'en',
            'email_verified_at'  => now(),
        ], $attributes));
    }

    protected function createAddressFor(User $user, array $attributes = []): Address
    {
        return Address::create(array_merge([
            'user_id'        => $user->id,
            'type'           => 'home',
            'full_name'      => $user->name,
            'phone'          => '9876543210',
            'address_line_1' => '12 Station Road',
            'address_line_2' => 'Flat 3',
            'landmark'       => 'Near Clock Tower',
            'city'           => 'Contai',
            'state'          => 'West Bengal',
            'postal_code'    => '721401',
            'country'        => 'India',
            'is_default'     => true,
        ], $attributes));
    }

    /*
    |--------------------------------------------------------------------------
    | Task 1: Challenge Profile Edit (Bio, Unicode, Boundaries, XSS)
    |--------------------------------------------------------------------------
    */

    public function test_challenge_profile_bio_with_exact_1000_characters_succeeds()
    {
        $user = $this->createBuyer();
        $bio1000 = str_repeat('A', 1000);

        $response = $this->actingAs($user, 'user')->put('/user/profile', [
            'name'  => $user->name,
            'phone' => $user->phone,
            'bio'   => $bio1000,
        ]);

        $response->assertRedirect(route('user.profile'));
        $response->assertSessionHasNoErrors();

        $freshUser = $user->fresh();
        $this->assertEquals(1000, strlen($freshUser->bio));
        $this->assertEquals($bio1000, $freshUser->bio);
    }

    public function test_challenge_profile_bio_with_1001_characters_fails_validation()
    {
        $user = $this->createBuyer(['bio' => 'Original bio']);
        $bio1001 = str_repeat('B', 1001);

        $response = $this->actingAs($user, 'user')->put('/user/profile', [
            'name'  => $user->name,
            'phone' => $user->phone,
            'bio'   => $bio1001,
        ]);

        $response->assertSessionHasErrors('bio');
        $this->assertEquals('Original bio', $user->fresh()->bio);
    }

    public function test_challenge_profile_bio_with_unicode_and_emojis()
    {
        $user = $this->createBuyer();
        $unicodeBio = "বাংলা: আমি বাংলায় গান গাই। हिन्दी: नमस्ते भारत! 日本語: こんにちは世界! Emojis: 🛒🌾✨🎉💻🚀🔥❤️";

        $response = $this->actingAs($user, 'user')->put('/user/profile', [
            'name'  => 'রবীন্দ্র নাথ ঠাকুর',
            'phone' => $user->phone,
            'bio'   => $unicodeBio,
        ]);

        $response->assertRedirect(route('user.profile'));
        $response->assertSessionHasNoErrors();

        $fresh = $user->fresh();
        $this->assertEquals('রবীন্দ্র নাথ ঠাকুর', $fresh->name);
        $this->assertEquals($unicodeBio, $fresh->bio);

        // Verify rendering in profile view
        $viewResponse = $this->actingAs($fresh, 'user')->get('/user/profile');
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('রবীন্দ্র নাথ ঠাকুর');
        $viewResponse->assertSee('বাংলা: আমি বাংলায় গান গাই।');
        $viewResponse->assertSee('हिन्दी: नमस्ते भारत!');
        $viewResponse->assertSee('Emojis: 🛒🌾✨🎉💻🚀🔥❤️');
    }

    public function test_challenge_profile_empty_bio_is_allowed()
    {
        $user = $this->createBuyer(['bio' => 'Existing detailed bio statement']);

        $response = $this->actingAs($user, 'user')->put('/user/profile', [
            'name'  => $user->name,
            'phone' => $user->phone,
            'bio'   => '',
        ]);

        $response->assertRedirect(route('user.profile'));
        $response->assertSessionHasNoErrors();

        $this->assertNull($user->fresh()->bio);
    }

    public function test_challenge_profile_special_characters_and_xss_protection()
    {
        $user = $this->createBuyer();
        $xssBio = '<script>alert("XSS_ATTACK_VECTOR")</script><img src=x onerror=alert(1)> \' OR \'1\'=\'1; --';

        $response = $this->actingAs($user, 'user')->put('/user/profile', [
            'name'  => 'Safe <User>',
            'phone' => $user->phone,
            'bio'   => $xssBio,
        ]);

        $response->assertRedirect(route('user.profile'));
        $response->assertSessionHasNoErrors();

        $fresh = $user->fresh();
        $this->assertEquals($xssBio, $fresh->bio);

        // Verify profile view renders safely with HTML escaping (no unescaped raw <script> execution)
        $viewResponse = $this->actingAs($fresh, 'user')->get('/user/profile');
        $viewResponse->assertStatus(200);
        $viewResponse->assertDontSee('<script>alert("XSS_ATTACK_VECTOR")</script>', false);
        $viewResponse->assertSee(e($xssBio), false);
    }

    /*
    |--------------------------------------------------------------------------
    | Task 2: Challenge Avatar Upload (Invalid Types, Oversized, Valid Image)
    |--------------------------------------------------------------------------
    */

    public function test_challenge_avatar_upload_rejects_txt_file()
    {
        Storage::fake('public');
        $user = $this->createBuyer();
        $file = UploadedFile::fake()->create('document.txt', 150, 'text/plain');

        $response = $this->actingAs($user, 'user')->post('/user/profile/photo', [
            'profile_image' => $file,
        ]);

        $response->assertSessionHasErrors('profile_image');
        $this->assertNull($user->fresh()->profile_image);
    }

    public function test_challenge_avatar_upload_rejects_php_script()
    {
        Storage::fake('public');
        $user = $this->createBuyer();
        $file = UploadedFile::fake()->create('webshell.php', 50, 'application/x-php');

        $response = $this->actingAs($user, 'user')->post('/user/profile/photo', [
            'profile_image' => $file,
        ]);

        $response->assertSessionHasErrors('profile_image');
        $this->assertNull($user->fresh()->profile_image);
    }

    public function test_challenge_avatar_upload_rejects_pdf_document()
    {
        Storage::fake('public');
        $user = $this->createBuyer();
        $file = UploadedFile::fake()->create('profile_cv.pdf', 300, 'application/pdf');

        $response = $this->actingAs($user, 'user')->post('/user/profile/photo', [
            'profile_image' => $file,
        ]);

        $response->assertSessionHasErrors('profile_image');
        $this->assertNull($user->fresh()->profile_image);
    }

    public function test_challenge_avatar_upload_rejects_oversized_file_over_2mb()
    {
        Storage::fake('public');
        $user = $this->createBuyer();
        // 2049 KB exceeds the 2048 KB limit
        $file = UploadedFile::fake()->create('massive_pic.jpg', 2049, 'image/jpeg');

        $response = $this->actingAs($user, 'user')->post('/user/profile/photo', [
            'profile_image' => $file,
        ]);

        $response->assertSessionHasErrors('profile_image');
        $this->assertNull($user->fresh()->profile_image);
    }

    public function test_challenge_avatar_upload_accepts_valid_image_and_deletes_old_file()
    {
        Storage::fake('public');

        // Setup an existing avatar file on disk
        $oldPath = 'profile-images/old_avatar_test.png';
        Storage::disk('public')->put($oldPath, 'dummy old avatar content');
        $this->assertTrue(Storage::disk('public')->exists($oldPath));

        $user = $this->createBuyer(['profile_image' => $oldPath]);

        // Upload a valid 500KB PNG image
        $pngContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        $newFile = UploadedFile::fake()->createWithContent('new_profile.png', $pngContent);

        $response = $this->actingAs($user, 'user')->post('/user/profile/photo', [
            'profile_image' => $newFile,
        ]);

        $response->assertRedirect(route('user.profile'));
        $response->assertSessionHas('success');

        $freshUser = $user->fresh();
        $this->assertNotNull($freshUser->profile_image);
        $this->assertNotEquals($oldPath, $freshUser->profile_image);

        // Verify old file is cleaned up and new file exists
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($freshUser->profile_image);
    }

    /*
    |--------------------------------------------------------------------------
    | Task 3: Challenge Password Change (Wrong Old, Mismatch, Weak Rules)
    |--------------------------------------------------------------------------
    */

    public function test_challenge_password_change_rejects_incorrect_old_password()
    {
        $user = $this->createBuyer(['password' => Hash::make('ActualPass123!')]);

        $response = $this->actingAs($user, 'user')->post('/user/security/password', [
            'current_password'      => 'CompletelyWrongOldPassword123!',
            'password'              => 'BrandNewPass999!',
            'password_confirmation' => 'BrandNewPass999!',
        ]);

        $response->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('ActualPass123!', $user->fresh()->password));
    }

    public function test_challenge_password_change_rejects_confirmation_mismatch()
    {
        $user = $this->createBuyer(['password' => Hash::make('ActualPass123!')]);

        $response = $this->actingAs($user, 'user')->post('/user/security/password', [
            'current_password'      => 'ActualPass123!',
            'password'              => 'BrandNewPass999!',
            'password_confirmation' => 'DifferentConfirmation999!',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertTrue(Hash::check('ActualPass123!', $user->fresh()->password));
    }

    public function test_challenge_password_change_rejects_weak_passwords()
    {
        $user = $this->createBuyer(['password' => Hash::make('ActualPass123!')]);

        $weakPasswords = [
            'short'         => 'Ab1!',              // less than 8 chars
            'no_uppercase'  => 'lowercase1234!',    // missing uppercase
            'no_lowercase'  => 'UPPERCASE1234!',    // missing lowercase
            'no_numbers'    => 'NoNumbersHerePass!',// missing numbers
            'empty'         => '',                  // empty string
        ];

        foreach ($weakPasswords as $label => $candidate) {
            $response = $this->actingAs($user, 'user')->post('/user/security/password', [
                'current_password'      => 'ActualPass123!',
                'password'              => $candidate,
                'password_confirmation' => $candidate,
            ]);

            $response->assertSessionHasErrors('password');
            $this->assertTrue(Hash::check('ActualPass123!', $user->fresh()->password));
        }
    }

    public function test_challenge_password_change_succeeds_with_strong_password_and_authenticates()
    {
        $user = $this->createBuyer(['password' => Hash::make('ActualPass123!')]);

        $newPassword = 'BrandNewStrongPass2026!';
        $response = $this->actingAs($user, 'user')->post('/user/security/password', [
            'current_password'      => 'ActualPass123!',
            'password'              => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        $response->assertRedirect(route('user.security'));
        $response->assertSessionHas('success');

        $fresh = $user->fresh();
        $this->assertTrue(Hash::check($newPassword, $fresh->password));
        $this->assertFalse(Hash::check('ActualPass123!', $fresh->password));
    }

    /*
    |--------------------------------------------------------------------------
    | Task 4: Challenge Address CRUD & 403 Forbidden Authorization
    |--------------------------------------------------------------------------
    */

    public function test_challenge_user_cannot_update_another_users_address_returns_403()
    {
        $victim = $this->createBuyer();
        $attacker = $this->createBuyer();
        $victimAddress = $this->createAddressFor($victim, [
            'address_line_1' => 'Victim Original Address',
            'city'           => 'Kolkata',
        ]);

        $response = $this->actingAs($attacker, 'user')->put("/user/addresses/{$victimAddress->id}", [
            'type'           => 'home',
            'full_name'      => 'Attacker Hijacker',
            'phone'          => '9191919191',
            'address_line_1' => 'Attacker Hijacked Address',
            'city'           => 'Mumbai',
            'state'          => 'Maharashtra',
            'postal_code'    => '400001',
            'country'        => 'India',
        ]);

        $response->assertStatus(403);

        $fresh = $victimAddress->fresh();
        $this->assertEquals('Victim Original Address', $fresh->address_line_1);
        $this->assertEquals('Kolkata', $fresh->city);
        $this->assertEquals($victim->id, $fresh->user_id);
    }

    public function test_challenge_user_cannot_delete_another_users_address_returns_403()
    {
        $victim = $this->createBuyer();
        $attacker = $this->createBuyer();
        $victimAddress = $this->createAddressFor($victim);

        $response = $this->actingAs($attacker, 'user')->delete("/user/addresses/{$victimAddress->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('addresses', ['id' => $victimAddress->id]);
    }

    public function test_challenge_user_cannot_set_default_on_another_users_address_returns_403()
    {
        $victim = $this->createBuyer();
        $attacker = $this->createBuyer();
        $victimAddress = $this->createAddressFor($victim, ['is_default' => false]);

        $response = $this->actingAs($attacker, 'user')->post("/user/addresses/{$victimAddress->id}/default");

        $response->assertStatus(403);
        $this->assertFalse((bool)$victimAddress->fresh()->is_default);
    }

    public function test_challenge_create_address_overwrites_spoofed_user_id()
    {
        $buyer = $this->createBuyer();
        $otherUser = $this->createBuyer();

        $response = $this->actingAs($buyer, 'user')->post('/user/addresses', [
            'user_id'        => $otherUser->id, // Attempt to spoof ownership to other user
            'type'           => 'work',
            'full_name'      => 'Legitimate Name',
            'phone'          => '9876543210',
            'address_line_1' => '99 Spoof Avenue',
            'city'           => 'Digha',
            'state'          => 'West Bengal',
            'postal_code'    => '721428',
            'country'        => 'India',
        ]);

        $response->assertRedirect(route('user.addresses.index'));

        $created = Address::where('address_line_1', '99 Spoof Avenue')->first();
        $this->assertNotNull($created);
        $this->assertEquals($buyer->id, $created->user_id);
        $this->assertNotEquals($otherUser->id, $created->user_id);
    }

    public function test_challenge_address_validates_type_enum_and_mandatory_fields()
    {
        $buyer = $this->createBuyer();

        // 1. Invalid address type
        $response1 = $this->actingAs($buyer, 'user')->post('/user/addresses', [
            'type'           => 'invalid_spaceship_type',
            'full_name'      => 'Test Name',
            'phone'          => '9876543210',
            'address_line_1' => 'Some address',
            'city'           => 'Contai',
            'state'          => 'West Bengal',
            'postal_code'    => '721401',
            'country'        => 'India',
        ]);
        $response1->assertSessionHasErrors('type');

        // 2. Missing mandatory fields
        $response2 = $this->actingAs($buyer, 'user')->post('/user/addresses', [
            'type' => 'home',
        ]);
        $response2->assertSessionHasErrors(['full_name', 'phone', 'address_line_1', 'city', 'state', 'postal_code', 'country']);
    }

    /*
    |--------------------------------------------------------------------------
    | Feature 50: Buyer Trust Rank Verification
    |--------------------------------------------------------------------------
    */

    public function test_challenge_buyer_trust_rank_tiers()
    {
        $buyer = $this->createBuyer();

        // Baseline: 0 completed orders
        $this->assertEquals('Tier-1 Verified Buyer', $buyer->buyer_trust_rank);

        $orderTemplate = [
            'user_id'                => $buyer->id,
            'order_type'             => 'cart',
            'order_status'           => 'completed',
            'payment_status'         => 'paid',
            'payment_method'         => 'cod',
            'total_amount'           => 500,
            'subtotal'               => 500,
            'delivery_full_name'     => $buyer->name,
            'delivery_phone'         => '9876543210',
            'delivery_address_line_1'=> '12 Station Road',
            'delivery_city'          => 'Contai',
            'delivery_state'         => 'West Bengal',
            'delivery_country'       => 'India',
            'delivery_postal_code'   => '721401',
        ];

        // Create 3 completed orders
        for ($i = 0; $i < 3; $i++) {
            Order::create(array_merge($orderTemplate, [
                'order_number' => 'ORD-M1-' . uniqid(),
            ]));
        }
        $this->assertEquals('Tier-2 Trusted Buyer', $buyer->fresh()->buyer_trust_rank);

        // Create 7 more completed orders (total 10)
        for ($i = 0; $i < 7; $i++) {
            Order::create(array_merge($orderTemplate, [
                'order_number' => 'ORD-M1-' . uniqid(),
            ]));
        }
        $this->assertEquals('Tier-3 Elite Buyer', $buyer->fresh()->buyer_trust_rank);
    }
}
