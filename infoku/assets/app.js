(function(){
'use strict';
var $=function(s){return document.querySelector(s)};
var esc=function(s){return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')};
var Q=function(q,o,a,w){return {q:q,o:o,a:a,w:w}};

/* ================= DATA MATERI ================= */
var MAT=[
{id:'M1',sem:1,judul:'Berpikir Komputasional',ringkas:'Memecah masalah, mengenali pola, dan menyusun langkah penyelesaian.',
 tujuan:['Memecah masalah besar menjadi bagian kecil (dekomposisi).','Mengenali pola dan memilih informasi yang penting (abstraksi).','Menyusun algoritma sebagai langkah yang berurutan.'],
 istilah:[['Dekomposisi','Memecah masalah besar menjadi bagian yang lebih kecil.'],['Pola','Kesamaan atau pengulangan yang muncul pada beberapa masalah.'],['Abstraksi','Mengambil hal penting dan mengabaikan detail yang tidak diperlukan.'],['Algoritma','Langkah berurutan dan terbatas untuk menyelesaikan masalah.']],
 pra:'Tidak ada. Cukup pernah membuat karya visual sederhana.',
 segmen:[
  {h:'Dekomposisi: memecah masalah',p:['Masalah besar terasa sulit karena terlalu banyak hal yang dipikirkan sekaligus.','Dekomposisi membagi masalah menjadi bagian kecil yang bisa dikerjakan satu per satu.'],k:['dekomposisi'],vis:{t:'tree',root:'Membuat poster digital',kids:['Menentukan tujuan','Mencari referensi','Membuat sketsa','Menata teks dan gambar','Mengekspor file']}},
  {h:'Pola dan abstraksi',p:['Pola adalah kesamaan yang muncul berulang. Poster acara, poster promosi, dan poster layanan masyarakat sama-sama punya judul, visual utama, dan informasi kontak.','Abstraksi berarti memilih yang penting. Saat membuat panduan tata letak, kamu tidak perlu mencatat warna tiap poster, cukup bagian yang selalu ada.'],k:['pola','abstraksi'],vis:{t:'flow',items:['Banyak poster','Cari kesamaan','Ambil yang penting','Templat poster']}},
  {h:'Algoritma dan struktur data dasar',p:['Algoritma adalah langkah berurutan yang jelas. Urutan yang salah memberi hasil yang salah.','Data yang dipakai algoritma perlu disimpan rapi. Daftar menyimpan banyak nilai berurutan, tabel menyimpan data dalam baris dan kolom.'],k:['algoritma','daftar','tabel'],vis:{t:'table',head:['Langkah','Aktivitas'],rows:[['1','Tentukan tujuan poster'],['2','Kumpulkan teks dan gambar'],['3','Buat tata letak'],['4','Periksa dan ekspor']]}}
 ],
 contoh:{judul:'Menyusun langkah kerja poster digital',konteks:'Kamu diminta membuat poster promosi pameran karya sekolah. Kita susun langkah kerjanya dengan berpikir komputasional.',langkah:[
  {h:'Dekomposisi',t:'Pecah tugas menjadi tujuan dan audiens, bahan teks, bahan visual, tata letak, pemeriksaan, dan ekspor.'},
  {h:'Kenali pola',t:'Poster yang pernah kamu lihat selalu punya judul, visual utama, waktu dan tempat, serta kontak.'},
  {h:'Abstraksi',t:'Ambil bagian wajib itu sebagai daftar periksa. Detail seperti gaya huruf ditentukan belakangan.'},
  {h:'Susun algoritma',t:'Urutkan: 1) tentukan tujuan, 2) kumpulkan bahan, 3) buat sketsa, 4) susun tata letak, 5) periksa daftar wajib, 6) ekspor.'}]},
 latihan:Q('Kamu memecah tugas “membuat poster” menjadi riset, sketsa, tata letak, dan ekspor. Kegiatan ini disebut...',['Dekomposisi','Abstraksi','Debugging','Enkripsi'],0,'Memecah masalah besar menjadi bagian kecil adalah dekomposisi. Abstraksi adalah memilih informasi yang penting.'),
 kuis:[
  Q('Urutan langkah yang jelas dan terbatas untuk menyelesaikan suatu masalah disebut...',['Pola','Algoritma','Abstraksi','Antarmuka'],1,'Itu definisi algoritma. Pola adalah kesamaan yang berulang, abstraksi memilih hal penting.'),
  Q('Panduan tata letak hanya mencatat bagian yang selalu ada di poster (judul, visual, kontak) dan melewatkan warna setiap poster. Ini contoh...',['Dekomposisi','Abstraksi','Algoritma','Simulasi'],1,'Mengambil yang penting dan mengabaikan detail yang tidak perlu adalah abstraksi.'),
  Q('Mengapa urutan langkah penting dalam algoritma?',['Karena urutan menentukan hasil akhir','Karena komputer hanya mengenal angka','Karena setiap algoritma harus sepuluh langkah','Karena urutan tidak memengaruhi hasil'],0,'Urutan yang berbeda dapat menghasilkan hasil yang berbeda, misalnya mengekspor poster sebelum selesai ditata.')]},

{id:'M2',sem:1,judul:'Literasi Digital dan TIK',ringkas:'Menilai informasi, mengolah dokumen, berkolaborasi, dan memahami hak cipta.',
 tujuan:['Menilai kredibilitas informasi dan sumber.','Memilih aplikasi dokumen, lembar kerja, atau presentasi sesuai kebutuhan.','Memahami hak cipta dan lisensi karya visual.'],
 istilah:[['Kredibilitas','Tingkat kepercayaan terhadap suatu sumber informasi.'],['Hak cipta','Perlindungan hukum atas karya milik pembuatnya.'],['Lisensi','Aturan yang menjelaskan bagaimana karya boleh dipakai.'],['Atribusi','Mencantumkan nama pembuat dan sumber karya.']],
 pra:'Pernah mencari gambar atau informasi di internet.',
 segmen:[
  {h:'Mencari dan menilai informasi',p:['Tidak semua informasi di internet dapat dipercaya. Periksa siapa penulisnya, kapan diterbitkan, apa tujuannya, dan apakah ada sumber lain yang mendukung.','Kredibilitas naik bila beberapa sumber yang berbeda memberi informasi yang sama.'],k:['kredibilitas'],vis:{t:'flow',items:['Temukan sumber','Periksa penulis','Periksa tanggal','Bandingkan sumber']}},
  {h:'Mengolah dokumen dan berkolaborasi',p:['Setiap aplikasi punya kegunaan utama. Pilih yang paling sesuai agar pekerjaan lebih cepat dan rapi.','Saat bekerja dalam tim, gunakan fitur berbagi, komentar, dan riwayat versi supaya perubahan tercatat.'],k:['riwayat versi'],vis:{t:'table',head:['Aplikasi','Cocok untuk'],rows:[['Dokumen','Laporan dan naskah'],['Lembar kerja','Anggaran, daftar, dan perhitungan'],['Presentasi','Menyampaikan ide dengan slide']]}},
  {h:'Hak cipta dan lisensi gambar',p:['Karya visual otomatis dilindungi hak cipta pembuatnya. Lisensi menjelaskan boleh atau tidaknya karya dipakai.','Gambar yang muncul di mesin pencari belum tentu bebas dipakai. Periksa lisensinya dan catat atribusi.'],k:['hak cipta','lisensi','atribusi'],vis:{t:'table',head:['Lisensi','Artinya'],rows:[['Hak cipta penuh','Perlu izin pembuat'],['CC BY','Boleh dipakai, wajib mencantumkan pembuat'],['CC0','Dilepas ke domain publik']]}}
 ],
 contoh:{judul:'Memeriksa kredibilitas referensi visual dan lisensinya',konteks:'Kamu menemukan sebuah ilustrasi untuk referensi desain poster. Sebelum dipakai, kita periksa dulu.',langkah:[
  {h:'Telusuri sumber asli',t:'Cari halaman asal gambar, bukan hanya hasil pencarian. Catat nama pembuat dan tanggal unggah.'},
  {h:'Nilai kredibilitas',t:'Apakah pembuatnya jelas? Apakah situsnya memuat informasi kontak atau profil? Bandingkan dengan sumber lain.'},
  {h:'Periksa lisensi',t:'Cari keterangan lisensi. Bila tidak ada, anggap hak cipta penuh dan minta izin atau cari gambar lain.'},
  {h:'Catat atribusi',t:'Tulis: judul, pembuat, sumber, lisensi. Simpan di dokumen proyek agar mudah dicantumkan.'}]},
 latihan:Q('Kamu menemukan gambar bagus di hasil pencarian, tetapi tidak ada informasi lisensinya. Langkah terbaik adalah...',['Langsung memakainya karena gambar itu ada di internet','Menelusuri sumber asli dan memeriksa lisensinya sebelum memakai','Menghapus nama pembuat agar terlihat orisinal','Mengubah warnanya sedikit lalu memakainya'],1,'Gambar yang muncul di mesin pencari belum tentu bebas dipakai. Telusuri sumbernya dan periksa lisensinya dulu.'),
 kuis:[
  Q('Lisensi CC BY berarti karya boleh dipakai dengan syarat...',['Membayar pembuatnya setiap bulan','Mencantumkan nama pembuat','Mengubah karya sepenuhnya','Tidak membagikan hasilnya'],1,'CC BY mewajibkan atribusi, yaitu mencantumkan pembuat karya.'),
  Q('Manakah tanda bahwa sebuah sumber lebih tepercaya?',['Judul memakai huruf kapital','Penulis jelas, tanggal terbit tercantum, dan ada rujukan','Banyak iklan di halaman','Dibagikan banyak orang tanpa sumber'],1,'Penulis, tanggal, dan rujukan membantu kita memeriksa kebenaran informasi.'),
  Q('Riwayat versi pada dokumen bersama berguna untuk...',['Memulihkan versi sebelumnya bila terjadi kesalahan','Mengunci dokumen dari semua orang','Menghapus komentar anggota tim','Mengubah lisensi dokumen'],0,'Riwayat versi menyimpan perubahan sehingga isi lama dapat dipulihkan.')]},

{id:'M3',sem:1,judul:'Sistem Komputer',ringkas:'Perangkat keras, perangkat lunak, sistem operasi, dan alur input, proses, output.',
 tujuan:['Mengelompokkan perangkat keras berdasarkan fungsinya.','Membedakan sistem operasi dan aplikasi.','Menjelaskan alur input, proses, dan output.'],
 istilah:[['Perangkat keras','Bagian komputer yang berwujud, seperti prosesor dan monitor.'],['Perangkat lunak','Program yang dijalankan komputer.'],['Sistem operasi','Perangkat lunak yang mengelola perangkat keras dan aplikasi.'],['RAM','Memori sementara untuk data yang sedang diproses.']],
 pra:'Pernah memakai komputer atau laptop.',
 segmen:[
  {h:'Perangkat keras',p:['Perangkat keras dibagi menurut fungsinya: memasukkan data, mengolah data, menyimpan data, dan menampilkan hasil.','Pilihan perangkat keras memengaruhi kenyamanan kerja, terutama untuk pekerjaan desain.'],k:['perangkat keras'],vis:{t:'table',head:['Kelompok','Contoh'],rows:[['Input','Keyboard, mouse, pen tablet, pemindai'],['Proses','CPU, GPU, RAM'],['Penyimpanan','SSD, HDD'],['Output','Monitor, printer, speaker']]}},
  {h:'Perangkat lunak dan sistem operasi',p:['Perangkat lunak adalah kumpulan program. Sistem operasi menjadi penghubung antara pengguna, aplikasi, dan perangkat keras.','Aplikasi, misalnya editor gambar, dipakai untuk tugas tertentu. Driver membantu sistem operasi mengenali perangkat.'],k:['sistem operasi','perangkat lunak'],vis:{t:'tree',root:'Perangkat lunak',kids:['Sistem operasi','Aplikasi','Driver dan utilitas']}},
  {h:'Alur input, proses, output',p:['Semua pekerjaan komputer mengikuti alur yang sama: data masuk, diolah, lalu hasilnya ditampilkan.','Contoh: goresan pen tablet dikirim ke komputer, aplikasi menghitungnya, lalu garis tampil di monitor.'],k:['input','proses','output'],vis:{t:'flow',items:['Pen tablet (input)','Aplikasi, CPU, GPU (proses)','Monitor (output)']}}
 ],
 contoh:{judul:'Memilih komponen komputer untuk kerja desain',konteks:'Seorang desainer mengedit poster besar dengan banyak layer dan sering membuka beberapa aplikasi sekaligus.',langkah:[
  {h:'Tulis kebutuhan',t:'Catat aplikasi yang dipakai, ukuran file, dan jumlah file yang dibuka bersamaan.'},
  {h:'Petakan ke komponen',t:'Banyak layer membutuhkan RAM yang lega. Render dan efek memakai CPU dan GPU. Membuka file cepat dibantu SSD. Warna yang akurat butuh monitor yang baik.'},
  {h:'Tentukan prioritas',t:'Dahulukan komponen yang paling membatasi pekerjaan, lalu sesuaikan dengan anggaran.'},
  {h:'Periksa kecocokan',t:'Pastikan komponen cocok satu sama lain dan dengan sistem operasi serta aplikasi yang dipakai.'}]},
 latihan:Q('Pen tablet yang dipakai untuk menggambar termasuk perangkat...',['Output','Penyimpanan','Input','Perangkat lunak'],2,'Pen tablet mengirim data goresan ke komputer, jadi termasuk perangkat input.'),
 kuis:[
  Q('Fungsi utama sistem operasi adalah...',['Mengedit gambar','Mengelola perangkat keras dan menjalankan aplikasi','Menghubungkan komputer ke listrik','Mencetak dokumen'],1,'Sistem operasi menjadi penghubung antara pengguna, aplikasi, dan perangkat keras.'),
  Q('RAM dipakai untuk...',['Menyimpan data permanen','Menyimpan sementara data yang sedang diproses','Menampilkan gambar di layar','Menghubungkan ke internet'],1,'RAM menyimpan sementara data dan program yang sedang dipakai. Data permanen disimpan di SSD atau HDD.'),
  Q('Urutan alur kerja komputer yang benar adalah...',['Output, proses, input','Proses, input, output','Input, proses, output','Input, output, proses'],2,'Data dimasukkan (input), diolah (proses), lalu ditampilkan (output).')]},

{id:'M4',sem:1,judul:'Jaringan dan Keamanan Digital',ringkas:'Konsep jaringan, berbagi file dengan aman, dan melindungi akun serta data.',
 tujuan:['Menjelaskan cara perangkat terhubung dalam jaringan dan internet.','Mengatur izin saat berbagi file desain.','Menerapkan kebiasaan aman untuk akun dan data pribadi.'],
 istilah:[['Jaringan','Kumpulan perangkat yang saling terhubung untuk berbagi data.'],['Router','Perangkat yang meneruskan data antarjaringan.'],['Phishing','Penipuan untuk mencuri data lewat pesan atau situs palsu.'],['Autentikasi dua langkah','Verifikasi tambahan selain kata sandi.']],
 pra:'Pernah memakai internet dan akun daring.',
 segmen:[
  {h:'Jaringan dan internet',p:['Jaringan menghubungkan perangkat agar bisa berbagi data. Jaringan kecil, seperti lab sekolah, disebut LAN. Internet adalah jaringan global dari banyak jaringan.','Perangkat yang meminta layanan disebut klien, perangkat yang melayani disebut server.'],k:['jaringan','router'],vis:{t:'flow',items:['Laptop (klien)','Router','Internet','Server']}},
  {h:'Berbagi file dengan aman',p:['Gunakan layanan penyimpanan daring dengan pengaturan izin. Berikan izin paling kecil yang cukup untuk tiap orang.','Bagikan ke orang tertentu, bukan ke siapa saja, terutama untuk desain yang belum dirilis.'],k:['izin'],vis:{t:'table',head:['Izin','Penerima boleh'],rows:[['Lihat','Hanya membuka file'],['Komentar','Memberi masukan'],['Edit','Mengubah file']]}},
  {h:'Keamanan akun dan data',p:['Kata sandi yang panjang dan unik lebih sulit ditebak. Aktifkan autentikasi dua langkah, dan jangan bagikan kode OTP.','Waspadai phishing: pesan yang mendesak dan meminta data pribadi. Periksa pengirim sebelum membuka tautan.'],k:['phishing','autentikasi dua langkah'],vis:{t:'flow',items:['Pesan mencurigakan','Jangan klik tautan','Periksa pengirim','Laporkan']}}
 ],
 sim:'pw',
 contoh:{judul:'Menganalisis cara aman berbagi file desain dengan tim',konteks:'Tim kamu membuat desain kemasan. Ada anggota tim, guru pembimbing, dan klien yang perlu melihat file.',langkah:[
  {h:'Tentukan siapa yang perlu akses',t:'Daftar orang dan kebutuhannya: anggota tim mengubah file, guru memberi masukan, klien melihat hasil.'},
  {h:'Pilih izin paling kecil',t:'Anggota tim: edit. Guru: komentar. Klien: lihat atau komentar.'},
  {h:'Amankan akun',t:'Aktifkan autentikasi dua langkah pada akun penyimpanan dan gunakan kata sandi yang unik.'},
  {h:'Rapikan versi',t:'Beri nama versi pada file dan simpan cadangan sebelum perubahan besar.'}]},
 latihan:Q('Klien hanya perlu memberi masukan pada desainmu. Izin yang paling tepat adalah...',['Edit','Komentar','Pemilik','Terbuka untuk siapa saja yang punya tautan'],1,'Berikan izin paling kecil yang cukup. Untuk memberi masukan, izin komentar sudah memadai.'),
 kuis:[
  Q('Kode OTP yang dikirim ke ponselmu sebaiknya...',['Dibagikan ke teman yang meminta','Diberikan kepada siapa pun yang mengaku petugas','Dijaga rahasia dan tidak dibagikan','Ditulis di profil agar tidak lupa'],2,'OTP adalah kunci tambahan untuk akunmu. Jangan dibagikan kepada siapa pun.'),
  Q('Phishing adalah...',['Teknik mempercepat internet','Upaya menipu agar korban menyerahkan data lewat pesan atau situs palsu','Cara membuat cadangan file','Jenis kabel jaringan'],1,'Phishing meniru pihak tepercaya untuk mencuri data seperti kata sandi.'),
  Q('Kata sandi mana yang paling kuat?',['Tanggal lahirmu','password','namamu123','Kalimat acak yang panjang berisi huruf, angka, dan simbol'],3,'Kata sandi yang panjang, acak, dan unik lebih sulit ditebak.')]},

{id:'M5',sem:2,judul:'Produksi dan Kolaborasi Konten Digital',ringkas:'Merencanakan, membuat, menyebarkan, dan mengevaluasi konten bersama tim.',
 tujuan:['Menyusun alur produksi konten sesuai tujuan dan audiens.','Berkolaborasi dengan pembagian peran dan pengelolaan file.','Mengevaluasi hasil penyebaran konten.'],
 istilah:[['Audiens','Kelompok orang yang menjadi sasaran konten.'],['Jangkauan','Jumlah akun berbeda yang melihat konten.'],['Interaksi','Tindakan audiens seperti suka, komentar, atau bagikan.'],['Versi','Tahapan perubahan sebuah file.']],
 pra:'Pernah membuat gambar, video, atau unggahan untuk media sosial.',
 segmen:[
  {h:'Tahap produksi konten',p:['Konten yang baik dimulai dari tujuan dan audiens, bukan dari alat yang dipakai.','Setelah itu konten dibuat, disunting, lalu dipublikasikan.'],k:['audiens'],vis:{t:'flow',items:['Rencana','Produksi','Penyuntingan','Publikasi']}},
  {h:'Kolaborasi dalam tim',p:['Bagi peran dengan jelas supaya pekerjaan tidak tumpang tindih. Simpan semua file di satu folder bersama.','Gunakan aturan penamaan file yang sama, misalnya poster_pameran_v2, agar versi tidak tertukar.'],k:['versi'],vis:{t:'table',head:['Peran','Tugas'],rows:[['Penulis naskah','Menyiapkan teks dan pesan utama'],['Desainer','Membuat visual'],['Penyunting','Memeriksa isi dan kerapian'],['Pengelola unggahan','Menjadwalkan dan mempublikasikan']]}},
  {h:'Penyebaran dan evaluasi',p:['Pilih platform yang biasa dipakai audiensmu, lalu tentukan jadwal unggah.','Setelah publikasi, lihat jangkauan dan interaksi. Hasilnya dipakai untuk memperbaiki konten berikutnya.'],k:['jangkauan','interaksi'],vis:{t:'flow',items:['Unggah','Ukur','Pelajari','Perbaiki']}}
 ],
 contoh:{judul:'Menyusun alur produksi konten promosi digital',konteks:'Tim kamu mempromosikan ekstrakurikuler desain kepada siswa baru melalui media sosial sekolah.',langkah:[
  {h:'Tentukan tujuan dan audiens',t:'Tujuan: menambah pendaftar. Audiens: siswa kelas X yang baru masuk.'},
  {h:'Bagi peran dan siapkan folder',t:'Tentukan penulis naskah, desainer, penyunting, dan pengelola unggahan. Buat satu folder bersama dengan aturan nama file.'},
  {h:'Produksi dan sunting',t:'Buat poster dan video pendek. Periksa teks, warna, dan lisensi gambar sebelum lanjut.'},
  {h:'Publikasikan di tempat audiens berada',t:'Pilih platform yang paling sering dipakai siswa baru, lalu tentukan jadwal unggah.'},
  {h:'Evaluasi',t:'Lihat jangkauan dan interaksi setelah satu minggu. Catat konten mana yang paling banyak ditanggapi.'}]},
 latihan:Q('Tahap yang dikerjakan paling awal dalam produksi konten promosi adalah...',['Mengunggah ke media sosial','Menentukan tujuan dan audiens','Menyunting video','Mengukur jangkauan'],1,'Tujuan dan audiens menentukan pesan, gaya visual, dan platform yang dipilih.'),
 kuis:[
  Q('Aturan penamaan file dalam tim berguna agar...',['File tampak lebih panjang','File mudah ditemukan dan versi tidak tertukar','Ukuran file mengecil','Aplikasi berjalan lebih cepat'],1,'Nama yang seragam memudahkan pencarian dan menghindari salah versi.'),
  Q('Ukuran yang menunjukkan berapa banyak akun berbeda melihat kontenmu disebut...',['Jangkauan','Resolusi','Lisensi','Piksel'],0,'Jangkauan menghitung jumlah akun berbeda yang melihat konten.'),
  Q('Platform untuk menyebarkan konten sebaiknya dipilih berdasarkan...',['Platform yang paling disukai pembuat konten','Kebiasaan audiens yang dituju','Platform yang tampilannya paling berwarna','Platform yang paling baru'],1,'Konten efektif bila berada di tempat audiens sasaran biasa mencari informasi.')]},

{id:'M6',sem:2,judul:'Algoritma dan Pemrograman',ringkas:'Pseudocode, flowchart, variabel, percabangan, dan perulangan.',
 tujuan:['Menulis algoritma dalam bentuk pseudocode dan flowchart.','Memakai variabel dan percabangan untuk memilih tindakan.','Memakai perulangan untuk pekerjaan yang berulang.'],
 istilah:[['Pseudocode','Tulisan langkah program dengan bahasa sehari-hari yang terstruktur.'],['Variabel','Wadah bernama untuk menyimpan nilai.'],['Percabangan','Pemilihan tindakan berdasarkan kondisi.'],['Perulangan','Pengulangan langkah beberapa kali.']],
 pra:'Memahami algoritma dari bab Berpikir Komputasional (M1).',
 segmen:[
  {h:'Pseudocode dan flowchart',p:['Algoritma dapat ditulis sebagai pseudocode atau digambar sebagai flowchart.','Pseudocode memakai bahasa sehari-hari yang terstruktur dan belum terikat satu bahasa pemrograman. Flowchart menggambarkannya dengan simbol.'],k:['pseudocode','flowchart'],vis:{t:'table',head:['Simbol flowchart','Fungsi'],rows:[['Oval','Mulai atau selesai'],['Jajar genjang','Input atau output'],['Persegi panjang','Proses'],['Belah ketupat','Keputusan']]}},
  {h:'Variabel dan percabangan',p:['Variabel adalah wadah bernama untuk menyimpan nilai, misalnya nilai = 80.','Percabangan memilih tindakan berdasarkan kondisi: JIKA kondisi benar MAKA lakukan A, SELAIN ITU lakukan B.'],k:['variabel','percabangan'],vis:{t:'code',x:'nilai = 80\nJIKA nilai >= 75 MAKA\n  TAMPILKAN "Lulus"\nSELAIN ITU\n  TAMPILKAN "Remedial"'}},
  {h:'Perulangan',p:['Perulangan menjalankan langkah yang sama beberapa kali tanpa menulisnya satu per satu.','Cara ini menghemat penulisan dan mengurangi kesalahan.'],k:['perulangan'],vis:{t:'code',x:'ULANGI 3 KALI\n  TAMPILKAN "Halo"'}}
 ],
 sim:'jam',
 contoh:{judul:'Menyusun logika interaksi sederhana pada media digital',konteks:'Sebuah halaman promosi memiliki tombol “Lihat Karya”. Warna tombol harus berubah saat kursor berada di atasnya.',langkah:[
  {h:'Tentukan kejadian',t:'Kejadian yang diamati: kursor berada di atas tombol atau tidak.'},
  {h:'Tulis kondisi dalam pseudocode',t:'JIKA kursor di atas tombol MAKA warna tombol = kuning. SELAIN ITU warna tombol = biru.'},
  {h:'Uji dengan dua kasus',t:'Kasus 1: kursor di atas tombol, hasil yang diharapkan kuning. Kasus 2: kursor di luar tombol, hasil yang diharapkan biru.'},
  {h:'Perbaiki bila perlu',t:'Bila hasil tidak sesuai, periksa kondisinya dulu sebelum mengubah bagian lain.'}]},
 latihan:Q('Struktur yang dipakai untuk memilih tindakan berdasarkan suatu kondisi disebut...',['Perulangan','Percabangan','Variabel','Flowchart'],1,'Percabangan (JIKA ... MAKA ... SELAIN ITU) memilih tindakan sesuai kondisi.'),
 kuis:[
  Q('Perintah ULANGI 4 KALI: TAMPILKAN "Halo" akan menampilkan kata Halo sebanyak...',['1 kali','2 kali','4 kali','8 kali'],2,'Perulangan menjalankan perintah di dalamnya sebanyak 4 kali.'),
  Q('Pada flowchart, bentuk belah ketupat dipakai untuk...',['Mulai atau selesai','Keputusan','Proses','Input atau output'],1,'Belah ketupat menandai keputusan dengan dua atau lebih cabang jawaban.'),
  Q('Perhatikan: JIKA nilai >= 75 MAKA "Lulus" SELAIN ITU "Remedial". Jika nilai = 70, hasilnya...',['Lulus','Remedial','Tidak ada hasil','Error'],1,'70 tidak memenuhi syarat nilai >= 75, jadi cabang SELAIN ITU yang dijalankan.')]},

{id:'M7',sem:2,judul:'Analisis Data',ringkas:'Mengumpulkan, mengolah, membaca, dan menyajikan data.',
 tujuan:['Mengumpulkan dan menata data dalam tabel.','Menghitung dan menafsirkan rata-rata, modus, dan persentase.','Memilih grafik yang sesuai untuk menyajikan data.'],
 istilah:[['Data','Fakta atau angka hasil pengamatan.'],['Responden','Orang yang menjawab survei.'],['Modus','Nilai yang paling sering muncul.'],['Persentase','Bagian dibanding total, dikali 100%.']],
 pra:'Mengenal tabel dan operasi hitung dasar.',
 segmen:[
  {h:'Mengumpulkan dan mengolah data',p:['Data bisa dikumpulkan lewat survei, observasi, atau wawancara. Data berupa angka disebut kuantitatif, data berupa uraian disebut kualitatif.','Susun data dalam tabel dan bersihkan data yang ganda atau kosong sebelum dihitung.'],k:['data'],vis:{t:'table',head:['Gaya visual','Jumlah pemilih'],rows:[['Minimalis','18'],['Ilustrasi','12'],['Fotografi','10']]}},
  {h:'Membaca dan menafsirkan data',p:['Ukuran sederhana membantu merangkum data. Setelah menghitung, tanyakan apa artinya untuk keputusan yang akan diambil.','Persentase membantu membandingkan kelompok yang jumlahnya berbeda.'],k:['modus','persentase'],vis:{t:'table',head:['Ukuran','Cara menghitung'],rows:[['Rata-rata','Jumlah nilai dibagi banyak data'],['Modus','Nilai yang paling sering muncul'],['Persentase','Bagian dibagi total, dikali 100%']]}},
  {h:'Menyajikan data',p:['Pilih grafik sesuai tujuan. Beri judul dan label agar pembaca langsung paham.','Grafik yang sederhana lebih mudah dibaca daripada grafik yang penuh hiasan.'],k:['grafik'],vis:{t:'table',head:['Tujuan','Grafik'],rows:[['Membandingkan kategori','Batang'],['Melihat perubahan dari waktu ke waktu','Garis'],['Bagian dari keseluruhan','Lingkaran']]}}
 ],
 sim:'data',
 contoh:{judul:'Menganalisis survei minat audiens untuk menentukan konsep desain',konteks:'Dari 40 responden, 18 memilih gaya minimalis, 12 ilustrasi, dan 10 fotografi. Kita tentukan konsep poster.',langkah:[
  {h:'Hitung total',t:'18 + 12 + 10 = 40 responden.'},
  {h:'Hitung persentase',t:'Minimalis 18 ÷ 40 = 45%. Ilustrasi 12 ÷ 40 = 30%. Fotografi 10 ÷ 40 = 25%.'},
  {h:'Cari modus',t:'Pilihan yang paling sering muncul adalah minimalis.'},
  {h:'Ambil keputusan',t:'Pakai gaya minimalis sebagai konsep utama dan ilustrasi sebagai aksen, lalu sajikan hasilnya dengan grafik batang.'}]},
 latihan:Q('Dari 40 responden, 10 memilih fotografi. Persentasenya adalah...',['10%','25%','40%','50%'],1,'10 ÷ 40 × 100% = 25%.'),
 kuis:[
  Q('Untuk menunjukkan perubahan jumlah pengunjung dari minggu ke minggu, grafik yang cocok adalah...',['Grafik garis','Grafik lingkaran','Tabel tanpa judul','Diagram alir'],0,'Grafik garis menampilkan perubahan dari waktu ke waktu.'),
  Q('Modus dari data 3, 5, 5, 7, 5, 9 adalah...',['3','5','7','9'],1,'Modus adalah nilai yang paling sering muncul. Angka 5 muncul tiga kali.'),
  Q('Rata-rata dari 4, 6, dan 8 adalah...',['5','6','7','18'],1,'(4 + 6 + 8) ÷ 3 = 6.')]},

{id:'M8',sem:2,judul:'Kecerdasan Artifisial',ringkas:'Konsep AI, menyusun prompt, mengevaluasi keluaran, dan etika penggunaan.',
 tujuan:['Menjelaskan konsep AI dan AI generatif.','Menyusun prompt yang jelas.','Mengevaluasi keluaran AI dan menerapkan etika penggunaan.'],
 istilah:[['Kecerdasan artifisial','Sistem komputer yang belajar pola dari data untuk membantu tugas tertentu.'],['AI generatif','AI yang membuat konten baru seperti teks atau gambar.'],['Prompt','Perintah yang kita berikan kepada AI.'],['Bias','Kecenderungan hasil yang tidak adil karena data atau cara belajar.']],
 pra:'Pernah mencoba aplikasi yang memakai AI, atau cukup membaca materi ini dengan teliti.',
 segmen:[
  {h:'Apa itu kecerdasan artifisial',p:['AI adalah sistem komputer yang mempelajari pola dari data untuk membuat prediksi, rekomendasi, atau konten.','AI generatif membuat konten baru dari prompt. AI tidak berpikir seperti manusia dan bisa keliru.'],k:['AI generatif'],vis:{t:'flow',items:['Data','Model belajar pola','Keluaran']}},
  {h:'Menyusun prompt',p:['Prompt yang jelas menghasilkan keluaran yang lebih sesuai. Sebutkan tujuan, konteks, format, dan batasan.','Jika hasilnya belum pas, perbaiki prompt dengan menambah informasi.'],k:['prompt'],vis:{t:'table',head:['Unsur','Contoh'],rows:[['Tujuan','Buat 3 ide poster'],['Konteks','Pameran karya siswa SMK DKV'],['Format','Daftar bernomor, satu kalimat per ide'],['Batasan','Tanpa tokoh yang dilindungi hak cipta']]}},
  {h:'Evaluasi dan etika',p:['Keluaran AI perlu diperiksa kebenarannya, kesesuaiannya dengan tujuan, dan kemungkinan bias atau pelanggaran hak cipta.','Jangan memasukkan data pribadi ke layanan AI, dan sebutkan bila kamu memakai bantuan AI.'],k:['bias'],vis:{t:'flow',items:['Keluaran AI','Periksa fakta','Periksa kesesuaian','Cek etika','Pakai atau revisi']}}
 ],
 contoh:{judul:'Mengevaluasi ide visual yang dihasilkan AI',konteks:'AI memberi ide: “Poster pameran dengan warna hangat dan huruf besar”. Kita periksa sebelum memakainya.',langkah:[
  {h:'Cek tujuan',t:'Apakah ide itu mengajak orang datang ke pameran? Bila tidak, ide perlu diubah.'},
  {h:'Cek keterbacaan',t:'Apakah teks cukup kontras dan mudah dibaca dari jauh?'},
  {h:'Cek orisinalitas dan hak cipta',t:'Apakah hasilnya mirip karya atau karakter tertentu? Bila mirip, jangan dipakai.'},
  {h:'Perbaiki prompt',t:'Tambahkan batasan yang kurang, misalnya palet warna dan tidak memakai karakter yang sudah ada, lalu coba lagi.'}]},
 latihan:Q('Bagian prompt yang menyatakan bahwa hasil harus berupa daftar tiga poin disebut...',['Tujuan','Konteks','Format','Batasan'],2,'Format menjelaskan bentuk keluaran yang diinginkan, misalnya daftar tiga poin.'),
 kuis:[
  Q('Mengapa keluaran AI perlu diperiksa sebelum dipakai?',['Karena AI selalu benar','Karena AI bisa keliru atau bias','Karena AI tidak boleh dipakai di sekolah','Karena AI hanya membuat angka'],1,'AI mempelajari pola dari data sehingga dapat keliru atau mengulang bias.'),
  Q('Data yang sebaiknya tidak dimasukkan ke layanan AI adalah...',['Pertanyaan umum tentang warna','Alamat rumah dan nomor identitas','Ide judul poster','Daftar langkah membuat desain'],1,'Data pribadi dapat disalahgunakan, jadi jangan dimasukkan ke layanan AI.'),
  Q('AI generatif adalah AI yang...',['Hanya menghitung rata-rata','Membuat konten baru seperti teks atau gambar dari perintah','Mematikan perangkat otomatis','Menyimpan data di server sekolah'],1,'AI generatif menghasilkan konten baru berdasarkan pola yang dipelajari dari data.')]},

{id:'M9',sem:2,judul:'Proyek Informatika',ringkas:'Merencanakan, membuat, menguji, dan mempresentasikan solusi digital.',
 tujuan:['Merencanakan proyek dengan masalah, tujuan, dan jadwal yang jelas.','Membuat dan menguji prototipe.','Mempresentasikan hasil dan menulis refleksi.'],
 istilah:[['Prototipe','Versi awal yang dibuat untuk diuji.'],['Pengguna','Orang yang memakai solusi yang kamu buat.'],['Uji pengguna','Mengamati orang memakai prototipe untuk menemukan masalah.'],['Refleksi','Menilai proses dan hasil untuk belajar dari pengalaman.']],
 pra:'Sebaiknya sudah mempelajari beberapa bab sebelumnya.',
 segmen:[
  {h:'Merencanakan proyek',p:['Mulai dari masalah nyata dan pengguna yang jelas. Tulis tujuan yang bisa diukur, tentukan fitur utama, jadwal, dan pembagian tugas.','Rencana yang jelas membantu tim tahu apa yang dikerjakan lebih dulu.'],k:['pengguna'],vis:{t:'flow',items:['Masalah','Tujuan','Fitur utama','Jadwal']}},
  {h:'Membuat dan menguji prototipe',p:['Prototipe dibuat sederhana agar ide cepat bisa diuji. Minta beberapa calon pengguna mencobanya dan catat bagian yang membuat mereka bingung.','Perbaiki bagian yang paling mengganggu lebih dulu.'],k:['prototipe'],vis:{t:'flow',items:['Prototipe','Uji pengguna','Catat masukan','Perbaiki']}},
  {h:'Presentasi dan refleksi',p:['Dalam presentasi, sampaikan masalah, solusi, dan hasil uji. Tunjukkan prototipe bila ada.','Refleksi mencatat apa yang berhasil, apa yang akan diperbaiki, dan apa yang dipelajari.'],k:['refleksi'],vis:{t:'table',head:['Bagian presentasi','Isi'],rows:[['Masalah','Siapa yang mengalami dan mengapa penting'],['Solusi','Apa yang kamu buat'],['Hasil uji','Apa yang ditemukan saat diuji'],['Refleksi','Pelajaran dan perbaikan berikutnya']]}}
 ],
 contoh:{judul:'Membuat prototipe solusi informasi untuk pameran karya',konteks:'Informasi pameran karya sekolah tersebar di banyak pesan sehingga banyak siswa tidak tahu jadwalnya.',langkah:[
  {h:'Rumuskan masalah dan tujuan',t:'Masalah: jadwal sulit ditemukan. Tujuan: 8 dari 10 orang bisa menemukan jadwal dalam 10 detik.'},
  {h:'Rancang solusi',t:'Satu halaman informasi berisi jadwal, denah, dan kontak panitia. Buat sketsa tata letaknya.'},
  {h:'Buat prototipe',t:'Ubah sketsa menjadi halaman sederhana. Cantumkan sumber gambar dan lisensinya.'},
  {h:'Uji kepada tiga teman',t:'Minta mereka mencari jadwal tanpa dijelaskan. Catat waktu dan bagian yang membingungkan.'},
  {h:'Perbaiki dan presentasikan',t:'Perbaiki bagian yang paling mengganggu, lalu sampaikan masalah, solusi, hasil uji, dan refleksi.'}]},
 latihan:Q('Tujuan utama menguji prototipe pada calon pengguna adalah...',['Menunjukkan bahwa prototipe sudah sempurna','Menemukan bagian yang membingungkan sebelum versi akhir','Menghemat waktu presentasi','Menghapus semua fitur'],1,'Uji pengguna menemukan masalah lebih awal sehingga perbaikan lebih mudah.'),
 kuis:[
  Q('Urutan tahap proyek yang benar adalah...',['Presentasi, uji, rencana, buat','Rencana, buat prototipe, uji, perbaiki, presentasi','Buat, presentasi, rencana, uji','Uji, rencana, presentasi, buat'],1,'Proyek dimulai dari perencanaan, lalu dibuat, diuji, diperbaiki, dan dipresentasikan.'),
  Q('Refleksi proyek berisi...',['Hanya nilai akhir','Apa yang berhasil, apa yang diperbaiki, dan apa yang dipelajari','Daftar nama anggota','Salinan kode atau desain'],1,'Refleksi membantu menyimpulkan pengalaman dan menentukan perbaikan berikutnya.'),
  Q('Prototipe adalah...',['Versi awal yang dibuat untuk diuji','Versi akhir yang tidak boleh diubah','Dokumen izin','Laporan keuangan'],0,'Prototipe dibuat sederhana agar ide bisa diuji sebelum dikembangkan penuh.')]}
];

var SEM={1:'Dasar berpikir komputasional, literasi digital, sistem komputer, serta jaringan dan keamanan.',2:'Produksi konten, algoritma, analisis data, kecerdasan artifisial, dan proyek.'};

var PROY=[
 {h:'Rencanakan',d:'Tentukan masalah informasi atau promosi yang ingin kamu bantu selesaikan.',c:['Menulis masalah dalam satu kalimat','Menentukan siapa pengguna atau audiensnya','Menentukan tujuan yang bisa diukur','Membagi tugas dan jadwal'],t:'Contoh masalah: jadwal pameran karya sekolah tersebar di banyak pesan sehingga banyak siswa tidak tahu. Tujuan terukur: 8 dari 10 orang yang mencoba bisa menemukan jadwal dalam 10 detik.'},
 {h:'Rancang',d:'Rancang isi dan tampilan sebelum membuat.',c:['Membuat daftar isi atau fitur utama','Membuat sketsa tata letak','Menulis alur dalam pseudocode bila ada interaksi','Memilih warna dan huruf yang mudah dibaca'],t:'Mulai dari tiga fitur saja. Fitur lain bisa ditambah setelah ada hasil uji.'},
 {h:'Buat prototipe',d:'Wujudkan rancangan menjadi versi awal yang bisa dicoba.',c:['Membuat versi awal sesuai sketsa','Memakai gambar dengan lisensi yang jelas dan mencantumkan sumbernya','Menyimpan file dengan nama dan versi yang rapi','Membagikan ke tim dengan izin yang tepat'],t:'Bagikan ke pengguna uji dengan izin lihat atau komentar, bukan edit.'},
 {h:'Uji',d:'Amati orang memakai prototipemu, lalu perbaiki.',c:['Meminta minimal tiga orang mencoba','Mencatat bagian yang membingungkan','Mencatat hasil, misalnya waktu atau jumlah yang berhasil','Memperbaiki bagian yang paling mengganggu'],t:'Jangan menjelaskan apa-apa saat uji. Perhatikan di mana pengguna berhenti atau bertanya.'},
 {h:'Presentasikan dan refleksikan',d:'Sampaikan hasilmu dan simpulkan pelajarannya.',c:['Menyusun penyampaian: masalah, solusi, hasil uji','Mempresentasikan prototipe','Menulis refleksi','Menentukan satu perbaikan berikutnya'],t:'Pertanyaan refleksi: Apa yang berhasil? Apa yang akan kamu ubah? Apa yang kamu pelajari tentang Informatika?'}
];
var KRITERIA=['Masalah dan tujuan ditulis jelas','Prototipe bisa dipakai untuk satu tugas utama','Ada hasil uji dari pengguna','Sumber gambar dan data dicantumkan','Refleksi menyebut satu perbaikan berikutnya'];

var byId=function(id){return MAT.filter(function(m){return m.id===id})[0]};

/* ================= STATE ================= */
var CFG=window.INFOKU||{};
if(Array.isArray(CFG.materi)&&CFG.materi.length){MAT=CFG.materi;}
var P={sel:{},lat:{},mulai:{},kuis:{},last:null,proj:{chk:{},ref:''}};
(function(){
  var o=CFG.progress||{};
  ['sel','lat','mulai','kuis'].forEach(function(k){P[k]=(o[k]&&typeof o[k]==='object'&&!Array.isArray(o[k]))?o[k]:{}});
  P.last=o.last||null;
  if(o.proj){P.proj={chk:(o.proj.chk&&typeof o.proj.chk==='object'&&!Array.isArray(o.proj.chk))?o.proj.chk:{},ref:o.proj.ref||''}}
})();
var ABS=CFG.absensi||{sesi:[],n:{hadir:0,izin:0,sakit:0,alpa:0},total:0,persen:null};
var AB={pick:{},kode:{},cat:{},err:{},ok:'',busy:false,scanner:null,scanLoading:false};
function absenScript(){return new Promise(function(resolve,reject){if(window.Html5Qrcode){resolve();return}var old=document.querySelector('script[data-absen-scanner]');if(old){old.addEventListener('load',resolve,{once:true});old.addEventListener('error',reject,{once:true});return}var sc=document.createElement('script');sc.src='https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js';sc.async=true;sc.dataset.absenScanner='1';sc.onload=resolve;sc.onerror=reject;document.head.appendChild(sc);});}
function absenScanStop(){if(AB.scanner){var q=AB.scanner;AB.scanner=null;q.stop().catch(function(){}).finally(function(){q.clear().catch(function(){});});}}
function absenScanStart(id){if(AB.scanner||AB.scanLoading)return;AB.scanLoading=true;var box=document.getElementById('scanbox'+id),msg=document.getElementById('scanmsg'+id);if(!box){AB.scanLoading=false;return}box.hidden=false;if(msg)msg.textContent='Meminta izin kamera…';absenScript().then(function(){if(!document.getElementById('scanbox'+id))return;var q=new Html5Qrcode('scanreader'+id);AB.scanner=q;return q.start({facingMode:'environment'},{fps:10,qrbox:{width:220,height:220}},function(decoded){var data;try{data=JSON.parse(decoded)}catch(e){if(msg)msg.textContent='QR tidak dikenali. Arahkan kamera ke QR dari guru.';return}if(data.app!=='infoku'||Number(data.sesi)!==Number(id)||!data.kode){if(msg)msg.textContent='QR bukan untuk sesi absensi ini.';return}absenScanStop();AB.ok='QR berhasil dipindai. Menyimpan kehadiran…';render({noscroll:true});absenKirimQR(id,String(data.kode).toUpperCase());}).catch(function(e){AB.scanner=null;if(msg)msg.textContent='Kamera tidak dapat dibuka. Izinkan akses kamera dan gunakan HTTPS atau localhost.';}).finally(function(){AB.scanLoading=false;});}).catch(function(){AB.scanLoading=false;if(msg)msg.textContent='Pemindai gagal dimuat. Periksa koneksi internet lalu coba lagi.';});}
function absenKirimQR(id,kode){
  if(AB.busy)return;AB.busy=true;AB.err[id]='';
  fetch('api.php?action=absen',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-Token':CFG.csrf||''},credentials:'same-origin',body:JSON.stringify({sesi:id,status:'hadir',kode:kode})})
    .then(function(r){return r.json().then(function(d){return{ok:r.ok,d:d}})})
    .then(function(x){AB.busy=false;if(x.ok&&x.d.sesi){ABS=x.d;AB.ok='Absensi berhasil tersimpan. Kehadiranmu sudah tercatat.';}else{AB.err[id]=(x.d&&x.d.error)||'Gagal menyimpan absensi.';AB.ok='';}render({noscroll:true});})
    .catch(function(){AB.busy=false;AB.err[id]='Koneksi ke server gagal. Coba pindai QR kembali.';AB.ok='';render({noscroll:true});});
}
function absenMuat(){
  try{
    fetch('api.php?action=absensi',{credentials:'same-origin',cache:'no-store'})
      .then(function(r){if(r.status===401){location.href='login.php';return null}return r.ok?r.json():null})
      .then(function(d){if(d&&d.sesi){ABS=d;if(S.view==='absensi'||S.view==='beranda')render({noscroll:true})}})
      .catch(function(){});
  }catch(e){}
}
var saveT=null;
function flush(){
  saveT=null;
  try{
    fetch('api.php?action=save',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-Token':CFG.csrf||''},body:JSON.stringify(P),credentials:'same-origin',keepalive:true})
      .then(function(r){if(r.status===401){location.href='login.php'}})
      .catch(function(){});
  }catch(e){}
}
function save(){if(saveT)clearTimeout(saveT);saveT=setTimeout(flush,500)}
window.addEventListener('pagehide',function(){if(saveT){clearTimeout(saveT);flush()}});
document.addEventListener('visibilitychange',function(){if(document.visibilityState==='hidden'&&saveT){clearTimeout(saveT);flush()}});

var S={view:'splash',sem:1,mat:'M1',seg:0,ex:1,lat:{pick:null,checked:false},quiz:null,res:null,pstage:0,hints:true,resetAsk:false};
var NAV=[['beranda','Beranda'],['materi','Materi'],['kuis','Kuis'],['progres','Progres Belajar'],['proyek','Proyek Informatika'],['absensi','Absensi'],['profil','Profil']];

/* ================= HELPERS ================= */
function logo(n){return '<svg width="'+n+'" height="'+n+'" viewBox="0 0 64 64" aria-hidden="true" focusable="false"><path d="M32 32 L32 18 A14 14 0 0 0 18 32 Z" fill="var(--c)"/><path d="M32 32 L46 32 A14 14 0 0 0 32 18 Z" fill="var(--m)"/><path d="M32 32 L18 32 A14 14 0 0 0 32 46 Z" fill="var(--y)"/><path d="M32 32 L32 46 A14 14 0 0 0 46 32 Z" fill="var(--ink)"/><circle cx="32" cy="32" r="20" fill="none" stroke="var(--ink)" stroke-width="2"/><path d="M32 4V60M4 32H60" stroke="var(--ink)" stroke-width="2"/></svg>'}
function status(m){return P.sel[m.id]?'ok':((P.mulai[m.id]||P.lat[m.id])?'prog':'new')}
function chip(s){return s==='ok'?'<span class="chip ok">Selesai</span>':s==='prog'?'<span class="chip prog">Sedang dipelajari</span>':'<span class="chip">Belum dimulai</span>'}
function pct(a,b){return b?Math.round(a/b*100):0}
function dec(n){return n.toFixed(1).replace('.',',')}
function bar(v){return '<div class="bar" role="img" aria-label="'+v+' persen"><i style="width:'+v+'%"></i></div>'}
function semOf(id){return id[0]==='S'?+id[1]:byId(id).sem}

function marked(paras,keys){
  var out=paras.map(esc);
  (keys||[]).forEach(function(k){
    var re=new RegExp('(^|[^\\p{L}])('+String(k).replace(/[.*+?^${}()|[\]\\]/g,'\\$&')+')(?![\\p{L}])','iu');
    for(var i=0;i<out.length;i++){
      if(re.test(out[i])){out[i]=out[i].replace(re,function(m,a,b){return a+'<mark>'+b+'</mark>'});break}
    }
  });
  return out.map(function(t){return '<p>'+t+'</p>'}).join('');
}
function vis(v){
  if(!v)return '';
  if(v.t==='flow')return '<ol class="v-flow" aria-label="Alur">'+v.items.map(function(i){return '<li><span class="bx">'+esc(i)+'</span></li>'}).join('')+'</ol>';
  if(v.t==='tree')return '<div class="v-tree"><div class="root">'+esc(v.root)+'</div><ul>'+v.kids.map(function(k){return '<li>'+esc(k)+'</li>'}).join('')+'</ul></div>';
  if(v.t==='table')return '<div class="tbl"><table><thead><tr>'+v.head.map(function(h){return '<th scope="col">'+esc(h)+'</th>'}).join('')+'</tr></thead><tbody>'+v.rows.map(function(r){return '<tr>'+r.map(function(c){return '<td>'+esc(c)+'</td>'}).join('')+'</tr>'}).join('')+'</tbody></table></div>';
  if(v.t==='code')return '<pre class="code" tabindex="0">'+esc(v.x)+'</pre>';
  return '';
}
function stepper(cur){
  var st=[['tujuan','Tujuan'],['segmen','Materi'],['contoh','Contoh'],['latihan','Latihan'],['kuis','Kuis']];
  return '<ol class="steps" aria-label="Alur belajar">'+st.map(function(s,i){
    return '<li><button class="st" data-act="open" data-id="'+S.mat+'" data-step="'+s[0]+'" '+(s[0]===cur?'aria-current="step"':'')+'>'+(i+1)+'. '+s[1]+'</button></li>';
  }).join('')+'</ol>';
}
function termsOf(m){
  var seen={};(m.segmen||[]).forEach(function(g){(g.i||[]).forEach(function(n){seen[n]=1})});
  return m.istilah.filter(function(t){return !seen[t[0]]});
}
function termDL(list){
  return '<dl class="terms">'+list.map(function(t){return '<div><dt>'+esc(t[0])+'</dt><dd>'+esc(t[1])+'</dd>'+(t[2]?'<dd class="eg"><span>Contoh:</span> '+esc(t[2])+'</dd>':'')+'</div>'}).join('')+'</dl>';
}
function newTerms(m,g){
  var names=g.i||[];if(!names.length)return '';
  var l=m.istilah.filter(function(t){return names.indexOf(t[0])>-1});
  return l.length?'<aside class="newterms" aria-label="Istilah baru"><h3>Istilah baru di bagian ini</h3>'+termDL(l)+'</aside>':'';
}
function matHead(m,cur){
  return '<div class="sem'+m.sem+'"><p class="eyebrow">Semester '+m.sem+' · '+m.id+'</p><h1>'+esc(m.judul)+'</h1>'+stepper(cur)+'</div>';
}

/* ================= NAVIGASI ================= */
function go(view,opts){
  S.view=view;
  if(view==='absensi'||view==='beranda')absenMuat();
  if(opts)for(var k in opts)S[k]=opts[k];
  render();
}
function openMat(id,step,x){
  step=step||'tujuan';
  S.mat=id;S.sem=byId(id).sem;
  if(step==='kuis'){startQuiz(id);return}
  S.seg=(x&&x.seg)||0;
  if(step==='segmen')P.mulai[id]=true;
  if(step==='contoh')S.ex=1;
  if(step==='latihan'){S.lat={pick:null,checked:false}}
  P.last={id:id,step:step,seg:S.seg};save();
  go(step);
}
function segGo(d){
  var m=byId(S.mat),n=m.segmen.length,t=S.seg+d;
  if(t<0){openMat(m.id,'tujuan');return}
  if(t>=n){openMat(m.id,'contoh');return}
  S.seg=t;P.last={id:m.id,step:'segmen',seg:t};save();render();
}
function navActive(){
  var v=S.view;
  if(v==='tujuan'||v==='segmen'||v==='contoh'||v==='latihan')return 'materi';
  if(v==='kuisjalan'||v==='hasil')return 'kuis';
  return v;
}

/* ================= TAMPILAN ================= */
var V={};
function header(){
  var a=navActive();
  return '<header class="top"><div class="wrap topin"><button class="brand" data-act="go" data-v="beranda" aria-label="infoKu, ke Beranda">'+logo(30)+'<span class="bw">info<b>Ku</b></span></button><div class="user"><span class="un">'+esc((CFG.user&&CFG.user.nama)||'')+'</span><a class="btn ghost sm" href="logout.php">Keluar</a></div><nav class="nav" aria-label="Menu utama">'+NAV.map(function(n){return '<button data-act="go" data-v="'+n[0]+'" '+(n[0]===a?'aria-current="page"':'')+'>'+n[1]+'</button>'}).join('')+'</nav></div></header>';
}
function footer(){return '<footer class="foot"><div class="wrap"><p class="small">infoKu dirancang dengan prinsip Cognitive Load Theory. Isi materi perlu dicocokkan dengan CP dan ATP Informatika di sekolah sasaran.</p></div></footer>'}

V.splash=function(){
  return '<div class="splash"><div class="wrap spl">'+logo(96)+'<p class="eyebrow" style="margin-top:18px">InformatikaKu</p><h1 class="wm">info<b>Ku</b></h1><p class="lead">Belajar Informatika lewat contoh dari dunia desain. Materi dibagi kecil, ada latihan langsung, dan kuis untuk menguji diri.</p><button class="btn" data-act="go" data-v="beranda">Mulai</button><p class="small" style="margin-top:14px">SMK Kelas X · Fase E · Semester 1 dan 2</p></div></div>';
};

V.beranda=function(){
  var done=MAT.filter(function(m){return P.sel[m.id]}).length;
  var last=P.last&&byId(P.last.id);
  var h='<p class="eyebrow">InformatikaKu · SMK Kelas X · Fase E</p><h1>Belajar Informatika lewat contoh dari dunia desain</h1><p class="lead">infoKu menyajikan materi Informatika selama dua semester dalam bagian-bagian kecil, lengkap dengan contoh, latihan, dan kuis. Pilih semester untuk mulai.</p>';
  if(last){
    var lbl={tujuan:'Tujuan',segmen:'Bagian '+((P.last.seg||0)+1),contoh:'Contoh',latihan:'Latihan'}[P.last.step]||'Tujuan';
    h+='<div class="card resume"><p><strong>Terakhir dibuka:</strong> '+last.id+' '+esc(last.judul)+' · '+lbl+'</p><button class="btn sm" data-act="resume">Lanjutkan belajar</button></div>';
  }
  var ab=ABS.sesi.filter(function(x){return x.buka&&!x.diisi})[0];
  if(ab)h+='<div class="card resume"><p><strong>Absensi terbuka:</strong> '+esc(ab.judul)+' · '+tgl(ab.tanggal)+'</p><button class="btn sm" data-act="go" data-v="absensi">Isi absensi</button></div>';
  h+='<h2>Pilih semester</h2><div class="grid2">';
  [1,2].forEach(function(n){
    var ms=MAT.filter(function(m){return m.sem===n}),d=ms.filter(function(m){return P.sel[m.id]}).length;
    h+='<div class="card sem'+n+'"><span class="tag">Semester '+n+'</span><h3>'+ms.length+' materi</h3><p class="small">'+SEM[n]+'</p>'+bar(pct(d,ms.length))+'<p class="small">'+d+' dari '+ms.length+' materi selesai</p><button class="btn sm" data-act="go" data-v="materi" data-sem="'+n+'">Buka Semester '+n+'</button></div>';
  });
  h+='</div><h2>Ringkasan progres</h2><div class="card"><p style="margin:0"><strong>'+done+' dari '+MAT.length+' materi selesai</strong></p>'+bar(pct(done,MAT.length))+'<p class="small" style="margin:0">Progres disimpan di akunmu. <button class="btn ghost sm" data-act="go" data-v="progres" style="margin-left:6px">Lihat detail</button></p></div>';
  return h;
};

V.materi=function(){
  var n=S.sem;
  var h='<p class="eyebrow">Daftar materi</p><h1>Materi</h1><p class="lead">Pilih bab untuk membuka tujuan, materi, contoh, latihan, dan kuis.</p><div class="tabs" role="group" aria-label="Pilih semester"><button class="tab" id="tab1" data-act="sem" data-sem="1" aria-pressed="'+(n===1)+'">Semester 1</button><button class="tab" id="tab2" data-act="sem" data-sem="2" aria-pressed="'+(n===2)+'">Semester 2</button></div>';
  h+='<p class="small">'+SEM[n]+'</p><div class="stack">';
  MAT.filter(function(m){return m.sem===n}).forEach(function(m){
    h+='<button class="row sem'+m.sem+'" data-act="open" data-id="'+m.id+'" data-step="tujuan"><span class="code">'+m.id+'</span><span class="rb"><span class="rt">'+esc(m.judul)+'</span><span class="rd">'+esc(m.ringkas)+'</span></span>'+chip(status(m))+'</button>';
  });
  return h+'</div>';
};

V.tujuan=function(){
  var m=byId(S.mat);
  var h=matHead(m,'tujuan')+'<div class="card"><h3>Tujuan pembelajaran</h3><ul class="plain">'+m.tujuan.map(function(t){return '<li>'+esc(t)+'</li>'}).join('')+'</ul><h3 style="margin-top:20px">Istilah kunci</h3>'+termDL(termsOf(m))+'<p class="small" style="margin:8px 0 0">Istilah lain akan muncul di bagian materi yang membahasnya.</p><h3 style="margin-top:20px">Pengetahuan awal</h3><p style="margin:0">'+esc(m.pra)+'</p></div>';
  h+='<div class="navrow sem'+m.sem+'"><button class="btn ghost" data-act="go" data-v="materi" data-sem="'+m.sem+'">Daftar materi</button><button class="btn push" data-act="open" data-id="'+m.id+'" data-step="segmen">Mulai Materi</button></div>';
  return h;
};

V.segmen=function(){
  var m=byId(S.mat),n=m.segmen.length,s=m.segmen[S.seg];
  var h=matHead(m,'segmen')+'<p class="small" style="margin-bottom:6px">Bagian '+(S.seg+1)+' dari '+n+'</p><div class="seg" aria-hidden="true">';
  for(var i=0;i<n;i++)h+='<i class="'+(i<=S.seg?'on':'')+'"></i>';
  h+='</div><div class="sem'+m.sem+'"><h2 style="margin-top:0">'+esc(s.h)+'</h2>'+marked(s.p,s.k)+vis(s.vis)+newTerms(m,s)+'<div class="kw" aria-label="Kata kunci">'+s.k.map(function(k){return '<span class="chip">'+esc(k)+'</span>'}).join('')+'</div></div>';
  h+='<div class="navrow sem'+m.sem+'"><button class="btn ghost" data-act="seg" data-d="-1">Kembali</button><button class="btn push" data-act="seg" data-d="1">'+(S.seg===n-1?'Lanjut ke Contoh':'Lanjut')+'</button></div>';
  return h;
};

function simHTML(m){
  if(m.sim==='pw')return '<section class="card sim" aria-labelledby="simt"><h3 id="simt">Coba: pemeriksa kata sandi</h3><p class="small">Simulasi ini berjalan di peramban kamu dan tidak mengirim apa pun. Tetap jangan mengetik kata sandi aslimu.</p><label for="pw">Ketik contoh kata sandi</label><input id="pw" type="text" autocomplete="off" autocapitalize="off" spellcheck="false" data-act="pwin" placeholder="contoh: Kopi-Hujan-Sore-2031!"><div class="meter" aria-hidden="true"><i id="pwbar"></i></div><p id="pwlab" aria-live="polite"><strong>Kekuatan:</strong> belum ada isian</p><ul class="checks" id="pwlist"></ul><p class="small" style="margin-top:12px">Pemeriksa ini hanya melihat jenis karakter. Kata sandi yang mudah ditebak tetap lemah walau memenuhi semua syarat.</p></section>';
  if(m.sim==='jam')return '<section class="card sim" aria-labelledby="simt"><h3 id="simt">Coba: sapaan berdasarkan jam</h3><p class="small">Geser jam dan lihat cabang mana yang dijalankan.</p><label for="jam">Jam: <output id="jamv">9</output>.00</label><input id="jam" type="range" min="0" max="23" value="9" data-act="jamin"><pre class="code" id="pseudo"></pre><p class="small" style="margin:0">Hasil pada halaman promosi:</p><div class="banner" id="sapa" aria-live="polite"></div></section>';
  if(m.sim==='data')return '<section class="card sim" aria-labelledby="simt"><h3 id="simt">Coba: survei platform favorit</h3><p class="small">Data contoh untuk latihan. Pilih kelompok untuk membandingkan.</p><label for="grp">Kelompok responden</label><select id="grp" data-act="grpin"><option value="X">Kelas X</option><option value="XI">Kelas XI</option><option value="ALL">Semua</option></select><div class="bars" id="bars"></div><p id="sum" aria-live="polite" style="margin:0"></p></section>';
  return '';
}
V.contoh=function(){
  var m=byId(S.mat),c=m.contoh,n=c.langkah.length;
  var h=matHead(m,'contoh')+'<div class="sem'+m.sem+'"><h2 style="margin-top:0">'+esc(c.judul)+'</h2><p>'+esc(c.konteks)+'</p><ol class="ex">';
  c.langkah.slice(0,S.ex).forEach(function(l){h+='<li><div><h3>'+esc(l.h)+'</h3><p>'+esc(l.t)+'</p></div></li>'});
  h+='</ol>';
  if(S.ex<n)h+='<button class="btn sm" id="exbtn" data-act="exnext">Lihat Langkah ('+S.ex+' dari '+n+')</button>';
  else h+='<p class="small">Semua '+n+' langkah sudah ditampilkan.</p>';
  h+=simHTML(m)+'<div class="navrow"><button class="btn ghost" data-act="open" data-id="'+m.id+'" data-step="segmen">Kembali ke Materi</button><button class="btn push" data-act="open" data-id="'+m.id+'" data-step="latihan">Lanjut ke Latihan</button></div></div>';
  return h;
};

function optHTML(j,t,cls,name,checked,dis){
  return '<label class="opt '+cls+'"><input type="radio" id="o'+j+'" name="'+name+'" value="'+j+'" data-act="'+(name==='lat'?'latpick':'qpick')+'" '+(checked?'checked':'')+(dis?' disabled':'')+'><span>'+esc(t)+'</span></label>';
}
V.latihan=function(){
  var m=byId(S.mat),q=m.latihan,L=S.lat;
  var h=matHead(m,'latihan')+'<div class="sem'+m.sem+'"><h2 style="margin-top:0">Latihan</h2><p class="small">Satu soal untuk memeriksa pemahamanmu dari bab ini.</p><fieldset><legend class="qtext">'+esc(q.q)+'</legend><div class="opts">';
  q.o.forEach(function(t,j){
    var cls='';
    if(L.checked){if(j===q.a)cls='right';else if(j===L.pick)cls='wrong'}
    else if(L.pick===j)cls='sel';
    h+=optHTML(j,t,cls,'lat',L.pick===j,L.checked);
  });
  h+='</div></fieldset>';
  if(L.checked){
    var ok=L.pick===q.a;
    h+='<div class="fb '+(ok?'ok':'bad')+'" role="status"><p><strong>'+(ok?'Benar.':'Belum tepat.')+'</strong> '+(ok?'':'Jawaban yang benar: '+esc(q.o[q.a])+'. ')+esc(q.w)+'</p></div>';
    h+='<div class="navrow"><button class="btn ghost" data-act="open" data-id="'+m.id+'" data-step="segmen">Pelajari Lagi</button><button class="btn push" data-act="open" data-id="'+m.id+'" data-step="kuis">Lanjut ke Kuis</button></div><div class="navrow" style="margin-top:12px"><button class="btn ghost sm" data-act="go" data-v="materi" data-sem="'+m.sem+'">Kembali ke daftar materi</button></div>';
  }else{
    h+='<div class="navrow"><button class="btn ghost" data-act="open" data-id="'+m.id+'" data-step="contoh">Kembali</button><button class="btn push" id="latbtn" data-act="latcheck" '+(L.pick===null?'disabled':'')+'>Periksa</button></div>';
  }
  return h+'</div>';
};

V.kuis=function(){
  var h='<p class="eyebrow">Evaluasi pemahaman</p><h1>Kuis</h1><p class="lead">Kerjakan kuis per bab atau kuis gabungan per semester. Skor terakhir disimpan di akunmu.</p><h2>Kuis per semester</h2><div class="grid2">';
  [1,2].forEach(function(n){
    var id='S'+n,ms=MAT.filter(function(m){return m.sem===n}),r=P.kuis[id];
    h+='<div class="card sem'+n+'"><span class="tag">Semester '+n+'</span><h3>Kuis gabungan, '+(ms.length*2)+' soal</h3><p class="small">'+(r?'Skor terakhir: '+r.s+' dari '+r.t:'Belum dikerjakan')+'</p><button class="btn sm" data-act="startquiz" data-id="'+id+'">'+(r?'Ulangi Kuis':'Mulai Kuis')+'</button></div>';
  });
  h+='</div><h2>Kuis per bab</h2><div class="stack">';
  MAT.forEach(function(m){
    var r=P.kuis[m.id];
    h+='<div class="row sem'+m.sem+'" style="cursor:default"><span class="code">'+m.id+'</span><span class="rb"><span class="rt">'+esc(m.judul)+'</span><span class="rd">'+(r?'Skor terakhir: '+r.s+' dari '+r.t:'Belum dikerjakan')+' · 3 soal</span></span><button class="btn sm" data-act="startquiz" data-id="'+m.id+'">'+(r?'Ulangi':'Mulai')+'</button></div>';
  });
  return h+'</div>';
};

function quizTitle(id){return id[0]==='S'?'Kuis Semester '+id[1]:'Kuis '+id+' · '+byId(id).judul}
function startQuiz(id){
  var items=[];
  if(id[0]==='S'){
    var n=+id[1];
    MAT.filter(function(m){return m.sem===n}).forEach(function(m){m.kuis.slice(0,2).forEach(function(q){items.push({mid:m.id,q:q.q,o:q.o,a:q.a,w:q.w})})});
  }else{
    byId(id).kuis.forEach(function(q){items.push({mid:id,q:q.q,o:q.o,a:q.a,w:q.w})});
  }
  S.sem=semOf(id);
  S.quiz={id:id,items:items,i:0,ans:items.map(function(){return null}),warn:''};
  go('kuisjalan');
}
V.kuisjalan=function(){
  var q=S.quiz,it=q.items[q.i],n=q.items.length,last=q.i===n-1;
  var h='<div class="sem'+S.sem+'"><p class="eyebrow">Soal '+(q.i+1)+' dari '+n+'</p><h1>'+esc(quizTitle(q.id))+'</h1><div class="qnav" role="group" aria-label="Nomor soal">';
  q.items.forEach(function(_,k){h+='<button class="qn '+(k===q.i?'cur':'')+' '+(q.ans[k]!==null?'ans':'')+'" data-act="qjump" data-i="'+k+'" aria-label="Soal '+(k+1)+(q.ans[k]!==null?', sudah dijawab':', belum dijawab')+'" '+(k===q.i?'aria-current="step"':'')+'>'+(k+1)+'</button>'});
  h+='</div><fieldset><legend class="qtext">'+esc(it.q)+'</legend><div class="opts">';
  it.o.forEach(function(t,j){h+=optHTML(j,t,q.ans[q.i]===j?'sel':'','q',q.ans[q.i]===j,false)});
  h+='</div></fieldset>';
  if(q.warn)h+='<p class="fb bad" role="alert">'+esc(q.warn)+'</p>';
  h+='<div class="navrow"><button class="btn ghost" data-act="qmove" data-d="-1" '+(q.i===0?'disabled':'')+'>Sebelumnya</button>'+(last?'<button class="btn push" data-act="qfinish">Selesai</button>':'<button class="btn push" data-act="qmove" data-d="1">Berikutnya</button>')+'</div></div>';
  return h;
};
V.hasil=function(){
  var r=S.res,n=r.items.length,p=pct(r.sc,n);
  var msg=p>=80?'Pemahamanmu sudah kuat. Lanjutkan ke bab berikutnya.':p>=60?'Cukup baik. Ulangi bagian materi dari soal yang masih salah.':'Pelajari kembali materinya, lalu ulangi kuis.';
  var wrong={};r.items.forEach(function(it,i){if(r.ans[i]!==it.a)wrong[it.mid]=true});
  var h='<div class="sem'+semOf(r.id)+'"><p class="eyebrow">Hasil · '+esc(quizTitle(r.id))+'</p><h1>Hasil dan Umpan Balik</h1><div class="card"><div class="score">'+r.sc+' / '+n+'</div><p style="margin:8px 0 0"><strong>'+p+'%</strong> benar. '+msg+'</p></div>';
  var wk=Object.keys(wrong);
  h+='<div class="navrow">';
  wk.forEach(function(id){h+='<button class="btn ghost sm" data-act="open" data-id="'+id+'" data-step="segmen">Pelajari Kembali '+id+'</button>'});
  h+='<button class="btn sm" data-act="startquiz" data-id="'+r.id+'">Ulangi Kuis</button><button class="btn ghost sm" data-act="go" data-v="kuis">Daftar kuis</button></div><h2>Pembahasan</h2><div class="stack">';
  r.items.forEach(function(it,i){
    var ok=r.ans[i]===it.a;
    h+='<div class="rev"><h3>'+(i+1)+'. '+esc(it.q)+'</h3><p><span class="chip '+(ok?'ok':'bad')+'">'+(ok?'Benar':'Kurang tepat')+'</span></p><p>Jawabanmu: '+esc(it.o[r.ans[i]])+'</p>'+(ok?'':'<p>Jawaban yang benar: '+esc(it.o[it.a])+'</p>')+'<p class="small">'+esc(it.w)+'</p></div>';
  });
  return h+'</div></div>';
};

V.progres=function(){
  var done=MAT.filter(function(m){return P.sel[m.id]}).length;
  var next=MAT.filter(function(m){return !P.sel[m.id]})[0];
  var h='<p class="eyebrow">Perkembangan belajar</p><h1>Progres Belajar</h1><div class="card"><p style="margin:0"><strong>'+done+' dari '+MAT.length+' materi selesai</strong></p>'+bar(pct(done,MAT.length))+'<p class="small" style="margin:0">Selesai berarti kamu sudah membaca materi dan memeriksa latihan.</p></div>';
  if(next)h+='<div class="navrow"><button class="btn" data-act="open" data-id="'+next.id+'" data-step="'+(P.mulai[next.id]?'segmen':'tujuan')+'">Langkah berikutnya: '+next.id+' '+esc(next.judul)+'</button></div>';
  h+='<h2>Per bab</h2><div class="plist">';
  MAT.forEach(function(m){
    var r=P.kuis[m.id],low=r&&pct(r.s,r.t)<70;
    h+='<div class="prow sem'+m.sem+'"><span class="code">'+m.id+'</span><strong>'+esc(m.judul)+'</strong><div class="meta">'+chip(status(m))+'<span>Latihan: '+(P.lat[m.id]?'sudah':'belum')+'</span><span>Kuis: '+(r?r.s+' dari '+r.t:'belum')+'</span>'+(low?'<span class="chip bad">Perlu diulang</span>':'')+(low?'<button class="btn ghost sm" data-act="open" data-id="'+m.id+'" data-step="segmen">Ulangi materi</button>':'')+'</div></div>';
  });
  h+='</div><h2>Kuis semester</h2><div class="card"><p style="margin:0">Semester 1: '+(P.kuis.S1?P.kuis.S1.s+' dari '+P.kuis.S1.t:'belum dikerjakan')+'<br>Semester 2: '+(P.kuis.S2?P.kuis.S2.s+' dari '+P.kuis.S2.t:'belum dikerjakan')+'</p></div>';
  h+='<h2>Hapus progres</h2><p class="small">Progres tersimpan di akunmu. Menghapusnya tidak bisa dibatalkan.</p>';
  h+=S.resetAsk?'<div class="navrow" style="margin-top:0"><button class="btn sm" data-act="reset2">Ya, hapus semua progres</button><button class="btn ghost sm" data-act="reset0">Batal</button></div>':'<button class="btn ghost sm" data-act="reset1">Hapus progres</button>';
  return h;
};

V.proyek=function(){
  var tot=0,ok=0;
  PROY.forEach(function(s,i){s.c.forEach(function(_,j){tot++;if(P.proj.chk['p'+i+'_'+j])ok++})});
  var h='<div class="sem2"><p class="eyebrow">Semester 2 · M9</p><h1>Proyek Informatika</h1><p class="lead">Buat prototipe solusi informasi atau promosi digital sederhana. Proyek ini menggabungkan beberapa konsep dari bab sebelumnya.</p>';
  h+='<div class="card"><h3>Masalah</h3><p>Pilih satu masalah informasi di sekitarmu, misalnya jadwal kegiatan yang sulit ditemukan atau promosi ekstrakurikuler yang kurang menjangkau siswa baru. Rancang dan uji solusi digital sederhana untuk masalah itu.</p><h3 style="margin-top:14px">Kriteria keberhasilan</h3><ul class="plain" style="margin-bottom:0">'+KRITERIA.map(function(k){return '<li>'+esc(k)+'</li>'}).join('')+'</ul></div>';
  h+='<h2>Tahapan</h2><p style="margin-bottom:6px"><strong>'+ok+' dari '+tot+' butir selesai</strong></p>'+bar(pct(ok,tot));
  h+='<div class="tabs" role="group" aria-label="Mode bantuan"><button class="tab" data-act="hint1" aria-pressed="'+S.hints+'">Dengan petunjuk</button><button class="tab" data-act="hint0" aria-pressed="'+(!S.hints)+'">Mode mandiri</button></div><div class="stack">';
  PROY.forEach(function(s,i){
    var open=S.pstage===i;
    h+='<div class="stage"><button class="sh" id="sh'+i+'" data-act="pstage" data-i="'+i+'" aria-expanded="'+open+'" aria-controls="sb'+i+'"><span class="num">'+(i+1)+'</span><span><strong>'+esc(s.h)+'</strong><br><span class="small">'+esc(s.d)+'</span></span></button>';
    if(open){
      h+='<div class="sb" id="sb'+i+'">';
      s.c.forEach(function(c,j){var id='p'+i+'_'+j;h+='<label class="cb"><input type="checkbox" id="c'+id+'" data-act="pchk" data-k="'+id+'" '+(P.proj.chk[id]?'checked':'')+'><span>'+esc(c)+'</span></label>'});
      if(S.hints)h+='<div class="hint"><p><strong>Petunjuk.</strong> '+esc(s.t)+'</p></div>';
      h+='</div>';
    }
    h+='</div>';
  });
  h+='</div><h2>Refleksi</h2><label for="ref" class="small" style="display:block;margin-bottom:6px">Apa yang berhasil, apa yang akan kamu ubah, dan apa yang kamu pelajari?</label><textarea id="ref" data-act="refin" placeholder="Tulis refleksimu di sini">'+esc(P.proj.ref||'')+'</textarea><p class="small">Tersimpan otomatis di akunmu.</p>';
  h+='<div class="navrow"><button class="btn ghost" data-act="open" data-id="M9" data-step="tujuan">Buka materi M9</button></div></div>';
  return h;
};

/* ================= ABSENSI ================= */
var AST={hadir:'Hadir',izin:'Izin',sakit:'Sakit',alpa:'Alpa'};
function tgl(d){
  var p=String(d).split('-');if(p.length<3)return esc(d);
  var b=['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
  return +p[2]+' '+b[+p[1]-1]+' '+p[0];
}
function achip(st){
  return st==='hadir' ? '<span class="chip ok">Hadir</span>' : '<span class="chip">Belum hadir</span>';
}
V.absensi=function(){
  var A0=ABS,n=A0.n||{};
  var h='<p class="eyebrow">Kehadiran</p><h1>Absensi</h1><p class="lead">Pindai QR yang ditampilkan guru menggunakan kamera. Kehadiran akan tersimpan otomatis setelah QR berhasil dipindai.</p>';
  if(AB.ok)h+='<div class="okbox" role="status">'+esc(AB.ok)+'</div>';
  var buka=A0.sesi.filter(function(x){return x.buka&&!x.diisi});
  h+='<h2>Perlu diisi sekarang</h2>';
  if(!buka.length)h+='<div class="card"><p style="margin:0">Tidak ada sesi absensi yang terbuka. Jika gurumu sudah memulai pertemuan, periksa kembali halaman ini.</p><p style="margin:10px 0 0"><button class="btn ghost sm" data-act="absenmuat">Periksa lagi</button></p></div>';
  buka.forEach(function(x){
    var er=AB.err[x.id]||'';
    h+='<section class="card abform"><span class="tag">'+tgl(x.tanggal)+(x.kelas?' · '+esc(x.kelas):'')+'</span><h3>'+esc(x.judul)+'</h3><p class="small">Status kehadiran: <strong>Hadir melalui QR</strong></p>';
    h+='<p><button type="button" class="btn" data-act="absenscan" data-id="'+x.id+'">📷 Buka kamera &amp; scan QR</button></p>';
    h+='<div id="scanbox'+x.id+'" class="card" hidden><div id="scanreader'+x.id+'" style="width:100%;max-width:420px;margin:auto"></div><p id="scanmsg'+x.id+'" class="small" role="status">Arahkan kamera ke QR yang ditampilkan guru.</p><button type="button" class="btn ghost sm" data-act="absenscanstop" data-id="'+x.id+'">Tutup kamera</button></div>';
    if(er)h+='<p class="errbox" role="alert">'+esc(er)+'</p>';
    h+='</section>';
  });
  h+='<h2>Rekap kehadiranku</h2><div class="card">';
  if(A0.total){h+='<p style="margin:0"><strong>'+A0.persen+'% hadir</strong> dari '+A0.total+' pertemuan</p>'+bar(A0.persen)+'<p class="small" style="margin:0">Total hadir: '+(n.hadir||0)+' pertemuan.</p>';}
  else h+='<p style="margin:0">Belum ada pertemuan yang tercatat.</p>';
  h+='</div><h2>Riwayat</h2>';
  if(!A0.sesi.length)return h+'<div class="card"><p style="margin:0">Belum ada sesi absensi untuk kelasmu.</p></div>';
  h+='<div class="tbl"><table><thead><tr><th scope="col">Tanggal</th><th scope="col">Pertemuan</th><th scope="col">Status</th></tr></thead><tbody>';
  A0.sesi.forEach(function(x){h+='<tr><td>'+tgl(x.tanggal)+'</td><td>'+esc(x.judul)+'</td><td>'+achip(x.status)+'</td></tr>';});
  return h+'</tbody></table></div><p class="small">Jika kamera tidak dapat digunakan, minta guru mencatat status Hadir secara manual.</p>';
};

V.profil=function(){
  return '<p class="eyebrow">Tentang media</p><h1>Profil infoKu</h1><div class="card"><p>infoKu (InformatikaKu) adalah website pembelajaran interaktif untuk mata pelajaran Informatika SMK kelas X, dengan contoh yang dekat dengan bidang Desain Komunikasi Visual (DKV). Cakupannya Semester 1 dan 2 Kurikulum Merdeka Fase E.</p><h3 style="margin-top:16px">Tujuan pengembangan</h3><p>Membantu siswa mempelajari Informatika melalui penyajian materi bertahap, visual yang relevan, latihan, dan evaluasi. Rancangan memakai Cognitive Load Theory agar beban kognitif yang tidak perlu berkurang.</p><h3 style="margin-top:16px">Pengembang</h3><p style="margin:0">Nama pengembang: (isi sesuai kebutuhan)<br>Institusi: (isi sesuai kebutuhan)</p></div><h2>Catatan</h2><p>Konteks DKV dipakai agar contoh lebih dekat dengan jurusan. Kompetensi yang diajarkan tetap mengacu pada mata pelajaran Informatika. Pembagian materi per semester adalah usulan desain dan perlu dicocokkan dengan CP dan ATP sekolah sasaran.</p><h2>Rujukan awal</h2><p>Kementerian Pendidikan Dasar dan Menengah. (2025). <em>Panduan Mata Pelajaran Informatika.</em> Repositori Kemendikdasmen.<br><a href="https://repositori.kemendikdasmen.go.id/33634/1/32.%20Panduan%20Mata%20Pelajaran%20Informatika.pdf" target="_blank" rel="noopener">Buka dokumen panduan</a></p>';
};

/* ================= RENDER ================= */
/* Warna latar mengikuti bagian yang dibuka: bab Semester 1, bab Semester 2, atau umum. */
function toneOf(){
  var v=S.view;
  if(v==='splash')return 'n';
  if(v==='proyek')return 's2';
  if(v==='tujuan'||v==='segmen'||v==='contoh'||v==='latihan'||v==='kuisjalan'||v==='hasil')return 's'+S.sem;
  if(v==='materi')return 's'+S.sem;
  return 'n';
}
function render(o){
  o=o||{};
  var y=window.scrollY;
  var app=$('#app');
  document.body.dataset.tone=toneOf();
  app.innerHTML=S.view==='splash'?V.splash():header()+'<main class="wrap main" id="main">'+V[S.view]()+'</main>'+footer();
  if(o.noscroll)window.scrollTo(0,y);else window.scrollTo(0,0);
  var f=o.focus&&document.getElementById(o.focus);
  if(f){f.focus({preventScroll:true})}
  else if(!o.noscroll){var h=app.querySelector('h1');if(h){h.tabIndex=-1;h.focus({preventScroll:true})}}
  simInit();
}

/* ================= SIMULASI ================= */
function pwUpdate(){
  var el=$('#pw');if(!el)return;
  var v=el.value;
  var cr=[['Minimal 12 karakter',v.length>=12],['Ada huruf kecil',/[a-z]/.test(v)],['Ada huruf besar',/[A-Z]/.test(v)],['Ada angka',/[0-9]/.test(v)],['Ada simbol',/[^A-Za-z0-9]/.test(v)],['Tidak memuat pola umum (password, 123456, qwerty)',v.length>0&&!/password|123456|qwerty|abcdef/i.test(v)]];
  var sc=cr.filter(function(c){return c[1]}).length;
  var lab,col;
  if(!v){lab='belum ada isian';col='var(--bad)';sc=0}
  else if(sc<=2){lab='lemah';col='var(--bad)'}
  else if(sc<=4){lab='sedang';col='var(--y)'}
  else if(sc===5){lab='kuat';col='var(--ok)'}
  else{lab='sangat kuat';col='var(--ok)'}
  var b=$('#pwbar');b.style.width=(sc/6*100)+'%';b.style.background=col;
  $('#pwlab').innerHTML='<strong>Kekuatan:</strong> '+lab;
  $('#pwlist').innerHTML=cr.map(function(c){return '<li class="'+(c[1]?'y':'')+'">'+(c[1]?'✓ ':'○ ')+esc(c[0])+'</li>'}).join('');
}
function jamUpdate(){
  var el=$('#jam');if(!el)return;
  var h=+el.value;
  $('#jamv').textContent=h;
  var br=[['JIKA jam < 4 MAKA sapaan = "Selamat malam"',h<4],['SELAIN ITU JIKA jam < 11 MAKA sapaan = "Selamat pagi"',h>=4&&h<11],['SELAIN ITU JIKA jam < 15 MAKA sapaan = "Selamat siang"',h>=11&&h<15],['SELAIN ITU JIKA jam < 18 MAKA sapaan = "Selamat sore"',h>=15&&h<18],['SELAIN ITU sapaan = "Selamat malam"',h>=18]];
  $('#pseudo').innerHTML=br.map(function(l){return '<span class="'+(l[1]?'on':'')+'">'+esc(l[0])+'</span>'}).join('\n');
  var t=['Selamat malam','Selamat pagi','Selamat siang','Selamat sore','Selamat malam'];
  var idx=0;br.forEach(function(l,i){if(l[1])idx=i});
  $('#sapa').textContent=t[idx]+', selamat datang di pameran karya!';
}
var DATA={X:{Instagram:14,TikTok:18,YouTube:8},XI:{Instagram:16,TikTok:9,YouTube:15}};
function dataUpdate(){
  var el=$('#grp');if(!el)return;
  var g=el.value,d;
  if(g==='ALL'){d={};Object.keys(DATA.X).forEach(function(k){d[k]=DATA.X[k]+DATA.XI[k]})}else d=DATA[g];
  var keys=Object.keys(d),tot=keys.reduce(function(a,k){return a+d[k]},0);
  var top=keys.reduce(function(a,k){return d[k]>d[a]?k:a},keys[0]);
  $('#bars').innerHTML=keys.map(function(k){var p=d[k]/tot*100;return '<div class="brow"><span class="bl">'+k+'</span><span class="bt"><i style="width:'+p+'%"></i></span><span class="bv">'+d[k]+' responden ('+dec(p)+'%)</span></div>'}).join('');
  $('#sum').innerHTML='Total <strong>'+tot+'</strong> responden. Modus: <strong>'+top+'</strong>, dipilih '+dec(d[top]/tot*100)+'% responden.';
}
function simInit(){
  if(S.view!=='contoh')return;
  var s=byId(S.mat).sim;
  if(s==='pw')pwUpdate();else if(s==='jam')jamUpdate();else if(s==='data')dataUpdate();
}

/* ================= AKSI ================= */
var A={
  absenmuat:function(){absenMuat()},
  absenscan:function(el){absenScanStart(+el.dataset.id)},
  absenscanstop:function(){absenScanStop();var boxes=document.querySelectorAll('[id^=scanbox]');boxes.forEach(function(b){b.hidden=true})},
  absenpick:function(el){AB.pick[el.dataset.id]=el.value;AB.err[el.dataset.id]='';render({noscroll:true,focus:'r'+el.dataset.id+el.value})},
  absenin:function(el){var id=el.dataset.id;if(el.dataset.f==='kode')AB.kode[id]=el.value.toUpperCase();else AB.cat[id]=el.value},
  absenkirim:function(el){
    var id=+el.dataset.id,st=AB.pick[id]||'hadir';absenScanStop();
    if(AB.busy)return;
    AB.busy=true;AB.err[id]='';AB.ok='';render({noscroll:true});
    fetch('api.php?action=absen',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-Token':CFG.csrf||''},credentials:'same-origin',
      body:JSON.stringify({sesi:id,status:st,kode:AB.kode[id]||'',catatan:AB.cat[id]||''})})
      .then(function(r){if(r.status===401){location.href='login.php';return null}return r.json().then(function(d){return{ok:r.ok,d:d}})})
      .then(function(x){
        AB.busy=false;if(!x)return;
        if(x.ok&&x.d.sesi){ABS=x.d;AB.ok='Absensi tersimpan. Gurumu bisa melihatnya sekarang.';delete AB.kode[id];delete AB.cat[id];delete AB.pick[id]}
        else AB.err[id]=(x.d&&x.d.error)||'Gagal menyimpan. Coba lagi.';
        render({noscroll:true});
      })
      .catch(function(){AB.busy=false;AB.err[id]='Tidak ada koneksi ke server. Coba lagi.';render({noscroll:true})});
  },
  go:function(el){var v=el.dataset.v;if(el.dataset.sem)S.sem=+el.dataset.sem;go(v)},
  sem:function(el){S.sem=+el.dataset.sem;render({noscroll:true,focus:'tab'+S.sem})},
  open:function(el){openMat(el.dataset.id,el.dataset.step)},
  resume:function(){var l=P.last;openMat(l.id,l.step,{seg:l.seg})},
  seg:function(el){segGo(+el.dataset.d)},
  exnext:function(){S.ex++;render({noscroll:true,focus:'exbtn'})},
  latpick:function(el){S.lat.pick=+el.value;render({noscroll:true,focus:'o'+el.value})},
  latcheck:function(){if(S.lat.pick===null)return;S.lat.checked=true;P.lat[S.mat]=true;P.sel[S.mat]=true;save();render({noscroll:true})},
  startquiz:function(el){startQuiz(el.dataset.id)},
  qpick:function(el){S.quiz.ans[S.quiz.i]=+el.value;S.quiz.warn='';render({noscroll:true,focus:'o'+el.value})},
  qjump:function(el){S.quiz.i=+el.dataset.i;S.quiz.warn='';render({noscroll:true})},
  qmove:function(el){S.quiz.i+=+el.dataset.d;S.quiz.warn='';render()},
  qfinish:function(){
    var q=S.quiz,un=q.ans.filter(function(a){return a===null}).length;
    if(un){q.warn='Masih ada '+un+' soal yang belum dijawab. Gunakan nomor soal di atas untuk kembali.';render({noscroll:true});return}
    var sc=0;q.items.forEach(function(it,i){if(q.ans[i]===it.a)sc++});
    P.kuis[q.id]={s:sc,t:q.items.length};save();
    S.res={id:q.id,items:q.items,ans:q.ans.slice(),sc:sc};
    go('hasil');
  },
  reset1:function(){S.resetAsk=true;render({noscroll:true})},
  reset0:function(){S.resetAsk=false;render({noscroll:true})},
  reset2:function(){P={sel:{},lat:{},mulai:{},kuis:{},last:null,proj:{chk:{},ref:''}};save();S.resetAsk=false;render()},
  hint1:function(){S.hints=true;render({noscroll:true})},
  hint0:function(){S.hints=false;render({noscroll:true})},
  pstage:function(el){var i=+el.dataset.i;S.pstage=S.pstage===i?-1:i;render({noscroll:true,focus:'sh'+i})},
  pchk:function(el){P.proj.chk[el.dataset.k]=el.checked;save();render({noscroll:true,focus:'c'+el.dataset.k})},
  refin:function(el){P.proj.ref=el.value;save()},
  pwin:function(){pwUpdate()},
  jamin:function(){jamUpdate()},
  grpin:function(){dataUpdate()}
};
var app=$('#app');
app.addEventListener('click',function(e){
  var el=e.target.closest('[data-act]');
  if(!el||el.matches('input,select,textarea'))return;
  var f=A[el.dataset.act];if(f)f(el);
});
app.addEventListener('change',function(e){
  var el=e.target;
  if(!el.matches('input[type=radio],input[type=checkbox],select'))return;
  var f=A[el.dataset.act];if(f)f(el);
});
app.addEventListener('input',function(e){
  var el=e.target;
  if(!el.matches('input[type=text],input[type=range],textarea'))return;
  var f=A[el.dataset.act];if(f)f(el);
});

render();
})();
