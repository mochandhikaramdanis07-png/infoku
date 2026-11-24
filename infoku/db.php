<?php
declare(strict_types=1);

session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();
date_default_timezone_set('Asia/Jakarta');

const MATERI_IDS = ['M1', 'M2', 'M3', 'M4', 'M5', 'M6', 'M7', 'M8', 'M9'];
const LANGKAH_IDS = ['tujuan', 'segmen', 'contoh', 'latihan'];

function config(): array
{
    static $cfg = null;
    if ($cfg === null) {
        $cfg = require __DIR__ . '/config.php';
    }
    return $cfg;
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }
    $c = config();
    try {
        $dsn = "mysql:host={$c['db_host']};port={$c['db_port']};dbname={$c['db_name']};charset=utf8mb4";
        $pdo = new PDO($dsn, $c['db_user'], $c['db_pass'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        $pdo->exec("SET time_zone = '+07:00'");
    } catch (PDOException $e) {
        http_response_code(500);
        header('Content-Type: text/plain; charset=utf-8');
        exit("Tidak dapat terhubung ke database.\n"
            . "Pastikan MySQL di XAMPP sudah aktif dan database.sql sudah diimpor lewat phpMyAdmin.\n"
            . "Periksa juga pengaturan di config.php.");
    }
    return $pdo;
}

function h(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_ok($token): bool
{
    return is_string($token)
        && isset($_SESSION['csrf'])
        && hash_equals($_SESSION['csrf'], $token);
}

/** Alamat dalam proyek, benar dari halaman biasa maupun dari folder guru/. */
function url(string $path): string
{
    $d = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    if (substr($d, -5) === '/guru') {
        $d = substr($d, 0, -5);
    }
    return $d . '/' . ltrim($path, '/');
}

/** Wajib masuk. Peran yang tidak cocok dialihkan ke area miliknya. */
function require_login(string $role = 'siswa'): array
{
    $login = $role === 'guru' ? 'guru/login.php' : 'login.php';
    if (empty($_SESSION['uid'])) {
        header('Location: ' . url($login));
        exit;
    }
    $st = db()->prepare('SELECT id, nama, username, kelas, role FROM users WHERE id = ?');
    $st->execute([$_SESSION['uid']]);
    $u = $st->fetch();
    if (!$u) {
        $_SESSION = [];
        session_destroy();
        header('Location: ' . url($login));
        exit;
    }
    if ($u['role'] !== $role) {
        header('Location: ' . url($u['role'] === 'guru' ? 'guru/index.php' : 'index.php'));
        exit;
    }
    return $u;
}

/** Mengambil progres pengguna dalam bentuk yang sama dengan state di app.js. */
function load_progress(int $uid): array
{
    $pdo = db();
    $p = ['sel' => [], 'lat' => [], 'mulai' => [], 'kuis' => [], 'last' => null,
          'proj' => ['chk' => [], 'ref' => '']];

    $st = $pdo->prepare('SELECT materi_id, mulai, latihan, selesai FROM materi_progress WHERE user_id = ?');
    $st->execute([$uid]);
    foreach ($st as $r) {
        if ($r['selesai']) { $p['sel'][$r['materi_id']] = true; }
        if ($r['latihan']) { $p['lat'][$r['materi_id']] = true; }
        if ($r['mulai'])   { $p['mulai'][$r['materi_id']] = true; }
    }

    $st = $pdo->prepare('SELECT kuis_id, skor, total FROM kuis_hasil WHERE user_id = ?');
    $st->execute([$uid]);
    foreach ($st as $r) {
        $p['kuis'][$r['kuis_id']] = ['s' => (int)$r['skor'], 't' => (int)$r['total']];
    }

    $st = $pdo->prepare('SELECT materi_id, langkah, bagian FROM posisi_terakhir WHERE user_id = ?');
    $st->execute([$uid]);
    if ($r = $st->fetch()) {
        $p['last'] = ['id' => $r['materi_id'], 'step' => $r['langkah'], 'seg' => (int)$r['bagian']];
    }

    $st = $pdo->prepare('SELECT checklist, refleksi FROM proyek_progress WHERE user_id = ?');
    $st->execute([$uid]);
    if ($r = $st->fetch()) {
        $chk = json_decode((string)$r['checklist'], true);
        $p['proj']['chk'] = is_array($chk) ? $chk : [];
        $p['proj']['ref'] = (string)$r['refleksi'];
    }

    // Objek kosong harus tetap {} saat di-JSON-kan, bukan [].
    foreach (['sel', 'lat', 'mulai', 'kuis'] as $k) { $p[$k] = (object)$p[$k]; }
    $p['proj']['chk'] = (object)$p['proj']['chk'];
    return $p;
}

const ABSEN_STATUS = ['hadir' => 'Hadir', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpa' => 'Alpa'];

/** Kode sesi 5 karakter tanpa huruf yang mudah tertukar (0/O, 1/I). */
function kode_sesi(): string
{
    $set = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $k = '';
    for ($i = 0; $i < 5; $i++) {
        $k .= $set[random_int(0, strlen($set) - 1)];
    }
    return $k;
}

/**
 * Sesi absensi yang berlaku untuk seorang siswa beserta isian miliknya.
 * Sesi tanpa kelas berlaku untuk semua siswa. Kode sesi tidak pernah dikirim ke siswa.
 */
function load_absensi(int $uid, ?string $kelas): array
{
    $kelas = (string)$kelas;
    $st = db()->prepare(
        "SELECT s.id, s.judul, s.tanggal, s.kelas, s.status AS sesi_status,
                a.status AS saya, a.catatan, a.waktu
         FROM absensi_sesi s
         LEFT JOIN absensi a ON a.sesi_id = s.id AND a.user_id = ?
         WHERE s.kelas = '' OR s.kelas = ?
         ORDER BY s.tanggal DESC, s.id DESC LIMIT 60"
    );
    $st->execute([$uid, $kelas]);
    $sesi = [];
    $n = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0];
    $total = 0;
    foreach ($st as $r) {
        $tutup = $r['sesi_status'] === 'tutup';
        $eff = $r['saya'] ?: ($tutup ? 'alpa' : null);
        if ($eff !== null) {
            $n[$eff]++;
            $total++;
        }
        $sesi[] = [
            'id'      => (int)$r['id'],
            'judul'   => $r['judul'],
            'tanggal' => $r['tanggal'],
            'kelas'   => $r['kelas'],
            'buka'    => !$tutup,
            'status'  => $eff,
            'diisi'   => $r['saya'] !== null,
            'catatan' => (string)$r['catatan'],
            'waktu'   => $r['waktu'],
        ];
    }
    return ['sesi' => $sesi, 'n' => $n, 'total' => $total,
            'persen' => $total ? (int)round($n['hadir'] * 100 / $total) : null];
}

function logo_svg(int $n): string
{
    return '<svg width="' . $n . '" height="' . $n . '" viewBox="0 0 64 64" aria-hidden="true" focusable="false">'
        . '<path d="M32 32 L32 18 A14 14 0 0 0 18 32 Z" fill="var(--c)"/>'
        . '<path d="M32 32 L46 32 A14 14 0 0 0 32 18 Z" fill="var(--m)"/>'
        . '<path d="M32 32 L18 32 A14 14 0 0 0 32 46 Z" fill="var(--y)"/>'
        . '<path d="M32 32 L32 46 A14 14 0 0 0 46 32 Z" fill="var(--ink)"/>'
        . '<circle cx="32" cy="32" r="20" fill="none" stroke="var(--ink)" stroke-width="2"/>'
        . '<path d="M32 4V60M4 32H60" stroke="var(--ink)" stroke-width="2"/></svg>';
}

function auth_head(string $title): void
{
    ?><!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="color-scheme" content="light dark">
<title><?= h($title) ?> | infoKu</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Familjen+Grotesk:wght@500;600;700&family=Instrument+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap">
<link rel="stylesheet" href="assets/app.css">
</head>
<body data-tone="auth">
<main class="wrap auth">
  <section class="authintro">
    <?= logo_svg(72) ?>
    <p class="eyebrow" style="margin-top:16px">InformatikaKu</p>
    <h1 class="wm">info<b>Ku</b></h1>
    <p class="lead">Belajar Informatika lewat contoh dari dunia desain. Masuk untuk menyimpan progres dan skor kuismu.</p>
    <p class="small">SMK Kelas X · Fase E · Semester 1 dan 2</p>
  </section>
  <section class="card authcard">
<?php
}

function auth_foot(): void
{
    ?>
  </section>
</main>
</body>
</html>
<?php
}
