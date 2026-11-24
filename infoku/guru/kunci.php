<?php
declare(strict_types=1);
require __DIR__ . '/_layout.php';

$u = require_login('guru');
$d = data_materi();

guru_head('Materi dan Kunci', 'kunci', $u);
?>
<p class="eyebrow">Bahan guru</p>
<h1>Materi dan Kunci Jawaban</h1>
<p class="lead">Tujuan pembelajaran, istilah kunci, soal latihan dan kuis beserta jawaban dan pembahasannya, serta rubrik proyek. Halaman ini tidak tampil untuk siswa.</p>

<?php if (!$d['materi']): ?>
  <div class="empty">Berkas data/materi.json tidak ditemukan atau tidak terbaca.</div>
<?php endif; ?>

<?php foreach ($d['materi'] as $m): ?>
<details class="kunci">
  <summary><span class="code" style="background:var(--y);color:#0F1923"><?= h($m['id']) ?></span><?= h($m['judul']) ?> <span class="chip">Semester <?= (int)$m['sem'] ?></span></summary>
  <div class="kbody">
    <p><?= h($m['ringkas']) ?></p>
    <h3>Tujuan pembelajaran</h3>
    <ul class="plain">
<?php foreach ($m['tujuan'] as $t): ?>
      <li><?= h($t) ?></li>
<?php endforeach; ?>
    </ul>
    <h3>Istilah kunci</h3>
    <dl class="terms">
<?php foreach ($m['istilah'] as [$ist, $arti]): ?>
      <div><dt><?= h($ist) ?></dt><dd><?= h($arti) ?></dd></div>
<?php endforeach; ?>
    </dl>
    <h3>Soal latihan</h3>
<?php
    $soal = array_merge([['label' => 'Latihan', 'q' => $m['latihan']]],
        array_map(fn($k, $i) => ['label' => 'Kuis ' . ($i + 1), 'q' => $k], $m['kuis'], array_keys($m['kuis'])));
    foreach ($soal as $item):
        $q = $item['q'];
        if ($item['label'] === 'Kuis 1'): ?>
    <h3>Soal kuis</h3>
<?php   endif; ?>
    <div class="q">
      <p><?= h($item['label']) ?>: <?= h($q['q']) ?></p>
      <ol>
<?php   foreach ($q['o'] as $j => $opsi): ?>
        <li<?= $j === (int)$q['a'] ? ' class="benar"' : '' ?>><?= h($opsi) ?><?= $j === (int)$q['a'] ? ' (jawaban benar)' : '' ?></li>
<?php   endforeach; ?>
      </ol>
      <p class="why"><?= h($q['w']) ?></p>
    </div>
<?php endforeach; ?>
  </div>
</details>
<?php endforeach; ?>

<?php if ($d['proyek']): ?>
<h2>Rubrik Proyek Informatika</h2>
<div class="card">
  <h3>Kriteria keberhasilan</h3>
  <ul class="plain">
<?php foreach ($d['kriteria'] as $k): ?>
    <li><?= h($k) ?></li>
<?php endforeach; ?>
  </ul>
  <h3 style="margin-top:18px">Tahapan dan butir yang dicentang siswa</h3>
  <div class="tbl"><table>
    <thead><tr><th scope="col">Tahap</th><th scope="col">Butir</th><th scope="col">Petunjuk untuk siswa</th></tr></thead>
    <tbody>
<?php foreach ($d['proyek'] as $i => $t): ?>
      <tr>
        <td><?= $i + 1 ?>. <?= h($t['h']) ?></td>
        <td><ul class="plain" style="margin:0"><?php foreach ($t['c'] as $c): ?><li><?= h($c) ?></li><?php endforeach; ?></ul></td>
        <td><?= h($t['t']) ?></td>
      </tr>
<?php endforeach; ?>
    </tbody>
  </table></div>
</div>
<?php endif; ?>
<?php guru_foot();
