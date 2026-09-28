<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Display the saved delivery address form.
     */
    public function address(Request $request): View
    {
        return view('profile.address', [
            'user' => $request->user(),
            'isOnboarding' => $request->session()->get('onboarding_address', false),
        ]);
    }

    /**
     * Store the user's default delivery address.
     */
    public function updateAddress(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'shipping_name' => ['required', 'string', 'max:255'],
            'shipping_phone' => ['required', 'string', 'max:50'],
            'shipping_address' => ['required', 'string', 'max:1000'],
            'shipping_postcode' => ['required', 'string', 'max:20'],
        ]);

        $request->user()->update($data);

        $intendedCheckout = $request->session()->pull('intended_checkout');
        $isOnboarding = $request->session()->pull('onboarding_address', false);

        return $intendedCheckout
            ? Redirect::to($intendedCheckout)->with('success', 'บันทึกที่อยู่จัดส่งแล้ว')
            : ($isOnboarding
                ? Redirect::route('home')->with('success', 'บันทึกที่อยู่จัดส่งแล้ว เริ่มเลือกสินค้าได้เลย')
                : Redirect::route('address.edit')->with('status', 'address-updated'));
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
