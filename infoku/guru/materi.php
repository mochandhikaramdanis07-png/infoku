<?php
declare(strict_types=1);
require __DIR__ . '/_layout.php';

$u = require_login('guru');
$pdo = db();

$tot = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'siswa'")->fetchColumn();

$mp = [];
foreach ($pdo->query(
    "SELECT mp.materi_id, SUM(mp.mulai) AS mulai, SUM(mp.selesai) AS selesai
     FROM materi_progress mp JOIN users u ON u.id = mp.user_id
     WHERE u.role = 'siswa' GROUP BY mp.materi_id"
) as $r) {
    $mp[$r['materi_id']] = $r;
}

$kh = [];
foreach ($pdo->query(
    "SELECT kh.kuis_id, COUNT(*) AS n, AVG(kh.skor * 100.0 / kh.total) AS rata,
            SUM(CASE WHEN kh.skor * 100.0 / kh.total >= " . KKM . " THEN 1 ELSE 0 END) AS tuntas
     FROM kuis_hasil kh JOIN users u ON u.id = kh.user_id
     WHERE u.role = 'siswa' GROUP BY kh.kuis_id"
) as $r) {
    $kh[$r['kuis_id']] = $r;
}

$ids = array_merge(MATERI_IDS, ['S1', 'S2']);

guru_head('Rekap Materi', 'materi', $u);
?>
<p class="eyebrow">Rekap per materi</p>
<h1>Rekap Materi</h1>
<p class="lead">Bab mana yang sudah dikuasai dan bab mana yang perlu dibahas ulang di kelas.</p>

<?php if ($tot === 0): ?>
  <div class="empty">Belum ada siswa terdaftar, jadi belum ada data untuk direkap.</div>
<?php else: ?>
  <p class="small">Dari <?= $tot ?> siswa. Sudah mulai berarti membuka materi. Selesai berarti latihan sudah diperiksa. Tuntas berarti skor kuis minimal <?= KKM ?>%. Bab dengan rata-rata di bawah <?= KKM ?>% diberi tanda merah atau kuning.</p>
  <div class="tbl wide"><table>
    <thead><tr>
      <th scope="col">Materi</th><th scope="col" class="num">Sudah mulai</th><th scope="col" class="num">Selesai</th>
      <th scope="col" class="num">Mengerjakan kuis</th><th scope="col">Rata-rata skor</th><th scope="col" class="num">Tuntas</th>
    </tr></thead>
    <tbody>
<?php foreach ($ids as $id):
    $isMateri = $id[0] === 'M';
    $a = $mp[$id] ?? null;
    $k = $kh[$id] ?? null; ?>
      <tr>
        <td><?= h(nama_kuis($id)) ?></td>
<?php if ($isMateri): ?>
        <td class="num"><?= $a ? (int)$a['mulai'] : 0 ?></td>
        <td class="num"><?= $a ? (int)$a['selesai'] : 0 ?></td>
<?php else: ?>
        <td class="num">-</td><td class="num">-</td>
<?php endif; ?>
        <td class="num"><?= $k ? (int)$k['n'] : 0 ?></td>
        <td><?= $k ? chip_skor($k['rata']) : '<span class="chip">Belum ada</span>' ?></td>
        <td class="num"><?= $k ? (int)$k['tuntas'] . ' dari ' . (int)$k['n'] : '-' ?></td>
      </tr>
<?php endforeach; ?>
    </tbody>
  </table></div>
  <p class="small">Skor yang dihitung adalah hasil kuis terakhir tiap siswa. Rincian jawaban per soal tidak disimpan, sehingga soal yang paling sering salah belum bisa ditampilkan.</p>
<?php endif; ?>
<?php guru_foot();
