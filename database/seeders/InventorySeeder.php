<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventorySeeder extends Seeder
{
    /**
     * Seed stok awal (kosong, siap diisi dari verifikasi setoran).
     *
     * Referensi harga material (TIDAK disimpan di DB, cuma untuk perhitungan poin):
     *   PP Berwarna (Gelas Plastik): Rp 2.400/kg
     *   PET/HDPE (Botol):            Rp 1.600/kg
     *   HVS/Buku Bekas:              Rp 1.800/kg
     *   Kardus/Koran:                 Rp 2.500/kg
     *   Kain Perca:                   Rp 10.000/kg
     *
     * Harga ini di-hardcode di config/sulapakarya.php, bukan di database.
     */
    public function run(): void
    {
        $now = now();

        $stocks = [
            ['category' => 'plastik', 'sub_category' => 'PP Berwarna'],
            ['category' => 'plastik', 'sub_category' => 'PET/HDPE'],
            ['category' => 'kertas',  'sub_category' => 'HVS/Buku'],
            ['category' => 'kertas',  'sub_category' => 'Kardus/Koran'],
            ['category' => 'kain',    'sub_category' => 'Kain Perca'],
        ];

        foreach ($stocks as $stock) {
            DB::table('inventory_stocks')->insert(array_merge($stock, [
                'quantity_kg' => 0,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]));
        }
    }
}
