<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Method untuk menampilkan halaman Blade.
     */
    public function viewPage()
    {
        // Mengirim data roles ke view untuk filter
        $data = [
            'roles' => Role::orderBy('role_name')->get(),
        ];
        return view('backend.users.index', compact('data'));
    }

    /**
     * Mengambil daftar pengguna dengan filter dan paginasi manual.
     */
    public function index()
    {
        // Ambil parameter dari request
        $perPage = request('perPage', 10);
        $search = request('search', '');
        $roleFilter = request('role', '');
        $statusFilter = request('status', '');

        // Query dasar
        $query = User::with('role')->where('id', '!=', Auth::id());

        // Terapkan filter pencarian
        $query->when($search, function ($q) use ($search) {
            $q->where(function ($subq) use ($search) {
                $subq->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        });

        // Terapkan filter peran (role)
        $query->when($roleFilter, function ($q) use ($roleFilter) {
            $q->where('role_id', $roleFilter);
        });

        // Terapkan filter status
        $query->when($statusFilter, function ($q) use ($statusFilter) {
            $q->where('status', $statusFilter);
        });

        // Ambil data dengan paginasi
        $users = $query->latest()->paginate($perPage);

        return response()->json($users);
    }

    /**
     * Menyimpan data pengguna baru.
     */
    public function store()
    {
        $validator = Validator::make(request()->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role_id' => 'required|exists:roles,role_id',
            'status' => 'required|in:active,inactive',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => request('name'),
            'email' => request('email'),
            'role_id' => request('role_id'),
            'status' => request('status'),
            'password' => Hash::make(request('password')),
        ]);

        return response()->json($user->load('role'), 201);
    }

    /**
     * Menampilkan satu data pengguna spesifik.
     */
    public function show(string $id)
    {
        $user = User::with('role')->findOrFail($id);
        return response()->json($user);
    }

    /**
     * Memperbarui data pengguna yang sudah ada.
     */
    public function update(string $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make(request()->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,role_id',
            'status' => 'required|in:active,inactive',
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = request()->except('password', 'password_confirmation');

        // Jika password diisi, hash dan tambahkan ke data update
        if (request()->filled('password')) {
            $data['password'] = Hash::make(request('password'));
        }

        $user->update($data);

        return response()->json($user->load('role'));
    }

    /**
     * Menghapus data pengguna.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        // Tambahan keamanan: pastikan user tidak bisa menghapus dirinya sendiri
        if ($user->id === Auth::id()) {
            return response()->json(['message' => 'Anda tidak dapat menghapus akun Anda sendiri.'], 403);
        }

        // Hapus foto profil jika ada
        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        $user->delete();

        return response()->json(null, 204);
    }
}
