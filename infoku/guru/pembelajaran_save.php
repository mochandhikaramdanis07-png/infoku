<?php
declare(strict_types=1);
require __DIR__ . '/_layout.php';
$u = require_login('guru');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_ok($_POST['csrf'] ?? null)) { http_response_code(403); exit('Permintaan tidak valid.'); }
$id = (string)($_POST['id'] ?? ''); $raw = (string)($_POST['materi_json'] ?? '');
if (!preg_match('/^M[1-9]$/', $id)) { http_response_code(422); exit('ID materi tidak valid.'); }
$item = json_decode($raw, true);
if (!is_array($item) || ($item['id'] ?? '') !== $id || !isset($item['judul'],$item['ringkas'],$item['tujuan'],$item['segmen'],$item['contoh'],$item['latihan'],$item['kuis']) || !is_array($item['kuis']) || count($item['kuis']) < 1) { http_response_code(422); exit('JSON materi tidak valid atau ada bagian wajib yang hilang.'); }
foreach (array_merge([$item['latihan']], $item['kuis']) as $q) { if (!isset($q['q'],$q['o'],$q['a'],$q['w']) || !is_array($q['o']) || count($q['o']) !== 4 || (int)$q['a'] < 0 || (int)$q['a'] > 3) { http_response_code(422); exit('Format soal harus memiliki empat opsi dan kunci 0–3.'); } }
$file = __DIR__ . '/../data/materi.json'; $d = json_decode((string)file_get_contents($file), true);
if (!is_array($d) || !is_array($d['materi'] ?? null)) { http_response_code(500); exit('Data materi utama tidak dapat dibaca.'); }
$found = false; foreach ($d['materi'] as $i => $old) { if (($old['id'] ?? '') === $id) { $item['sem'] = (int)($old['sem'] ?? $item['sem'] ?? 1); $d['materi'][$i] = $item; $found = true; break; } }
if (!$found) { http_response_code(404); exit('Materi tidak ditemukan.'); }
$json = json_encode($d, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
if ($json === false || file_put_contents($file, $json."\n", LOCK_EX) === false) { http_response_code(500); exit('Gagal menyimpan. Pastikan folder data dapat ditulis oleh Apache.'); }
header('Location: pembelajaran.php?id='.urlencode($id).'&saved=1'); exit;
