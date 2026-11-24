<?php
declare(strict_types=1);
require __DIR__ . '/_layout.php';

$u = require_login('guru');
$pdo = db();

$tot = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'siswa'")->fetchColumn();

$st = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'siswa' AND last_login >= ?");
$st->execute([date('Y-m-d H:i:s', time() - 7 * 86400)]);
$aktif = (int)$st->fetchColumn();

$selesai = (int)$pdo->query(
    "SELECT COUNT(*) FROM materi_progress mp JOIN users u ON u.id = mp.user_id
     WHERE u.role = 'siswa' AND mp.selesai = 1"
)->fetchColumn();
$rataProgres = $tot > 0 ? (int)round($selesai / ($tot * TOTAL_MATERI) * 100) : 0;

$rataKuis = $pdo->query(
    "SELECT AVG(kh.skor * 100.0 / kh.total) FROM kuis_hasil kh JOIN users u ON u.id = kh.user_id
     WHERE u.role = 'siswa' AND kh.kuis_id LIKE 'M%'"
)->fetchColumn();
$rataKuis = ($rataKuis === null || $rataKuis === false) ? null : (float)$rataKuis;

$babRendah = $pdo->query(
    "SELECT kh.kuis_id, COUNT(*) AS n, AVG(kh.skor * 100.0 / kh.total) AS rata
     FROM kuis_hasil kh JOIN users u ON u.id = kh.user_id
     WHERE u.role = 'siswa' AND kh.kuis_id LIKE 'M%'
     GROUP BY kh.kuis_id ORDER BY rata ASC LIMIT 3"
)->fetchAll();

$perlu = $pdo->query(
    "SELECT u.id, u.nama, u.kelas, AVG(kh.skor * 100.0 / kh.total) AS rata, COUNT(*) AS n
     FROM users u JOIN kuis_hasil kh ON kh.user_id = u.id AND kh.kuis_id LIKE 'M%'
     WHERE u.role = 'siswa'
     GROUP BY u.id, u.nama, u.kelas
     HAVING AVG(kh.skor * 100.0 / kh.total) < " . KKM . "
     ORDER BY rata ASC LIMIT 6"
)->fetchAll();

$terbaru = $pdo->query(
    "SELECT u.id, u.nama, u.kelas, kh.kuis_id, kh.skor, kh.total, kh.updated_at
     FROM kuis_hasil kh JOIN users u ON u.id = kh.user_id
     WHERE u.role = 'siswa' ORDER BY kh.updated_at DESC LIMIT 8"
)->fetchAll();

$sesiBuka = $pdo->query("SELECT id, judul, tanggal, kelas FROM absensi_sesi WHERE status = 'buka' ORDER BY tanggal DESC, id DESC LIMIT 3")->fetchAll();
$isiBuka = [];
foreach ($pdo->query("SELECT a.sesi_id, COUNT(*) AS n FROM absensi a JOIN absensi_sesi s ON s.id = a.sesi_id WHERE s.status = 'buka' GROUP BY a.sesi_id") as $r) {
    $isiBuka[(int)$r['sesi_id']] = (int)$r['n'];
}

guru_head('Ringkasan', 'index', $u);
?>
<p class="eyebrow">Ringkasan kelas</p>
<h1>Halo, <?= h($u['nama']) ?></h1>
<p class="lead">Gambaran singkat progres belajar dan hasil kuis seluruh siswa.</p>

<?php if ($tot === 0): ?>
  <div class="empty">
    <p style="margin:0 auto 8px"><strong>Belum ada siswa terdaftar.</strong></p>
    <p style="margin:0 auto">Minta siswa membuat akun di halaman siswa. Data mereka akan muncul di sini.</p>
  </div>
<?php else: ?>
  <div class="stats">
    <div class="card stat"><div class="n"><?= $tot ?></div><div class="l">Siswa terdaftar</div></div>
    <div class="card stat"><div class="n"><?= $aktif ?></div><div class="l">Masuk dalam 7 hari terakhir</div></div>
    <div class="card stat"><div class="n"><?= $rataProgres ?>%</div><div class="l">Rata-rata materi selesai</div></div>
    <div class="card stat"><div class="n"><?= $rataKuis === null ? '-' : (int)round($rataKuis) . '%' ?></div><div class="l">Rata-rata skor kuis bab</div></div>
  </div>
  <p class="small">Materi selesai dihitung dari bab yang latihannya sudah diperiksa siswa (maksimal <?= TOTAL_MATERI ?> bab). Ketuntasan kuis memakai ambang <?= KKM ?>%.</p>

  <section class="card" style="margin-bottom:16px">
    <h3>Absensi</h3>
<?php if (!$sesiBuka): ?>
    <p class="small" style="margin:0 0 10px">Tidak ada sesi absensi yang terbuka.</p>
<?php else: ?>
    <ul class="list">
<?php foreach ($sesiBuka as $b): ?>
      <li><span><a href="absensi_detail.php?id=<?= (int)$b['id'] ?>"><?= h($b['judul']) ?></a><br><span class="small"><?= h($b['kelas'] !== '' ? $b['kelas'] : 'Semua kelas') ?> · <?= $isiBuka[(int)$b['id']] ?? 0 ?> siswa sudah mengisi</span></span><span class="chip ok">Terbuka</span></li>
<?php endforeach; ?>
    </ul>
<?php endif; ?>
    <p style="margin:10px 0 0"><a class="btn sm" href="absensi.php">Kelola absensi</a></p>
  </section>

  <div class="cols">
    <section class="card">
      <h3>Bab yang perlu dibahas ulang</h3>
      <?php if (!$babRendah): ?>
        <p class="small" style="margin:0">Belum ada kuis bab yang dikerjakan.</p>
      <?php else: ?>
        <ul class="list">
<?php foreach ($babRendah as $r): ?>
          <li><span><?= h(nama_kuis($r['kuis_id'])) ?><br><span class="small"><?= (int)$r['n'] ?> siswa mengerjakan</span></span><?= chip_skor($r['rata']) ?></li>
<?php endforeach; ?>
        </ul>
        <p class="small" style="margin:10px 0 0"><a href="materi.php">Lihat rekap semua materi</a></p>
      <?php endif; ?>
    </section>

    <section class="card">
      <h3>Siswa yang perlu pendampingan</h3>
      <?php if (!$perlu): ?>
        <p class="small" style="margin:0">Tidak ada siswa dengan rata-rata kuis di bawah <?= KKM ?>%.</p>
      <?php else: ?>
        <ul class="list">
<?php foreach ($perlu as $r): ?>
          <li><span><a href="detail.php?id=<?= (int)$r['id'] ?>"><?= h($r['nama']) ?></a><br><span class="small"><?= h((string)($r['kelas'] ?: 'Tanpa kelas')) ?> · <?= (int)$r['n'] ?> kuis</span></span><?= chip_skor($r['rata']) ?></li>
<?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </section>
  </div>

  <h2>Hasil kuis terbaru</h2>
  <?php if (!$terbaru): ?>
    <div class="empty">Belum ada siswa yang mengerjakan kuis.</div>
  <?php else: ?>
    <div class="tbl wide"><table>
      <thead><tr><th scope="col">Siswa</th><th scope="col">Kelas</th><th scope="col">Kuis</th><th scope="col" class="num">Skor</th><th scope="col">Waktu</th></tr></thead>
      <tbody>
<?php foreach ($terbaru as $r): ?>
        <tr>
          <td><a href="detail.php?id=<?= (int)$r['id'] ?>"><?= h($r['nama']) ?></a></td>
          <td><?= h((string)($r['kelas'] ?: '-')) ?></td>
          <td><?= h(nama_kuis($r['kuis_id'])) ?></td>
          <td class="num"><?= (int)$r['skor'] ?> / <?= (int)$r['total'] ?> <?= chip_skor($r['skor'] * 100.0 / $r['total']) ?></td>
          <td><?= h(waktu($r['updated_at'])) ?></td>
        </tr>
<?php endforeach; ?>
      </tbody>
    </table></div>
  <?php endif; ?>
<?php endif; ?>
<?php guru_foot();
