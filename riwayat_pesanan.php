<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/base_path.php';
require_once __DIR__ . '/includes/auth_pelanggan.php';

if (empty($_SESSION['pelanggan_id'])) { header('Location: ' . base_url('login.php')); exit; }

$list = db()->prepare("
    SELECT ps.*, p.nama AS produk_nama, v.nama AS variasi_nama,
           v.gambar, k.nama AS kat_nama
    FROM pesanan ps
    JOIN produk p  ON p.id  = ps.produk_id
    JOIN variasi v ON v.id  = ps.variasi_id
    JOIN kategori k ON k.id = p.kategori_id
    WHERE ps.pelanggan_id = ?
    ORDER BY ps.dibuat_pada DESC
");
$list->execute([$_SESSION['pelanggan_id']]);
$list = $list->fetchAll();

$sudah_diulas = [];
$ulasan_query = db()->prepare("
    SELECT pesanan_id FROM testimoni
    WHERE pelanggan_id = ? AND pesanan_id IS NOT NULL
");
$ulasan_query->execute([$_SESSION['pelanggan_id']]);
foreach ($ulasan_query->fetchAll() as $row) {
    $sudah_diulas[] = $row['pesanan_id'];
}

$status_label = [
    'menunggu'     => ['label'=>'Menunggu Konfirmasi', 'color'=>'#c9a44a', 'bg'=>'#faeeda'],
    'dikonfirmasi' => ['label'=>'Dikonfirmasi',         'color'=>'#3b6d11', 'bg'=>'#eaf3de'],
    'dikirim'      => ['label'=>'Sedang Dikirim',       'color'=>'#185fa5', 'bg'=>'#e6f1fb'],
    'selesai'      => ['label'=>'Selesai',              'color'=>'#3b6d11', 'bg'=>'#eaf3de'],
    'dibatalkan'   => ['label'=>'Dibatalkan',           'color'=>'#a32d2d', 'bg'=>'#fcebeb'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Riwayat Pesanan – Talenta Florist</title>
  <link rel="stylesheet" href="<?= base_url('css/riwayat_pesanan.css') ?>" />
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
      <?php if (is_admin_browsing()): ?>
        <span class="nav-user">Admin: <strong><?= htmlspecialchars($_SESSION['admin_nama'] ?? 'Admin') ?></strong></span>
      <?php else: ?>
        <span class="nav-user">Halo, <strong><?= htmlspecialchars($_SESSION['pelanggan_nama']) ?></strong></span>
        <a class="btn btn-outline btn-sm" href="<?= base_url('riwayat_pesanan.php') ?>">📋 Riwayat Pesanan</a>
        <a class="btn btn-outline btn-sm" href="<?= base_url('notifikasi.php') ?>" style="position:relative;">
          🔔
          <span id="notif-badge" class="notif-badge-nav" style="display:none;">0</span>
        </a>
        <a class="btn btn-outline btn-sm" href="<?= base_url('logout.php') ?>">Keluar</a>
      <?php endif; ?>
    </div>
  </nav>

  <div class="history-wrap">
    <p class="page-eyebrow">Akun Saya</p>
    <h1 class="page-title">Riwayat <em>Pesanan</em></h1>

    <?php if (empty($list)): ?>
    <div class="empty-state">
      <span class="empty-icon">🌸</span>
      <p class="empty-txt">Kamu belum memiliki pesanan.<br>Yuk mulai belanja bunga favoritmu!</p>
      <a class="btn btn-outline" href="<?= base_url('katalog.php') ?>">Lihat Katalog</a>
    </div>

    <?php else: ?>
    <?php foreach ($list as $i => $ps):
      $st = $status_label[$ps['status']] ?? $status_label['menunggu'];
      $is_selesai   = $ps['status'] === 'selesai';
      $sudah_review = in_array($ps['id'], $sudah_diulas);
    ?>
    <div class="order-card" style="animation-delay:<?= $i * .08 ?>s;">
      <div class="order-head">
        <div>
          <div class="order-id">Pesanan #<?= $ps['id'] ?></div>
          <div class="order-date"><?= date('d M Y, H:i', strtotime($ps['dibuat_pada'])) ?></div>
        </div>
        <span class="status-badge" style="background:<?= $st['bg'] ?>;color:<?= $st['color'] ?>;">
          <?= $st['label'] ?>
        </span>
      </div>
      <div class="order-body">
        <?php if ($ps['gambar']): ?>
        <img class="order-img" src="<?= htmlspecialchars($ps['gambar']) ?>" alt="" />
        <?php else: ?>
        <div class="order-img-ph">🌸</div>
        <?php endif; ?>
        <div class="order-info">
          <div class="order-produk"><?= htmlspecialchars($ps['produk_nama']) ?></div>
          <div class="order-variasi"><?= htmlspecialchars($ps['variasi_nama']) ?> · <?= htmlspecialchars($ps['kat_nama']) ?></div>
          <div class="order-meta">
            <span>Jumlah: <strong><?= $ps['jumlah'] ?></strong></span>
            <span>Penerima: <strong><?= htmlspecialchars($ps['nama_penerima']) ?></strong></span>
            <span>Telp: <strong><?= htmlspecialchars($ps['telepon']) ?></strong></span>
          </div>
        </div>
        <div class="order-total">Rp <?= number_format($ps['total_harga'],0,',','.') ?></div>
      </div>

      <?php if ($ps['status'] === 'menunggu' || $is_selesai): ?>
      <div class="order-foot">
        <?php if ($ps['status'] === 'menunggu'): ?>
        <a href="https://wa.me/6285233608339?text=<?= urlencode("Halo, saya ingin konfirmasi pesanan #{$ps['id']} atas nama {$_SESSION['pelanggan_nama']}.") ?>"
           target="_blank" class="wa-btn">
          💬 Konfirmasi WA
        </a>
        <?php endif; ?>

        <?php if ($is_selesai): ?>
          <?php if ($sudah_review): ?>
          <span class="ulasan-btn sudah">✅ Sudah Diulas</span>
          <?php else: ?>
          <a href="<?= base_url('testimoni.php') ?>?pesanan_id=<?= $ps['id'] ?>&produk=<?= urlencode($ps['produk_nama']) ?>"
             class="ulasan-btn">
            ⭐ Beri Ulasan
          </a>
          <?php endif; ?>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <footer>
    <p>&copy; <?= date('Y') ?> <strong>Talenta Florist</strong> &middot; Kota Tomohon, Sulawesi Utara</p>
  </footer>

  <script src="<?= base_url('js/main.js') ?>"></script>
  <script src="<?= base_url('js/notif-pelanggan.js') ?>"></script>
</body>
</html>