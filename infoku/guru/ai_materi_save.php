<?php
declare(strict_types=1);require __DIR__.'/_layout.php';$u=require_login('guru');
if($_SERVER['REQUEST_METHOD']!=='POST'||!csrf_ok($_POST['csrf']??null)){http_response_code(403);exit('Permintaan tidak valid.');}
$id=(string)($_POST['id']??'');$part=json_decode((string)($_POST['content_json']??''),true);if(!preg_match('/^M[1-9]$/',$id)||!is_array($part)){http_response_code(422);exit('Data tidak valid. Periksa hasil AI.');}
$file=__DIR__.'/../data/materi.json';$d=json_decode((string)file_get_contents($file),true);if(!is_array($d)||!is_array($d['materi']??null)){http_response_code(500);exit('Data materi tidak dapat dibaca.');}$found=false;
foreach($d['materi'] as &$m){if(($m['id']??'')!==$id)continue;
foreach(['ringkas','tujuan','istilah','pra','segmen','contoh'] as $k)if(!array_key_exists($k,$part)) {http_response_code(422);exit('Hasil materi belum lengkap: '.$k);} if(!is_array($part['tujuan'])||!is_array($part['istilah'])||!is_array($part['segmen'])||!is_array($part['contoh']['langkah']??null)){http_response_code(422);exit('Struktur materi AI tidak valid.');}
foreach($part as $k=>$v)$m[$k]=$v;$found=true;break;}unset($m);
if(!$found){http_response_code(404);exit('Pertemuan tidak ditemukan.');}$json=json_encode($d,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);if($json===false||file_put_contents($file,$json."\n",LOCK_EX)===false){http_response_code(500);exit('Gagal menyimpan konten AI.');}header('Location: ai_materi.php?id='.urlencode($id).'&saved=1');exit;
