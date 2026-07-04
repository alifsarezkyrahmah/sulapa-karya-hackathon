@extends('layouts.dashboard', ['title' => 'Dashboard Pengangkut — SulapaKarya'])

@section('dashboard-content')
@php
    // Mengambil data real-time pengangkut dari database berdasarkan session login Anda
    $currentUser = \App\Models\User::find(session('user_id'));
@endphp

<div class="space-y-8 animate-fadeIn">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gradient-to-r from-white to-cream p-6 rounded-[1.5rem] border border-ink/5 shadow-md shadow-ink/[0.01]">
        <div class="flex items-center gap-4">
            <div class="avatar {{ $currentUser && $currentUser->foto_profil ? '' : 'placeholder' }}">
                <div class="bg-gradient-to-tr from-forest to-forest-dark text-white rounded-full w-16 h-16 shadow-lg shadow-forest/20 ring-4 ring-white overflow-hidden flex items-center justify-center">
                    @if($currentUser && $currentUser->foto_profil)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($currentUser->foto_profil) }}?v={{ time() }}" alt="Foto Profil {{ $currentUser->name }}" class="w-full h-full object-cover" />
                    @else
                        <span class="text-xl font-bold font-display">{{ strtoupper(substr(session('name', 'P'), 0, 1)) }}</span>
                    @endif
                </div>
            </div>
            
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="font-display font-extrabold text-2xl text-ink leading-tight">Selamat Datang, {{ session('name', 'Andi') }}</h1>
                    <span class="badge bg-forest/10 text-forest border-none text-[10px] font-bold px-2 py-0.5 rounded-full">DD 1234 HH</span>
                </div>
                <p class="text-xs text-ink-soft font-semibold mt-1">SELAMAT BEKERJA, REKAN PENGANGKUT!</p>
            </div>
        </div>

        <div class="flex items-center gap-3 bg-white border border-forest/10 p-3 rounded-2xl w-full sm:w-auto shadow-sm">
            <div class="bg-amber-50 text-amber-500 p-2 rounded-xl border border-amber-100 flex items-center justify-center">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="text-amber-500"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
            </div>
            <div class="text-left">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-ink-soft/60 block">PROFIL ANDA</span>
                <span class="text-sm font-bold text-ink flex items-center gap-1">
                    ★ 4.9 <span class="text-xs text-ink-soft font-medium">Rating</span>
                </span>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-r from-forest via-forest to-forest-dark p-6 rounded-[1.5rem] text-white shadow-xl shadow-forest/10 relative overflow-hidden">
        <div class="relative z-10">
            <h3 class="font-display font-bold text-2xl text-cream">Kelola rute penjemputan & validasi setoran dalam satu genggaman.</h3>
            <p class="text-xs text-cream/75 mt-2 font-medium max-w-xl">Pastikan Anda menimbang sampah secara akurat dan memvalidasi setoran menggunakan QR Code guna mendistribusikan poin reward warga.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="bg-white border border-ink/5 rounded-2xl p-5 shadow-sm">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <span class="text-xs text-ink-soft font-bold tracking-wide block uppercase">PROGRES PENJEMPUTAN</span>
                    <p class="text-3xl font-extrabold text-ink mt-0.5">40%</p>
                </div>
                <span class="text-xs font-bold text-ink-soft">2/5 Selesai</span>
            </div>
            <progress class="progress progress-success w-full h-2.5 bg-sand/40" value="40" max="100"></progress>
            <div class="flex justify-end mt-2">
                <a href="#page-jadwal" class="text-xs text-forest font-bold hover:underline flex items-center gap-1">Lihat detail →</a>
            </div>
        </div>

        <div class="bg-white border border-ink/5 rounded-2xl p-5 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-xs text-ink-soft font-bold tracking-wide block uppercase">VALIDASI SETORAN</span>
                <p class="text-2xl font-extrabold text-ink mt-0.5">2 Setoran di validasi</p>
            </div>
            <button onclick="alert('Membuka Kamera Pemindai QR...')" class="btn btn-sm bg-forest hover:bg-forest-dark border-none text-white font-bold rounded-xl normal-case px-4 shadow-md">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="mr-1"><path d="M3 7V5a2 2 0 0 1 2-2h2m10 0h2a2 2 0 0 1 2 2v2m0 10v2a2 2 0 0 1-2 2h-2M7 21H5a2 2 0 0 1-2-2v-2M12 7v10M9 12h6"/></svg>
                Mulai validasi setoran baru
            </button>
        </div>
    </div>

    <div class="bg-white border border-ink/5 rounded-2xl p-6 shadow-sm">
        <div class="mb-4">
            <h2 class="font-display font-extrabold text-xl text-ink">Log Validasi Setoran Terakhir</h2>
            <p class="text-xs text-ink-soft font-medium">Setoran yang telah Anda validasi dan berhasil didistribusikan poin reward-nya ke warga.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="table table-zebra w-full text-sm">
                <thead>
                    <tr class="bg-sand/30 text-ink border-b border-ink/5">
                        <th class="font-extrabold">PENYETOR</th>
                        <th class="font-extrabold">JENIS SAMPAH</th>
                        <th class="font-extrabold">BERAT AKTUAL</th>
                        <th class="font-extrabold">POIN DIKIRIM</th>
                        <th class="font-extrabold">STATUS VALIDASI</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-ink/5">
                        <td class="font-semibold text-ink">Andi</td>
                        <td>Kertas</td>
                        <td class="font-medium">3.0 Kg</td>
                        <td class="text-forest font-bold">+ 1.000</td>
                        <td>
                            <span class="badge bg-emerald-50 text-emerald-600 border border-emerald-100 text-[11px] font-bold px-2.5 py-1 rounded-md">Terkirim | 10.00 WITA</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div id="page-jadwal" class="bg-white border border-ink/5 rounded-2xl p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h2 class="font-display font-extrabold text-xl text-ink">Daftar Rute & Jadwal Penjemputan</h2>
                <p class="text-xs text-ink-soft font-medium">Kelola status operasional langsung saat berkendara di lapangan.</p>
            </div>
            <div class="join border border-ink/10 rounded-xl overflow-hidden bg-white">
                <button class="join-item btn btn-xs bg-forest text-white border-none normal-case font-bold px-3">Semua Rute</button>
                <button class="join-item btn btn-xs bg-white text-ink-soft hover:bg-cream border-none normal-case font-bold px-3">Belum Selesai</button>
                <button class="join-item btn btn-xs bg-white text-ink-soft hover:bg-cream border-none normal-case font-bold px-3">Dalam Perjalanan</button>
                <button class="join-item btn btn-xs bg-white text-ink-soft hover:bg-cream border-none normal-case font-bold px-3">Selesai</button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full text-sm">
                <thead>
                    <tr class="bg-sand/30 text-ink border-b border-ink/5">
                        <th class="font-extrabold">PENYETOR</th>
                        <th class="font-extrabold">JENIS SAMPAH</th>
                        <th class="font-extrabold">BERAT AKTUAL</th>
                        <th class="font-extrabold">ALAMAT</th>
                        <th class="font-extrabold">STATUS PENJEMPUTAN</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-ink/5 hover:bg-sand/10 transition-colors">
                        <td class="font-semibold text-ink">Andi</td>
                        <td>Kertas</td>
                        <td class="font-medium">3.0 Kg</td>
                        <td class="text-ink-soft max-w-xs truncate">Jl. Tupai No. 1</td>
                        <td>
                            <select class="select select-bordered select-xs w-36 rounded-md font-bold bg-emerald-50 text-emerald-600 border-emerald-200">
                                <option value="belum">Belum Selesai</option>
                                <option value="jalan">Dalam Perjalanan</option>
                                <option value="selesai" selected>SELESAI</option>
                            </select>
                        </td>
                    </tr>
                    <tr class="border-b border-ink/5 hover:bg-sand/10 transition-colors">
                        <td class="font-semibold text-ink">Caca</td>
                        <td>Besi</td>
                        <td class="font-medium">20 Kg</td>
                        <td class="text-ink-soft max-w-xs truncate">Jl. dgtata 1 No. 1</td>
                        <td>
                            <select class="select select-bordered select-xs w-36 rounded-md font-bold bg-amber-50 text-amber-600 border-amber-200">
                                <option value="belum">Belum Selesai</option>
                                <option value="jalan" selected>Sedang menjemput</option>
                                <option value="selesai">Selesai</option>
                            </select>
                        </td>
                    </tr>
                    <tr class="border-b border-ink/5 hover:bg-sand/10 transition-colors">
                        <td class="font-semibold text-ink">Caca</td>
                        <td>Besi</td>
                        <td class="font-medium">20 Kg</td>
                        <td class="text-ink-soft max-w-xs truncate">Jl. Todopuli</td>
                        <td>
                            <select class="select select-bordered select-xs w-36 rounded-md font-bold bg-slate-50 text-slate-500 border-slate-200">
                                <option value="belum" selected>Belum Selesai</option>
                                <option value="jalan">Dalam Perjalanan</option>
                                <option value="selesai">Selesai</option>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection