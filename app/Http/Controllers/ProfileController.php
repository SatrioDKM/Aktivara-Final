<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(): View
    {
        $data = [
            'user' => Auth::user(),
        ];
        // Eager load relasi role untuk menghindari N+1 di view
        $data['user']->load('role');

        return view('backend.profile.edit', compact('data'));
    }

    /**
     * Update the user's profile information.
     * (Menggunakan ProfileUpdateRequest adalah best practice untuk validasi)
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // Ambil user yang sedang login
        $user = $request->user();

        // Ambil data yang sudah divalidasi
        $validatedData = $request->validated();

        // Handle upload file foto profil
        if ($request->hasFile('profile_picture')) {
            // Hapus foto profil lama jika ada dan bukan default
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            // Simpan foto baru dan dapatkan path-nya
            $path = $request->file('profile_picture')->store('profile-pictures', 'public');
            $validatedData['profile_picture'] = $path;
        }

        // Isi data user dengan data yang sudah divalidasi
        $user->fill($validatedData);

        // Jika email diubah, reset status verifikasi email
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Simpan semua perubahan
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(): RedirectResponse
    {
        $user = Auth::user();

        // Validasi password secara manual
        if (!Hash::check(request('password'), $user->password)) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ])->errorBag('userDeletion');
        }

        Auth::logout();

        // Hapus foto profil dari storage saat akun dihapus
        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        $user->delete();

        $session = request()->session();
        $session->invalidate();
        $session->regenerateToken();

        return Redirect::to('/');
    }
}
