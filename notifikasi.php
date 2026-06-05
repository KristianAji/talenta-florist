<?php
// ============================================================
// notifikasi.php  — Halaman inbox notifikasi PELANGGAN
// ============================================================
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/base_path.php';
require_once __DIR__ . '/includes/auth_pelanggan.php';
require_once __DIR__ . '/includes/notifikasi.php';

// Hanya pelanggan yang boleh akses
if (!is_pelanggan()) {
    header('Location: ' . base_url('login.php'));
    exit;
}

$pelanggan_id = (int) $_SESSION['pelanggan_id'];

// Tandai semua dibaca
tandai_semua_dibaca('pelanggan', $pelanggan_id);

// Ambil semua notifikasi milik pelanggan ini (50 terbaru)
$stmt = db()->prepare("
    SELECT * FROM notifikasi
    WHERE penerima = 'pelanggan' AND pelanggan_id = ?
    ORDER BY dibuat_pada DESC
    LIMIT 50
");
$stmt->execute([$pelanggan_id]);
$notifs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Notifikasi – Talenta Florist</title>
  <link rel="stylesheet" href="<?= base_url('css/notifikasi-pelanggan.css') ?>" />
  <script>window.BASE_URL = '<?= base_url('') ?>';</script>
</head>
<body>
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
      <span class="nav-user">Halo, <strong><?= htmlspecialchars($_SESSION['pelanggan_nama']) ?></strong></span>
      <a class="btn btn-outline btn-sm" href="<?= base_url('riwayat_pesanan.php') ?>">📋 Riwayat Pesanan</a>
      <a class="btn btn-outline btn-sm" href="<?= base_url('notifikasi.php') ?>" style="position:relative">
        🔔
        <span id="notif-badge" style="display:none" class="notif-badge-nav">0</span>
      </a>
      <a class="btn btn-outline btn-sm" href="<?= base_url('logout.php') ?>">Keluar</a>
    </div>
  </nav>

  <div class="notif-page">
    <div class="notif-header reveal">
      <p class="section-label">Pusat Notifikasi</p>
      <h1 class="section-title">Pembaruan <em>Pesanan</em> Anda</h1>
      <p class="section-desc">Semua pemberitahuan terkait pesanan bunga Anda ditampilkan di sini.</p>
    </div>

    <div class="notif-list">
      <?php if (empty($notifs)): ?>
        <div class="notif-empty reveal">
          <div class="notif-empty-icon">🌸</div>
          <p>Belum ada notifikasi</p>
          <span>Notifikasi akan muncul saat ada pembaruan pesanan Anda.</span>
          <a href="<?= base_url('katalog.php') ?>" class="btn btn-primary" style="margin-top:1.5rem">Lihat Katalog</a>
        </div>
      <?php else: ?>
        <?php foreach ($notifs as $n): ?>
        <a class="notif-item reveal <?= $n['dibaca'] ? '' : 'belum-dibaca' ?>"
           href="<?= $n['link'] ? base_url(htmlspecialchars($n['link'])) : '#' ?>">
          <div class="notif-icon">
            <?php
              $pesan = $n['pesan'];
              if (str_contains($pesan, 'diterima') || str_contains($pesan, 'baru'))  echo '🌸';
              elseif (str_contains($pesan, 'proses'))                                echo '⚙️';
              elseif (str_contains($pesan, 'siap'))                                  echo '✅';
              elseif (str_contains($pesan, 'selesai'))                               echo '🎉';
              elseif (str_contains($pesan, 'dibatalkan') || str_contains($pesan, 'batal')) echo '❌';
              else                                                                    echo '📋';
            ?>
          </div>
          <div class="notif-body">
            <p class="notif-pesan"><?= htmlspecialchars($pesan) ?></p>
            <span class="notif-waktu"><?= date('d M Y, H:i', strtotime($n['dibuat_pada'])) ?></span>
          </div>
          <?php if (!$n['dibaca']): ?>
          <div class="notif-dot"></div>
          <?php endif; ?>
        </a>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <footer>
    <p>&copy; <?= date('Y') ?> <strong>Talenta Florist</strong> &middot; Kota Tomohon, Sulawesi Utara</p>
  </footer>

  <script src="<?= base_url('js/main.js') ?>"></script>
  <script src="<?= base_url('js/notif-pelanggan.js') ?>"></script>
</body>
</html>