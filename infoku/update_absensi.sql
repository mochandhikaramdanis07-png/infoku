-- Jalankan sekali di phpMyAdmin (pilih database infoku > tab SQL, atau Import) bila database sudah dibuat sebelumnya.
USE infoku;

-- Absensi. Satu sesi = satu pertemuan yang dibuka guru. kelas kosong = berlaku untuk semua kelas.
CREATE TABLE IF NOT EXISTS absensi_sesi (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  judul      VARCHAR(120) NOT NULL,
  tanggal    DATE         NOT NULL,
  kelas      VARCHAR(30)  NOT NULL DEFAULT '',
  kode       VARCHAR(8)   NOT NULL,
  status     ENUM('buka','tutup') NOT NULL DEFAULT 'buka',
  dibuat_oleh INT UNSIGNED NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_as_tgl (tanggal),
  KEY idx_as_kelas (kelas)
) ENGINE=InnoDB;

-- Kehadiran siswa per sesi. Siswa yang tidak punya baris di sesi yang sudah ditutup dihitung alpa.
CREATE TABLE IF NOT EXISTS absensi (
  sesi_id    INT UNSIGNED NOT NULL,
  user_id    INT UNSIGNED NOT NULL,
  status     ENUM('hadir','izin','sakit','alpa') NOT NULL,
  catatan    VARCHAR(200) NULL,
  waktu      DATETIME NOT NULL,
  oleh_guru  TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (sesi_id, user_id),
  CONSTRAINT fk_ab_sesi FOREIGN KEY (sesi_id) REFERENCES absensi_sesi(id) ON DELETE CASCADE,
  CONSTRAINT fk_ab_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
