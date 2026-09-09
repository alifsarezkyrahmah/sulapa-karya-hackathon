<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat Dampak Keberlanjutan — {{ $user->business_name }}</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .certificate-container { border: 4px double #1C1A16 !important; box-shadow: none !important; }
        }
    </style>
</head>
<body class="bg-sand/30 text-ink font-sans p-4 sm:p-10 flex flex-col items-center min-h-screen">
    
    <!-- Tombol Navigasi & Cetak (Hilang saat diprint) -->
    <div class="w-full max-w-4xl mb-4 flex justify-between items-center no-print">
        <a href="javascript:history.back()" class="text-xs font-bold text-forest hover:underline">← Kembali ke Panel Mitra PRO</a>
        <button onclick="window.print()" class="btn btn-sm bg-forest hover:bg-forest-dark text-white border-none rounded-xl px-5 text-xs font-bold shadow-sm">
            🖨 Cetak Dokumen / Simpan PDF
        </button>
    </div>

    <!-- Kontainer Utama Sertifikat -->
    <div class="w-full max-w-4xl bg-white rounded-3xl border border-ink/15 certificate-container p-8 sm:p-14 shadow-xl relative overflow-hidden space-y-8">
        
        <!-- Ornamen Sudut Estetis -->
        <div class="absolute top-0 left-0 w-24 h-24 border-t-4 border-l-4 border-forest/30 rounded-tl-3xl pointer-events-none"></div>
        <div class="absolute top-0 right-0 w-24 h-24 border-t-4 border-r-4 border-forest/30 rounded-tr-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-24 h-24 border-b-4 border-l-4 border-forest/30 rounded-bl-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 right-0 w-24 h-24 border-b-4 border-r-4 border-forest/30 rounded-br-3xl pointer-events-none"></div>

        <!-- Header Dokumen -->
        <div class="text-center space-y-2">
            <span class="inline-block text-[10px] font-mono font-bold tracking-widest text-forest bg-forest/10 px-3 py-1 rounded-full uppercase">
                Sertifikat Akuntabilitas Lingkungan &bull; SulapaKarya PRO
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-ink tracking-tight">Sertifikat Kontribusi Sirkular</h1>
            <p class="text-xs text-ink-soft max-w-md mx-auto">Dokumen resmi pengakuan atas komitmen mitigasi sampah dan penerapan prinsip ekonomi sirkular di Kota Makassar.</p>
        </div>

        <!-- Pernyataan Kepada Mitra -->
        <div class="text-center space-y-3 py-2">
            <span class="text-xs text-ink-soft uppercase font-semibold tracking-wider block">Sertifikat ini diberikan dengan bangga kepada:</span>
            <h2 class="text-2xl sm:text-3xl font-black text-forest tracking-tight">{{ $user->business_name }}</h2>
            <p class="text-xs text-ink-soft max-w-lg mx-auto leading-relaxed">
                Atas dedikasi dan konsistensi unit usaha dalam menyetorkan serta mengelola limbah operasional melalui sistem logistik terpadu SulapaKarya hingga periode <strong>{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</strong>.
            </p>
        </div>

        <!-- Blok Metrik Dampak Utama -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 py-4 border-y border-ink/10">
            <div class="bg-cream/40 p-4 rounded-2xl border border-ink/5 text-center space-y-1">
                <span class="text-[10px] uppercase font-bold text-ink-soft block font-mono">Total Limbah Teralihkan</span>
                <span class="text-2xl font-black font-mono text-forest block">{{ number_format($totalWeight, 1) }} kg</span>
                <span class="text-[10px] text-ink-soft">Sampah terkelola dari TPA</span>
            </div>
            <div class="bg-cream/40 p-4 rounded-2xl border border-ink/5 text-center space-y-1">
                <span class="text-[10px] uppercase font-bold text-ink-soft block font-mono">Reduksi Emisi Karbon</span>
                <span class="text-2xl font-black font-mono text-forest block">{{ number_format($co2Saved, 1) }} kg</span>
                <span class="text-[10px] text-ink-soft">Setara penekanan gas rumah kaca</span>
            </div>
            <div class="bg-cream/40 p-4 rounded-2xl border border-ink/5 text-center space-y-1">
                <span class="text-[10px] uppercase font-bold text-ink-soft block font-mono">Ruang TPA Dihemat</span>
                <span class="text-2xl font-black font-mono text-forest block">{{ number_format($landfillSavedM3, 2) }} m³</span>
                <span class="text-[10px] text-ink-soft">Divergen dari TPA Tamangapa</span>
            </div>
        </div>

        <!-- Tabel Komposisi Material -->
        <div class="space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-ink">Rincian Komposisi Material Terkelola</h3>
            <div class="overflow-x-auto">
                <table class="table table-sm w-full text-xs">
                    <thead>
                        <tr class="bg-cream/50 text-ink-soft border-b border-ink/10 text-[10px] uppercase font-bold">
                            <th class="py-2.5 pl-3">Kategori Material</th>
                            <th class="py-2.5">Frekuensi Penjemputan</th>
                            <th class="py-2.5 pr-3 text-right">Berat Bersih</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink/5 font-mono font-medium">
                        @forelse($wasteStats as $st)
                            <tr>
                                <td class="py-3 pl-3 font-sans font-bold text-ink">{{ $st->category }}</td>
                                <td class="py-3 text-ink-soft font-sans">{{ $st->total_pickup }}x kunjungan armada</td>
                                <td class="py-3 pr-3 text-right font-black text-forest">{{ number_format($st->total_weight, 1) }} kg</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-6 text-center text-ink-soft font-sans">Belum ada rekam jejak material yang selesai diproses.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bagian Footer Tanda Tangan -->
        <div class="pt-6 border-t border-ink/10 flex flex-col sm:flex-row justify-between items-center sm:items-end gap-6 text-xs">
            <div class="space-y-1 text-center sm:text-left">
                <span class="text-[10px] font-mono text-forest font-bold block uppercase tracking-wider">Verifikasi Keaslian Dokumen</span>
                <p class="text-[11px] text-ink-soft max-w-xs leading-relaxed">
                    Sertifikat elektronik ini diterbitkan secara otomatis oleh sistem pencatatan logistik SulapaKarya dan sah tanpa stempel basah.
                </p>
                <span class="text-[10px] font-mono text-ink-soft block">ID Mitra: PRO-MTR-{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</span>
            </div>

            <div class="text-center w-52 shrink-0 space-y-1">
                <span class="text-[10px] text-ink-soft block">Makassar, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span>
                <div class="h-14 flex items-center justify-center">
                    <!-- Tanda Tangan / Cap Otoritas -->
                    <span class="font-display italic font-bold text-forest text-sm">Tim Manajemen SulapaKarya</span>
                </div>
                <div class="border-t border-ink/30 pt-1">
                    <strong class="text-xs font-bold text-ink block">Direktorat Operasional B2B</strong>
                </div>
            </div>
        </div>

    </div>
</body>
</html>