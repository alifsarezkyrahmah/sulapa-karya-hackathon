<?php

/*
|--------------------------------------------------------------------------
| TABEL 3: INVENTORY_STOCKS (Stok Gudang Bahan Baku)
|--------------------------------------------------------------------------
|
| Fungsi: Tracking jumlah bahan baku yang tersedia di gudang SulapaKarya.
|         Tabel ini TIDAK punya FK langsung ke tabel lain.
|         Data-nya diupdate via LOGIC di controller:
|
|         [+] BERTAMBAH saat deposit diverifikasi (status: completed)
|             → quantity_kg += actual_weight dari deposit
|
|         [-] BERKURANG saat material_request di-approve admin
|             → quantity_kg -= actual_quantity_kg dari request
|
| Contoh isi tabel:
|   id=1 | plastik | PET/HDPE    | 12.50 kg
|   id=2 | plastik | PP Berwarna |  8.30 kg
|   id=3 | kertas  | HVS/Buku   |  6.00 kg
|   id=4 | kertas  | Kardus/Koran| 20.50 kg
|   id=5 | kain    | Kain Perca  | 15.20 kg
|
| Unique constraint: satu baris per kombinasi category + sub_category
| Artinya: tidak boleh ada 2 baris "plastik + PET/HDPE" — cuma 1, di-update terus
|
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_stocks', function (Blueprint $table) {
            $table->id();
            $table->enum('category', ['plastik', 'kertas', 'kain']);
            $table->string('sub_category')->nullable();                 // PET/HDPE, HVS/Buku, Kardus/Koran, Kain Perca, dll
            $table->decimal('quantity_kg', 10, 2)->default(0);          // Stok tersedia saat ini (kg)
            $table->timestamps();

            $table->unique(['category', 'sub_category']);               // Satu baris per kombinasi — tidak boleh duplikat
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_stocks');
    }
};
