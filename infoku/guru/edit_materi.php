<?php
declare(strict_types=1);
require __DIR__ . '/_layout.php';
$u=require_login('guru'); $d=data_materi();
$id=(string)($_GET['id']??($d['materi'][0]['id']??'M1'));
$item=null; foreach($d['materi'] as $m) if(($m['id']??'')===$id){$item=$m;break;}
if(!$item){http_response_code(404);exit('Pertemuan tidak ditemukan.');}
$csrf=csrf_token(); guru_head('Edit Materi','edit_materi',$u);
function lines_text($v): string { return is_array($v) ? implode("\n",array_map('strval',$v)) : (string)$v; }
?>
<div class="learning-editor manual-editor">
 <header class="editor-heading"><div><p class="eyebrow">Pengelolaan pembelajaran</p><h1>Edit Materi Manual</h1><p class="lead">Ubah isi setiap pertemuan melalui formulir. Tidak perlu mengedit JSON dan tidak menggunakan AI di halaman ini.</p></div></header>
 <?php if(isset($_GET['saved'])): ?><div class="editor-alert success">Materi berhasil disimpan dan diperbarui di halaman siswa.</div><?php endif; ?>
 <form method="post" action="materi_save.php" id="materialForm">
  <input type="hidden" name="csrf" value="<?= h($csrf) ?>"><input type="hidden" name="id" value="<?= h($id) ?>">
  <section class="editor-card"><div class="section-heading"><span class="step-number">01</span><div><h2>Informasi pertemuan</h2><p>Pilih pertemuan yang akan diedit.</p></div></div>
   <label for="meeting">Pilih pertemuan</label><select class="field" id="meeting"><?php foreach($d['materi'] as $m): ?><option value="<?= h($m['id']) ?>"<?= $id===$m['id']?' selected':'' ?>><?= h($m['id'].' · Pertemuan '.$m['sem'].' · '.$m['judul']) ?></option><?php endforeach; ?></select>
   <div class="manual-grid"><label>ID Pertemuan<input class="field" value="<?= h($item['id']) ?>" disabled></label><label>Nomor pertemuan<input class="field" type="number" min="1" name="sem" value="<?= (int)($item['sem']??1) ?>" required></label><label class="wide">Judul materi<input class="field" name="judul" value="<?= h($item['judul']??'') ?>" required></label><label class="wide">Ringkasan materi<textarea class="field" name="ringkas" rows="3"><?= h($item['ringkas']??'') ?></textarea></label><label class="wide">Pengetahuan awal / prasyarat<textarea class="field" name="pra" rows="2"><?= h($item['pra']??'') ?></textarea></label></div>
  </section>
  <section class="editor-card"><div class="section-heading"><span class="step-number">02</span><div><h2>Tujuan pembelajaran</h2><p>Satu tujuan untuk setiap baris.</p></div></div><textarea class="field" name="tujuan_text" rows="5" placeholder="Satu tujuan per baris"><?= h(lines_text($item['tujuan']??[])) ?></textarea></section>
  <section class="editor-card"><div class="section-heading"><span class="step-number">03</span><div><h2>Istilah penting</h2><p>Tambahkan istilah dan penjelasannya.</p></div></div>
   <div id="termsList" class="manual-stack"><?php foreach(($item['istilah']??[]) as $term): ?><div class="manual-row term-row"><label>Istilah<input class="field" name="term_name[]" value="<?= h($term[0]??'') ?>"></label><label>Penjelasan<input class="field" name="term_desc[]" value="<?= h($term[1]??'') ?>"></label><button type="button" class="btn ghost remove-row">Hapus</button></div><?php endforeach; ?></div><button type="button" class="btn ghost add-row" data-target="termsList" data-kind="term">+ Tambah istilah</button>
  </section>
  <section class="editor-card"><div class="section-heading"><span class="step-number">04</span><div><h2>Isi materi per subbab</h2><p>Tambah beberapa bagian materi. Pisahkan paragraf dengan baris kosong dan poin penting satu per baris.</p></div></div>
   <div id="segmentsList" class="manual-stack"><?php foreach(($item['segmen']??[]) as $seg): ?><div class="manual-panel segment-row"><div class="manual-panel-head"><strong>Bagian materi</strong><button type="button" class="btn ghost remove-row">Hapus bagian</button></div><label>Judul bagian<input class="field" name="seg_h[]" value="<?= h($seg['h']??'') ?>"></label><label>Penjelasan<textarea class="field" name="seg_p[]" rows="5"><?= h(lines_text($seg['p']??[])) ?></textarea></label><label>Poin penting (satu per baris)<textarea class="field" name="seg_k[]" rows="3"><?= h(lines_text($seg['k']??[])) ?></textarea></label><input type="hidden" name="seg_vis[]" value="<?= h(json_encode($seg['vis']??null,JSON_UNESCAPED_UNICODE)) ?>"></div><?php endforeach; ?></div><button type="button" class="btn ghost add-row" data-target="segmentsList" data-kind="segment">+ Tambah bagian materi</button>
  </section>
  <section class="editor-card"><div class="section-heading"><span class="step-number">05</span><div><h2>Contoh penerapan</h2><p>Isi contoh kontekstual dan langkah-langkahnya.</p></div></div>
   <div class="manual-grid"><label class="wide">Judul contoh<input class="field" name="contoh_judul" value="<?= h($item['contoh']['judul']??'') ?>"></label><label class="wide">Konteks contoh<textarea class="field" name="contoh_konteks" rows="3"><?= h($item['contoh']['konteks']??'') ?></textarea></label></div>
   <div id="stepsList" class="manual-stack"><?php foreach(($item['contoh']['langkah']??[]) as $step): ?><div class="manual-row step-row"><label>Nama langkah<input class="field" name="step_h[]" value="<?= h($step['h']??'') ?>"></label><label>Penjelasan<textarea class="field" name="step_t[]" rows="2"><?= h($step['t']??'') ?></textarea></label><button type="button" class="btn ghost remove-row">Hapus</button></div><?php endforeach; ?></div><button type="button" class="btn ghost add-row" data-target="stepsList" data-kind="step">+ Tambah langkah</button>
  </section>
  <div class="editor-actions"><button class="btn" type="submit">Simpan Materi</button><a class="btn ghost" href="ai_materi.php?id=<?= h($id) ?>">Buat materi dengan AI</a></div>
 </form>
</div>
<template id="termTemplate"><div class="manual-row term-row"><label>Istilah<input class="field" name="term_name[]"></label><label>Penjelasan<input class="field" name="term_desc[]"></label><button type="button" class="btn ghost remove-row">Hapus</button></div></template>
<template id="segmentTemplate"><div class="manual-panel segment-row"><div class="manual-panel-head"><strong>Bagian materi baru</strong><button type="button" class="btn ghost remove-row">Hapus bagian</button></div><label>Judul bagian<input class="field" name="seg_h[]"></label><label>Penjelasan<textarea class="field" name="seg_p[]" rows="5"></textarea></label><label>Poin penting (satu per baris)<textarea class="field" name="seg_k[]" rows="3"></textarea></label><input type="hidden" name="seg_vis[]" value=""></div></template>
<template id="stepTemplate"><div class="manual-row step-row"><label>Nama langkah<input class="field" name="step_h[]"></label><label>Penjelasan<textarea class="field" name="step_t[]" rows="2"></textarea></label><button type="button" class="btn ghost remove-row">Hapus</button></div></template>
<script>
(()=>{const $=s=>document.querySelector(s);$('#meeting').addEventListener('change',e=>location.href='?id='+encodeURIComponent(e.target.value));document.addEventListener('click',e=>{const del=e.target.closest('.remove-row');if(del){del.closest('.manual-row,.manual-panel')?.remove();return;}const add=e.target.closest('.add-row');if(add){const kind=add.dataset.kind;const tpl=document.getElementById(kind+'Template');document.getElementById(add.dataset.target).append(tpl.content.cloneNode(true));}});})();
</script>
<?php guru_foot(); ?>