<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Deposit;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // ========================================================================
    // BLOK 1: KELOLA PENGGUNA (CRUD USERS)
    // ========================================================================

    /**
     * READ: Menampilkan daftar semua pengguna aplikasi (Kecuali Admin yang sedang login)
     */
    public function manageUsers()
    {
        $users = User::where('id', '!=', session('user_id'))
                     ->orderBy('created_at', 'desc')
                     ->get();

        return view('dashboard.admin.users', compact('users'));
    }

    /**
     * CREATE: Mendaftarkan pengguna baru dengan standar keamanan
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'phone'    => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'required|in:user,penjemput,pengrajin,admin'
        ], [
            'name.required'     => 'Nama lengkap wajib diisi.',
            'email.required'    => 'Alamat email wajib diisi.',
            'email.email'       => 'Format alamat email tidak valid.',
            'email.unique'      => 'Alamat email ini sudah terdaftar di sistem SulapaKarya.',
            'phone.required'    => 'Nomor telepon/HP wajib diisi untuk menghindari eror database.',
            'password.required' => 'Kata sandi pendaftaran wajib diisi.',
            'password.min'      => 'Kata sandi pendaftaran minimal harus 6 karakter.',
            'password.confirmed'=> 'Konfirmasi kata sandi tidak cocok. Pastikan ulang input Anda sama.'
        ]);

        try {
            User::create([
                'supabase_id'         => (string) Str::uuid(), 
                'name'                => $request->name,
                'email'               => $request->email,
                'phone'               => $request->phone, 
                'password'            => Hash::make($request->password), 
                'role'                => $request->role,
                'qr_code'             => (string) Str::uuid(), 
                'points_balance'      => 0, 
                'cash_received_total' => 0  
            ]);

            return back()->with('success', 'Registrasi berhasil! Akun ' . $request->name . ' dengan peran ' . strtoupper($request->role) . ' telah aktif.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal mendaftarkan akun: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * UPDATE: Memperbarui nama, email, nomor HP, dan role pengguna
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'phone' => 'required|string|max:20',
            'role'  => 'required|in:user,penjemput,pengrajin,admin'
        ], [
            'name.required'  => 'Nama lengkap tidak boleh dikosongkan.',
            'email.required' => 'Alamat email tidak boleh dikosongkan.',
            'email.unique'   => 'Alamat email sudah digunakan oleh pengguna lain.',
            'phone.required' => 'Nomor telepon tidak boleh dikosongkan.'
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role'  => $request->role
        ]);

        return back()->with('success', 'Data profil ' . $user->name . ' berhasil diperbarui!');
    }

    /**
     * DELETE: Menghapus akun pengguna secara permanen
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $namaLama = $user->name;
        $user->delete();

        return back()->with('success', 'Akun ' . $namaLama . ' telah dihapus secara permanen dari ekosistem.');
    }


    // ========================================================================
    // BLOK 2: KELOLA SETORAN SAMPAH (DEPOSITS & PENUGASAN)
    // ========================================================================

    /**
     * TAMPILAN ADMIN: Halaman Verifikasi Setoran Sampah
     */
    public function manageDeposits()
    {
        // Tarik semua data setoran sampah
        $deposits = Deposit::orderBy('created_at', 'desc')->get();
        
        // Tarik daftar pengguna yang berstatus 'penjemput' untuk dropdown penugasan
        $penjemputs = User::where('role', 'penjemput')->get();

        return view('dashboard.admin.verifikasi-setoran', compact('deposits', 'penjemputs'));
    }

    /**
     * FUNGSI ADMIN: Memproses (Setujui & Tugaskan / Tolak) Setoran Warga
     */
    public function approveDeposit(Request $request, $id)
    {
        $request->validate([
            'keputusan'    => 'required|in:terima,tolak',
            'penjemput_id' => 'required_if:keputusan,terima', 
            'admin_notes'  => 'nullable|string'
        ], [
            'penjemput_id.required_if' => 'Anda wajib menugaskan satu kurir/penjemput jika setoran disetujui.'
        ]);

        try {
            $deposit = Deposit::findOrFail($id);

            // PERBAIKAN: Deteksi status 'pending' atau 'menunggu_admin'
            if (!in_array($deposit->status, ['pending', 'menunggu_admin'])) {
                return back()->withErrors(['error' => 'Setoran ini sudah diverifikasi sebelumnya dan tidak dapat diubah lagi.']);
            }

            if ($request->keputusan === 'terima') {
                $deposit->update([
                    'status'       => 'menunggu_penjemput', 
                    'penjemput_id' => $request->penjemput_id, 
                    'verified_by'  => session('user_id'),
                    'verified_at'  => Carbon::now(),
                    'admin_notes'  => $request->admin_notes,
                ]);
                $pesanSukses = 'Berhasil! Setoran disetujui dan tugas telah dilempar ke Penjemput.';
            } else {
                $deposit->update([
                    'status'      => 'ditolak',
                    'verified_by' => session('user_id'),
                    'verified_at' => Carbon::now(),
                    'admin_notes' => $request->admin_notes,
                ]);
                $pesanSukses = 'Setoran warga telah resmi ditolak.';
            }

            return back()->with('success', $pesanSukses);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }
}