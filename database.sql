-- ============================================================
-- DATABASE: talenta_florist
-- ============================================================
CREATE DATABASE IF NOT EXISTS talenta_florist CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE talenta_florist;

-- ── TABEL KATEGORI ──
CREATE TABLE IF NOT EXISTS kategori (
  id       INT AUTO_INCREMENT PRIMARY KEY,
  slug     VARCHAR(60)  NOT NULL UNIQUE,
  nama     VARCHAR(100) NOT NULL,
  urutan   INT DEFAULT 0
) ENGINE=InnoDB;

-- ── TABEL PRODUK ──
CREATE TABLE IF NOT EXISTS produk (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  kategori_id INT NOT NULL,
  nama        VARCHAR(150) NOT NULL,
  deskripsi   TEXT,
  badge       VARCHAR(60) DEFAULT NULL,
  aktif       TINYINT(1) DEFAULT 1,
  dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── TABEL VARIASI PRODUK ──
CREATE TABLE IF NOT EXISTS variasi (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  produk_id INT NOT NULL,
  nama      VARCHAR(100) NOT NULL,
  harga     INT NOT NULL DEFAULT 0,
  gambar    VARCHAR(255) DEFAULT NULL,
  urutan    INT DEFAULT 0,
  FOREIGN KEY (produk_id) REFERENCES produk(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── TABEL ADMIN ──
CREATE TABLE IF NOT EXISTS admin (
  id       INT AUTO_INCREMENT PRIMARY KEY,
  nama     VARCHAR(100) NOT NULL,
  username VARCHAR(60)  NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ── TABEL TESTIMONI ──
CREATE TABLE IF NOT EXISTS testimoni (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  nama        VARCHAR(100) NOT NULL,
  pesan       TEXT NOT NULL,
  rating      TINYINT DEFAULT 5,
  aktif       TINYINT(1) DEFAULT 1,
  dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ── TABEL ANGGOTA KELOMPOK (halaman statis dari DB) ──
CREATE TABLE IF NOT EXISTS anggota (
  id     INT AUTO_INCREMENT PRIMARY KEY,
  nama   VARCHAR(150) NOT NULL,
  nim    VARCHAR(30)  NOT NULL,
  peran  VARCHAR(100) DEFAULT 'Anggota',
  foto   VARCHAR(255) DEFAULT NULL,
  urutan INT DEFAULT 0
) ENGINE=InnoDB;

-- ============================================================
-- SEED DATA
-- ============================================================

-- Kategori
INSERT INTO kategori (slug, nama, urutan) VALUES
  ('bucket',   'Bucket',       1),
  ('rangkaian','Rangkaian',    2),
  ('dekorasi', 'Dekorasi',     3),
  ('krans',    'Bunga Krans',  4);

-- Admin default  (password: admin123)
INSERT INTO admin (nama, username, password) VALUES
  ('Administrator', 'admin', '$2y$12$eUQ3RuQCb2FkRD0SkdYDZuR7C0Ob4G5kGJZ2j0Z.Y6bGu2JqBrCIi');

-- Produk & Variasi – Bucket
INSERT INTO produk (kategori_id, nama, deskripsi, badge) VALUES
  (1, 'Bouquet Boneka',    'Bucket bunga dengan boneka, cocok untuk hadiah spesial.', 'Custom'),
  (1, 'Money Bouquet',     'Bucket yang memadukan keindahan bunga dengan lembaran uang kertas yang disusun artistik, menciptakan hadiah istimewa yang berkesan dan fungsional.', 'Custom'),
  (1, 'Bouquet Mawar',     'Buket mawar premium disusun dengan komposisi padat dan simetris, memancarkan kemewahan klasik yang sangat cocok untuk momen perayaan spesial.', 'Custom'),
  (1, 'Bucket Krisan Campur','Kombinasi harmonis berbagai jenis krisan dan bunga musiman yang disusun rimbun untuk menciptakan kesan segar dan ceria.', 'Custom');

-- Variasi Bouquet Boneka
INSERT INTO variasi (produk_id, nama, harga, gambar, urutan) VALUES
  (1,'Variasi 1',175000,'image/Bucket01.png',1),
  (1,'Variasi 2',225000,'image/Bucket02.png',2);

-- Variasi Money Bouquet
INSERT INTO variasi (produk_id, nama, harga, gambar, urutan) VALUES
  (2,'Variasi 1',150000,'image/Bucket03.png',1);

-- Variasi Bouquet Mawar
INSERT INTO variasi (produk_id, nama, harga, gambar, urutan) VALUES
  (3,'Variasi 1',500000,'image/Bucket04.png',1),
  (3,'Variasi 2',400000,'image/Bucket05.png',2),
  (3,'Variasi 3',450000,'image/Bucket06.png',3),
  (3,'Variasi 4',400000,'image/Bucket07.png',4),
  (3,'Variasi 5',500000,'image/Bucket08.png',5);

-- Variasi Krisan Campur
INSERT INTO variasi (produk_id, nama, harga, gambar, urutan) VALUES
  (4,'Variasi 1',350000,'image/Bucket09.png',1),
  (4,'Variasi 2',250000,'image/Bucket10.png',2),
  (4,'Variasi 3',300000,'image/Bucket11.png',3),
  (4,'Variasi 4',300000,'image/Bucket12.png',4),
  (4,'Variasi 5',150000,'image/Bucket13.png',5),
  (4,'Variasi 6',225000,'image/Bucket14.png',6);

-- Produk & Variasi – Rangkaian
INSERT INTO produk (kategori_id, nama, deskripsi, badge) VALUES
  (2,'Rangkaian Meja Full Krisan','Rangkaian bunga krisan yang disusun padat dengan hiasan aster kecil, memancarkan kesan anggun, harmonis, dan penuh ketulusan.','Custom'),
  (2,'Rangkaian Campur Mawar','Rangkaian bunga yang memadukan krisan dan mawar dengan bentuk oval dan bulat, sangat ideal diletakkan di tengah meja panjang atau meja tamu.','Custom'),
  (2,'Rangkaian Campur Mawar (Mewah)','Rangkaian bunga yang memadukan mawar dengan bunga mewah lain seperti bunga terompet, casablanca, dan bunga lainnya yang memancarkan kesan mewah.','Custom'),
  (2,'Rangkaian Full Mawar','Rangkaian full mawar dengan bentuk bulat dan oval yang cocok ditaruh di meja.','Custom');

INSERT INTO variasi (produk_id, nama, harga, gambar, urutan) VALUES
  (5,'Variasi 1', 30000,'image/Rangkaian03.png',1),
  (5,'Variasi 2', 50000,'image/Rangkaian09.png',2),
  (5,'Variasi 3',150000,'image/Rangkaian06.png',3),
  (5,'Variasi 4',200000,'image/Rangkaian05.png',4),
  (5,'Variasi 5',300000,'image/Rangkaian01.png',5);

INSERT INTO variasi (produk_id, nama, harga, gambar, urutan) VALUES
  (6,'Variasi 1',125000,'image/Rangkaian08.png',1),
  (6,'Variasi 2',250000,'image/Rangkaian07.jpeg',2),
  (6,'Variasi 3',750000,'image/Rangkaian04.png',3),
  (6,'Variasi 4',350000,'image/Rangkaian13.png',4),
  (6,'Variasi 5',350000,'image/Rangkaian14.png',5),
  (6,'Variasi 6',700000,'image/Rangkaian20.png',6);

INSERT INTO variasi (produk_id, nama, harga, gambar, urutan) VALUES
  (7,'Variasi 1',1000000,'image/Rangkaian11.png',1),
  (7,'Variasi 2',1000000,'image/Rangkaian12.png',2),
  (7,'Variasi 3',1000000,'image/Rangkaian15.png',3),
  (7,'Variasi 4', 750000,'image/Rangkaian16.png',4),
  (7,'Variasi 5', 200000,'image/Rangkaian17.png',5),
  (7,'Variasi 6',1250000,'image/Rangkaian18.png',6),
  (7,'Variasi 7',1250000,'image/Rangkaian19.png',7);

INSERT INTO variasi (produk_id, nama, harga, gambar, urutan) VALUES
  (8,'Variasi 1',650000,'image/Rangkaian10.png',1),
  (8,'Variasi 2',650000,'image/Rangkaian02.png',2);

-- Produk & Variasi – Dekorasi
INSERT INTO produk (kategori_id, nama, deskripsi, badge) VALUES
  (3,'Dekorasi Tempat','Rangkaian bunga dekoratif bergaya mini garden yang dirancang memanjang untuk memberikan batas visual yang estetis dan segar pada area panggung atau podium.','Custom'),
  (3,'Dekorasi Kendaraan','Rangkaian bunga fresh premium yang dirancang khusus untuk mempercantik kendaraan.','Custom');

INSERT INTO variasi (produk_id, nama, harga, gambar, urutan) VALUES
  (9,'Variasi 1',2500000,'image/Deckor01.jpeg',1),
  (9,'Variasi 2',3000000,'image/Deckor02.jpeg',2),
  (9,'Variasi 3',5000000,'image/Deckor03.jpeg',3),
  (9,'Variasi 4',7500000,'image/Deckor07.jpeg',4),
  (9,'Variasi 5',3500000,'image/Deckor05.jpeg',5),
  (9,'Variasi 6',7500000,'image/Deckor06.jpeg',6);

INSERT INTO variasi (produk_id, nama, harga, gambar, urutan) VALUES
  (10,'Variasi 1',7500000,'image/Deckor04.jpeg',1);

-- Produk & Variasi – Krans
INSERT INTO produk (kategori_id, nama, deskripsi, badge) VALUES
  (4,'Bunga Papan Suka dan Duka','Rangkaian bunga papan yang dirancang sebagai bentuk penghormatan dan penyampaian pesan dari hati untuk momen-momen penting.','Custom'),
  (4,'Bunga Krans Duka','Rangkaian bunga melingkar yang dirancang khusus sebagai simbol penghormatan terakhir dan ungkapan simpati yang mendalam.','Custom');

INSERT INTO variasi (produk_id, nama, harga, gambar, urutan) VALUES
  (11,'Variasi 1', 500000,'image/Papan01.jpeg',1),
  (11,'Variasi 2',1000000,'image/Papan04.jpeg',2),
  (11,'Variasi 3', 750000,'image/Papan08.jpeg',3),
  (11,'Variasi 4',1000000,'image/Papan05.jpeg',4),
  (11,'Variasi 5',1750000,'image/Papan06.jpeg',5),
  (11,'Variasi 6', 750000,'image/Papan07.jpeg',6),
  (11,'Variasi 7',3500000,'image/Papan02.jpeg',7),
  (11,'Variasi 8',3000000,'image/Papan09.jpeg',8);

INSERT INTO variasi (produk_id, nama, harga, gambar, urutan) VALUES
  (12,'Variasi 1',150000,'image/Papan12.jpeg',1),
  (12,'Variasi 2',250000,'image/Papan10.jpeg',2),
  (12,'Variasi 3',600000,'image/Papan11.jpeg',3),
  (12,'Variasi 4',500000,'image/Papan13.jpeg',4);

-- Testimoni
INSERT INTO testimoni (nama, pesan, rating) VALUES
  ('Maria T.','Rangkaian bunganya sangat cantik dan tahan lama! Pengiriman tepat waktu untuk hari pernikahan kami.',5),
  ('Reza A.', 'Pesan untuk wisuda adik, hasilnya melampaui ekspektasi. Highly recommended!',5),
  ('Sandra K.','Customer service responsif, bunga fresh, dan harga terjangkau. Akan pesan lagi!',5);

-- Anggota Kelompok (isi sesuai kelompok Anda)
INSERT INTO anggota (nama, nim, peran, urutan) VALUES
  ('Nama Anggota 1','123456789','Ketua Kelompok',1),
  ('Nama Anggota 2','123456790','Anggota',2),
  ('Nama Anggota 3','123456791','Anggota',3);