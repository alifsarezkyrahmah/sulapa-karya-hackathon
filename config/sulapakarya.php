<?php

return [

    'point_conversion' => [
        'plastik' => [
            'Gelas Plastik (PP Berwarna)' => 960,
            'Botol Plastik (PET/HDPE)'    => 640,
            'Plastik Kresek (LDPE)'       => 400,
        ],
        'kertas' => [
            'Kertas HVS/Buku Bekas'          => 720,
            'Kardus Bekas'                    => 800,
            'Kertas Koran'                    => 600,
            'Kertas Dupleks (Karton Makanan)' => 400,
        ],
        'kain' => [
            'Kain Perca / Limbah Tekstil' => 4000,
        ],
        'logam' => [
            'Kaleng Aluminium (Minuman)' => 4800,
            'Kaleng Besi / Seng'         => 1000,
            'Logam Tembaga'              => 24000,
            'Besi Tua / Padat'           => 1600,
        ],
        'kaca' => [
            'Botol Kaca' => 200,
        ],
        'elektronik' => [
            'Elektronik Bekas (E-Waste)' => 2000,
        ],
    ],

    'material_prices' => [
        'plastik' => [
            'Gelas Plastik (PP Berwarna)' => 2400,
            'Botol Plastik (PET/HDPE)'    => 1600,
            'Plastik Kresek (LDPE)'       => 1000,
        ],
        'kertas' => [
            'Kertas HVS/Buku Bekas'          => 1800,
            'Kardus Bekas'                    => 2000,
            'Kertas Koran'                    => 1500,
            'Kertas Dupleks (Karton Makanan)' => 1000,
        ],
        'kain' => [
            'Kain Perca / Limbah Tekstil' => 10000,
        ],
        'logam' => [
            'Kaleng Aluminium (Minuman)' => 12000,
            'Kaleng Besi / Seng'         => 2500,
            'Logam Tembaga'              => 60000,
            'Besi Tua / Padat'           => 4000,
        ],
        'kaca' => [
            'Botol Kaca' => 500,
        ],
        'elektronik' => [
            'Elektronik Bekas (E-Waste)' => 5000,
        ],
    ],

    'cash_minimum_kg' => 1.0,

    'cash_withdrawal_min_points' => 1000000,

    'membership_tiers' => [
        'pemula'  => ['price' => 0,      'max_products' => 5],
        'aktif'   => ['price' => 35000,  'max_products' => null],
        'premium' => ['price' => 100000, 'max_products' => null],
    ],
];
