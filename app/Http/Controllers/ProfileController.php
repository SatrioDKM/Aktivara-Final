<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Support\Facades\Storage; // <-- Tambahkan ini

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
        // Ambil data yang sudah divalidasi
        $validatedData = $request->validated();

        // Handle upload file foto profil
        if ($request->hasFile('profile_picture')) {
            // Hapus foto profil lama jika ada
            if ($request->user()->profile_picture) {
                Storage::disk('public')->delete($request->user()->profile_picture);
            }
            // Simpan foto baru dan dapatkan path-nya
            $path = $request->file('profile_picture')->store('profile-pictures', 'public');
            $validatedData['profile_picture'] = $path;
        }

        // Isi data user dengan data yang sudah divalidasi (termasuk path foto baru jika ada)
        $request->user()->fill($validatedData);

        // Jika email diubah, reset status verifikasi email
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        // Simpan semua perubahan
        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
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

        // Hapus foto profil dari storage saat akun dihapus
        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
