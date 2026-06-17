<?php

/*
|--------------------------------------------------------------------------
| CONFIG: SulapaKarya Business Rules
|--------------------------------------------------------------------------
|
| File ini berisi SEMUA angka-angka bisnis yang dipakai di controller.
| Taruh di: project-laravel/config/sulapakarya.php
| Akses di controller: config('sulapakarya.material_prices.plastik.PET/HDPE')
|
| Kenapa di config, bukan di database?
|   - Harga material jarang berubah
|   - Lebih cepat diakses (tidak perlu query DB)
|   - Gampang diubah: edit file ini, selesai
|   - Kalau nanti mau pindah ke DB (misal admin bisa edit harga dari dashboard),
|     tinggal buat tabel baru dan ganti config() jadi DB query
|
*/

return [

    // Harga beli material per kg (dari user/penyetor)
    // Dipakai untuk menghitung reward poin/cash
    'material_prices' => [
        'plastik' => [
            'PP Berwarna'  => 2400,   // Rp/kg — Gelas plastik
            'PET/HDPE'     => 1600,   // Rp/kg — Botol minuman/sampo
        ],
        'kertas' => [
            'HVS/Buku'     => 1800,   // Rp/kg
            'Kardus/Koran' => 2500,   // Rp/kg
        ],
        'kain' => [
            'Kain Perca'   => 10000,  // Rp/kg — Limbah tekstil
        ],
    ],

    // Rumus konversi poin:
    // poin = berat_kg × harga_per_kg × points_multiplier
    // Contoh: 0.6 kg PET → 0.6 × 1600 × 10 = 9.600 poin
    'points_multiplier' => 10,         // 1 Rupiah = 10 Poin

    // Minimum berat untuk reward uang tunai
    'cash_minimum_kg' => 1.0,          // Di bawah 1 kg = hanya bisa pilih poin

    // Syarat penarikan tunai dari poin
    'cash_withdrawal_min_points' => 1000000,  // 1.000.000 poin = Rp 100.000

    // Membership tiers
    'membership_tiers' => [
        'pemula'  => ['price' => 0,      'max_products' => 5],
        'aktif'   => ['price' => 35000,  'max_products' => null],  // null = unlimited
        'premium' => ['price' => 100000, 'max_products' => null],
    ],
];
