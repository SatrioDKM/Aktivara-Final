<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
     * Menampilkan halaman daftar pengguna (index).
     */
    public function viewPage(): View
    {
        $data = ['roles' => Role::orderBy('role_name')->get()];
        return view('backend.users.index', compact('data'));
    }

    /**
     * Menampilkan halaman formulir tambah pengguna.
     */
    public function create(): View
    {
        $data = ['roles' => Role::orderBy('role_name')->get()];
        return view('backend.users.create', compact('data'));
    }

    /**
     * Menampilkan halaman detail pengguna.
     */
    public function show(string $id): View
    {
        $data = ['user' => User::with('role')->findOrFail($id)];
        return view('backend.users.show', compact('data'));
    }

    /**
     * Menampilkan halaman formulir edit pengguna.
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
     * API: Mengambil daftar pengguna dengan filter dan paginasi.
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::with('role')->where('id', '!=', Auth::id());

        $query->when($request->input('search'), function ($q, $search) {
            $q->where(function ($subq) use ($search) {
                $subq->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
            });
        });

        $query->when($request->input('role'), fn($q, $role) => $q->where('role_id', $role));
        $query->when($request->input('status'), fn($q, $status) => $q->where('status', $status));

        $users = $query->latest()->paginate($request->input('perPage', 10));

        return response()->json($users);
    }

    /**
     * API: Menyimpan data pengguna baru.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
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
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'role_id' => $request->input('role_id'),
            'status' => $request->input('status'),
            'password' => Hash::make($request->input('password')),
        ]);

        return response()->json($user->load('role'), 201);
    }

    /**
     * API: Memperbarui data pengguna yang ada.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,role_id',
            'status' => 'required|in:active,inactive',
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->except('password', 'password_confirmation');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->input('password'));
        }

        $user->update($data);

        return response()->json($user->load('role'));
    }

    /**
     * API: Menghapus data pengguna.
     */
    public function destroy(string $id): JsonResponse
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return response()->json(['message' => 'Anda tidak dapat menghapus akun Anda sendiri.'], 403);
        }

        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        $user->delete();

        return response()->json(['message' => 'Pengguna berhasil dihapus.'], 200);
    }
}
