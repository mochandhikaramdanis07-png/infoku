<?php
declare(strict_types=1);
require __DIR__ . '/_layout.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
function ai_out(int $status, array $data): never {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}
$u = require_login('guru');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_ok($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null)) {
    ai_out(403, ['error'=>'Sesi keamanan tidak valid. Muat ulang halaman lalu coba lagi.']);
}
$cfgFile = __DIR__ . '/../ai_config.php';
if (!is_file($cfgFile) || !is_readable($cfgFile)) ai_out(503, ['error'=>'File konfigurasi tidak ditemukan. Pastikan namanya tepat ai_config.php (bukan ai_config.php.txt) dan letaknya langsung di htdocs/infoku/, sejajar dengan index.php.']);
try { $cfg = require $cfgFile; } catch (Throwable $e) { ai_out(503, ['error'=>'Ada kesalahan pada file ai_config.php.']); }
if (!is_array($cfg)) ai_out(503, ['error'=>'ai_config.php harus mengembalikan array konfigurasi.']);
$key = trim((string)($cfg['api_key'] ?? ''));
if ($key === '' || str_contains($key, 'ISI_API_KEY') || str_contains($key, 'TEMPEL_API_KEY')) ai_out(503, ['error'=>'API key Gemini belum ditempel. Buka htdocs/infoku/ai_config.php lalu ganti TEMPEL_API_KEY_GEMINI_DI_SINI dengan API key dari Google AI Studio.']);
if (!function_exists('curl_init')) ai_out(503, ['error'=>'PHP cURL belum aktif. Aktifkan extension=curl di php.ini dan restart Apache.']);
$in = json_decode((string)file_get_contents('php://input'), true);
if (!is_array($in)) ai_out(400, ['error'=>'Data permintaan tidak valid.']);
$id = (string)($in['id'] ?? '');
$judul = trim((string)($in['judul'] ?? ''));
$kelas = trim((string)($in['kelas'] ?? 'SMK Kelas X'));
$mapel = trim((string)($in['mapel'] ?? ''));
if ($mapel === '') $mapel = 'Mata pelajaran umum';
$topik = trim((string)($in['topik'] ?? ''));
$mode = (string)($in['mode'] ?? '');
$sem = (int)($in['sem'] ?? 1);
$jumlah = max(3, min(15, (int)($in['jumlah_soal'] ?? 5)));
if (!preg_match('/^M[1-9]$/', $id) || $judul === '' || $topik === '' || !in_array($mode,['materi','kuis'],true)) ai_out(422,['error'=>'Bab, topik, atau jenis generator tidak valid.']);
$file = __DIR__ . '/../data/materi.json';
$all = json_decode((string)@file_get_contents($file), true);
$current = null;
foreach (($all['materi'] ?? []) as $m) if (($m['id'] ?? '') === $id) { $current = $m; break; }
if (!$current) ai_out(404,['error'=>'Bab materi tidak ditemukan.']);
$model = trim((string)($cfg['model'] ?? 'gemini-3.5-flash-lite'));
if ($model === '') $model = 'gemini-3.5-flash-lite';
if (!preg_match('/^[A-Za-z0-9._-]+$/',$model)) ai_out(503,['error'=>'Nama model Gemini tidak valid.']);
$mc = is_array($cfg['materi'] ?? null) ? $cfg['materi'] : [];
$nSeg  = max(3, min(10, (int)($mc['segmen'] ?? 7)));
$nPar  = trim((string)($mc['paragraf'] ?? '4-5')); if (!preg_match('/^\d(-\d)?$/', $nPar)) $nPar = '4-5';
$nIst  = max(4, min(15, (int)($mc['istilah'] ?? 10)));
$nTuj  = max(3, min(8,  (int)($mc['tujuan'] ?? 6)));
$nLang = max(4, min(8,  (int)($mc['langkah_contoh'] ?? 6)));
$minSeg = max(1, min($nSeg, (int)($mc['min_segmen'] ?? 5)));
$temp  = max(0.0, min(1.0, (float)($cfg['temperature'] ?? 0.5)));
$maxTok = max(8000, min(60000, (int)($cfg['max_output_tokens'] ?? 24000)));
$timeout = max(60, min(300, (int)($cfg['timeout'] ?? 180)));
if ($mode === 'materi') {
  $prompt = "Kamu adalah guru berpengalaman yang menulis bahan ajar lengkap berbahasa Indonesia. Tulis materi untuk satu pertemuan pada mata pelajaran {$mapel}, jenjang {$kelas}. Pertemuan {$id}, semester {$sem}, judul pertemuan: {$judul}. Topik fokus: {$topik}.\n\n"
   . "PRINSIP ISI\n"
   . "- Materi harus sesuai mata pelajaran yang dipilih. Jangan mengaitkan ke Informatika atau teknologi kecuali memang relevan dengan mata pelajaran dan topik.\n"
   . "- Isi harus luas dan mendalam, bukan ringkasan: jelaskan konsep dari dasar, beri alasan (mengapa), beri contoh konkret dari kehidupan sehari-hari, dunia kerja SMK, atau lingkungan sekitar siswa, lalu hubungkan antarkonsep.\n"
   . "- Urutan penyajian: pengantar dan makna -> konsep inti -> pembahasan rinci -> perbandingan atau kekeliruan umum -> penerapan -> manfaat dan refleksi.\n"
   . "- Hanya tulis fakta yang kamu yakini benar (nama, tahun, angka, rumus, istilah). Bila ragu, gunakan rumusan umum tanpa angka spesifik. Jangan mengarang sumber atau kutipan.\n"
   . "- Bahasa jelas, runtut, sesuai tingkat siswa SMK, tidak bertele-tele.\n\n"
   . "STRUKTUR JSON (keluarkan JSON valid saja, tanpa markdown): {\"ringkas\":string,\"tujuan\":[string],\"istilah\":[[string,string,string]],\"pra\":string,\"segmen\":[{\"h\":string,\"p\":[string],\"k\":[string],\"i\":[string],\"vis\":{...}}],\"contoh\":{\"judul\":string,\"konteks\":string,\"langkah\":[{\"h\":string,\"t\":string}]}}\n\n"
   . "KETENTUAN JUMLAH DAN PANJANG\n"
   . "- ringkas: 1-2 kalimat tentang isi pertemuan.\n"
   . "- tujuan: tepat {$nTuj} butir, diawali kata kerja operasional (menjelaskan, menyusun, menganalisis, dan sejenisnya).\n"
   . "- istilah: tepat {$nIst} butir berbentuk [istilah, definisi satu kalimat, contoh singkat satu kalimat yang konkret].\n"
   . "- Bagi istilah: 4-5 istilah paling inti dibiarkan tanpa penempatan (tampil di awal). Istilah lainnya ditempatkan pada bagian yang paling relevan lewat field i di segmen, berisi nama istilah persis sama dengan yang ada di daftar istilah. Satu istilah hanya boleh muncul di satu segmen.\n"
   . "- pra: satu paragraf pengetahuan awal berisi analogi atau pertanyaan pemantik, lalu 2-3 hal yang perlu diingat sebelum belajar.\n"
   . "- segmen: tepat {$nSeg} bagian. Setiap bagian punya h (judul bagian), p (array {$nPar} paragraf, tiap paragraf 3-5 kalimat), k (array 3-4 kata kunci), dan vis.\n"
   . "- k: kata atau frasa PENDEK (1-3 kata) yang benar-benar muncul persis di salah satu paragraf bagian itu, tanpa tanda kurung atau simbol khusus.\n"
   . "- vis wajib ada di setiap bagian dan jenisnya divariasikan, salah satu dari: {\"t\":\"flow\",\"items\":[3-5 string pendek]}, {\"t\":\"tree\",\"root\":string,\"kids\":[3-5 string]}, {\"t\":\"table\",\"head\":[string,string],\"rows\":[[string,string]]} (3-6 baris), atau {\"t\":\"code\",\"x\":string} hanya bila mata pelajaran memang memakai kode atau rumus. Isi vis harus akurat dan menjelaskan isi bagian itu.\n"
   . "- contoh: satu studi kasus kontekstual dengan judul, konteks (2-3 kalimat), dan tepat {$nLang} langkah; tiap langkah punya h (judul singkat) dan t (1-2 kalimat penjelasan).\n"
   . "- Pastikan seluruh string di JSON aman: tanpa baris baru mentah di dalam string, dan tanda kutip ganda di dalam teks ditulis dengan escape.";
} else {
  $prompt = "Buat soal latihan dan kuis untuk mata pelajaran {$mapel} berbahasa Indonesia untuk {$kelas}. Bab/pertemuan {$id}, semester {$sem}, judul bab {$judul}. Topik: {$topik}. Soal harus menguji pemahaman konsep dan penerapan dalam konteks nyata yang sesuai mata pelajaran, bukan otomatis konteks Informatika. Output JSON valid saja dengan struktur {\"latihan\":{\"q\":string,\"o\":[empat opsi],\"a\":integer indeks jawaban benar 0-3,\"w\":string pembahasan},\"kuis\":[objek soal dengan struktur sama]}. Buat tepat {$jumlah} soal kuis pilihan ganda, setiap soal tepat 4 opsi, satu jawaban benar, pembahasan singkat dan akurat. Soal bervariasi dan sesuai tingkat SMK.";
}
$apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/'.rawurlencode($model).':generateContent';
$payload = ['contents'=>[['role'=>'user','parts'=>[['text'=>$prompt]]]],'generationConfig'=>['temperature'=>($mode==='materi'?$temp:0.4),'responseMimeType'=>'application/json','maxOutputTokens'=>$maxTok]];
$ch=curl_init($apiUrl);
curl_setopt_array($ch,[CURLOPT_POST=>true,CURLOPT_RETURNTRANSFER=>true,CURLOPT_HTTPHEADER=>['Content-Type: application/json','x-goog-api-key: '.$key],CURLOPT_POSTFIELDS=>json_encode($payload,JSON_UNESCAPED_UNICODE),CURLOPT_CONNECTTIMEOUT=>20,CURLOPT_TIMEOUT=>$timeout]);
$res=curl_exec($ch); $err=curl_error($ch); $http=(int)curl_getinfo($ch,CURLINFO_HTTP_CODE); curl_close($ch);
if ($res===false) ai_out(502,['error'=>'Koneksi Gemini gagal: '.$err]);
$body=json_decode($res,true);
if ($http<200 || $http>=300) ai_out(502,['error'=>'Gemini API: '.($body['error']['message'] ?? 'HTTP '.$http)]);
$parts=$body['candidates'][0]['content']['parts'] ?? []; $content='';
foreach($parts as $p) if(isset($p['text'])) $content.=$p['text'];
$generated=json_decode(trim($content),true);
if(!is_array($generated)) ai_out(502,['error'=>'Gemini tidak mengembalikan JSON yang valid. Coba generate kembali.']);
$result=$current;
if($mode==='materi'){
  foreach(['ringkas','tujuan','istilah','pra','segmen','contoh'] as $k) {
    if(!array_key_exists($k,$generated)) ai_out(502,['error'=>'Hasil AI tidak lengkap pada bagian '.$k.'.']);
    $result[$k]=$generated[$k];
  }
  if(!is_array($generated['segmen']) || count($generated['segmen'])<$minSeg) ai_out(502,['error'=>'Materi dari AI terlalu singkat (kurang dari '.$minSeg.' bagian). Coba generate kembali.']);
  foreach($generated['segmen'] as $i=>$sg) if(!is_array($sg) || !isset($sg['h']) || !is_array($sg['p'] ?? null) || !count($sg['p']) || !is_array($sg['k'] ?? null)) ai_out(502,['error'=>'Bagian materi nomor '.($i+1).' dari AI tidak lengkap. Coba generate kembali.']);
} else {
  if(!isset($generated['latihan']['q'],$generated['latihan']['o'],$generated['latihan']['a'],$generated['latihan']['w']) || !is_array($generated['kuis'] ?? null) || count($generated['kuis'])!==$jumlah) ai_out(502,['error'=>'Format soal dari Gemini belum lengkap. Coba lagi.']);
  foreach(array_merge([$generated['latihan']],$generated['kuis']) as $i=>$q) if(!isset($q['q'],$q['o'],$q['a'],$q['w']) || !is_array($q['o']) || count($q['o'])!==4 || (int)$q['a']<0 || (int)$q['a']>3) ai_out(502,['error'=>'Format soal nomor '.($i+1).' tidak valid.']);
  $result['latihan']=$generated['latihan']; $result['kuis']=$generated['kuis'];
}
ai_out(200,['materi'=>$result]);
