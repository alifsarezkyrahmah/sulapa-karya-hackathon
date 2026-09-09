<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'link',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // =========================================================================
    // FUNGSI HELPER TERPUSAT UNTUK MEMICU NOTIFIKASI
    // =========================================================================

    /**
     * Kirim Notifikasi Umum
     */
    public static function send($userId, $title, $message, $type = 'info', $link = null)
    {
        return self::create([
            'user_id' => $userId,
            'title'   => $title,
            'message' => $message,
            'type'    => $type, // 'deposit', 'payout', 'order', 'warning', 'success'
            'link'    => $link,
            'is_read' => false,
        ]);
    }

    /**
     * 1. Notifikasi Perubahan Status Setoran Sampah (Untuk Warga)
     */
    public static function notifyDeposit($deposit)
    {
        $code = $deposit->deposit_code;
        $title = "Setoran Sampah #{$code}";
        $link = "/riwayat-setoran";
        $type = 'deposit';

        switch ($deposit->status) {
            case 'menunggu_penjemput':
                $message = "Setoran Anda telah diverifikasi Admin dan menunggu penjemputan armada kurir.";
                break;
            case 'penjemput_menuju_lokasi':
                $message = "Kurir armada sedang menuju alamat Anda. Siapkan sampah pilah dan QR Code Anda.";
                $type = 'warning';
                break;
            case 'penjemput_tiba':
                $message = "Kurir telah tiba di alamat Anda. Tunjukkan QR Code pada dashboard ke kurir.";
                $type = 'warning';
                break;
            case 'sedang_diproses':
                $message = "Sampah Anda telah divalidasi & ditimbang ({$deposit->actual_weight} kg). Menunggu verifikasi audit poin oleh Admin.";
                break;
            case 'ditolak':
                $message = "Setoran sampah Anda ditolak. Alasan: " . ($deposit->admin_notes ?? $deposit->qc_notes ?? 'Belum memenuhi SOP 3C');
                $type = 'warning';
                break;
            default:
                $message = "Status setoran sampah Anda telah diperbarui menjadi: " . str_replace('_', ' ', $deposit->status);
                break;
        }

        return self::send($deposit->user_id, $title, $message, $type, $link);
    }

    /**
     * 2. Notifikasi Pengajuan Setoran Baru (UNTUK SEMUA ADMIN)
     */
    public static function notifyAdminNewDeposit($deposit)
    {
        $admins = User::where('role', 'admin')->get();
        $code = $deposit->deposit_code;
        $title = "Ajuan Setoran Baru #{$code}";
        $userName = $deposit->user->name ?? 'Warga';
        $category = ucfirst($deposit->category ?? 'Sampah');
        $weight = $deposit->estimated_weight ?? 0;
        
        $message = "Pengguna {$userName} mengajukan setoran {$category} (est. {$weight} kg). Butuh verifikasi admin.";
        $link = "/verifikasi-setoran";

        foreach ($admins as $admin) {
            self::send($admin->id, $title, $message, 'deposit', $link);
        }
    }

    /**
     * 3. Notifikasi Penugasan Penjemputan (UNTUK KURIR/PENJEMPUT)
     */
    public static function notifyPenjemputAssignment($deposit)
    {
        if (!$deposit->penjemput_id) {
            return;
        }

        $code = $deposit->deposit_code;
        $title = "Tugas Jemput Baru #{$code}";
        $kelurahan = $deposit->kelurahan ?? $deposit->user->kelurahan ?? 'Makassar';
        $category = ucfirst($deposit->category ?? 'Sampah');
        $message = "Admin telah menugaskan Anda menjemput setoran {$category} di area {$kelurahan}. Buka misi Anda.";
        $link = "/kurir/misi";

        return self::send($deposit->penjemput_id, $title, $message, 'warning', $link);
    }

    /**
     * 4. Notifikasi Kurir Selesai Timbang Sampah (UNTUK SEMUA ADMIN -> VERIFIKASI POIN)
     */
    public static function notifyAdminPendingPoints($deposit)
    {
        $admins = User::where('role', 'admin')->get();
        $code = $deposit->deposit_code;
        $title = "Antrean Audit Poin #{$code}";
        $kurirName = $deposit->penjemput->name ?? 'Kurir Lapangan';
        $weight = $deposit->actual_weight ?? $deposit->estimated_weight ?? 0;
        $points = number_format($deposit->points_earned ?? 0);
        
        $message = "Kurir {$kurirName} telah memvalidasi setoran #{$code} ({$weight} kg, {$points} poin). Menunggu verifikasi audit Anda.";
        $link = "/verifikasi-poin";

        foreach ($admins as $admin) {
            self::send($admin->id, $title, $message, 'warning', $link);
        }
    }

    /**
     * 5. Notifikasi Keputusan Verifikasi Poin (UNTUK WARGA: Setujui, Tolak, atau Auto-Release >24 Jam)
     */
    public static function notifyUserPointsDecision($deposit, $status, $isAutoReleased = false)
    {
        $code = $deposit->deposit_code;
        $link = "/dashboard";
        $points = number_format($deposit->points_earned ?? 0);

        if ($status === 'setujui') {
            if ($isAutoReleased) {
                $title = "Poin Otomatis Masuk (Auto-Release)";
                $message = "Poin reward sebesar +{$points} dari setoran #{$code} telah dicairkan otomatis ke saldo akun Anda karena telah melampaui batas waktu 24 jam.";
            } else {
                $title = "Poin Setoran Berhasil Cair";
                $message = "Poin reward sebesar +{$points} dari setoran #{$code} telah diverifikasi oleh Admin dan resmi masuk ke saldo Anda.";
            }
            return self::send($deposit->user_id, $title, $message, 'payout', $link);
        } else {
            $title = "Verifikasi Poin Ditolak";
            $notes = $deposit->qc_notes ? " Catatan: {$deposit->qc_notes}" : "";
            $message = "Audit poin untuk setoran #{$code} ditolak oleh Admin. Saldo poin Anda tidak bertambah.{$notes}";
            return self::send($deposit->user_id, $title, $message, 'warning', $link);
        }
    }

    /**
     * 6. Notifikasi Pencairan Uang Tunai / Payout Umum
     */
    public static function notifyPayout($userId, $amount, $type = 'poin', $status = 'berhasil', $code = null)
    {
        $link = "/dashboard";
        if ($status === 'berhasil') {
            $title = $type === 'poin' ? "Poin Reward Dicairkan" : "Insentif Berhasil Cair";
            $message = $type === 'poin'
                ? "Poin sebesar +" . number_format($amount) . " dari setoran #{$code} telah masuk ke saldo Anda."
                : "Dana insentif tunai sebesar Rp " . number_format($amount, 0, ',', '.') . " telah berhasil dicairkan.";
            return self::send($userId, $title, $message, 'payout', $link);
        } else {
            $title = "Pencairan Ditolak";
            $message = "Pencairan poin untuk transaksi setoran #{$code} ditolak oleh audit admin.";
            return self::send($userId, $title, $message, 'warning', $link);
        }
    }

    /**
     * 7. Notifikasi Pembelian Produk Kriya
     */
    public static function notifyOrder($transaction)
    {
        $code = $transaction->order_id;
        $title = "Pesanan Kriya #{$code}";
        $link = "/riwayat-pembelian";
        $status = strtolower($transaction->status ?? '');

        if (str_contains($status, 'pending')) {
            $message = "Pesanan telah dibuat. Silakan selesaikan pembayaran untuk mengamankan produk kriya Anda.";
            $type = 'order';
        } elseif (str_contains($status, 'siap') || str_contains($status, 'paid') || str_contains($status, 'success')) {
            $message = "Pembayaran dikonfirmasi. Produk kriya pesanan Anda siap diambil / diserahterimakan.";
            $type = 'success';
        } elseif (str_contains($status, 'picked') || str_contains($status, 'selesai')) {
            $message = "Produk kriya telah berhasil diterima. Terima kasih telah mendukung produk daur ulang Makassar.";
            $type = 'success';
        } elseif (str_contains($status, 'batal') || str_contains($status, 'cancel')) {
            $message = "Pesanan kriya dibatalkan. Poin yang terpakai telah dikembalikan ke saldo Anda.";
            $type = 'warning';
        } else {
            $message = "Status pesanan produk kriya Anda diperbarui: " . str_replace('_', ' ', $transaction->status);
            $type = 'order';
        }

        return self::send($transaction->user_id, $title, $message, $type, $link);
    }


    /**
     * Notifikasi Pendaftaran Bisnis Baru ke Admin
     */
    public static function notifyAdminNewBusinessPartner($user)
    {
        $admins = User::where('role', 'admin')->get();
        $title = "Ajuan Mitra Bisnis Baru";
        $message = "Unit usaha '{$user->business_name}' ({$user->business_type}) mendaftar sebagai mitra bisnis. Harap lakukan verifikasi.";
        $link = "/kelola-pengguna";

        foreach ($admins as $admin) {
            self::send($admin->id, $title, $message, 'order', $link);
        }
    }

    /**
     * Notifikasi Keputusan Kemitraan Bisnis ke Pemilik Usaha
     */
    public static function notifyBusinessVerification($user, $status)
    {
        $link = "/mitra-bisnis";
        if ($status === 'approved') {
            $title = "Kemitraan Bisnis Disetujui 🎉";
            $message = "Selamat! Usaha Anda ({$user->business_name}) resmi terdaftar sebagai Mitra Bisnis SulapaKarya. Anda kini dapat mengatur Jadwal Rutin.";
            return self::send($user->id, $title, $message, 'success', $link);
        } else {
            $title = "Ajuan Kemitraan Bisnis Ditolak";
            $message = "Ajuan kemitraan untuk '{$user->business_name}' belum disetujui. Catatan: " . ($user->business_admin_notes ?? 'Data belum memenuhi verifikasi.');
            return self::send($user->id, $title, $message, 'warning', $link);
        }
    }
}