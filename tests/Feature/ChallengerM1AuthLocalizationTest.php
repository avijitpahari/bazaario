<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ChallengerM1AuthLocalizationTest extends TestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | Task 1: Seller Authentication Transitions & Redirect Loops
    |--------------------------------------------------------------------------
    */

    /**
     * EMPIRICAL FINDING: Pending seller registration redirects to /seller/pending,
     * but visiting /seller/pending crashes with 500 because view('seller.pending') is missing.
     */
    public function test_challenge_pending_seller_registration_view_missing(): void
    {
        $this->postJson(route('register.send-otp'), [
            'name'                  => 'Pending Merchant',
            'email'                 => 'pending.merchant@bazaario.com',
            'phone'                 => '+91 9876543299',
            'password'              => 'MerchantSecret123!',
            'password_confirmation' => 'MerchantSecret123!',
            'role'                  => 'seller',
            'terms'                 => '1',
        ])->assertStatus(200);

        $otpRecord = DB::table('email_otps')->where('email', 'pending.merchant@bazaario.com')->first();
        $this->assertNotNull($otpRecord);

        $verify = $this->postJson(route('register.verify-otp'), [
            'otp' => $otpRecord->otp,
        ]);

        $verify->assertStatus(200)
            ->assertJson([
                'success'  => true,
                'redirect' => route('seller.pending'),
            ]);

        $this->assertTrue(Auth::guard('seller')->check());

        // Visiting seller.pending - empirically demonstrates missing view bug
        $pendingPage = $this->get(route('seller.pending'));
        $pendingPage->assertStatus(200);
    }

    /**
     * EMPIRICAL FINDING: Pending seller login redirects to /seller/pending,
     * but visiting /seller/pending crashes with 500 because view('seller.pending') is missing.
     */
    public function test_challenge_pending_seller_login_view_missing(): void
    {
        $seller = User::factory()->create([
            'email'    => 'seller.under.review@bazaario.com',
            'password' => Hash::make('SellerPass123!'),
            'role'     => 'seller',
            'status'   => 'active',
        ]);

        SellerProfile::create([
            'user_id'     => $seller->id,
            'shop_name'   => 'Under Review Shop',
            'shop_slug'   => 'under-review-shop',
            'status'      => 'pending',
            'trust_score' => 50,
        ]);

        $loginResponse = $this->post('/login', [
            'email'    => 'seller.under.review@bazaario.com',
            'password' => 'SellerPass123!',
        ]);

        $loginResponse->assertRedirect(route('seller.pending'));
        $this->assertTrue(Auth::guard('seller')->check());

        // Visiting seller.pending - empirically demonstrates missing view bug
        $pendingResponse = $this->get(route('seller.pending'));
        $pendingResponse->assertStatus(200);
    }

    /**
     * Approved seller login transitions to dashboard with zero redirect loops.
     */
    public function test_challenge_approved_seller_login_dashboard_transition(): void
    {
        $seller = User::factory()->create([
            'email'    => 'approved.merchant@bazaario.com',
            'password' => Hash::make('ApprovedPass123!'),
            'role'     => 'seller',
            'status'   => 'active',
        ]);

        SellerProfile::create([
            'user_id'     => $seller->id,
            'shop_name'   => 'Approved Pottery',
            'shop_slug'   => 'approved-pottery',
            'status'      => 'approved',
            'trust_score' => 99,
        ]);

        $loginResponse = $this->post('/login', [
            'email'    => 'approved.merchant@bazaario.com',
            'password' => 'ApprovedPass123!',
        ]);

        $loginResponse->assertRedirect(route('seller.dashboard'));
        $this->assertTrue(Auth::guard('seller')->check());

        // Visiting dashboard returns 200 without redirect
        $dashboardResponse = $this->get(route('seller.dashboard'));
        $dashboardResponse->assertStatus(200);

        // Visiting pending redirects approved seller to dashboard
        $pendingResponse = $this->get(route('seller.pending'));
        $pendingResponse->assertRedirect(route('seller.dashboard'));

        // Visiting login while authenticated redirects to dashboard
        $loginAgain = $this->get(route('login'));
        $loginAgain->assertRedirect(route('seller.dashboard'));
    }

    /**
     * Suspended seller cannot login and is rejected cleanly.
     */
    public function test_challenge_suspended_seller_login_blocked(): void
    {
        User::factory()->create([
            'email'    => 'suspended.seller@bazaario.com',
            'password' => Hash::make('Secret123!'),
            'role'     => 'seller',
            'status'   => 'suspended',
        ]);

        $response = $this->post('/login', [
            'email'    => 'suspended.seller@bazaario.com',
            'password' => 'Secret123!',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertFalse(Auth::guard('seller')->check());
        $this->assertFalse(Auth::guard('user')->check());
    }

    /*
    |--------------------------------------------------------------------------
    | Task 2: Password Reset Workflow Challenges
    |--------------------------------------------------------------------------
    */

    /**
     * Challenge 2a: Request reset for invalid email format.
     */
    public function test_challenge_password_reset_invalid_email_format(): void
    {
        $response = $this->post(route('password.email'), [
            'email' => 'not-a-valid-email',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => 'not-a-valid-email',
        ]);
    }

    /**
     * Challenge 2b: Request reset for non-existent email.
     */
    public function test_challenge_password_reset_nonexistent_email(): void
    {
        $response = $this->post(route('password.email'), [
            'email' => 'ghost.user.404@bazaario.com',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertEquals(
            'We could not find an account associated with that email address.',
            session('errors')->first('email')
        );
        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => 'ghost.user.404@bazaario.com',
        ]);
    }

    /**
     * Challenge 2c: Request reset with empty email.
     */
    public function test_challenge_password_reset_empty_email(): void
    {
        $response = $this->post(route('password.email'), [
            'email' => '',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Challenge 2d: Password reset submission with empty token.
     */
    public function test_challenge_password_reset_empty_token(): void
    {
        $user = User::factory()->create([
            'email'    => 'token.target@bazaario.com',
            'password' => Hash::make('OriginalPass123!'),
        ]);

        $response = $this->post(route('password.update'), [
            'token'                 => '',
            'email'                 => 'token.target@bazaario.com',
            'password'              => 'NewValidPass123!',
            'password_confirmation' => 'NewValidPass123!',
        ]);

        $response->assertSessionHasErrors(['token']);

        // Assert password unchanged
        $user->refresh();
        $this->assertTrue(Hash::check('OriginalPass123!', $user->password));
    }

    /**
     * Challenge 2e: Password reset submission with invalid / tampered token.
     */
    public function test_challenge_password_reset_tampered_token(): void
    {
        $user = User::factory()->create([
            'email'    => 'tamper.target@bazaario.com',
            'password' => Hash::make('OriginalPass123!'),
        ]);

        Password::broker()->createToken($user);

        $response = $this->post(route('password.update'), [
            'token'                 => 'forged-fake-token-1234567890',
            'email'                 => 'tamper.target@bazaario.com',
            'password'              => 'NewValidPass123!',
            'password_confirmation' => 'NewValidPass123!',
        ]);

        $response->assertSessionHasErrors(['email']);

        // Assert password unchanged
        $user->refresh();
        $this->assertTrue(Hash::check('OriginalPass123!', $user->password));
    }

    /**
     * Challenge 2f: Password reset submission with mismatched password confirmation.
     */
    public function test_challenge_password_reset_mismatched_passwords(): void
    {
        $user = User::factory()->create([
            'email'    => 'mismatch.target@bazaario.com',
            'password' => Hash::make('OriginalPass123!'),
        ]);

        $token = Password::broker()->createToken($user);

        $response = $this->post(route('password.update'), [
            'token'                 => $token,
            'email'                 => 'mismatch.target@bazaario.com',
            'password'              => 'PasswordOne123!',
            'password_confirmation' => 'PasswordDifferent999!',
        ]);

        $response->assertSessionHasErrors(['password']);

        // Assert password unchanged
        $user->refresh();
        $this->assertTrue(Hash::check('OriginalPass123!', $user->password));
    }

    /**
     * Challenge 2g: Password reset submission with password under 8 characters.
     */
    public function test_challenge_password_reset_short_password(): void
    {
        $user = User::factory()->create([
            'email'    => 'short.target@bazaario.com',
            'password' => Hash::make('OriginalPass123!'),
        ]);

        $token = Password::broker()->createToken($user);

        $response = $this->post(route('password.update'), [
            'token'                 => $token,
            'email'                 => 'short.target@bazaario.com',
            'password'              => 'short1!',
            'password_confirmation' => 'short1!',
        ]);

        $response->assertSessionHasErrors(['password']);

        // Assert password unchanged
        $user->refresh();
        $this->assertTrue(Hash::check('OriginalPass123!', $user->password));
    }

    /**
     * Challenge 2h: Password reset replay attack (re-using the same token after successful reset).
     */
    public function test_challenge_password_reset_token_replay_rejected(): void
    {
        $user = User::factory()->create([
            'email'    => 'replay.target@bazaario.com',
            'password' => Hash::make('OriginalPass123!'),
        ]);

        $token = Password::broker()->createToken($user);

        // First reset: should succeed
        $response1 = $this->post(route('password.update'), [
            'token'                 => $token,
            'email'                 => 'replay.target@bazaario.com',
            'password'              => 'FirstResetPass123!',
            'password_confirmation' => 'FirstResetPass123!',
        ]);
        $response1->assertRedirect(route('login'));

        $user->refresh();
        $this->assertTrue(Hash::check('FirstResetPass123!', $user->password));

        // Replay attack: try using the exact same token again
        $response2 = $this->post(route('password.update'), [
            'token'                 => $token,
            'email'                 => 'replay.target@bazaario.com',
            'password'              => 'SecondResetPass999!',
            'password_confirmation' => 'SecondResetPass999!',
        ]);

        $response2->assertSessionHasErrors(['email']);

        // Assert password was NOT changed to the second one
        $user->refresh();
        $this->assertTrue(Hash::check('FirstResetPass123!', $user->password));
        $this->assertFalse(Hash::check('SecondResetPass999!', $user->password));
    }

    /*
    |--------------------------------------------------------------------------
    | Task 3: Language Switching & Persistence Challenges
    |--------------------------------------------------------------------------
    */

    /**
     * Challenge 3a: Language switch endpoint rejects invalid locales cleanly (422 JSON / redirect).
     */
    public function test_challenge_language_switch_rejects_invalid_locales(): void
    {
        // 1. French (unsupported)
        $this->postJson(route('language.switch'), ['locale' => 'fr'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['locale']);

        // 2. Arbitrary string 'xx'
        $this->postJson(route('language.switch'), ['locale' => 'xx'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['locale']);

        // 3. Empty string
        $this->postJson(route('language.switch'), ['locale' => ''])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['locale']);

        // 4. Numeric injection
        $this->postJson(route('language.switch'), ['locale' => '123'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['locale']);

        // 5. Path traversal / SQL injection probe
        $this->postJson(route('language.switch'), ['locale' => '../../locale'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['locale']);
    }

    /**
     * Challenge 3b: SetLocale middleware cleanses invalid session locale and falls back to 'en'.
     */
    public function test_challenge_middleware_cleanses_invalid_session_locale(): void
    {
        // Forcibly inject invalid locale 'fr' into session
        $response = $this->withSession(['locale' => 'fr'])
            ->get(route('home'));

        $response->assertStatus(200);
        $this->assertEquals('en', App::getLocale());
    }

    /**
     * Challenge 3c: SetLocale middleware cleanses invalid cookie locale and falls back to 'en'.
     */
    public function test_challenge_middleware_cleanses_invalid_cookie_locale(): void
    {
        // Forcibly provide validly encrypted cookie 'xx' with no session
        $response = $this->withCookie('locale', 'xx')
            ->get(route('home'));

        $response->assertStatus(200);
        $this->assertEquals('en', App::getLocale());
    }

    /**
     * Challenge 3d: Locale persists via cookie across requests without session state.
     */
    public function test_challenge_locale_persists_via_cookie_without_session(): void
    {
        // Send request with standard encrypted cookie locale = 'hi' without session
        $response = $this->withCookie('locale', 'hi')
            ->get(route('home'));

        $response->assertStatus(200);
        $this->assertEquals('hi', App::getLocale());
        $this->assertEquals('hi', session('locale'));
    }

    /**
     * Challenge 3e: Authenticated user preferred_language takes precedence when session is empty.
     */
    public function test_challenge_user_preferred_language_persists_across_sessions(): void
    {
        $user = User::factory()->create([
            'preferred_language' => 'bn',
        ]);

        $this->actingAs($user, 'user');

        // Without session locale, SetLocale should retrieve from user profile
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $this->assertEquals('bn', App::getLocale());
        $this->assertEquals('bn', session('locale'));
    }

    /**
     * Challenge 3f: Switching language as authenticated user updates session, cookie, and database.
     */
    public function test_challenge_authenticated_language_switch_updates_all_tiers(): void
    {
        $user = User::factory()->create([
            'preferred_language' => 'en',
        ]);

        $this->actingAs($user, 'user');

        $response = $this->post(route('language.switch'), [
            'locale' => 'hi',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('locale', 'hi');
        $response->assertCookie('locale', 'hi');

        $user->refresh();
        $this->assertEquals('hi', $user->preferred_language);
    }

    /*
    |--------------------------------------------------------------------------
    | Task 4: Cross-Guard RBAC Isolation & Logout
    |--------------------------------------------------------------------------
    */

    /**
     * Challenge 4a: Customer user cannot access seller protected routes.
     */
    public function test_challenge_customer_cannot_access_seller_routes(): void
    {
        $buyer = User::factory()->create(['role' => 'user']);
        Auth::guard('user')->login($buyer);

        $response = $this->get(route('seller.dashboard'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Challenge 4b: Seller cannot access admin protected routes.
     */
    public function test_challenge_seller_cannot_access_admin_routes(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);
        Auth::guard('seller')->login($seller);

        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('admin.login'));
    }

    /**
     * Challenge 4c: Admin credentials rejected from public /login form.
     */
    public function test_challenge_admin_rejected_from_public_login(): void
    {
        User::factory()->create([
            'email'    => 'admin.probe@bazaario.com',
            'password' => Hash::make('AdminPass123!'),
            'role'     => 'admin',
            'status'   => 'active',
        ]);

        $response = $this->post('/login', [
            'email'    => 'admin.probe@bazaario.com',
            'password' => 'AdminPass123!',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertFalse(Auth::guard('user')->check());
        $this->assertFalse(Auth::guard('admin')->check());
    }

    /**
     * Challenge 4d: Seller logout invalidates seller guard session.
     */
    public function test_challenge_seller_logout_terminates_seller_guard(): void
    {
        $seller = User::factory()->create(['role' => 'seller', 'status' => 'active']);
        Auth::guard('seller')->login($seller);

        $this->assertTrue(Auth::guard('seller')->check());

        $logoutResponse = $this->post(route('logout'));
        $logoutResponse->assertRedirect(route('login'));

        $this->assertFalse(Auth::guard('seller')->check());

        // Subsequent request to seller dashboard must be redirected to login
        $dashboardResponse = $this->get(route('seller.dashboard'));
        $dashboardResponse->assertRedirect(route('login'));
    }
}

