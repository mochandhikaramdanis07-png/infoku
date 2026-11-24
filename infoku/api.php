<?php
declare(strict_types=1);
require __DIR__ . '/db.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function out(int $code, array $body): void
{
    http_response_code($code);
    echo json_encode($body, JSON_UNESCAPED_UNICODE);
    exit;
}

if (empty($_SESSION['uid'])) {
    out(401, ['error' => 'Belum masuk.']);
}
$uid = (int)$_SESSION['uid'];

$action = (string)($_GET['action'] ?? '');
$method = $_SERVER['REQUEST_METHOD'];

/* ---------- Absensi ---------- */
if ($action === 'absensi' || $action === 'absen') {
    $st = db()->prepare('SELECT id, kelas, role FROM users WHERE id = ?');
    $st->execute([$uid]);
    $me = $st->fetch();
    if (!$me || $me['role'] !== 'siswa') {
        out(403, ['error' => 'Khusus akun siswa.']);
    }
    if ($action === 'absensi' && $method === 'GET') {
        out(200, load_absensi($uid, $me['kelas']));
    }
    if ($action === 'absen' && $method === 'POST') {
        if (!csrf_ok($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null)) {
            out(403, ['error' => 'Token tidak valid.']);
        }
        $in = json_decode((string)file_get_contents('php://input'), true);
        if (!is_array($in)) {
            out(400, ['error' => 'Data tidak valid.']);
        }
        $sid = (int)($in['sesi'] ?? 0);
        $status = (string)($in['status'] ?? '');
        $kode = strtoupper(trim((string)($in['kode'] ?? '')));
        if ($status !== 'hadir') {
            out(422, ['error' => 'Kehadiran siswa hanya dapat dicatat melalui QR.']);
        }
        $q = db()->prepare('SELECT id, kode, kelas, status FROM absensi_sesi WHERE id = ?');
        $q->execute([$sid]);
        $s = $q->fetch();
        if (!$s || ($s['kelas'] !== '' && $s['kelas'] !== (string)$me['kelas'])) {
            out(404, ['error' => 'Sesi absensi tidak ditemukan.']);
        }
        if ($s['status'] !== 'buka') {
            out(409, ['error' => 'Sesi ini sudah ditutup guru.']);
        }
        // Token QR sesi diverifikasi server; kode tidak pernah ditampilkan sebagai teks kepada siswa.
        $gagal = $_SESSION['absen_gagal'] ?? ['n' => 0, 't' => 0];
        if ($gagal['n'] >= 8 && time() - $gagal['t'] < 300) {
            out(429, ['error' => 'Terlalu banyak pemindaian tidak valid. Tunggu beberapa menit.']);
        }
        if (!hash_equals($s['kode'], $kode)) {
            $_SESSION['absen_gagal'] = ['n' => ($gagal['n'] >= 8 ? 0 : $gagal['n']) + 1, 't' => time()];
            out(422, ['error' => 'QR tidak valid atau bukan untuk sesi ini. Pindai QR dari guru.']);
        }
        unset($_SESSION['absen_gagal']);
        $ada = db()->prepare('SELECT 1 FROM absensi WHERE sesi_id = ? AND user_id = ?');
        $ada->execute([$sid, $uid]);
        if ($ada->fetch()) {
            out(409, ['error' => 'Kamu sudah mengisi absensi sesi ini.']);
        }
        db()->prepare('INSERT INTO absensi (sesi_id, user_id, status, catatan, waktu, oleh_guru) VALUES (?, ?, ?, ?, ?, 0)')
            ->execute([$sid, $uid, 'hadir', null, date('Y-m-d H:i:s')]);
        out(200, ['ok' => true] + load_absensi($uid, $me['kelas']));
    }
    out(405, ['error' => 'Metode tidak didukung.']);
}

if ($action !== 'save' || $method !== 'POST') {
    out(404, ['error' => 'Tidak ditemukan.']);
}
if (!csrf_ok($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null)) {
    out(403, ['error' => 'Token tidak valid.']);
}

$in = json_decode((string)file_get_contents('php://input'), true);
if (!is_array($in)) {
    out(400, ['error' => 'Data tidak valid.']);
}

$flag = function (string $key) use ($in): array {
    return (isset($in[$key]) && is_array($in[$key])) ? $in[$key] : [];
};
$sel = $flag('sel');
$lat = $flag('lat');
$mulai = $flag('mulai');
$kuis = $flag('kuis');

$kuisIds = array_merge(MATERI_IDS, ['S1', 'S2']);

$pdo = db();
try {
    $pdo->beginTransaction();

    // Status materi
    $pdo->prepare('DELETE FROM materi_progress WHERE user_id = ?')->execute([$uid]);
    $ins = $pdo->prepare('INSERT INTO materi_progress (user_id, materi_id, mulai, latihan, selesai) VALUES (?, ?, ?, ?, ?)');
    foreach (MATERI_IDS as $m) {
        $a = !empty($mulai[$m]) ? 1 : 0;
        $b = !empty($lat[$m]) ? 1 : 0;
        $c = !empty($sel[$m]) ? 1 : 0;
        if ($a || $b || $c) {
            $ins->execute([$uid, $m, $a, $b, $c]);
        }
    }

    // Skor kuis
    $pdo->prepare('DELETE FROM kuis_hasil WHERE user_id = ?')->execute([$uid]);
    $ik = $pdo->prepare('INSERT INTO kuis_hasil (user_id, kuis_id, skor, total) VALUES (?, ?, ?, ?)');
    foreach ($kuisIds as $id) {
        if (isset($kuis[$id]['s'], $kuis[$id]['t'])) {
            $s = (int)$kuis[$id]['s'];
            $t = (int)$kuis[$id]['t'];
            if ($t >= 1 && $t <= 40 && $s >= 0 && $s <= $t) {
                $ik->execute([$uid, $id, $s, $t]);
            }
        }
    }

    // Posisi terakhir
    $pdo->prepare('DELETE FROM posisi_terakhir WHERE user_id = ?')->execute([$uid]);
    $last = $in['last'] ?? null;
    if (is_array($last)
        && in_array($last['id'] ?? null, MATERI_IDS, true)
        && in_array($last['step'] ?? null, LANGKAH_IDS, true)) {
        $seg = max(0, min(9, (int)($last['seg'] ?? 0)));
        $pdo->prepare('INSERT INTO posisi_terakhir (user_id, materi_id, langkah, bagian) VALUES (?, ?, ?, ?)')
            ->execute([$uid, $last['id'], $last['step'], $seg]);
    }

    // Proyek
    $chk = [];
    $proj = (isset($in['proj']) && is_array($in['proj'])) ? $in['proj'] : [];
    if (isset($proj['chk']) && is_array($proj['chk'])) {
        foreach ($proj['chk'] as $k => $val) {
            if (is_string($k) && preg_match('/^p[0-4]_[0-3]$/', $k) && $val) {
                $chk[$k] = true;
            }
        }
    }
    $ref = isset($proj['ref']) ? mb_substr((string)$proj['ref'], 0, 5000) : '';
    $pdo->prepare('DELETE FROM proyek_progress WHERE user_id = ?')->execute([$uid]);
    if ($chk || $ref !== '') {
        $pdo->prepare('INSERT INTO proyek_progress (user_id, checklist, refleksi) VALUES (?, ?, ?)')
            ->execute([$uid, json_encode($chk), $ref]);
    }

    $pdo->commit();
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    out(500, ['error' => 'Gagal menyimpan.']);
}

out(200, ['ok' => true]);
