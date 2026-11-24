<?php
declare(strict_types=1);
require __DIR__ . '/_layout.php';

$u = require_login('guru');
$pdo = db();

$id = (int)($_GET['id'] ?? 0);
$st = $pdo->prepare('SELECT id, judul, tanggal, kelas, kode, status FROM absensi_sesi WHERE id = ?');
$st->execute([$id]);
$x = $st->fetch();
if (!$x) {
    http_response_code(404);
    guru_head('Sesi tidak ditemukan', 'absensi', $u);
    echo '<h1>Sesi tidak ditemukan</h1><p><a class="btn" href="absensi.php">Kembali ke daftar sesi</a></p>';
    guru_foot();
    exit;
}

$flash = '';
$err = '';
$edit = isset($_GET['ubah']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = (string)($_POST['aksi'] ?? '');
    if (!csrf_ok($_POST['csrf'] ?? null)) {
        $err = 'Sesi formulir kedaluwarsa. Muat ulang halaman lalu coba lagi.';
    } elseif ($aksi === 'buka' || $aksi === 'tutup') {
        $pdo->prepare('UPDATE absensi_sesi SET status = ? WHERE id = ?')->execute([$aksi, $id]);
        $x['status'] = $aksi;
        $flash = $aksi === 'tutup'
            ? 'Sesi ditutup. Siswa yang belum mengisi dihitung alpa.'
            : 'Sesi dibuka kembali. Siswa bisa mengisi lagi.';
    } elseif ($aksi === 'kode') {
        $kode = kode_sesi();
        $pdo->prepare('UPDATE absensi_sesi SET kode = ? WHERE id = ?')->execute([$kode, $id]);
        $x['kode'] = $kode;
        $flash = 'Kode baru dibuat. Siswa yang sudah mengisi tidak terpengaruh.';
    } elseif ($aksi === 'hapus') {
        $pdo->prepare('DELETE FROM absensi_sesi WHERE id = ?')->execute([$id]);
        header('Location: absensi.php');
        exit;
    } elseif ($aksi === 'simpan') {
        $stat = $_POST['status'] ?? [];
        $cat = $_POST['catatan'] ?? [];
        $ok = array_keys(ABSEN_STATUS);
        $pdo->beginTransaction();
        $del = $pdo->prepare('DELETE FROM absensi WHERE sesi_id = ? AND user_id = ?');
        $ins = $pdo->prepare('INSERT INTO absensi (sesi_id, user_id, status, catatan, waktu, oleh_guru) VALUES (?, ?, ?, ?, ?, 1)');
        $nUbah = 0;
        foreach (siswa_sesi($pdo, $x['kelas']) as $s) {
            $uid = (int)$s['id'];
            $v = is_array($stat) ? (string)($stat[$uid] ?? '') : '';
            $c = is_array($cat) ? mb_substr(trim((string)($cat[$uid] ?? '')), 0, 200) : '';
            $lama = $pdo->prepare('SELECT status, catatan FROM absensi WHERE sesi_id = ? AND user_id = ?');
            $lama->execute([$id, $uid]);
            $l = $lama->fetch();
            $sama = $l ? ($l['status'] === $v && (string)$l['catatan'] === $c) : ($v === '' && $c === '');
            if ($sama) {
                continue;
            }
            $del->execute([$id, $uid]);
            if (in_array($v, $ok, true)) {
                $ins->execute([$id, $uid, $v, $c !== '' ? $c : null, date('Y-m-d H:i:s')]);
            }
            $nUbah++;
        }
        $pdo->commit();
        $flash = $nUbah ? "Perubahan tersimpan untuk $nUbah siswa." : 'Tidak ada yang berubah.';
        $edit = false;
    }
}

$siswa = siswa_sesi($pdo, $x['kelas']);
$rows = [];
$q = $pdo->prepare('SELECT user_id, status, catatan, waktu, oleh_guru FROM absensi WHERE sesi_id = ?');
$q->execute([$id]);
foreach ($q as $r) {
    $rows[(int)$r['user_id']] = $r;
}
$n = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0, 'belum' => 0];
foreach ($siswa as $s) {
    $r = $rows[(int)$s['id']] ?? null;
    if ($r) {
        $n[$r['status']]++;
    } else {
        $n['belum']++;
    }
}
$live = $x['status'] === 'buka' && !$edit;

guru_head($x['judul'], 'absensi', $u, '<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js?v=20261120-2" defer></script>' . ($live ? '<meta http-equiv="refresh" content="15">' : ''));
?>
<p class="small" style="margin-bottom:6px"><a href="absensi.php">&larr; Daftar sesi</a></p>
<div class="pagehead">
  <div>
    <p class="eyebrow"><?= h(date('d/m/Y', strtotime($x['tanggal']))) ?> · <?= h($x['kelas'] !== '' ? $x['kelas'] : 'Semua kelas') ?></p>
    <h1><?= h($x['judul']) ?></h1>
  </div>
  <a class="btn sm" href="absensi_export.php?id=<?= (int)$x['id'] ?>">Unduh sesi ini (CSV)</a>
</div>

<?php if ($flash): ?><p class="flash" role="status"><?= h($flash) ?></p><?php endif; ?>
<?php if ($err): ?><p class="fb bad" role="alert"><?= h($err) ?></p><?php endif; ?>

<div class="cols">
  <section class="card kodebox">
    <h3>QR absensi</h3>
<?php if ($x['status'] === 'buka'): ?>
    <p class="small" style="margin:0">Tampilkan QR ini di kelas. Siswa memindainya melalui kamera pada halaman Absensi. Kode tidak perlu dibacakan atau diketik.</p>
    <div id="absen-qr" data-payload="<?= h(json_encode(['app' => 'infoku', 'sesi' => (int)$x['id'], 'kode' => $x['kode']], JSON_UNESCAPED_SLASHES)) ?>" style="display:flex;justify-content:center;padding:14px;background:#fff;border-radius:12px;margin:14px auto;max-width:230px;min-height:190px;align-items:center"><span style="color:#333">Memuat QR…</span></div>
<?php else: ?>
    <p class="small" style="margin:0">Sesi ditutup. QR tidak berlaku lagi.</p>
<?php endif; ?>
    <div class="acts">
      <form method="post"><input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
<?php if ($x['status'] === 'buka'): ?>
        <input type="hidden" name="aksi" value="tutup"><button class="btn sm" type="submit">Tutup sesi</button>
      </form>
      <form method="post"><input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>"><input type="hidden" name="aksi" value="kode"><button class="btn ghost sm" type="submit">Buat QR baru</button></form>
<?php else: ?>
        <input type="hidden" name="aksi" value="buka"><button class="btn sm" type="submit">Buka kembali</button>
      </form>
<?php endif; ?>
    </div>
  </section>
  <section class="card">
    <h3>Ringkasan</h3>
    <dl class="dl">
      <dt>Hadir</dt><dd><?= $n['hadir'] ?> dari <?= count($siswa) ?> siswa</dd>
      <dt>Belum hadir</dt><dd><?= $n['belum'] ?></dd>
      <dt>Alpa</dt><dd><?= $n['alpa'] + ($x['status'] === 'tutup' ? $n['belum'] : 0) ?></dd>
      <dt>Belum mengisi</dt><dd><?= $x['status'] === 'buka' ? $n['belum'] : 0 ?></dd>
    </dl>
<?php if ($live): ?>
    <p class="small" style="margin:8px 0 0">Halaman ini menyegarkan diri tiap 15 detik selama sesi terbuka.</p>
<?php endif; ?>
  </section>
</div>

<h2>Daftar siswa</h2>
<?php if (!$siswa): ?>
  <div class="empty">Belum ada siswa di kelas ini.</div>
<?php elseif ($edit): ?>
<form method="post">
  <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
  <input type="hidden" name="aksi" value="simpan">
  <div class="tbl wide"><table>
    <thead><tr><th scope="col">Siswa</th><th scope="col">Status</th></tr></thead>
    <tbody>
<?php foreach ($siswa as $s):
    $r = $rows[(int)$s['id']] ?? null; $cur = $r['status'] ?? ''; ?>
      <tr>
        <td><?= h($s['nama']) ?><br><span class="small"><?= h((string)($s['kelas'] ?: '-')) ?></span></td>
        <td>
          <label class="sr" for="st<?= (int)$s['id'] ?>">Status <?= h($s['nama']) ?></label>
          <select id="st<?= (int)$s['id'] ?>" name="status[<?= (int)$s['id'] ?>]">
            <option value="">Belum mengisi</option>
            <option value="hadir"<?= $cur === 'hadir' ? ' selected' : '' ?>>Hadir</option>
          </select>
        </td>
      </tr>
<?php endforeach; ?>
    </tbody>
  </table></div>
  <p><button class="btn" type="submit">Simpan perubahan</button> <a class="btn ghost" href="absensi_detail.php?id=<?= (int)$x['id'] ?>">Batal</a></p>
</form>
<?php else: ?>
  <div class="tbl wide"><table>
    <thead><tr><th scope="col">Siswa</th><th scope="col">Status</th><th scope="col">Waktu isi</th></tr></thead>
    <tbody>
<?php foreach ($siswa as $s):
    $r = $rows[(int)$s['id']] ?? null;
    $eff = $r ? $r['status'] : ($x['status'] === 'tutup' ? 'alpa' : ''); ?>
      <tr>
        <td><a href="detail.php?id=<?= (int)$s['id'] ?>"><?= h($s['nama']) ?></a><br><span class="small"><?= h((string)($s['kelas'] ?: '-')) ?></span></td>
        <td><?= $eff === 'hadir' ? chip_absen('hadir') : '<span class="chip">Belum hadir</span>' ?></td>
        <td><?= $r ? h(waktu($r['waktu'])) . ($r['oleh_guru'] ? ' <span class="small">(diubah guru)</span>' : '') : '-' ?></td>
      </tr>
<?php endforeach; ?>
    </tbody>
  </table></div>
  <p><a class="btn sm" href="absensi_detail.php?id=<?= (int)$x['id'] ?>&amp;ubah=1">Ubah status siswa</a></p>
<?php endif; ?>

<h2>Hapus sesi</h2>
<section class="card resetbox">
  <p class="small" style="margin-top:0">Menghapus sesi juga menghapus semua isian kehadiran di dalamnya. Tidak bisa dibatalkan.</p>
  <form method="post" onsubmit="return confirm('Hapus sesi ini beserta semua isiannya?')">
    <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
    <input type="hidden" name="aksi" value="hapus">
    <button class="btn ghost sm" type="submit">Hapus sesi</button>
  </form>
</section>
<script>
(function(){var el=document.getElementById('absen-qr');if(!el)return;var draw=function(){if(!window.QRCode)return false;el.innerHTML='';new QRCode(el,{text:el.dataset.payload,width:190,height:190,colorDark:'#111111',colorLight:'#ffffff',correctLevel:QRCode.CorrectLevel.M});return true;};if(!draw()){var n=0,t=setInterval(function(){if(draw()||++n>30)clearInterval(t);},150);}})();
</script>
<?php guru_foot();
