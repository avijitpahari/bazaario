<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function index()
    {
        $user      = Auth::user();
        $addresses = $user->addresses()->latest()->get();

        return view('user.account.addresses', compact('user', 'addresses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type'             => 'required|in:home,work,other',
            'full_name'        => 'required|string|max:150',
            'phone'            => 'required|string|max:20',
            'address_line_1'   => 'required|string|max:255',
            'address_line_2'   => 'nullable|string|max:255',
            'landmark'         => 'nullable|string|max:255',
            'city'             => 'required|string|max:100',
            'state'            => 'required|string|max:100',
            'postal_code'      => 'required|string|max:20',
            'country'          => 'required|string|max:100',
            'is_default'       => 'boolean',
        ]);

        $user = Auth::user();
        $data['user_id'] = $user->id;

        // If setting as default, unset other defaults first
        if (!empty($data['is_default'])) {
            $user->addresses()->update(['is_default' => false]);
        }

        // If this is the first address, make it default
        if ($user->addresses()->count() === 0) {
            $data['is_default'] = true;
        }

        Address::create($data);

        return redirect()->route('user.addresses.index')
            ->with('success', 'Address added successfully.');
    }

    public function update(Request $request, Address $address)
    {
        abort_unless($address->user_id === Auth::id(), 403);

        $data = $request->validate([
            'type'             => 'required|in:home,work,other',
            'full_name'        => 'required|string|max:150',
            'phone'            => 'required|string|max:20',
            'address_line_1'   => 'required|string|max:255',
            'address_line_2'   => 'nullable|string|max:255',
            'landmark'         => 'nullable|string|max:255',
            'city'             => 'required|string|max:100',
            'state'            => 'required|string|max:100',
            'postal_code'      => 'required|string|max:20',
            'country'          => 'required|string|max:100',
            'is_default'       => 'boolean',
        ]);

        if (!empty($data['is_default'])) {
            Auth::user()->addresses()->update(['is_default' => false]);
        }

        $address->update($data);

        return redirect()->route('user.addresses.index')
            ->with('success', 'Address updated successfully.');
    }

    public function destroy(Address $address)
    {
        abort_unless($address->user_id === Auth::id(), 403);
        $address->delete();

        return redirect()->route('user.addresses.index')
            ->with('success', 'Address removed.');
    }

    public function setDefault(Address $address)
    {
        abort_unless($address->user_id === Auth::id(), 403);

        Auth::user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return redirect()->route('user.addresses.index')
            ->with('success', 'Default address updated.');
    }
}
