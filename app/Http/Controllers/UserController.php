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
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Method untuk menampilkan halaman utama (index).
     */
    public function viewPage(): View
    {
        $data = ['roles' => Role::orderBy('role_name')->get()];
        return view('backend.users.index', compact('data'));
    }

    /**
     * Method untuk menampilkan halaman formulir tambah pengguna.
     */
    public function create(): View
    {
        $data = ['roles' => Role::orderBy('role_name')->get()];
        return view('backend.users.create', compact('data'));
    }

    /**
     * Method untuk menampilkan halaman detail pengguna (read-only).
     */
    public function show(string $id): View
    {
        $data = ['user' => User::with('role')->findOrFail($id)];
        return view('backend.users.show', compact('data'));
    }

    /**
     * Method untuk menampilkan halaman formulir edit pengguna.
     */
    public function edit(string $id): View
    {
        $data = [
            'user' => User::with('role')->findOrFail($id),
            'roles' => Role::orderBy('role_name')->get(),
        ];
        return view('backend.users.edit', compact('data'));
    }

    // ===================================================================
    // API METHODS
    // ===================================================================

    /**
     * Mengambil daftar pengguna dengan filter dan paginasi manual untuk API.
     */
    public function index()
    {
        $query = User::with('role')->where('id', '!=', Auth::id());

        // Terapkan filter pencarian
        if (request('search', '')) {
            $search = request('search', '');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Terapkan filter peran (role)
        if (request('role', '')) {
            $query->where('role_id', request('role'));
        }

        // Terapkan filter status
        if (request('status', '')) {
            $query->where('status', request('status'));
        }

        // ================== BAGIAN YANG DIPERBARUI ==================
        // Gunakan paginator standar Laravel yang menghasilkan 'links', 'data', dll.
        $users = $query->latest()->paginate(request('perPage', 10));

        return response()->json($users);
        // ==========================================================
    }

    /**
     * Menyimpan data pengguna baru dari API.
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
     * Memperbarui data pengguna yang sudah ada dari API.
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

        if (request()->filled('password')) {
            $data['password'] = Hash::make(request('password'));
        }

        $user->update($data);

        return response()->json($user->load('role'));
    }

    /**
     * Menghapus data pengguna dari API.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return response()->json(['message' => 'Anda tidak dapat menghapus akun Anda sendiri.'], 403);
        }

        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        $user->delete();

        return response()->json(null, 204);
    }
}
