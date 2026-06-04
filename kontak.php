<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/auth_pelanggan.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Kontak – Talenta Florist</title>
  <link rel="stylesheet" href="css/kontak.css" />
</head>
<body>
  <div class="petal"></div><div class="petal"></div>

  <nav>
    <a class="logo" href="index.php">Talenta <span>Florist</span></a>
    <ul class="nav-links">
      <li><a href="tentang.php">Tentang</a></li>
      <li><a href="katalog.php">Katalog</a></li>
      <li><a href="pesan.php">Cara Pesan</a></li>
      <li><a href="kontak.php" class="active">Kontak</a></li>
      <li><a href="testimoni.php">Ulasan</a></li>
    </ul>
    <div style="display:flex;gap:.75rem;align-items:center;">
      <?php if (!empty($_SESSION['pelanggan_id'])): ?>
        <span class="nav-user">Halo, <strong><?= htmlspecialchars($_SESSION['pelanggan_nama']) ?></strong></span>
        <a class="btn btn-outline" href="riwayat_pesanan.php">📋 Riwayat Pesanan</a>
        <a class="btn btn-outline btn-sm" href="logout.php">Keluar</a>
      <?php else: ?>
        <a class="btn btn-outline btn-sm" href="login.php">Masuk</a>
        <a class="btn btn-primary btn-sm" href="daftar.php">Daftar</a>
      <?php endif; ?>
    </div>
  </nav>

  <section class="kontak-section">
    <div class="contact-grid">
      <div class="reveal-left">
        <p class="section-label">Hubungi Kami</p>
        <h2 class="section-title">Temukan <em>Kami</em></h2>
        <p class="section-desc">Kami siap melayani Anda setiap hari. Hubungi kami melalui salah satu channel di bawah ini.</p>
        <div class="contact-cards">
          <a class="contact-card" href="https://wa.me/6285233608339" target="_blank">
            <span class="contact-icon">💬</span>
            <div><div class="contact-label">WhatsApp</div><div class="contact-value">0852-3360-8339</div></div>
          </a>
          <a class="contact-card" href="https://maps.app.goo.gl/4P4Kyo859XVRhHW58" target="_blank">
            <span class="contact-icon">📍</span>
            <div><div class="contact-label">Lokasi</div><div class="contact-value">Kota Tomohon, Sulawesi Utara</div></div>
          </a>
          <a class="contact-card" href="https://www.instagram.com/kbtalenta" target="_blank">
            <span class="contact-icon">📸</span>
            <div><div class="contact-label">Instagram</div><div class="contact-value">@kbtalenta</div></div>
          </a>
          <div class="contact-card" style="cursor:default;">
            <span class="contact-icon">🕐</span>
            <div><div class="contact-label">Jam Operasional</div><div class="contact-value">Setiap Hari, 07.00 – 20.00 WITA</div></div>
          </div>
        </div>
      </div>
      <div class="reveal-right">
        <div class="map-placeholder" id="mapBox">
          <div>
            Kios Bunga Talenta<br>
            <small style="opacity:.7">Kota Tomohon, Sulawesi Utara</small><br><br>
            <a class="btn btn-primary btn-sm" href="https://maps.app.goo.gl/4P4Kyo859XVRhHW58" target="_blank">Buka di Google Maps</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <footer class="reveal">
    <p>&copy; <?= date('Y') ?> <strong>Talenta Florist</strong> &middot; Kota Tomohon, Sulawesi Utara</p>
  </footer>

  <script src="js/main.js"></script>
  <script src="js/kontak.js"></script>
</body>
</html>