<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WastePrice;
use Illuminate\Support\Str;

class WastePriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'Gelas Plastik (PP Berwarna)', 'price' => 2400],
            ['name' => 'Botol Plastik (PET/HDPE)', 'price' => 1600],
            ['name' => 'Kertas HVS/Buku Bekas', 'price' => 1800],
            ['name' => 'Kardus Bekas', 'price' => 2000],
            ['name' => 'Kertas Koran', 'price' => 1500],
            ['name' => 'Kain Perca / Limbah Tekstil', 'price' => 10000],
            ['name' => 'Plastik Kresek (LDPE)', 'price' => 1000],
            ['name' => 'Kaleng Aluminium (Minuman)', 'price' => 12000],
            ['name' => 'Kaleng Besi / Seng', 'price' => 2500],
            ['name' => 'Botol Kaca', 'price' => 500],
            ['name' => 'Logam Tembaga', 'price' => 60000],
            ['name' => 'Besi Tua / Padat', 'price' => 4000],
            ['name' => 'Elektronik Bekas (E-Waste)', 'price' => 5000],
            ['name' => 'Kertas Dupleks (Karton Makanan)', 'price' => 1000],
        ];

        foreach ($data as $item) {
            $price = $item['price'];
            $point = (int) round($price * 0.40); // Otomatis 40%

            WastePrice::updateOrCreate(
                ['name' => $item['name']],
                [
                    'id'           => (string) Str::uuid(),
                    'price_per_kg' => $price,
                    'point_per_kg' => $point,
                    'unit'         => 'kg',
                ]
            );
        }
    }
}