<?php
declare(strict_types=1);
require __DIR__ . '/_layout.php';

require_login('guru');
$pdo = db();

function csv_aman2($v): string
{
    $s = (string)$v;
    return ($s !== '' && strpbrk($s[0], "=+-@\t\r") !== false) ? "'" . $s : $s;
}

$id = (int)($_GET['id'] ?? 0);
$out = null;
$buka = function (string $nama) use (&$out): void {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $nama . '"');
    header('Cache-Control: no-store');
    $out = fopen('php://output', 'w');
    fwrite($out, "\xEF\xBB\xBF");
};
$sep = ';';

if ($id > 0) {
    // Satu sesi: siapa saja, statusnya, catatan, dan waktu isi.
    $st = $pdo->prepare('SELECT id, judul, tanggal, kelas, status FROM absensi_sesi WHERE id = ?');
    $st->execute([$id]);
    $x = $st->fetch();
    if (!$x) {
        http_response_code(404);
        exit('Sesi tidak ditemukan.');
    }
    $rows = [];
    $q = $pdo->prepare('SELECT user_id, status, catatan, waktu, oleh_guru FROM absensi WHERE sesi_id = ?');
    $q->execute([$id]);
    foreach ($q as $r) {
        $rows[(int)$r['user_id']] = $r;
    }
    $buka('absensi-' . $x['tanggal'] . '-' . $id . '.csv');
    fputcsv($out, ['Nama', 'Username', 'Kelas', 'Status', 'Catatan', 'Waktu isi', 'Diubah guru'], $sep);
    foreach (siswa_sesi($pdo, $x['kelas']) as $s) {
        $r = $rows[(int)$s['id']] ?? null;
        $eff = $r ? ABSEN_STATUS[$r['status']] : ($x['status'] === 'tutup' ? 'Alpa' : 'Belum mengisi');
        fputcsv($out, [csv_aman2($s['nama']), csv_aman2($s['username']), csv_aman2($s['kelas'] ?? ''), $eff,
            csv_aman2($r['catatan'] ?? ''), $r['waktu'] ?? '', $r ? ($r['oleh_guru'] ? 'Ya' : 'Tidak') : ''], $sep);
    }
    fclose($out);
    exit;
}

// Semua sesi: satu baris per siswa, satu kolom per pertemuan.
$kelas = trim((string)($_GET['kelas'] ?? ''));
$rek = rekap_kehadiran($pdo, $kelas);
$buka('rekap-kehadiran-' . date('Ymd') . '.csv');
$head = ['Nama', 'Username', 'Kelas'];
foreach ($rek['sesi'] as $x) {
    $head[] = csv_aman2(date('d/m/Y', strtotime($x['tanggal'])) . ' ' . $x['judul']);
}
array_push($head, 'Hadir', 'Izin', 'Sakit', 'Alpa', 'Kehadiran (%)');
fputcsv($out, $head, $sep);
foreach ($rek['siswa'] as $s) {
    $row = [csv_aman2($s['nama']), csv_aman2($s['username']), csv_aman2($s['kelas'] ?? '')];
    foreach ($rek['sesi'] as $x) {
        $v = $s['kolom'][(int)$x['id']] ?? null;
        $row[] = $v === null ? '' : ($v === '' ? 'Belum' : ABSEN_STATUS[$v]);
    }
    array_push($row, $s['n']['hadir'], $s['n']['izin'], $s['n']['sakit'], $s['n']['alpa'], $s['persen'] ?? '');
    fputcsv($out, $row, $sep);
}
fclose($out);
