<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/config/db.php';

if (empty($_SESSION['pelanggan_id'])) { header('Location: login.php'); exit; }

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
  <link rel="stylesheet" href="css/riwayat_pesanan.css" />
</head>
<body style="display:flex;flex-direction:column;min-height:100vh;">

  <nav>
    <a class="logo" href="index.php">Talenta <span>Florist</span></a>
    <ul class="nav-links">
      <li><a href="tentang.php">Tentang</a></li>
      <li><a href="katalog.php">Katalog</a></li>
      <li><a href="pesan.php">Cara Pesan</a></li>
      <li><a href="kontak.php">Kontak</a></li>
      <li><a href="testimoni.php">Ulasan</a></li>
    </ul>
    <div style="display:flex;gap:.75rem;align-items:center;">
      <span class="nav-user">Halo, <strong><?= htmlspecialchars($_SESSION['pelanggan_nama']) ?></strong></span>
      <a class="btn btn-outline" href="riwayat_pesanan.php">📋 Riwayat Pesanan</a>
      <a class="btn btn-outline btn-sm" href="logout.php">Keluar</a>
    </div>
  </nav>

  <div class="history-wrap">
    <p class="page-eyebrow">Akun Saya</p>
    <h1 class="page-title">Riwayat <em>Pesanan</em></h1>

    <?php if (empty($list)): ?>
    <div class="empty-state">
      <span class="empty-icon">🌸</span>
      <p class="empty-txt">Kamu belum memiliki pesanan.<br>Yuk mulai belanja bunga favoritmu!</p>
      <a class="btn btn-outline" href="katalog.php">Lihat Katalog</a>
    </div>

    <?php else: ?>
    <?php foreach ($list as $i => $ps):
      $st = $status_label[$ps['status']] ?? $status_label['menunggu'];
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
      <?php if ($ps['status'] === 'menunggu'): ?>
      <div class="order-foot">
        <a href="https://wa.me/6285233608339?text=<?= urlencode("Halo, saya ingin konfirmasi pesanan #{$ps['id']} atas nama {$_SESSION['pelanggan_nama']}.") ?>"
           target="_blank" class="wa-btn">
          💬 Konfirmasi WA
        </a>
      </div>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <footer>
    <p>© <?= date('Y') ?> <strong>Kios Bunga Talenta</strong> · Kota Tomohon, Sulawesi Utara</p>
  </footer>
</body>
</html>