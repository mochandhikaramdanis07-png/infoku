<?php
declare(strict_types=1);
require __DIR__ . '/_layout.php';

$u = require_login('guru');
$pdo = db();

$id = (int)($_GET['id'] ?? 0);
$st = $pdo->prepare("SELECT id, nama, username, kelas, created_at, last_login FROM users WHERE id = ? AND role = 'siswa'");
$st->execute([$id]);
$s = $st->fetch();

if (!$s) {
    http_response_code(404);
    guru_head('Siswa tidak ditemukan', 'siswa', $u);
    echo '<h1>Siswa tidak ditemukan</h1><p>Akun ini tidak ada atau bukan akun siswa.</p><p><a class="btn" href="siswa.php">Kembali ke daftar siswa</a></p>';
    guru_foot();
    exit;
}

$flash = '';
$ferr = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'reset') {
    $pw = (string)($_POST['password'] ?? '');
    if (!csrf_ok($_POST['csrf'] ?? null)) {
        $ferr = 'Sesi formulir kedaluwarsa. Muat ulang halaman lalu coba lagi.';
    } elseif (strlen($pw) < 8) {
        $ferr = 'Kata sandi baru minimal 8 karakter.';
    } else {
        $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ? AND role = 'siswa'")
            ->execute([password_hash($pw, PASSWORD_DEFAULT), $id]);
        $flash = 'Kata sandi ' . $s['nama'] . ' sudah diganti. Sampaikan kata sandi baru kepada siswa.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'kelas') {
    $kb = trim((string)($_POST['kelas'] ?? ''));
    if (!csrf_ok($_POST['csrf'] ?? null)) {
        $ferr = 'Sesi formulir kedaluwarsa. Muat ulang halaman lalu coba lagi.';
    } elseif (mb_strlen($kb) > 30) {
        $ferr = 'Nama kelas terlalu panjang (maksimal 30 karakter).';
    } else {
        $pdo->prepare("UPDATE users SET kelas = ? WHERE id = ? AND role = 'siswa'")
            ->execute([$kb !== '' ? $kb : null, $id]);
        $s['kelas'] = $kb !== '' ? $kb : null;
        $flash = 'Kelas ' . $s['nama'] . ' diperbarui. Absensi mengikuti kelas yang baru.';
    }
}

$abs = load_absensi($id, $s['kelas']);

$mp = [];
$q = $pdo->prepare('SELECT materi_id, mulai, latihan, selesai FROM materi_progress WHERE user_id = ?');
$q->execute([$id]);
foreach ($q as $r) {
    $mp[$r['materi_id']] = $r;
}
$kh = [];
$q = $pdo->prepare('SELECT kuis_id, skor, total, updated_at FROM kuis_hasil WHERE user_id = ?');
$q->execute([$id]);
foreach ($q as $r) {
    $kh[$r['kuis_id']] = $r;
}
$q = $pdo->prepare('SELECT checklist, refleksi FROM proyek_progress WHERE user_id = ?');
$q->execute([$id]);
$pp = $q->fetch();
$chk = $pp ? json_decode((string)$pp['checklist'], true) : [];
$nChk = is_array($chk) ? count($chk) : 0;
$totChk = 0;
foreach (data_materi()['proyek'] as $tahap) {
    $totChk += count($tahap['c']);
}
$q = $pdo->prepare('SELECT materi_id, langkah FROM posisi_terakhir WHERE user_id = ?');
$q->execute([$id]);
$pos = $q->fetch();

$mm = materi_map();
$nSelesai = 0;
$sumKuis = 0.0;
$nKuis = 0;
foreach (MATERI_IDS as $m) {
    if (!empty($mp[$m]['selesai'])) {
        $nSelesai++;
    }
    if (isset($kh[$m])) {
        $sumKuis += $kh[$m]['skor'] * 100.0 / $kh[$m]['total'];
        $nKuis++;
    }
}
$rata = $nKuis ? $sumKuis / $nKuis : null;

guru_head($s['nama'], 'siswa', $u);
?>
<p class="small" style="margin-bottom:6px"><a href="siswa.php">&larr; Daftar siswa</a></p>
<div class="pagehead">
  <div>
    <p class="eyebrow">Detail siswa</p>
    <h1><?= h($s['nama']) ?></h1>
  </div>
</div>

<?php if ($flash): ?><p class="flash" role="status"><?= h($flash) ?></p><?php endif; ?>
<?php if ($ferr): ?><p class="fb bad" role="alert"><?= h($ferr) ?></p><?php endif; ?>

<div class="cols">
  <section class="card">
    <h3>Identitas</h3>
    <dl class="dl">
      <dt>Username</dt><dd><?= h($s['username']) ?></dd>
      <dt>Kelas</dt><dd><?= h((string)($s['kelas'] ?: '-')) ?></dd>
      <dt>Terdaftar</dt><dd><?= h(waktu($s['created_at'])) ?></dd>
      <dt>Terakhir masuk</dt><dd><?= h(waktu($s['last_login'])) ?></dd>
      <dt>Posisi belajar</dt><dd><?= $pos ? h(nama_kuis($pos['materi_id']) . ' · ' . $pos['langkah']) : 'Belum mulai' ?></dd>
    </dl>
  </section>
  <section class="card">
    <h3>Capaian</h3>
    <dl class="dl">
      <dt>Materi selesai</dt><dd><?= $nSelesai ?> dari <?= TOTAL_MATERI ?></dd>
      <dt>Rata-rata kuis bab</dt><dd><?= chip_skor($rata) ?></dd>
      <dt>Kuis semester 1</dt><dd><?= isset($kh['S1']) ? (int)$kh['S1']['skor'] . ' dari ' . (int)$kh['S1']['total'] : 'Belum dikerjakan' ?></dd>
      <dt>Kuis semester 2</dt><dd><?= isset($kh['S2']) ? (int)$kh['S2']['skor'] . ' dari ' . (int)$kh['S2']['total'] : 'Belum dikerjakan' ?></dd>
      <dt>Proyek</dt><dd><?= $nChk ?> dari <?= $totChk ?> butir selesai</dd>
    </dl>
  </section>
</div>

<h2>Kehadiran</h2>
<section class="card">
<?php if ($abs['total']): ?>
  <p style="margin:0"><strong><?= (int)$abs['persen'] ?>% hadir</strong> dari <?= (int)$abs['total'] ?> pertemuan
    · Hadir <?= $abs['n']['hadir'] ?> · Izin <?= $abs['n']['izin'] ?> · Sakit <?= $abs['n']['sakit'] ?> · Alpa <?= $abs['n']['alpa'] ?></p>
  <div class="bar" role="img" aria-label="<?= (int)$abs['persen'] ?> persen hadir"><i style="width:<?= (int)$abs['persen'] ?>%"></i></div>
<?php else: ?>
  <p style="margin:0">Belum ada pertemuan yang tercatat untuk siswa ini.</p>
<?php endif; ?>
</section>
<?php if ($abs['sesi']): ?>
<div class="tbl wide"><table>
  <thead><tr><th scope="col">Tanggal</th><th scope="col">Pertemuan</th><th scope="col">Status</th><th scope="col">Catatan</th></tr></thead>
  <tbody>
<?php foreach (array_slice($abs['sesi'], 0, 15) as $a): ?>
    <tr>
      <td><?= h(date('d/m/Y', strtotime($a['tanggal']))) ?></td>
      <td><a href="absensi_detail.php?id=<?= (int)$a['id'] ?>"><?= h($a['judul']) ?></a></td>
      <td><?= chip_absen($a['status'] ?? '') ?></td>
      <td><?= h($a['catatan']) ?></td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table></div>
<?php endif; ?>

<h2>Per bab</h2>
<div class="tbl wide"><table>
  <thead><tr><th scope="col">Materi</th><th scope="col">Status</th><th scope="col">Latihan</th><th scope="col">Kuis bab</th></tr></thead>
  <tbody>
<?php foreach (MATERI_IDS as $m):
    $r = $mp[$m] ?? null;
    $status = !$r ? '<span class="chip">Belum dimulai</span>'
        : ($r['selesai'] ? '<span class="chip ok">Selesai</span>' : '<span class="chip prog">Sedang dipelajari</span>'); ?>
    <tr>
      <td><?= h($m . ' ' . ($mm[$m]['judul'] ?? '')) ?></td>
      <td><?= $status ?></td>
      <td><?= !empty($r['latihan']) ? 'Sudah' : 'Belum' ?></td>
      <td><?= isset($kh[$m]) ? (int)$kh[$m]['skor'] . ' dari ' . (int)$kh[$m]['total'] . ' ' . chip_skor($kh[$m]['skor'] * 100.0 / $kh[$m]['total']) : '<span class="chip">Belum</span>' ?></td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table></div>

<h2>Refleksi proyek</h2>
<?php if ($pp && trim((string)$pp['refleksi']) !== ''): ?>
  <blockquote class="ref"><?= h((string)$pp['refleksi']) ?></blockquote>
<?php else: ?>
  <div class="empty">Siswa belum menulis refleksi proyek.</div>
<?php endif; ?>

<h2>Ubah kelas</h2>
<section class="card resetbox">
  <p class="small" style="margin-top:0">Kelas menentukan sesi absensi yang tampil untuk siswa. Samakan penulisannya dengan kelas lain, misalnya <?= h((string)(daftar_kelas($pdo)[0] ?? 'X DKV 1')) ?>.</p>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
    <input type="hidden" name="aksi" value="kelas">
    <div class="fld">
      <label for="kelas">Kelas</label>
      <input id="kelas" name="kelas" type="text" maxlength="30" value="<?= h((string)($s['kelas'] ?? '')) ?>" list="kelaslist">
      <datalist id="kelaslist"><?php foreach (daftar_kelas($pdo) as $k): ?><option value="<?= h($k) ?>"><?php endforeach; ?></datalist>
    </div>
    <button class="btn sm" type="submit">Simpan kelas</button>
  </form>
</section>

<h2>Atur ulang kata sandi</h2>
<section class="card resetbox">
  <p class="small" style="margin-top:0">Pakai bila siswa lupa kata sandi. Kata sandi lama tidak bisa dilihat, hanya bisa diganti.</p>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
    <input type="hidden" name="aksi" value="reset">
    <div class="fld">
      <label for="password">Kata sandi baru (minimal 8 karakter)</label>
      <input id="password" name="password" type="text" autocomplete="off" minlength="8" required>
    </div>
    <button class="btn sm" type="submit">Ganti kata sandi</button>
  </form>
</section>
<?php guru_foot();
