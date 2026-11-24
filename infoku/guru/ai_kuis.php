<?php
declare(strict_types=1);
require __DIR__ . '/_layout.php';
$u=require_login('guru'); $d=data_materi();
$id=(string)($_GET['id'] ?? ($d['materi'][0]['id'] ?? 'M1'));
if(!preg_match('/^M[1-9]$/',$id)) $id='M1';
$item=null; foreach($d['materi'] as $m) if(($m['id']??'')===$id){$item=$m;break;}
if(!$item){http_response_code(404);exit('Bab tidak ditemukan.');}
$csrf=csrf_token();
guru_head('AI Pembuat Kuis','ai_kuis',$u,'<script>window.INFOAI_CSRF='.json_encode($csrf).';</script>');
?>
<div class="learning-editor">
 <header class="editor-heading"><div><p class="eyebrow">Fitur AI guru</p><h1>AI Pembuat Kuis</h1><p class="lead">Buat soal latihan dan kuis otomatis dengan Gemini, lalu simpan untuk digunakan siswa.</p></div></header>
 <section class="editor-card">
  <div class="section-heading"><span class="step-number">01</span><div><h2>Pengaturan konten</h2><p>Pilih bab dan masukkan topik yang ingin dibuat.</p></div></div>
  <div class="generator-grid">
   <label for="bab">Bab pembelajaran<select class="field" id="bab"><?php foreach($d['materi'] as $m): ?><option value="<?= h($m['id']) ?>"<?= $id===$m['id']?' selected':'' ?>><?= h($m['id'].' · '.$m['judul']) ?></option><?php endforeach; ?></select></label>
   <label for="topik">Topik kuis<input class="field" id="topik" placeholder="Contoh: Dasar HTML dan struktur halaman web"></label>
   <label for="mapel">Mata pelajaran<input class="field" id="mapel" placeholder="Contoh: Matematika, IPA, Sejarah, Bahasa Indonesia" required></label>
   <label for="kelas">Kelas<input class="field" id="kelas" value="SMK Kelas X"></label>
   <label for="jumlah">Jumlah soal kuis<select class="field" id="jumlah"><?php for($n=3;$n<=15;$n++): ?><option value="<?= $n ?>"<?= $n===5?' selected':'' ?>><?= $n ?> soal</option><?php endfor; ?></select></label>
  </div>
  <div class="editor-actions"><button class="btn" id="generate" type="button">Generate dengan Gemini</button></div>
  <div id="status" class="editor-status" role="status" aria-live="polite"></div>
 </section>
 <section class="editor-card">
  <div class="section-heading"><span class="step-number">02</span><div><h2>Periksa hasil AI</h2><p>Hasil generate dapat diperiksa sebelum disimpan.</p></div></div>
  <form method="post" action="ai_kuis_save.php">
   <input type="hidden" name="csrf" value="<?= h($csrf) ?>"><input type="hidden" name="id" id="saveId" value="<?= h($id) ?>">
   <label for="content" class="editor-label">Soal latihan dan kuis (JSON)</label>
   <textarea class="field json-editor" id="content" name="content_json" rows="22" spellcheck="false"><?= h(json_encode(array_intersect_key($item,array_flip(['latihan','kuis'])), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)) ?></textarea>
   <div class="editor-actions"><button class="btn" type="submit">Simpan ke Halaman Siswa</button><button class="btn ghost" type="button" id="format">Rapikan JSON</button></div>
  </form>
 </section>
</div>
<script>
(()=> {
 const btn=document.getElementById('generate'),status=document.getElementById('status'),content=document.getElementById('content'),bab=document.getElementById('bab');
 bab.addEventListener('change',()=>location.href='?id='+encodeURIComponent(bab.value));
 btn.addEventListener('click',async()=>{
  const topik=document.getElementById('topik').value.trim();
  if(!topik){status.textContent='Isi topik terlebih dahulu.';return;}
  btn.disabled=true;status.textContent='Gemini sedang membuat konten…';status.className='editor-status is-loading';
  try{
   const r=await fetch('ai_generate.php',{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json','X-CSRF-Token':window.INFOAI_CSRF},body:JSON.stringify({mode:'kuis',id:bab.value,judul:bab.options[bab.selectedIndex].text,mapel:document.getElementById('mapel').value,sem:<?= (int)$item['sem'] ?>,topik:topik,kelas:document.getElementById('kelas').value,jumlah_soal:parseInt(document.getElementById('jumlah').value,10)})});
   const raw=await r.text();let data;try{data=JSON.parse(raw)}catch(e){throw new Error('Server tidak mengirim JSON. Periksa error PHP.')}
   if(!r.ok)throw new Error(data.error||'Generate gagal.');
   const obj=data.materi,keys=['latihan','kuis'],part={};keys.forEach(k=>part[k]=obj[k]);content.value=JSON.stringify(part,null,2);
   document.getElementById('saveId').value=bab.value;status.textContent='Berhasil dibuat. Periksa hasilnya lalu simpan.';status.className='editor-status is-success';
  }catch(e){status.textContent=e.message;status.className='editor-status is-error'}finally{btn.disabled=false}
 });
 document.getElementById('format').addEventListener('click',()=>{try{content.value=JSON.stringify(JSON.parse(content.value),null,2);status.textContent='JSON dirapikan.';status.className='editor-status is-success'}catch(e){status.textContent='JSON tidak valid: '+e.message;status.className='editor-status is-error'}});
})();
</script>
<?php guru_foot(); ?>