# infoKu (InformatikaKu)

Website pembelajaran Informatika SMK kelas X dengan konteks DKV, berbasis Cognitive Load Theory.
Dibuat dengan PHP dan MySQL. Akun, progres belajar, skor kuis, dan proyek tersimpan di database
yang bisa dilihat lewat phpMyAdmin.

## Yang dibutuhkan

- XAMPP (atau Laragon) dengan Apache dan MySQL. PHP 8.0 atau lebih baru.
- VS Code. Ekstensi yang disarankan: PHP Intelephense.
- Koneksi internet saat membuka halaman (untuk memuat font Google).

## Cara menjalankan

### Langkah 1. Siapkan database

1. Buka XAMPP Control Panel, klik **Start** pada **Apache** dan **MySQL**.
2. Buka http://localhost/phpmyadmin
3. Klik tab **Import**, pilih berkas `database.sql` dari folder ini, lalu klik **Import** (Go).
   Database `infoku` dan semua tabelnya dibuat otomatis.

### Langkah 2A. Jalankan lewat Apache XAMPP

1. Salin seluruh folder `infoku` ke `C:\xampp\htdocs\` (Windows) atau `/opt/lampp/htdocs/` (Linux).
2. Buka folder itu di VS Code (**File > Open Folder**).
3. Buka http://localhost/infoku/ di peramban.

### Langkah 2B. Jalankan dari VS Code tanpa Apache

Cara ini memakai server bawaan PHP. MySQL XAMPP tetap harus aktif.

1. Buka folder `infoku` di VS Code.
2. Pastikan perintah `php` bisa dipakai di terminal. Cek dengan `php -v`.
   Jika belum, tambahkan `C:\xampp\php` ke PATH Windows, lalu buka ulang VS Code.
3. Tekan **Ctrl+Shift+B** (atau **Terminal > Run Build Task**) untuk menjalankan tugas
   **Jalankan infoKu**. Atau ketik manual di terminal: `php -S localhost:8000`
4. Buka http://localhost:8000/

### Langkah 3. Daftar dan masuk

Klik **Daftar** di halaman login, isi data, lalu mulai belajar. Progres tersimpan otomatis
ke database. Buka phpMyAdmin > database `infoku` untuk melihat tabelnya.

## Warna

Latar memakai gradasi lembut sesuai bagian (bukan putih polos). Halaman masuk dan daftar (siswa dan guru) memakai foto sekolah di `assets/sekolah.jpg`. Ganti berkas itu dengan foto lain (nama sama) bila perlu. Foto beresolusi tinggi (minimal 1600 px lebar) akan terlihat lebih tajam.

Latar halaman mengikuti bagian yang dibuka: kebiruan untuk Semester 1, kemerahan lembut untuk Semester 2,
abu terang untuk halaman umum dan halaman masuk, serta krem hangat untuk area guru. Kartu tetap putih agar teks
terbaca jelas, dan ada pita empat warna (cyan, magenta, kuning, hitam) di atas header. Mode gelap ikut pengaturan
perangkat. Nilai warna ada di bagian atas `assets/app.css` (variabel `--t-s1`, `--t-s2`, `--t-guru`, `--t-n`, `--t-auth`).

## Pengaturan database

Ubah `config.php` bila MySQL kamu tidak memakai pengaturan bawaan XAMPP
(user `root`, tanpa kata sandi, port 3306). Contoh: Laragon atau XAMPP dengan port 3307.

## Absensi (siswa dan guru)

Absensi terhubung langsung: yang diisi siswa muncul di halaman guru, dan perubahan dari guru terlihat siswa.

**Alur singkat**
1. Guru masuk, buka **Absensi**, isi judul pertemuan, tanggal, dan kelas, lalu klik **Buka sesi dan buat kode**.
2. Guru menuliskan atau memproyeksikan kode 5 karakter di kelas.
3. Siswa membuka menu **Absensi** di infoKu, memilih **Hadir** lalu mengetik kode. Untuk **Izin** atau **Sakit**,
   siswa cukup menulis alasan singkat tanpa kode.
4. Guru melihat daftar yang mengisi (halaman detail sesi menyegarkan diri tiap 15 detik selama sesi terbuka).
5. Setelah selesai, guru klik **Tutup sesi**. Siswa yang belum mengisi otomatis dihitung **alpa**.

Guru juga bisa mengubah status siswa (misalnya siswa sakit dan memberi kabar lewat orang tua), membuat kode baru,
membuka kembali sesi, menghapus sesi, dan mengunduh CSV per sesi maupun rekap seluruh pertemuan.
Kartu **Kehadiran** juga tampil di detail tiap siswa, dan guru bisa **mengubah kelas** siswa dari halaman itu.

**Jika database sudah dibuat sebelumnya**, jalankan sekali `update_absensi.sql` di phpMyAdmin
(pilih database `infoku`, tab **Import**, pilih berkas itu). Jika baru memulai, `database.sql` sudah memuatnya.

Aturan: sesi untuk satu kelas hanya tampil bagi siswa dengan kelas yang sama persis (huruf besar-kecil tidak
dibedakan di MySQL), sesi "Semua kelas" tampil untuk semua. Siswa hanya bisa mengisi sekali, dan tebakan kode
dibatasi (8 kali salah, lalu tunggu 5 menit). Kode tidak pernah dikirim ke peramban siswa.

## Akun guru

Halaman guru terpisah dari halaman siswa dan isinya berbeda.

1. Buka `http://localhost/infoku/guru/register.php`.
2. Isi nama, username, kata sandi, dan **kode guru**. Kode ada di `config.php` pada baris
   `kode_guru` (bawaan: `GURU-INFOKU-2026`). **Ganti kode ini** dengan kode rahasia milikmu
   sebelum dipakai di luar komputer sendiri, dan jangan berikan ke siswa.
3. Setelah itu masuk lewat `http://localhost/infoku/guru/login.php`.

Akun siswa tidak bisa membuka halaman guru, dan akun guru tidak bisa masuk lewat halaman siswa.
Tidak perlu mengimpor ulang `database.sql`, tabelnya sama.

| Halaman guru | Isi |
| --- | --- |
| Ringkasan | Sesi absensi yang sedang terbuka, jumlah siswa, siswa aktif 7 hari, rata-rata progres dan skor, bab yang perlu dibahas ulang, siswa yang perlu pendampingan, hasil kuis terbaru |
| Siswa | Daftar siswa dengan cari nama dan filter kelas, progres, rata-rata kuis, terakhir masuk, tombol unduh rekap CSV |
| Detail siswa | Kehadiran, ubah kelas, status per bab, skor kuis, checklist dan refleksi proyek, atur ulang kata sandi siswa |
| Absensi | Buka dan tutup sesi, kode absensi, daftar hadir langsung, ubah status, rekap per siswa, unduh CSV |
| Rekap Materi | Per bab: berapa siswa mulai, selesai, mengerjakan kuis, rata-rata skor, dan jumlah yang tuntas |
| Materi dan Kunci | Tujuan, istilah, soal latihan dan kuis dengan kunci dan pembahasan, rubrik proyek |

Ambang ketuntasan kuis adalah 75% (`KKM` di `guru/_layout.php`). Berkas CSV memakai pemisah titik koma
supaya langsung terbaca rapi di Excel berbahasa Indonesia.

## Struktur berkas

| Berkas | Fungsi |
| --- | --- |
| `index.php` | Halaman utama siswa, hanya bisa dibuka setelah masuk |
| `login.php`, `register.php`, `logout.php` | Masuk, daftar, keluar (siswa) |
| `api.php` | Menyimpan progres siswa ke database (dipanggil otomatis oleh halaman) |
| `db.php`, `config.php` | Koneksi database, sesi, peran, dan kode guru |
| `database.sql` | Skema database untuk diimpor di phpMyAdmin |
| `assets/app.css`, `assets/app.js` | Tampilan dan logika antarmuka siswa |
| `assets/guru.css` | Tampilan tambahan area guru |
| `guru/login.php`, `guru/register.php`, `guru/logout.php` | Masuk, daftar (dengan kode guru), keluar (guru) |
| `guru/index.php`, `siswa.php`, `detail.php`, `materi.php`, `kunci.php`, `export.php` | Halaman guru |
| `guru/absensi.php`, `absensi_detail.php`, `absensi_export.php` | Absensi: daftar sesi dan rekap, detail sesi, unduh CSV |
| `update_absensi.sql` | Tambahan tabel absensi untuk database lama |
| `guru/_layout.php` | Kerangka halaman guru dan fungsi bantu |
| `data/materi.json` | Isi materi, soal, dan kunci untuk halaman guru. Dilindungi `.htaccess` |

## Tabel database

| Tabel | Isi |
| --- | --- |
| `users` | Nama, username, kelas, kata sandi (di-hash), peran |
| `materi_progress` | Status mulai, latihan, dan selesai per materi |
| `kuis_hasil` | Skor terakhir kuis per bab dan per semester |
| `proyek_progress` | Checklist dan refleksi Proyek Informatika |
| `posisi_terakhir` | Posisi belajar terakhir untuk tombol Lanjutkan belajar |
| `absensi_sesi` | Sesi absensi: judul, tanggal, kelas, kode, terbuka atau ditutup |
| `absensi` | Status hadir, izin, sakit, atau alpa tiap siswa per sesi |

## Keamanan yang sudah diterapkan

- Kata sandi disimpan dengan `password_hash`, bukan teks biasa.
- Semua query memakai prepared statement.
- Formulir dan API dilindungi token CSRF. ID sesi diganti saat masuk.
- Percobaan masuk yang salah dibatasi: 5 kali, lalu ditahan 5 menit.

Untuk dipakai di internet (bukan hanya di komputer sendiri), pakai HTTPS, buat user MySQL
khusus dengan kata sandi, dan jangan memakai user `root`.

## Catatan

- Pendaftaran biasa selalu menghasilkan akun siswa. Akun guru hanya bisa dibuat dengan kode guru.
- Materi dan soal yang tampil di halaman siswa dimuat dari `data/materi.json`. Perubahan yang disimpan melalui menu **Editor & AI** otomatis menjadi sumber data halaman siswa dan halaman kunci guru.
- Isi materi dan soal adalah draf dari matriks storyboard. Cocokkan dengan CP dan ATP
  Informatika di sekolah sasaran sebelum dipakai.
- Nama dan institusi pengembang di halaman Profil masih kosong. Isi di `assets/app.js`,
  pada bagian `V.profil`.

## Absensi manual dan QR

- Guru membuka **Absensi**, membuat sesi, lalu membuka detail sesi.
- Untuk absensi manual, pilih **Ubah status siswa**, tentukan Hadir/Izin/Sakit/Alpa per siswa, lalu simpan perubahan.
- Untuk absensi QR, tampilkan QR pada halaman detail sesi. Siswa membuka menu **Absensi**, memilih **Scan QR dengan kamera**, mengizinkan akses kamera, lalu mengarahkan kamera ke QR guru. Setelah kode terisi otomatis, siswa menekan **Kirim absensi**.
- Kode manual tetap tersedia sebagai alternatif jika kamera tidak bisa digunakan.
- Akses kamera browser memerlukan HTTPS atau alamat localhost. Pastikan perangkat memiliki koneksi internet untuk memuat pustaka QR dari CDN.


## AI pembuat dan editor materi/kuis

1. Buka folder proyek `infoku`, salin `ai_config.example.php` menjadi `ai_config.php`.
2. Isi `api_key` dengan Gemini API key dari Google AI Studio. Model default adalah `gemini-3.5-flash-lite`. API key disimpan di server PHP dan tidak dikirim ke browser.
3. Pastikan ekstensi PHP cURL aktif di XAMPP (`php_curl` pada `php.ini`), lalu restart Apache.
4. Login sebagai guru dan buka menu **Editor & AI**. Pilih bab, isi topik, lalu klik **Generate Materi + Kuis**.
5. Periksa hasil AI di editor JSON. Guru dapat mengubah judul, tujuan, isi materi, contoh, latihan, opsi, kunci, dan pembahasan soal.
6. Klik **Simpan & Terbitkan ke Siswa**. Materi akan disimpan ke `data/materi.json` dan dimuat pada halaman siswa saat halaman dibuka ulang.

**Penting:** Hasil AI adalah draf. Guru perlu memeriksa ketepatan isi, kunci jawaban, dan kesesuaian dengan CP/ATP sebelum diterbitkan. Folder `data` harus dapat ditulis oleh Apache. Jangan membagikan `ai_config.php` atau API key ke publik.


## Integrasi Gemini API

1. Salin `ai_config.example.php` menjadi `ai_config.php` di folder utama `infoku/`.
2. Isi `api_key` dengan Gemini API key dari Google AI Studio.
3. Model default project adalah `gemini-3.5-flash-lite`.
4. Generator memanggil Gemini REST `generateContent` dengan header `x-goog-api-key` dan JSON response mode.
5. Pastikan ekstensi PHP cURL aktif di XAMPP.
6. File `ai_config.php` tidak dibundel ke ZIP agar API key pribadi tidak tertimpa atau ikut dibagikan.

API reference: https://ai.google.dev/gemini-api/docs


## Menu guru: fitur dipisah per halaman

Menu gabungan "Editor & AI" telah diganti menjadi empat halaman:
- `guru/ai_materi.php` — generator materi menggunakan Gemini.
- `guru/ai_kuis.php` — generator soal latihan dan kuis menggunakan Gemini.
- `guru/edit_materi.php` — mengedit konten materi secara terpisah.
- `guru/edit_kuis.php` — mengedit latihan, soal, opsi, kunci, dan pembahasan secara terpisah.

Setiap halaman menyimpan perubahan ke `data/materi.json`, sehingga materi/kuis yang diperbarui tetap menjadi konten yang dibaca halaman siswa. File `ai_config.php` tetap disimpan lokal dan tidak dibundel dalam ZIP.
