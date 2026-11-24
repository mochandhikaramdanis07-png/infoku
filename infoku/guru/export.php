<?php
declare(strict_types=1);
require __DIR__ . '/_layout.php';

require_login('guru');
$pdo = db();

$kelas = trim((string)($_GET['kelas'] ?? ''));

$sql = "SELECT u.id, u.nama, u.username, u.kelas, u.last_login FROM users u WHERE u.role = 'siswa'";
$par = [];
if ($kelas !== '') {
    $sql .= ' AND u.kelas = ?';
    $par[] = $kelas;
}
$sql .= ' ORDER BY u.kelas, u.nama';
$st = $pdo->prepare($sql);
$st->execute($par);
$siswa = $st->fetchAll();

$selesai = [];
foreach ($pdo->query('SELECT user_id, COUNT(*) AS n FROM materi_progress WHERE selesai = 1 GROUP BY user_id') as $r) {
    $selesai[(int)$r['user_id']] = (int)$r['n'];
}
$kuis = [];
foreach ($pdo->query('SELECT user_id, kuis_id, skor, total FROM kuis_hasil') as $r) {
    $kuis[(int)$r['user_id']][$r['kuis_id']] = (int)round($r['skor'] * 100.0 / $r['total']);
}

/** Mencegah sel yang diawali = + - @ dibaca sebagai rumus oleh Excel. */
function csv_aman($v): string
{
    $s = (string)$v;
    return ($s !== '' && strpbrk($s[0], "=+-@\t\r") !== false) ? "'" . $s : $s;
}

$nama = 'rekap-infoku-' . date('Ymd') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $nama . '"');
header('Cache-Control: no-store');

$out = fopen('php://output', 'w');
fwrite($out, "\xEF\xBB\xBF"); // BOM agar Excel membaca UTF-8
$sep = ';';                    // Excel berbahasa Indonesia memakai titik koma

$head = ['Nama', 'Username', 'Kelas', 'Materi selesai'];
foreach (MATERI_IDS as $m) {
    $head[] = $m . ' (%)';
}
array_push($head, 'Kuis Semester 1 (%)', 'Kuis Semester 2 (%)', 'Rata-rata kuis bab (%)', 'Terakhir masuk');
fputcsv($out, $head, $sep);

foreach ($siswa as $s) {
    $id = (int)$s['id'];
    $k = $kuis[$id] ?? [];
    $row = [csv_aman($s['nama']), csv_aman($s['username']), csv_aman($s['kelas'] ?? ''), $selesai[$id] ?? 0];
    $bab = [];
    foreach (MATERI_IDS as $m) {
        $row[] = $k[$m] ?? '';
        if (isset($k[$m])) {
            $bab[] = $k[$m];
        }
    }
    $row[] = $k['S1'] ?? '';
    $row[] = $k['S2'] ?? '';
    $row[] = $bab ? (int)round(array_sum($bab) / count($bab)) : '';
    $row[] = $s['last_login'] ?? '';
    fputcsv($out, $row, $sep);
}
fclose($out);
