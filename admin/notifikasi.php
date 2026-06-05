<?php
// ============================================================
// admin/notifikasi.php  — Halaman inbox notifikasi ADMIN
// ============================================================
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/base_path.php';
require_once __DIR__ . '/../includes/notifikasi.php';
require_once __DIR__ . '/../includes/auth_check.php';

$active_menu = 'notifikasi';

// Hitung SEBELUM ditandai dibaca
$jml_belum_dibaca = hitung_notif_belum_dibaca('admin');

// Tandai semua dibaca
tandai_semua_dibaca('admin');

// Ambil semua notifikasi admin (100 terbaru)
$notifs = db()->query("
    SELECT n.*, p.nama AS nama_pelanggan
    FROM notifikasi n
    LEFT JOIN pelanggan p ON p.id = n.pelanggan_id
    WHERE n.penerima = 'admin'
    ORDER BY n.dibuat_pada DESC
    LIMIT 100
")->fetchAll();

$jml_belum_dibaca = count(array_filter($notifs, fn($n) => !$n['dibaca']));
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Notifikasi – Admin Talenta Florist</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?= base_url('admin/css/admin-shared.css') ?>" />
  <link rel="stylesheet" href="<?= base_url('admin/css/notifikasi.css') ?>" />
  <script>window.BASE_URL = '<?= base_url('') ?>';</script>
</head>
<body>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<div class="admin-main">
  <div class="admin-topbar">
    <button id="sidebar-toggle">☰</button>
    <h1 class="topbar-title">Notifikasi <em>Pesanan</em></h1>
    <div style="display:flex;align-items:center;gap:.75rem;">
      <span class="topbar-info"><?= $jml_belum_dibaca ?> belum dibaca</span>
      <div class="notif-bell-wrap">
        <button class="notif-bell-btn" id="notif-bell" title="Notifikasi">
          🔔
          <span id="notif-badge" class="notif-badge-nav" style="display:none">0</span>
        </button>
        <div class="notif-dropdown" id="notif-dropdown">
          <div class="notif-dd-header">
            <span class="notif-dd-title">Notifikasi Pesanan</span>
            <span class="notif-dd-count" id="notif-dd-count">0 baru</span>
          </div>
          <div class="notif-dd-list" id="notif-dd-list">
            <div class="notif-dd-empty"><span>🌸</span>Memuat…</div>
          </div>
          <div class="notif-dd-footer">
            <a href="<?= base_url('admin/notifikasi.php') ?>">Lihat semua notifikasi →</a>
          </div>
        </div>
      </div>
      <a class="btn btn-outline btn-sm" href="<?= base_url('index.php') ?>">🌐 Lihat Website</a>
    </div>
  </div>

  <div class="admin-body">
    <div class="notif-list">
      <?php if (empty($notifs)): ?>
        <div class="notif-empty">
          <div class="notif-empty-icon">🌸</div>
          <p>Belum ada notifikasi</p>
          <span>Notifikasi pesanan baru dari pelanggan akan muncul di sini.</span>
        </div>
      <?php else: ?>
        <?php foreach ($notifs as $n): ?>
        <a class="notif-item <?= $n['dibaca'] ? '' : 'belum-dibaca' ?>"
           href="<?= $n['link'] ? base_url(htmlspecialchars($n['link'])) : '#' ?>">
          <div class="notif-icon">
            <?php
              $pesan = $n['pesan'];
              if (str_contains($pesan, 'baru'))           echo '🌸';
              elseif (str_contains($pesan, 'dibatalkan')) echo '❌';
              elseif (str_contains($pesan, 'ulasan'))     echo '⭐';
              else                                        echo '📋';
            ?>
          </div>
          <div class="notif-body">
            <p class="notif-pesan"><?= htmlspecialchars($n['pesan']) ?></p>
            <?php if ($n['nama_pelanggan']): ?>
            <span class="notif-dari">dari <?= htmlspecialchars($n['nama_pelanggan']) ?></span>
            <?php endif; ?>
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
</div>

<script src="<?= base_url('js/admin.js') ?>"></script>
<script src="<?= base_url('js/notif-dropdown.js') ?>"></script>
</body>
</html>