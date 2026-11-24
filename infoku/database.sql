-- infoKu (InformatikaKu) - skema database
-- Cara pakai: phpMyAdmin > tab Import > pilih berkas ini > Kirim (Go).
-- Database "infoku" akan dibuat otomatis.

CREATE DATABASE IF NOT EXISTS infoku
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE infoku;

-- Akun pengguna
CREATE TABLE IF NOT EXISTS users (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nama          VARCHAR(100) NOT NULL,
  username      VARCHAR(30)  NOT NULL,
  kelas         VARCHAR(30)  NULL,
  password_hash VARCHAR(255) NOT NULL,
  role          ENUM('siswa','guru') NOT NULL DEFAULT 'siswa',
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_login    DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_username (username)
) ENGINE=InnoDB;

-- Status belajar per materi (M1 sampai M9)
CREATE TABLE IF NOT EXISTS materi_progress (
  user_id    INT UNSIGNED NOT NULL,
  materi_id  VARCHAR(4)   NOT NULL,
  mulai      TINYINT(1)   NOT NULL DEFAULT 0,
  latihan    TINYINT(1)   NOT NULL DEFAULT 0,
  selesai    TINYINT(1)   NOT NULL DEFAULT 0,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, materi_id),
  CONSTRAINT fk_mp_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Skor terakhir kuis. kuis_id: M1..M9 (per bab) atau S1, S2 (per semester)
CREATE TABLE IF NOT EXISTS kuis_hasil (
  user_id    INT UNSIGNED NOT NULL,
  kuis_id    VARCHAR(4)   NOT NULL,
  skor       TINYINT UNSIGNED NOT NULL,
  total      TINYINT UNSIGNED NOT NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, kuis_id),
  CONSTRAINT fk_kh_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Checklist dan refleksi Proyek Informatika
CREATE TABLE IF NOT EXISTS proyek_progress (
  user_id    INT UNSIGNED NOT NULL,
  checklist  TEXT NULL,
  refleksi   TEXT NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id),
  CONSTRAINT fk_pp_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Posisi terakhir belajar (untuk tombol "Lanjutkan belajar")
CREATE TABLE IF NOT EXISTS posisi_terakhir (
  user_id    INT UNSIGNED NOT NULL,
  materi_id  VARCHAR(4)   NOT NULL,
  langkah    VARCHAR(10)  NOT NULL,
  bagian     TINYINT UNSIGNED NOT NULL DEFAULT 0,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id),
  CONSTRAINT fk_pt_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

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
