<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * READ: Tampilan Utama Tabel Produk Admin
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // 1. Filter Pencarian
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('material_source', 'ilike', "%{$search}%");
            });
        }

        // 2. Filter Kategori
        if ($request->filled('category')) {
            $query->where('product_category', trim($request->category));
        }

        // 3. Filter Status
        if ($request->filled('status')) {
            $status = strtolower(trim($request->status));
            if ($status === 'available') {
                $query->whereRaw("LOWER(status) = 'available'");
            } elseif ($status === 'sold_out') {
                $query->where(function ($q) {
                    $q->whereRaw("LOWER(status) = 'sold_out'")
                      ->orWhere('stock', '<=', 0);
                });
            }
        }

        $products = $query->orderBy('created_at', 'desc')->get();

        return view('dashboard.admin.kelola-produk', compact('products'));
    }

    /**
     * CREATE: Menyimpan Produk Baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'product_category' => 'required|string|max:255',
            'material_source'  => 'nullable|string|max:255',
            'price'            => 'required|integer|min:0',
            'stock'            => 'required|integer|min:0',
            'description'      => 'nullable|string',
            'photo'            => 'required|image|mimes:jpeg,png,jpg,webp|max:3048',
            'status'           => 'required|in:available,sold_out',
        ]);

        try {
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('products', 'public');
            }

            // Hapus 'id' => Str::uuid(), biarkan PostgreSQL mengisi auto-increment
            Product::create([
                'artisan_id'       => null, 
                'name'             => $request->name,
                'description'      => $request->description,
                'price'            => $request->price, 
                'material_source'  => $request->material_source,
                'product_category' => $request->product_category,
                'photo_path'       => $photoPath,
                'stock'            => $request->stock,
                'is_featured'      => $request->has('is_featured') ? 1 : 0,
                'status'           => $request->status,
            ]);

            return back()->with('success', 'Berhasil menambahkan produk baru!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menyimpan: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * UPDATE: Memperbarui Data Produk & Foto Lama
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name'             => 'required|string|max:255',
            'product_category' => 'required|string|max:255',
            'material_source'  => 'nullable|string|max:255',
            'price'            => 'required|integer|min:0',
            'stock'            => 'required|integer|min:0',
            'description'      => 'nullable|string',
            'photo'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3048',
            'status'           => 'required|in:available,sold_out',
        ]);

        try {
            $photoPath = $product->photo_path;
            
            if ($request->hasFile('photo')) {
                if ($product->photo_path && Storage::disk('public')->exists($product->photo_path)) {
                    Storage::disk('public')->delete($product->photo_path);
                }
                $photoPath = $request->file('photo')->store('products', 'public');
            }

            $product->update([
                'name'             => $request->name,
                'description'      => $request->description,
                'price'            => $request->price,
                'material_source'  => $request->material_source,
                'product_category' => $request->product_category,
                'photo_path'       => $photoPath,
                'stock'            => $request->stock,
                'is_featured'      => $request->has('is_featured') ? 1 : 0,
                'status'           => $request->status,
            ]);

            return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memperbarui: ' . $e->getMessage()]);
        }
    }

    /**
     * DELETE: Menghapus Produk & File Gambar Terkait
     */
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            
            if ($product->photo_path && Storage::disk('public')->exists($product->photo_path)) {
                Storage::disk('public')->delete($product->photo_path);
            }

            $product->delete();
            return back()->with('success', 'Produk telah berhasil dihapus dari sistem.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus produk: ' . $e->getMessage()]);
        }
    }

    /**
     * Tampilan katalog untuk Warga
     */
    public function catalog()
    {
        $featuredProducts = Product::where('status', 'available')->where('is_featured', 1)->get();
        $allProducts = Product::where('status', 'available')->orderBy('created_at', 'desc')->get();
        return view('katalog', compact('featuredProducts', 'allProducts'));
    }
}