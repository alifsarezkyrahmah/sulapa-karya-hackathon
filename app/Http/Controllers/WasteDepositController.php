<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WasteDeposit;
use App\Models\User;
use Illuminate\Support\Str;

class WasteDepositController extends Controller
{
    public function create()
    {
        // 1. Ambil data User untuk auto-fill Alamat
        $user = User::find(session('user_id'));

        // 2. Kategori Sampah yang sesuai dengan ENUM database kamu
        $categories = [
            (object)['id' => 'plastik', 'nama' => '♻️ Plastik (Botol, Gelas, Kemasan)'],
            (object)['id' => 'kertas',  'nama' => '📄 Kertas (Buku, Kardus, Koran)'],
            (object)['id' => 'kain',    'nama' => '👕 Kain (Pakaian Bekas, Perca)']
        ];

        return view('dashboard.setor-sampah', compact('user', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category'         => 'required|in:plastik,kertas,kain',
            'sub_category'     => 'nullable|string|max:255',
            'estimated_weight' => 'required|numeric|min:0.1',
            'reward_type'      => 'required|in:cash,points',
            'pickup_address'   => 'required|string',
            'pickup_date'      => 'nullable|date|after_or_equal:today',
            'pickup_time'      => 'nullable|date_format:H:i',
            'photo'            => 'required|image|mimes:jpeg,png,jpg,webp|max:3048', 
        ], [
            'pickup_date.after_or_equal' => 'Tanggal penjemputan tidak boleh hari yang sudah lewat.',
            'category.required'          => 'Kategori sampah wajib dipilih.',
            'estimated_weight.required'  => 'Perkiraan berat wajib diisi.',
            'photo.required'             => 'Foto bukti sampah wajib diunggah.'
        ]);

        try {
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('waste_photos', 'public');
            }

            $depositCode = 'TRX-' . strtoupper(substr(uniqid(), -6));

            WasteDeposit::create([
                'user_id'          => session('user_id'),
                'deposit_code'     => $depositCode,
                'category'         => $request->category,
                'sub_category'     => $request->sub_category,
                'estimated_weight' => $request->estimated_weight,
                'photo_path'       => $photoPath,
                'reward_type'      => $request->reward_type,
                'status'           => 'pending', 
                'pickup_address'   => $request->pickup_address,
                'pickup_date'      => $request->pickup_date,
                'pickup_time'      => $request->pickup_time,
            ]);

            return back()->with('success', 'Berhasil! Setoran sampah Anda dengan kode ' . $depositCode . ' sedang menunggu penjemputan.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal mengirim setoran: ' . $e->getMessage()])->withInput();
        }
    }

    public function history()
    {
        // Tarik data riwayat khusus untuk user yang sedang login
        $deposits = WasteDeposit::where('user_id', session('user_id'))
                                ->orderBy('created_at', 'desc')
                                ->get();

        return view('dashboard.riwayat-setoran', compact('deposits'));
    }
}