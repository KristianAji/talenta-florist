<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/base_path.php';
require_once __DIR__ . '/includes/auth_pelanggan.php';

$jml_produk    = db()->query('SELECT COUNT(*) FROM produk WHERE aktif=1')->fetchColumn();
$jml_testimoni = db()->query('SELECT COUNT(*) FROM testimoni WHERE aktif=1')->fetchColumn();
$testimoni     = db()->query('SELECT * FROM testimoni WHERE aktif=1 ORDER BY dibuat_pada DESC LIMIT 3')->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Talenta Florist – Kota Tomohon</title>
  <link rel="stylesheet" href="<?= base_url('css/index.css') ?>" />
</head>
<body>
  <?php if (is_admin_browsing()): ?>
  <div class="admin-bar">
    🔍 Anda sedang melihat tampilan publik sebagai Admin
    <a href="<?= base_url('admin/dashboard.php') ?>">← Kembali ke Dashboard</a>
  </div>
  <div class="admin-bar-spacer"></div>
  <?php endif; ?>

  <section class="hero">
    <div class="petal"></div><div class="petal"></div><div class="petal"></div>
    <div class="petal"></div><div class="petal"></div>

    <nav>
      <a class="logo" href="<?= base_url('index.php') ?>">Talenta <span>Florist</span></a>
      <ul class="nav-links">
        <li><a href="<?= base_url('tentang.php') ?>">Tentang</a></li>
        <li><a href="<?= base_url('katalog.php') ?>">Katalog</a></li>
        <li><a href="<?= base_url('pesan.php') ?>">Cara Pesan</a></li>
        <li><a href="<?= base_url('kontak.php') ?>">Kontak</a></li>
        <li><a href="<?= base_url('testimoni.php') ?>">Ulasan</a></li>
      </ul>
      <div style="display:flex;gap:.75rem;align-items:center;">
        <?php if (is_admin_browsing()): ?>
          <span class="nav-user">Admin: <strong><?= htmlspecialchars($_SESSION['admin_nama'] ?? 'Admin') ?></strong></span>
        <?php elseif (is_pelanggan()): ?>
          <span class="nav-user">Halo, <strong><?= htmlspecialchars($_SESSION['pelanggan_nama']) ?></strong></span>
          <a class="btn btn-outline" href="riwayat_pesanan.php">📋 Riwayat Pesanan</a>
          <a class="btn btn-outline btn-sm" href="<?= base_url('logout.php') ?>">Keluar</a>
        <?php else: ?>
          <a class="btn btn-outline btn-sm" href="<?= base_url('login.php') ?>">Masuk</a>
          <a class="btn btn-primary btn-sm" href="<?= base_url('daftar.php') ?>">Daftar</a>
        <?php endif; ?>
      </div>
    </nav>

    <div class="hero-center">
      <p class="hero-eyebrow">Kota Tomohon, Sulawesi Utara</p>
      <h1>Rangkaian Bunga<br><em>Terbaik</em> untuk<br>Setiap Momen</h1>
      <p class="hero-sub">Dari pernikahan hingga wisuda, kami hadirkan keindahan bunga segar pilihan dengan harga terjangkau dan pengiriman ke seluruh Tomohon.</p>
      <div class="hero-btns">
        <a class="btn btn-primary" href="<?= base_url('katalog.php') ?>">🌸 Lihat Katalog</a>
        <a class="btn btn-outline" href="<?= base_url('pesan.php') ?>">Cara Pemesanan</a>
      </div>
    </div>

    <div class="hero-strip">
      <div class="stat">
        <div class="stat-num">100+</div>
        <div class="stat-label">Pelanggan Puas</div>
      </div>
      <div class="stat">
        <div class="stat-num"><?= $jml_produk ?>+</div>
        <div class="stat-label">Jenis Produk</div>
      </div>
      <div class="stat">
        <div class="stat-num">5★</div>
        <div class="stat-label">Rating Layanan</div>
      </div>
    </div>
  </section>

  <div class="divider"></div>

  <?php if ($testimoni): ?>
  <section class="testi-section">
    <p class="section-label reveal">Apa Kata Mereka</p>
    <h2 class="section-title reveal">Ulasan <em>Pelanggan</em></h2>
    <div class="testi-grid">
      <?php foreach ($testimoni as $t): ?>
      <div class="testi-card reveal">
        <div class="testi-stars"><?= str_repeat('★', (int)$t['rating']) ?></div>
        <p class="testi-pesan">"<?= htmlspecialchars($t['pesan']) ?>"</p>
        <span class="testi-nama">— <?= htmlspecialchars($t['nama']) ?></span>
      </div>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

  <footer class="reveal">
    <p>&copy; <?= date('Y') ?> <strong>Talenta Florist</strong> &middot; Kota Tomohon, Sulawesi Utara</p>
  </footer>

  <script src="<?= base_url('js/main.js') ?>"></script>
</body>
</html>