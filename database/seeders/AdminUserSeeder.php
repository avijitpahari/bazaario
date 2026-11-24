<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\AdminFeed;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed primary Super Admin account and initialize operational feed.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@bazaario.com'],
            [
                'name'               => 'Bazaario Super Admin',
                'phone'              => '9800000000',
                'password'           => Hash::make('Password123!'),
                'role'               => 'admin',
                'preferred_language' => 'en',
                'email_verified_at'  => now(),
                'status'             => 'active',
            ]
        );

        AdminFeed::updateOrCreate(
            ['title' => 'Admin Control Center Initialized'],
            [
                'type'        => 'system',
                'description' => 'Platform operational telemetry connected. Dynamic feed table active.',
                'icon'        => 'shield_lock',
                'badge_text'  => 'System Live',
                'badge_color' => 'emerald',
                'action_url'  => route('admin.dashboard'),
            ]
        );

        $this->command->info("✅ Super Admin user seeded: {$admin->email} (Password: Password123!)");
    }
}
