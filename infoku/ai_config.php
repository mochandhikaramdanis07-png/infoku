<?php
// Konfigurasi Gemini untuk infoKu.
// Ganti nilai di bawah dengan API key dari https://aistudio.google.com/app/apikey
// Jangan bagikan file ini atau unggah ke repositori publik.
return [
    'provider' => 'gemini',
    'api_key' => 'AQ.Ab8RN6I1ZMVxs1Ck7h6_v66wzuZjsfMgOdKFQ2W41lmn1vp7Dw',
    'model' => 'gemini-3.5-flash-lite',
    // Pengaturan hasil generate materi (dipakai guru/ai_generate.php).
    'temperature' => 0.5,
    'max_output_tokens' => 24000,
    'timeout' => 180,
    'materi' => [
        'segmen'        => 7,    // jumlah bagian pembahasan
        'paragraf'      => '4-5', // paragraf per bagian (tiap paragraf 3-5 kalimat)
        'istilah'       => 10,   // jumlah istilah kunci
        'tujuan'        => 6,    // jumlah tujuan pembelajaran
        'langkah_contoh'=> 6,    // jumlah langkah pada contoh kasus
        'min_segmen'    => 5,    // hasil AI ditolak bila segmen kurang dari ini
    ],
];
