<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\WastePrice;


class HomeController extends Controller
{
    public function index()
    {
        $products = Product::where('status', 'available')->orderBy('created_at', 'desc')->get();
        
        // Ambil data harga dan poin sampah dari database Supabase
        $wastePrices = WastePrice::orderBy('name', 'asc')->get();
        
        // Ambil tanggal terakhir harga diperbarui
        $latestWastePrice = WastePrice::orderBy('updated_at', 'desc')->first();
        $lastUpdatedDate = $latestWastePrice && $latestWastePrice->updated_at 
            ? $latestWastePrice->updated_at->translatedFormat('d F Y') 
            : date('d F Y');

        return view('home', compact('products', 'wastePrices', 'lastUpdatedDate'));
    }
}