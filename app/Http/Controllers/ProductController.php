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

        // 1. Filter Pencarian Nama / Deskripsi / Bahan
        if ($request->filled('q')) {
            $keyword = trim($request->q);
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%")
                  ->orWhere('material_source', 'like', "%{$keyword}%");
            });
        }

        // 2. Filter Kategori
        if ($request->filled('kategori') && $request->kategori !== 'semua') {
            $query->where('product_category', $request->kategori);
        }

        // 3. Sorting / Pengurutan
        switch ($request->get('sort', 'terbaru')) {
            case 'termurah':
                $query->orderBy('price', 'asc');
                break;
            case 'termahal':
                $query->orderBy('price', 'desc');
                break;
            case 'terpopuler':
                $query->orderBy('stock', 'asc');
                break;
            default:
                $query->latest();
                break;
        }

        $allProducts = $query->paginate(12)->withQueryString();

        $categories = Product::select('product_category')
            ->whereNotNull('product_category')
            ->distinct()
            ->pluck('product_category');

        // Sesuaikan nama view admin jika berbeda, misal 'admin.products.index'
        return view('katalog', compact('allProducts', 'categories'));
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
     * Tampilan katalog untuk Warga (Mendukung Pencarian, Kategori, Sorting, & Pagination)
     */
    public function catalog(Request $request)
    {
        $query = Product::where('status', 'available');

        // 1. Filter Pencarian Nama / Deskripsi / Bahan
        if ($request->filled('q')) {
            $keyword = trim($request->q);
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%")
                  ->orWhere('material_source', 'like', "%{$keyword}%");
            });
        }

        // 2. Filter Kategori
        if ($request->filled('kategori') && $request->kategori !== 'semua') {
            $query->where('product_category', $request->kategori);
        }

        // 3. Sorting / Pengurutan
        switch ($request->get('sort', 'terbaru')) {
            case 'termurah':
                $query->orderBy('price', 'asc');
                break;
            case 'termahal':
                $query->orderBy('price', 'desc');
                break;
            case 'terpopuler':
                $query->orderBy('stock', 'asc');
                break;
            default:
                $query->latest();
                break;
        }

        $allProducts = $query->paginate(12)->withQueryString();
        
        $featuredProducts = Product::where('status', 'available')->where('is_featured', 1)->get();
        
        $categories = Product::select('product_category')
            ->whereNotNull('product_category')
            ->distinct()
            ->pluck('product_category');

        return view('katalog', compact('featuredProducts', 'allProducts', 'categories'));
    }
}