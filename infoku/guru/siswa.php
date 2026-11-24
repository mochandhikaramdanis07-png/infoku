<?php
declare(strict_types=1);
require __DIR__ . '/_layout.php';

$u = require_login('guru');
$pdo = db();

$kelas = trim((string)($_GET['kelas'] ?? ''));
$q = trim((string)($_GET['q'] ?? ''));

$where = ["u.role = 'siswa'"];
$par = [];
if ($kelas !== '') {
    $where[] = 'u.kelas = ?';
    $par[] = $kelas;
}
if ($q !== '') {
    // Tanda ! dipakai sebagai karakter escape agar % dan _ yang diketik dianggap huruf biasa.
    $esc = str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $q);
    $where[] = "(u.nama LIKE ? ESCAPE '!' OR u.username LIKE ? ESCAPE '!')";
    $par[] = '%' . $esc . '%';
    $par[] = '%' . $esc . '%';
}

$sql = "SELECT u.id, u.nama, u.username, u.kelas, u.last_login,
  (SELECT COUNT(*) FROM materi_progress mp WHERE mp.user_id = u.id AND mp.selesai = 1) AS selesai,
  (SELECT AVG(kh.skor * 100.0 / kh.total) FROM kuis_hasil kh WHERE kh.user_id = u.id AND kh.kuis_id LIKE 'M%') AS rata
  FROM users u WHERE " . implode(' AND ', $where) . ' ORDER BY u.nama';
$st = $pdo->prepare($sql);
$st->execute($par);
$rows = $st->fetchAll();

$daftarKelas = $pdo->query(
    "SELECT DISTINCT kelas FROM users WHERE role = 'siswa' AND kelas IS NOT NULL AND kelas <> '' ORDER BY kelas"
)->fetchAll(PDO::FETCH_COLUMN);

guru_head('Siswa', 'siswa', $u);
?>
<div class="pagehead">
  <div>
    <p class="eyebrow">Daftar siswa</p>
    <h1>Siswa</h1>
  </div>
  <a class="btn sm" href="export.php<?= $kelas !== '' ? '?kelas=' . rawurlencode($kelas) : '' ?>">Unduh rekap nilai (CSV)</a>
</div>

<form class="filt" method="get" action="siswa.php">
  <div class="fld">
    <label for="q">Cari nama atau username</label>
    <input id="q" name="q" type="text" value="<?= h($q) ?>" autocomplete="off">
  </div>
  <div class="fld">
    <label for="kelas">Kelas</label>
    <select id="kelas" name="kelas">
      <option value="">Semua kelas</option>
<?php foreach ($daftarKelas as $k): ?>
      <option value="<?= h($k) ?>"<?= $k === $kelas ? ' selected' : '' ?>><?= h($k) ?></option>
<?php endforeach; ?>
    </select>
  </div>
  <button class="btn sm" type="submit">Terapkan</button>
  <?php if ($q !== '' || $kelas !== ''): ?><a class="btn ghost sm" href="siswa.php">Hapus filter</a><?php endif; ?>
</form>

<?php if (!$rows): ?>
  <div class="empty">
    <?= ($q !== '' || $kelas !== '') ? 'Tidak ada siswa yang cocok dengan filter.' : 'Belum ada siswa terdaftar.' ?>
  </div>
<?php else: ?>
  <p class="small"><?= count($rows) ?> siswa. Skor kuis adalah rata-rata kuis bab yang sudah dikerjakan.</p>
  <div class="tbl wide"><table>
    <thead><tr>
      <th scope="col">Nama</th><th scope="col">Kelas</th><th scope="col">Materi selesai</th>
      <th scope="col">Rata-rata kuis</th><th scope="col">Terakhir masuk</th>
    </tr></thead>
    <tbody>
<?php foreach ($rows as $r):
    $p = (int)round(((int)$r['selesai']) / TOTAL_MATERI * 100); ?>
      <tr>
        <td><a href="detail.php?id=<?= (int)$r['id'] ?>"><?= h($r['nama']) ?></a><br><span class="small"><?= h($r['username']) ?></span></td>
        <td><?= h((string)($r['kelas'] ?: '-')) ?></td>
        <td><div class="mini"><div class="bar" role="img" aria-label="<?= $p ?> persen"><i style="width:<?= $p ?>%"></i></div><span><?= (int)$r['selesai'] ?> / <?= TOTAL_MATERI ?></span></div></td>
        <td><?= chip_skor($r['rata']) ?></td>
        <td><?= h(waktu($r['last_login'])) ?></td>
      </tr>
<?php endforeach; ?>
    </tbody>
  </table></div>
<?php endif; ?>
<?php guru_foot();
