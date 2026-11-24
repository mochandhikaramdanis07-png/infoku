<?php
declare(strict_types=1);
require __DIR__ . '/_layout.php';

$u = require_login('guru');
$pdo = db();
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'buat') {
    $judul = trim((string)($_POST['judul'] ?? ''));
    $tgl = trim((string)($_POST['tanggal'] ?? ''));
    $kls = trim((string)($_POST['kelas'] ?? ''));
    $d = DateTime::createFromFormat('Y-m-d', $tgl);
    if (!csrf_ok($_POST['csrf'] ?? null)) {
        $err = 'Sesi formulir kedaluwarsa. Muat ulang halaman lalu coba lagi.';
    } elseif ($judul === '' || mb_strlen($judul) > 120) {
        $err = 'Judul pertemuan wajib diisi (maksimal 120 karakter).';
    } elseif (!$d || $d->format('Y-m-d') !== $tgl) {
        $err = 'Tanggal tidak valid.';
    } elseif (mb_strlen($kls) > 30) {
        $err = 'Nama kelas terlalu panjang.';
    } else {
        $pdo->prepare('INSERT INTO absensi_sesi (judul, tanggal, kelas, kode, status, dibuat_oleh) VALUES (?, ?, ?, ?, ?, ?)')
            ->execute([$judul, $tgl, $kls, kode_sesi(), 'buka', $u['id']]);
        header('Location: absensi_detail.php?id=' . (int)$pdo->lastInsertId() . '&baru=1');
        exit;
    }
}

$kelasFilter = trim((string)($_GET['kelas'] ?? ''));
$kelasList = daftar_kelas($pdo);

$sesi = $pdo->query('SELECT id, judul, tanggal, kelas, kode, status FROM absensi_sesi ORDER BY tanggal DESC, id DESC LIMIT 100')->fetchAll();
$cnt = [];
foreach ($pdo->query('SELECT sesi_id, status, COUNT(*) AS n FROM absensi GROUP BY sesi_id, status') as $r) {
    $cnt[(int)$r['sesi_id']][$r['status']] = (int)$r['n'];
}
$jumlahSiswa = [];
foreach ($pdo->query("SELECT COALESCE(kelas, '') AS k, COUNT(*) AS n FROM users WHERE role = 'siswa' GROUP BY COALESCE(kelas, '')") as $r) {
    $jumlahSiswa[$r['k']] = (int)$r['n'];
}
$semua = array_sum($jumlahSiswa);

$rek = rekap_kehadiran($pdo, $kelasFilter);

guru_head('Absensi', 'absensi', $u);
?>
<div class="pagehead">
  <div>
    <p class="eyebrow">Kehadiran siswa</p>
    <h1>Absensi</h1>
  </div>
  <a class="btn sm" href="absensi_export.php<?= $kelasFilter !== '' ? '?kelas=' . rawurlencode($kelasFilter) : '' ?>">Unduh rekap kehadiran (CSV)</a>
</div>
<p class="lead">Buka sesi untuk satu pertemuan, tampilkan QR kepada siswa, atau tandai kehadiran secara manual.</p>

<?php if ($err): ?><p class="fb bad" role="alert"><?= h($err) ?></p><?php endif; ?>

<section class="card">
  <h3>Buka sesi absensi baru</h3>
  <form method="post" class="f absnew">
    <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
    <input type="hidden" name="aksi" value="buat">
    <div class="fld">
      <label for="judul">Judul pertemuan</label>
      <input id="judul" name="judul" type="text" maxlength="120" required placeholder="contoh: Pertemuan 3 · Berpikir Komputasional">
    </div>
    <div class="fld">
      <label for="tanggal">Tanggal</label>
      <input id="tanggal" name="tanggal" type="date" required value="<?= h(date('Y-m-d')) ?>">
    </div>
    <div class="fld">
      <label for="kelasb">Untuk kelas</label>
      <select id="kelasb" name="kelas">
        <option value="">Semua kelas</option>
<?php foreach ($kelasList as $k): ?>
        <option value="<?= h($k) ?>"><?= h($k) ?></option>
<?php endforeach; ?>
      </select>
    </div>
    <button class="btn" type="submit">Buka sesi dan buat QR</button>
  </form>
</section>

<h2>Daftar sesi</h2>
<?php if (!$sesi): ?>
  <div class="empty">Belum ada sesi absensi. Buat sesi pertama di atas.</div>
<?php else: ?>
  <div class="tbl wide"><table>
    <thead><tr><th scope="col">Tanggal</th><th scope="col">Pertemuan</th><th scope="col">Kelas</th><th scope="col">Status</th><th scope="col" class="num">Hadir</th><th scope="col" class="num">Izin</th><th scope="col" class="num">Sakit</th><th scope="col" class="num">Alpa</th></tr></thead>
    <tbody>
<?php foreach ($sesi as $x):
    $c = $cnt[(int)$x['id']] ?? [];
    $isi = array_sum($c);
    $sc = $x['kelas'] === '' ? $semua : ($jumlahSiswa[$x['kelas']] ?? 0);
    $alpa = ($c['alpa'] ?? 0) + ($x['status'] === 'tutup' ? max(0, $sc - $isi) : 0); ?>
      <tr>
        <td><?= h(date('d/m/Y', strtotime($x['tanggal']))) ?></td>
        <td><a href="absensi_detail.php?id=<?= (int)$x['id'] ?>"><?= h($x['judul']) ?></a></td>
        <td><?= h($x['kelas'] !== '' ? $x['kelas'] : 'Semua') ?></td>
        <td><?= $x['status'] === 'buka' ? '<span class="chip ok">Terbuka</span>' : '<span class="chip">Ditutup</span>' ?></td>
        <td class="num"><?= (int)($c['hadir'] ?? 0) ?> / <?= $sc ?></td>
        <td class="num"><?= (int)($c['izin'] ?? 0) ?></td>
        <td class="num"><?= (int)($c['sakit'] ?? 0) ?></td>
        <td class="num"><?= $alpa ?></td>
      </tr>
<?php endforeach; ?>
    </tbody>
  </table></div>
<?php endif; ?>

<h2>Rekap per siswa</h2>
<form class="filt" method="get" action="absensi.php">
  <div class="fld">
    <label for="kelas">Kelas</label>
    <select id="kelas" name="kelas">
      <option value="">Semua kelas</option>
<?php foreach ($kelasList as $k): ?>
      <option value="<?= h($k) ?>"<?= $k === $kelasFilter ? ' selected' : '' ?>><?= h($k) ?></option>
<?php endforeach; ?>
    </select>
  </div>
  <button class="btn sm" type="submit">Terapkan</button>
</form>
<?php if (!$rek['siswa']): ?>
  <div class="empty">Tidak ada siswa untuk ditampilkan.</div>
<?php else: ?>
  <div class="tbl wide"><table>
    <thead><tr><th scope="col">Siswa</th><th scope="col">Kelas</th><th scope="col" class="num">Hadir</th><th scope="col" class="num">Izin</th><th scope="col" class="num">Sakit</th><th scope="col" class="num">Alpa</th><th scope="col" class="num">Kehadiran</th></tr></thead>
    <tbody>
<?php foreach ($rek['siswa'] as $s): ?>
      <tr>
        <td><a href="detail.php?id=<?= (int)$s['id'] ?>"><?= h($s['nama']) ?></a></td>
        <td><?= h((string)($s['kelas'] ?: '-')) ?></td>
        <td class="num"><?= $s['n']['hadir'] ?></td>
        <td class="num"><?= $s['n']['izin'] ?></td>
        <td class="num"><?= $s['n']['sakit'] ?></td>
        <td class="num"><?= $s['n']['alpa'] ?></td>
        <td class="num"><?= $s['persen'] === null ? '-' : chip_skor($s['persen']) ?></td>
      </tr>
<?php endforeach; ?>
    </tbody>
  </table></div>
  <p class="small">Sesi yang sudah ditutup dan tidak diisi siswa dihitung alpa. Sesi yang masih terbuka belum dihitung bagi siswa yang belum mengisi. Warna persen memakai ambang <?= KKM ?>%.</p>
<?php endif; ?>
<?php guru_foot();
