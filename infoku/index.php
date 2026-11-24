<?php
declare(strict_types=1);
require __DIR__ . '/db.php';

// Hindari browser memakai JavaScript absensi versi lama dari cache.
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

$user = require_login();
$materiFile = __DIR__ . '/data/materi.json';
$materiData = is_file($materiFile) ? json_decode((string)file_get_contents($materiFile), true) : [];
$boot = [
    'materi' => is_array($materiData['materi'] ?? null) ? $materiData['materi'] : [],
    'user'     => ['nama' => $user['nama'], 'username' => $user['username'], 'kelas' => $user['kelas']],
    'csrf'     => csrf_token(),
    'progress' => load_progress((int)$user['id']),
    'absensi'  => load_absensi((int)$user['id'], $user['kelas']),
];
?><!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="color-scheme" content="light dark">
<title>infoKu</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Familjen+Grotesk:wght@500;600;700&family=Instrument+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap">
<link rel="stylesheet" href="assets/app.css">
</head>
<body>
<div id="app"></div>
<script>window.INFOKU = <?= json_encode($boot, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;</script>
<script src="assets/app.js?v=20261121-istilah-kontekstual"></script>
</body>
</html>
