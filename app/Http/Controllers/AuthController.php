<?php

namespace App\Http\Controllers;

use App\Mail\EmailOtpMail;
use App\Mail\WelcomeEmail;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Show Login Page
    |--------------------------------------------------------------------------
    */

    public function showLogin()
    {
        if (Auth::guard('user')->check()) {
            return $this->redirectByRole(Auth::guard('user')->user());
        }

        return view('auth.login');
    }


    /*
    |--------------------------------------------------------------------------
    | Login user / Seller
    |--------------------------------------------------------------------------
    */

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember') || $request->boolean('remember_me');

        /*
        |--------------------------------------------------------------------------
        | Login using USER guard
        |--------------------------------------------------------------------------
        */

        if (
            Auth::guard('user')->attempt(
                $credentials,
                $remember
            )
        ) {

            // Prevent session fixation
            $request->session()->regenerate();

            $user = Auth::guard('user')->user();

            /*
            |--------------------------------------------------------------------------
            | Check account status
            |--------------------------------------------------------------------------
            */

            if ($user->status !== 'active') {

                Auth::guard('user')->logout();

                return back()
                    ->withErrors([
                        'email' => 'Your account is currently ' . $user->status . '.',
                    ])
                    ->onlyInput('email');
            }

            /*
            |--------------------------------------------------------------------------
            | Admin should NOT login from normal login page
            |--------------------------------------------------------------------------
            */

            if ($user->role === 'admin') {

                Auth::guard('user')->logout();

                return back()
                    ->withErrors([
                        'email' => 'Please use the Admin Login page.',
                    ])
                    ->onlyInput('email');
            }

            /*
            |--------------------------------------------------------------------------
            | Redirect user / Seller
            |--------------------------------------------------------------------------
            */

            return $this->redirectByRole($user);
        }

        /*
        |--------------------------------------------------------------------------
        | Login Failed
        |--------------------------------------------------------------------------
        */

        return back()
            ->withErrors([
                'email' => 'The provided email or password is incorrect.',
            ])
            ->onlyInput('email');
    }


    /*
    |--------------------------------------------------------------------------
    | Show Register Page
    |--------------------------------------------------------------------------
    */

    public function showRegister()
    {
        if (Auth::guard('user')->check()) {
            return $this->redirectByRole(Auth::guard('user')->user());
        }

        return view('auth.register');
    }


    /*
    |--------------------------------------------------------------------------
    | Step 1 — Validate Registration & Send OTP
    |--------------------------------------------------------------------------
    */

    public function sendOtp(Request $request)
    {
        // Return JSON validation errors for AJAX requests
        $validator = Validator::make($request->all(), [
            'name'          => ['required', 'string', 'max:100'],
            'email'         => ['required', 'email', 'max:150', 'unique:users,email'],
            'phone'         => ['nullable', 'string'],
            'password'      => ['required', 'confirmed', 'min:8'],
            'role'          => ['required', 'in:user,seller'],
            'terms'         => ['accepted'],
            'profile_image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $validated = $validator->validated();

        /*
        |--------------------------------------------------------------------------
        | Generate OTP
        |--------------------------------------------------------------------------
        */

        $otp = str_pad(
            random_int(0, 999999),
            6,
            '0',
            STR_PAD_LEFT
        );

        /*
        |--------------------------------------------------------------------------
        | Remove Previous OTP
        |--------------------------------------------------------------------------
        */

        DB::table('email_otps')
            ->where('email', $validated['email'])
            ->delete();

        /*
        |--------------------------------------------------------------------------
        | Store New OTP
        |--------------------------------------------------------------------------
        */

        DB::table('email_otps')->insert([
            'email'      => $validated['email'],
            'otp'        => $otp,
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Send OTP Email
        |--------------------------------------------------------------------------
        */

        Mail::to($validated['email'])
            ->send(new EmailOtpMail($otp));

        /*
        |--------------------------------------------------------------------------
        | Temporarily Store Registration Data
        |--------------------------------------------------------------------------
        */

        $profileImageTmpPath = null;

        if ($request->hasFile('profile_image')) {

            $profileImageTmpPath = $request
                ->file('profile_image')
                ->store('tmp/profile_images', 'local');
        }

        // Store bcrypt-hashed password so User::create cast doesn't double-hash
        $request->session()->put('pending_registration', [

            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'phone'             => $validated['phone'] ?? null,
            'password'          => bcrypt($validated['password']),
            'role'              => $validated['role'],
            'profile_image_tmp' => $profileImageTmpPath,

        ]);

        return response()->json([
            'success' => true,
            'message' => 'OTP sent to ' . $validated['email'],
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Resend OTP for Pending Registration
    |--------------------------------------------------------------------------
    */

    public function resendOtp(Request $request)
    {
        $pending = $request->session()->get('pending_registration');

        if (!$pending) {
            return response()->json([
                'success' => false,
                'message' => 'Registration session expired. Please fill the form again.',
            ], 422);
        }

        $email = $pending['email'];

        $otp = str_pad(
            random_int(0, 999999),
            6,
            '0',
            STR_PAD_LEFT
        );

        DB::table('email_otps')->where('email', $email)->delete();

        DB::table('email_otps')->insert([
            'email'      => $email,
            'otp'        => $otp,
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
        ]);

        Mail::to($email)->send(new EmailOtpMail($otp));

        return response()->json([
            'success' => true,
            'message' => 'New OTP sent to ' . $email,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Step 2 — Verify OTP & Register User
    |--------------------------------------------------------------------------
    */

    public function verifyOtpAndRegister(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Get Pending Registration
        |--------------------------------------------------------------------------
        */

        $pending = $request->session()
            ->get('pending_registration');

        if (!$pending) {

            return response()->json([
                'success' => false,
                'message' => 'Session expired. Please fill the form again.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Find OTP
        |--------------------------------------------------------------------------
        */

        $record = DB::table('email_otps')
            ->where('email', $pending['email'])
            ->where('otp', $request->input('otp'))
            ->first();

        if (!$record) {

            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP. Please try again.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Check OTP Expiration
        |--------------------------------------------------------------------------
        */

        if (now()->isAfter($record->expires_at)) {

            DB::table('email_otps')
                ->where('email', $pending['email'])
                ->delete();

            return response()->json([
                'success' => false,
                'message' => 'OTP has expired. Please request a new one.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | OTP Valid — Delete OTP
        |--------------------------------------------------------------------------
        */

        DB::table('email_otps')
            ->where('email', $pending['email'])
            ->delete();


        /*
        |--------------------------------------------------------------------------
        | Move Profile Image
        |--------------------------------------------------------------------------
        */

        $profileImagePath = null;

        if (
            !empty($pending['profile_image_tmp']) &&
            Storage::disk('local')
                ->exists($pending['profile_image_tmp'])
        ) {

            $extension = pathinfo(
                $pending['profile_image_tmp'],
                PATHINFO_EXTENSION
            );

            $permanentPath =
                'profile_images/' .
                Str::uuid() .
                '.' .
                $extension;

            $fileContent = Storage::disk('local')
                ->get($pending['profile_image_tmp']);

            Storage::disk('public')
                ->put($permanentPath, $fileContent);

            Storage::disk('local')
                ->delete($pending['profile_image_tmp']);

            $profileImagePath = $permanentPath;
        }


        /*
        |--------------------------------------------------------------------------
        | Create User
        |--------------------------------------------------------------------------
        */

        $user = DB::transaction(function () use (
            $pending,
            $profileImagePath
        ) {

            $user = User::create([

                'name'               => $pending['name'],
                'email'              => $pending['email'],
                'phone'              => $pending['phone'],
                'password'           => $pending['password'],
                'role'               => $pending['role'],
                'profile_image'      => $profileImagePath,
                'email_verified_at'  => now(),
                'preferred_language' => 'en',
                'status'             => 'active',

            ]);


            /*
            |--------------------------------------------------------------------------
            | Create Seller Profile
            |--------------------------------------------------------------------------
            */

            if ($user->role === 'seller') {

                SellerProfile::create([

                    'user_id'   => $user->id,
                    'shop_name' => $user->name . "'s Shop",
                    'shop_slug' => Str::slug(
                        $user->name . '-' . $user->id
                    ),
                    'status'    => 'pending',

                ]);
            }

            return $user;
        });


        /*
        |--------------------------------------------------------------------------
        | Clear Registration Session
        |--------------------------------------------------------------------------
        */

        $request->session()
            ->forget('pending_registration');


        /*
        |--------------------------------------------------------------------------
        | Login User After Registration & Send Welcome Email
        |--------------------------------------------------------------------------
        */

        Auth::guard('user')->login($user);

        $request->session()->regenerate();

        try {
            Mail::to($user->email)->send(new WelcomeEmail($user));
        } catch (\Exception $e) {
            // Log mail failure gracefully so registration completion is not blocked
            logger()->error('Failed to send welcome email: ' . $e->getMessage());
        }

        return response()->json([

            'success' => true,

            'redirect' =>
                $this->getRedirectUrlAfterRegistration($user),

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Logout user / Seller
    |--------------------------------------------------------------------------
    */

    public function logout(Request $request)
    {
        Auth::guard('user')->logout();
        Auth::guard('seller')->logout();
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with(
                'success',
                'You have been logged out successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Redirect User According To Role
    |--------------------------------------------------------------------------
    */

    private function redirectByRole(User $user)
    {
        switch ($user->role) {

            /*
            |--------------------------------------------------------------------------
            | Seller
            |--------------------------------------------------------------------------
            */

            case 'seller':

                if (
                    $user->sellerProfile &&
                    $user->sellerProfile->status === 'approved'
                ) {

                    return redirect()
                        ->route('seller.dashboard');
                }

                return redirect()
                    ->route('seller.pending')
                    ->with(
                        'info',
                        'Your seller account is waiting for admin approval.'
                    );


            /*
            |--------------------------------------------------------------------------
            | user
            |--------------------------------------------------------------------------
            */

            case 'user':

                return redirect()
                    ->route('products.index');


            /*
            |--------------------------------------------------------------------------
            | Admin
            |--------------------------------------------------------------------------
            |
            | Admin login is handled by AdminAuthController.
            |
            */

            case 'admin':

                Auth::guard('user')->logout();

                return redirect()
                    ->route('admin.login')
                    ->withErrors([
                        'email' =>
                            'Please use the Admin Login page.',
                    ]);


            /*
            |--------------------------------------------------------------------------
            | Unknown Role
            |--------------------------------------------------------------------------
            */

            default:

                Auth::guard('user')->logout();

                return redirect()
                    ->route('login')
                    ->withErrors([
                        'email' => 'Invalid account role.',
                    ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Get Redirect URL After Registration
    |--------------------------------------------------------------------------
    */

    private function getRedirectUrlAfterRegistration(User $user): string
    {
        if ($user->role === 'seller') {

            return route('seller.pending');
        }

        return route('products.index');
    }
}