<?php
declare(strict_types=1);
require __DIR__ . '/../db.php';

/** Ambang ketuntasan kuis (persen). Ubah di sini bila KKM sekolah berbeda. */
const KKM = 75;
const TOTAL_MATERI = 9;

/** Isi materi, soal, dan kunci jawaban (data/materi.json). */
function data_materi(): array
{
    static $d = null;
    if ($d === null) {
        $f = __DIR__ . '/../data/materi.json';
        $d = is_file($f) ? json_decode((string)file_get_contents($f), true) : null;
        if (!is_array($d)) {
            $d = ['materi' => [], 'proyek' => [], 'kriteria' => []];
        }
    }
    return $d;
}

/** Daftar materi dengan kunci id (M1..M9). */
function materi_map(): array
{
    $m = [];
    foreach (data_materi()['materi'] as $x) {
        $m[$x['id']] = $x;
    }
    return $m;
}

function nama_kuis(string $id): string
{
    if ($id === 'S1' || $id === 'S2') {
        return 'Kuis Semester ' . substr($id, 1);
    }
    $m = materi_map();
    return $id . (isset($m[$id]) ? ' ' . $m[$id]['judul'] : '');
}

function waktu(?string $t): string
{
    if (!$t) {
        return 'Belum pernah';
    }
    $ts = strtotime($t);
    return $ts ? date('d/m/Y H:i', $ts) : '-';
}

/** Chip persentase: hijau bila tuntas, kuning bila mendekati, merah bila rendah. */
function chip_skor($p): string
{
    if ($p === null || $p === '') {
        return '<span class="chip">Belum ada</span>';
    }
    $p = (float)$p;
    $c = $p >= KKM ? 'ok' : ($p >= 60 ? 'prog' : 'bad');
    return '<span class="chip ' . $c . '">' . (int)round($p) . '%</span>';
}

/** Daftar kelas yang dipakai siswa (untuk pilihan filter dan sesi absensi). */
function daftar_kelas(PDO $pdo): array
{
    return $pdo->query(
        "SELECT DISTINCT kelas FROM users WHERE role = 'siswa' AND kelas IS NOT NULL AND kelas <> '' ORDER BY kelas"
    )->fetchAll(PDO::FETCH_COLUMN);
}

/** Siswa yang termasuk dalam sebuah sesi (kelas kosong = semua siswa). */
function siswa_sesi(PDO $pdo, string $kelas): array
{
    if ($kelas === '') {
        return $pdo->query("SELECT id, nama, username, kelas FROM users WHERE role = 'siswa' ORDER BY kelas, nama")->fetchAll();
    }
    $st = $pdo->prepare("SELECT id, nama, username, kelas FROM users WHERE role = 'siswa' AND kelas = ? ORDER BY nama");
    $st->execute([$kelas]);
    return $st->fetchAll();
}

/**
 * Rekap kehadiran per siswa. Sesi yang sudah ditutup dan tidak diisi dihitung alpa.
 * Mengembalikan [siswa => [... + n, total, persen], sesi => daftar sesi, sel => [sesi_id][user_id] => status].
 */
function rekap_kehadiran(PDO $pdo, string $kelas = ''): array
{
    $sesi = $pdo->query('SELECT id, judul, tanggal, kelas, status FROM absensi_sesi ORDER BY tanggal, id')->fetchAll();
    $sel = [];
    foreach ($pdo->query('SELECT sesi_id, user_id, status FROM absensi') as $r) {
        $sel[(int)$r['sesi_id']][(int)$r['user_id']] = $r['status'];
    }
    $sis = siswa_sesi($pdo, $kelas);
    $out = [];
    foreach ($sis as $s) {
        $n = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0];
        $tot = 0;
        $kolom = [];
        foreach ($sesi as $x) {
            if ($x['kelas'] !== '' && $x['kelas'] !== (string)$s['kelas']) {
                $kolom[(int)$x['id']] = null;   // sesi bukan untuk kelas siswa ini
                continue;
            }
            $st = $sel[(int)$x['id']][(int)$s['id']] ?? ($x['status'] === 'tutup' ? 'alpa' : '');
            $kolom[(int)$x['id']] = $st;
            if ($st !== '') {
                $n[$st]++;
                $tot++;
            }
        }
        $s['n'] = $n;
        $s['total'] = $tot;
        $s['persen'] = $tot ? (int)round($n['hadir'] * 100 / $tot) : null;
        $s['kolom'] = $kolom;
        $out[] = $s;
    }
    return ['siswa' => $out, 'sesi' => $sesi];
}

function chip_absen(?string $st): string
{
    if ($st === null) {
        return '<span class="small">-</span>';
    }
    if ($st === '') {
        return '<span class="chip">Belum</span>';
    }
    $c = $st === 'hadir' ? 'ok' : ($st === 'alpa' ? 'bad' : 'prog');
    return '<span class="chip ' . $c . '">' . h(ABSEN_STATUS[$st]) . '</span>';
}

function guru_head(string $title, string $active, array $u, string $extraHead = ''): void
{
    $nav = [
        'index'  => ['index.php', 'Ringkasan'],
        'siswa'  => ['siswa.php', 'Siswa'],
        'absensi' => ['absensi.php', 'Absensi'],
        'materi' => ['materi.php', 'Rekap Materi'],
        'kunci'  => ['kunci.php', 'Materi dan Kunci'],
        'ai_materi' => ['ai_materi.php', 'AI Pembuat Materi'],
        'ai_kuis' => ['ai_kuis.php', 'AI Pembuat Kuis'],
        'edit_materi' => ['edit_materi.php', 'Edit Materi Manual'],
        'edit_kuis' => ['edit_kuis.php', 'Edit Kuis Manual'],
    ];
    ?><!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="color-scheme" content="light dark">
<title><?= h($title) ?> | infoKu Guru</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Familjen+Grotesk:wght@500;600;700&family=Instrument+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap">
<link rel="stylesheet" href="../assets/app.css">
<link rel="stylesheet" href="../assets/guru.css?v=20261120-jsonwrap2">
<?= $extraHead ?>
</head>
<body class="guru">
<header class="top">
  <div class="wrap topin">
    <a class="brand" href="index.php" aria-label="infoKu Guru, ke Ringkasan"><?= logo_svg(30) ?><span class="bw">info<b>Ku</b></span><span class="role">GURU</span></a>
    <div class="user"><span class="un"><?= h($u['nama']) ?></span><a class="btn ghost sm" href="logout.php">Keluar</a></div>
    <nav class="nav" aria-label="Menu guru">
<?php foreach ($nav as $key => [$href, $label]): ?>
      <a href="<?= h($href) ?>"<?= $key === $active ? ' aria-current="page"' : '' ?>><?= h($label) ?></a>
<?php endforeach; ?>
    </nav>
  </div>
</header>
<main class="wrap main">
<?php
}

function guru_foot(): void
{
    ?>
</main>
<footer class="foot"><div class="wrap"><p class="small">Area guru infoKu. Data berasal dari progres dan skor kuis siswa yang tersimpan di database.</p></div></footer>
</body>
</html>
<?php
}

function guru_auth_head(string $title): void
{
    ?><!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="color-scheme" content="light dark">
<title><?= h($title) ?> | infoKu Guru</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Familjen+Grotesk:wght@500;600;700&family=Instrument+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap">
<link rel="stylesheet" href="../assets/app.css">
<link rel="stylesheet" href="../assets/guru.css?v=20261120-jsonwrap2">
</head>
<body class="guru" data-tone="auth">
<main class="wrap auth">
  <section class="authintro">
    <?= logo_svg(72) ?>
    <p class="eyebrow" style="margin-top:16px">InformatikaKu · Area guru</p>
    <h1 class="wm">info<b>Ku</b></h1>
    <p class="lead">Pantau progres belajar dan hasil kuis siswa, lihat bab yang perlu dibahas ulang, dan unduh rekap nilai.</p>
    <p class="small">Halaman ini khusus guru. Siswa masuk lewat <a href="../login.php">halaman siswa</a>.</p>
  </section>
  <section class="card authcard">
<?php
}

function guru_auth_foot(): void
{
    ?>
  </section>
</main>
</body>
</html>
<?php
}
