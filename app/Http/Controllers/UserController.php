<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Method untuk menampilkan halaman Blade.
     */
    public function viewPage()
    {
        $roles = Role::all();
        return view('admin.users.index', compact('roles'));
    }

    /**
     * Menampilkan daftar semua pengguna.
     */
    public function index()
    {
        // Ambil semua user kecuali user yang sedang login
        $users = User::with('role')->where('id', '!=', Auth::id())->latest()->get();
        return response()->json($users);
    }

    /**
     * Menyimpan data pengguna baru.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role_id' => 'required|exists:roles,role_id',
            'status' => 'required|in:active,inactive',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'status' => $request->status,
            'password' => Hash::make($request->password),
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
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,role_id',
            'status' => 'required|in:active,inactive',
            // Password bersifat opsional saat update
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->except('password');

        // Jika password diisi, hash dan tambahkan ke data update
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
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

        $user->delete();

        return response()->json(null, 204);
    }
}
