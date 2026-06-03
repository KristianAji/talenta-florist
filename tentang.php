<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth_pelanggan.php';

$anggota = db()->query('SELECT * FROM anggota ORDER BY urutan')->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tentang Kami – Talenta Florist</title>
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>
  <div class="petal"></div><div class="petal"></div><div class="petal"></div>

  <nav>
    <a class="logo" href="index.php">Talenta <span>Florist</span></a>
    <ul class="nav-links">
      <li><a href="tentang.php" class="active">Tentang</a></li>
      <li><a href="katalog.php">Katalog</a></li>
      <li><a href="pesan.php">Cara Pesan</a></li>
      <li><a href="kontak.php">Kontak</a></li>
      <li><a href="testimoni.php">Ulasan</a></li>
    </ul>
    <div style="display:flex;gap:.75rem;align-items:center;">
      <?php if (!empty($_SESSION['pelanggan_id'])): ?>
        <span class="nav-user">Halo, <strong><?= htmlspecialchars($_SESSION['pelanggan_nama']) ?></strong></span>
        <a class="btn btn-outline" href="riwayat_pesanan.php">📋 Pesanan</a>
        <a class="btn btn-outline btn-sm" href="logout.php">Keluar</a>
      <?php else: ?>
        <a class="btn btn-outline btn-sm" href="login.php">Masuk</a>
        <a class="btn btn-primary btn-sm" href="daftar.php">Daftar</a>
      <?php endif; ?>
    </div>
  </nav>

  <section class="about-section">
    <div class="about-grid">
      <div class="about-img-wrap reveal-left">
        <img src="image/Talenta.jpeg" alt="Kios Bunga Talenta" />
        <div class="about-badge">
          <span class="about-badge-num">★</span>
          <span class="about-badge-txt">Tomohon<br>Florist</span>
        </div>
      </div>
      <div class="reveal-right">
        <p class="section-label">Tentang Kami</p>
        <h2 class="section-title">Kios Bunga <em>Talenta</em></h2>
        <p class="section-desc">Kios Bunga Talenta adalah usaha florist lokal yang berlokasi di Kota Tomohon, kota bunga terbaik di Sulawesi Utara. Kami menyediakan berbagai rangkaian bunga segar untuk kebutuhan personal maupun acara seremonial.</p>
        <div class="about-features">
          <div class="feature-card reveal" style="transition-delay:.05s">
            <div class="feature-icon">🌷</div>
            <div class="feature-title">Bunga Segar</div>
            <div class="feature-desc">Langsung dari sumber terpercaya di Tomohon</div>
          </div>
          <div class="feature-card reveal" style="transition-delay:.15s">
            <div class="feature-icon">🚚</div>
            <div class="feature-title">Antar ke Lokasi</div>
            <div class="feature-desc">Pengiriman ke seluruh area Tomohon</div>
          </div>
          <div class="feature-card reveal" style="transition-delay:.25s">
            <div class="feature-icon">💌</div>
            <div class="feature-title">Custom Order</div>
            <div class="feature-desc">Desain sesuai keinginan dan budget Anda</div>
          </div>
          <div class="feature-card reveal" style="transition-delay:.35s">
            <div class="feature-icon">⚡</div>
            <div class="feature-title">Respons Cepat</div>
            <div class="feature-desc">Balas WhatsApp dalam hitungan menit</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <div class="divider"></div>

  <section class="anggota-section">
    <p class="section-label reveal">Tim Kami</p>
    <h2 class="section-title reveal">Anggota <em>Kelompok</em></h2>
    <p class="section-desc reveal">Website ini merupakan proyek kelompok dalam mata kuliah Pemrograman Web, Universitas Sam Ratulangi.</p>
    <div class="anggota-grid">
      <?php foreach ($anggota as $a): ?>
      <div class="anggota-card reveal">
        <?php if ($a['foto']): ?>
        <img class="anggota-foto" src="uploads/<?= htmlspecialchars($a['foto']) ?>" alt="<?= htmlspecialchars($a['nama']) ?>" />
        <?php else: ?>
        <div class="anggota-foto-placeholder"><?= mb_substr($a['nama'], 0, 1) ?></div>
        <?php endif; ?>
        <div class="anggota-nama"><?= htmlspecialchars($a['nama']) ?></div>
        <div class="anggota-nim"><?= htmlspecialchars($a['nim']) ?></div>
        <span class="anggota-peran"><?= htmlspecialchars($a['peran']) ?></span>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <footer class="reveal">
    <p>© <?= date('Y') ?> <strong>Kios Bunga Talenta</strong> · Kota Tomohon, Sulawesi Utara</p>
    <p style="margin-top:.4rem">Website ini dibuat sebagai bagian dari program digitalisasi UMKM · <a href="https://unsrat.ac.id" target="_blank">Universitas Sam Ratulangi</a></p>
  </footer>

  <script src="js/main.js"></script>
</body>
</html>