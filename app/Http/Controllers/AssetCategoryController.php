<?php

namespace App\Http\Controllers;

use App\Models\AssetCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AssetCategoryController extends Controller
{
    // ==========================
    // BAGIAN WEB
    // ==========================
    public function index()
    {
        $categories = AssetCategory::latest()->paginate(10);
        return view('backend.master.asset_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('backend.master.asset_categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:asset_categories',
        ]);

        AssetCategory::create([
            'name' => $request->name,
        ]);

        return redirect()->route('master.asset_categories.index')
            ->with('success', 'Kategori Aset berhasil ditambahkan.');
    }

    public function edit(AssetCategory $assetCategory)
    {
        return view('backend.master.asset_categories.edit', compact('assetCategory'));
    }

    public function update(Request $request, AssetCategory $assetCategory)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('asset_categories')->ignore($assetCategory->id),
            ],
        ]);

        $assetCategory->update([
            'name' => $request->name,
        ]);

        return redirect()->route('master.asset_categories.index')
            ->with('success', 'Kategori Aset berhasil diperbarui.');
    }

    public function destroy(AssetCategory $assetCategory)
    {
        if ($assetCategory->assets()->count() > 0) {
            return redirect()->route('master.asset_categories.index')
                ->with('error', 'Kategori Aset tidak dapat dihapus karena masih digunakan oleh aset lain.');
        }

        $assetCategory->delete();

        return redirect()->route('master.asset_categories.index')
            ->with('success', 'Kategori Aset berhasil dihapus.');
    }

    // ==========================
    // BAGIAN API
    // ==========================
    public function apiIndex()
    {
        return response()->json([
            'data' => AssetCategory::latest()->get(),
        ]);
    }

    public function apiStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:asset_categories',
        ]);

        $category = AssetCategory::create(['name' => $request->name]);

        return response()->json([
            'message' => 'Kategori aset berhasil ditambahkan.',
            'data' => $category,
        ], 201);
    }

    public function apiUpdate(Request $request, AssetCategory $assetCategory)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('asset_categories')->ignore($assetCategory->id),
            ],
        ]);

        $assetCategory->update(['name' => $request->name]);

        return response()->json([
            'message' => 'Kategori aset berhasil diperbarui.',
            'data' => $assetCategory,
        ]);
    }

    public function apiDestroy(AssetCategory $assetCategory)
    {
        if ($assetCategory->assets()->count() > 0) {
            return response()->json([
                'message' => 'Kategori aset tidak dapat dihapus karena masih digunakan oleh aset lain.',
            ], 400);
        }

        $assetCategory->delete();

        return response()->json([
            'message' => 'Kategori aset berhasil dihapus.',
        ]);
    }
}
