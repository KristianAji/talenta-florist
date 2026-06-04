<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/auth_pelanggan.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cara Pesan – Talenta Florist</title>
  <link rel="stylesheet" href="css/pesan.css" />
</head>
<body>
  <div class="petal"></div><div class="petal"></div>

  <nav>
    <a class="logo" href="index.php">Talenta <span>Florist</span></a>
    <ul class="nav-links">
      <li><a href="tentang.php">Tentang</a></li>
      <li><a href="katalog.php">Katalog</a></li>
      <li><a href="pesan.php" class="active">Cara Pesan</a></li>
      <li><a href="kontak.php">Kontak</a></li>
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

  <!-- CARA PESAN VIA WHATSAPP -->
  <section class="pesan-section">
    <div class="steps-bg">
      <p class="section-label">Mudah & Cepat</p>
      <h2 class="section-title">Pesan via <em>WhatsApp</em></h2>
      <p class="section-desc">Pesan bunga favorit Anda hanya dalam 4 langkah mudah melalui WhatsApp.</p>
      <div class="steps-grid">
        <div class="step">
          <div class="step-num">01</div>
          <div class="step-title">Pilih Produk</div>
          <div class="step-desc">Jelajahi katalog dan pilih rangkaian bunga yang Anda inginkan.</div>
        </div>
        <div class="step">
          <div class="step-num">02</div>
          <div class="step-title">Hubungi Kami</div>
          <div class="step-desc">Chat via WhatsApp, sebutkan produk dan tanggal kebutuhan.</div>
        </div>
        <div class="step">
          <div class="step-num">03</div>
          <div class="step-title">Konfirmasi & Bayar</div>
          <div class="step-desc">Setujui harga dan lakukan pembayaran via transfer bank.</div>
        </div>
        <div class="step">
          <div class="step-num">04</div>
          <div class="step-title">Terima Bunga</div>
          <div class="step-desc">Bunga diantar ke lokasi Anda atau dapat diambil di kios.</div>
        </div>
      </div>
      <div style="margin-top:2rem;">
        <a class="btn btn-primary" href="https://wa.me/6285233608339" target="_blank">💬 Chat WhatsApp Sekarang</a>
      </div>
    </div>
  </section>

  <!-- CARA PESAN VIA WEB -->
  <section class="pesan-section" style="padding-top:0;">
    <div class="steps-bg steps-bg-light">
      <p class="section-label">Alternatif Pemesanan</p>
      <h2 class="section-title">Pesan via <em>Website</em></h2>
      <p class="section-desc">Kamu juga bisa memesan langsung melalui website tanpa perlu WhatsApp terlebih dahulu.</p>
      <div class="steps-grid">
        <div class="step">
          <div class="step-num">01</div>
          <div class="step-title">Daftar / Masuk</div>
          <div class="step-desc">Buat akun atau login terlebih dahulu untuk bisa memesan.</div>
        </div>
        <div class="step">
          <div class="step-num">02</div>
          <div class="step-title">Pilih Produk</div>
          <div class="step-desc">Buka katalog, pilih produk dan variasi yang kamu inginkan.</div>
        </div>
        <div class="step">
          <div class="step-num">03</div>
          <div class="step-title">Isi Form Pesanan</div>
          <div class="step-desc">Isi data penerima, alamat pengiriman, jumlah, dan catatan.</div>
        </div>
        <div class="step">
          <div class="step-num">04</div>
          <div class="step-title">Konfirmasi via WA</div>
          <div class="step-desc">Setelah pesanan masuk, konfirmasi pembayaran via WhatsApp ke admin.</div>
        </div>
      </div>
      <div style="margin-top:2rem;display:flex;gap:1rem;flex-wrap:wrap;">
        <a class="btn btn-primary" href="katalog.php">🌸 Mulai Pesan</a>
        <?php if (empty($_SESSION['pelanggan_id'])): ?>
        <a class="btn btn-outline steps-btn-outline" href="daftar.php">Daftar Akun</a>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <section class="faq-section">
    <p class="section-label reveal">Pertanyaan Umum</p>
    <h2 class="section-title reveal">FAQ</h2>
    <div class="faq-list">
      <?php
      $faqs = [
        ['q'=>'Apakah bisa custom desain?',             'a'=>'Tentu! Kami menerima custom order. Hubungi kami via WhatsApp dan ceritakan konsep yang Anda inginkan, kami akan membantu mewujudkannya.'],
        ['q'=>'Berapa lama proses pemesanan?',          'a'=>'Untuk pesanan reguler, kami membutuhkan minimal 1 hari. Untuk dekorasi acara besar, disarankan memesan 3–7 hari sebelum acara.'],
        ['q'=>'Apakah ada pengiriman ke luar Tomohon?', 'a'=>'Saat ini kami melayani pengiriman di dalam kota Tomohon. Untuk area lain silakan hubungi kami untuk konfirmasi ketersediaan.'],
        ['q'=>'Metode pembayaran apa yang tersedia?',   'a'=>'Kami menerima transfer bank (BRI, BNI, Mandiri) dan pembayaran tunai di kios.'],
        ['q'=>'Apakah bunga yang dijual selalu segar?', 'a'=>'Ya, kami mengutamakan kualitas. Bunga kami langsung dari sumber terpercaya di Tomohon dan dikerjakan fresh sesuai tanggal pesanan Anda.'],
      ];
      foreach ($faqs as $f): ?>
      <div class="faq-item reveal">
        <button class="faq-q"><?= htmlspecialchars($f['q']) ?></button>
        <div class="faq-a"><?= htmlspecialchars($f['a']) ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <footer class="reveal">
    <p>&copy; <?= date('Y') ?> <strong>Talenta Florist</strong> &middot; Kota Tomohon, Sulawesi Utara</p>
  </footer>

  <script src="js/main.js"></script>
  <script src="js/pesan.js"></script>
</body>
</html>