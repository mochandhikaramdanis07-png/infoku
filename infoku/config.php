<?php
// Pengaturan koneksi database.
// Bawaan XAMPP: user "root" tanpa kata sandi, port 3306.
// Ubah di sini bila MySQL kamu memakai pengaturan lain.
return [
    'db_host' => 'localhost',
    'db_port' => 3306,
    'db_name' => 'infoku',
    'db_user' => 'root',
    'db_pass' => '',

    // Kode rahasia untuk mendaftar sebagai guru (guru/register.php).
    // WAJIB diganti sebelum dipakai di luar komputer sendiri.
    'kode_guru' => 'GURU-INFOKU-2026',
];
