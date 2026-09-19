<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $profileImageUrl = $user->profile_image
            ? Storage::url($user->profile_image)
            : null;

        $hour = (int) now()->format('G');

        if ($hour >= 5 && $hour <= 11) {
            $greeting = 'Good morning';
        } elseif ($hour >= 12 && $hour <= 16) {
            $greeting = 'Good afternoon';
        } elseif ($hour >= 17 && $hour <= 20) {
            $greeting = 'Good evening';
        } else {
            $greeting = 'Good night';
        }

        return view('user.products.index', compact('user', 'profileImageUrl', 'greeting'));
    }
}