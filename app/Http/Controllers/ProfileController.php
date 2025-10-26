<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage; // <-- Tambahkan use Storage
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        // PERBAIKAN: Gunakan $data['user'] agar konsisten dengan view yg kamu kirim
        return view('backend.profile.edit', [
            'data' => ['user' => $request->user()]
            // 'user' => $request->user(), // Ganti ini
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated()); // Mengisi nama, email dari validasi

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // --- LOGIKA UPLOAD FOTO PROFIL (JIKA ADA) ---
        if ($request->hasFile('profile_picture')) {
            // (Pastikan validasi profile_picture ada di ProfileUpdateRequest)
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $path = $request->file('profile_picture')->store('profile-pictures', 'public');
            $user->profile_picture = $path;
        }
        // --- AKHIR LOGIKA FOTO PROFIL ---

        $user->save(); // Simpan nama, email, foto profil

        // Pesan sukses untuk update profil umum
        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Memperbarui tanda tangan digital pengguna.
     */
    public function updateSignature(Request $request): RedirectResponse
    {
        $user = $request->user();

        // 1. Handle Penghapusan TTD
        if ($request->boolean('delete_signature') && $user->signature_image) {
            Storage::disk('public')->delete($user->signature_image); // Hapus file lama
            $user->signature_image = null;                         // Set null di DB
        }

        // 2. Handle Upload File Baru
        if ($request->hasFile('signature_image')) {
            // Validasi file TTD dengan error bag 'updateSignature'
            $request->validateWithBag('updateSignature', [
                'signature_image' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:1024'] // Max 1MB
            ]);

            // Hapus file TTD lama (jika ada) sebelum upload baru
            if ($user->signature_image && !$request->boolean('delete_signature')) {
                Storage::disk('public')->delete($user->signature_image);
            }

            // Simpan file baru ke storage/app/public/signatures
            $path = $request->file('signature_image')->storeAs(
                'signatures', // Folder tujuan
                'user_' . $user->id . '.' . $request->file('signature_image')->getClientOriginalExtension(), // Nama file
                'public' // Disk public
            );

            // Simpan path ke database
            $user->signature_image = $path;
        }

        // 3. Simpan perubahan jika ada
        if ($user->isDirty('signature_image')) {
            $user->save();
        }

        // Redirect kembali ke halaman edit profile dengan pesan sukses TTD
        return Redirect::route('profile.edit')->with('status', 'signature-updated');
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

        // Hapus file terkait user (jika ada) sebelum delete user
        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }
        if ($user->signature_image) {
            Storage::disk('public')->delete($user->signature_image);
        }

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
