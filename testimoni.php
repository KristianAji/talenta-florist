<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/config/db.php';

// Harus login sebagai pelanggan
if (empty($_SESSION['pelanggan_id'])) {
    header('Location: login.php?ref=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama   = trim($_POST['nama']  ?? '');
    $pesan  = trim($_POST['pesan'] ?? '');
    $rating = min(5, max(1, (int)($_POST['rating'] ?? 5)));

    if (!$nama || !$pesan) {
        $error = 'Nama dan pesan wajib diisi.';
    } else {
        db()->prepare('INSERT INTO testimoni (nama, pesan, rating, aktif) VALUES (?,?,?,0)')
           ->execute([$nama, $pesan, $rating]);
        $success = 'Terima kasih! Ulasan kamu sedang menunggu persetujuan admin.';
    }
}

// Ambil semua ulasan milik pelanggan ini
$ulasan_saya = db()->prepare("SELECT * FROM testimoni WHERE nama = ? ORDER BY id DESC");
$ulasan_saya->execute([$_SESSION['pelanggan_nama']]);
$ulasan_saya = $ulasan_saya->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Beri Ulasan – Talenta Florist</title>
  <link rel="stylesheet" href="css/testimoni.css" />
</head>
<body style="display:flex;flex-direction:column;min-height:100vh;">
  <div class="petal"></div>
  <div class="petal"></div>
  <div class="petal"></div>

  <nav>
    <a class="logo" href="index.php">Talenta <span>Florist</span></a>
    <ul class="nav-links">
      <li><a href="tentang.php">Tentang</a></li>
      <li><a href="katalog.php">Katalog</a></li>
      <li><a href="pesan.php">Cara Pesan</a></li>
      <li><a href="kontak.php">Kontak</a></li>
      <li><a href="testimoni.php" class="active">Ulasan</a></li>
    </ul>
    <div style="display:flex;gap:.75rem;align-items:center;">
      <span class="nav-user">Halo, <strong><?= htmlspecialchars($_SESSION['pelanggan_nama']) ?></strong></span>
      <a class="btn btn-outline btn-sm" href="riwayat_pesanan.php">📋 Riwayat Pesanan</a>
      <a class="btn btn-outline btn-sm" href="logout.php">Keluar</a>
    </div>
  </nav>

  <div class="ulasan-section">
    <div class="card" style="max-width:560px;">
      <p class="card-eyebrow">Bagikan Pengalamanmu</p>
      <h1 class="card-title">Tulis <em>Ulasan</em></h1>
      <p class="card-sub">Ulasanmu sangat berarti bagi kami dan membantu pelanggan lain dalam memilih produk terbaik.</p>

      <div class="user-info">
        <div class="user-avatar"><?= mb_strtoupper(mb_substr($_SESSION['pelanggan_nama'], 0, 1)) ?></div>
        <div>
          <div class="user-name"><?= htmlspecialchars($_SESSION['pelanggan_nama']) ?></div>
          <div class="user-sub">Menulis sebagai pelanggan terdaftar</div>
        </div>
      </div>

      <?php if (!empty($error)): ?>
      <div class="alert alert-error">&#9888; <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <?php if (!empty($success)): ?>
      <div class="alert alert-success">&#10003; <?= $success ?></div>
      <?php endif; ?>

      <?php if (empty($success)): ?>
      <form method="POST">
        <div class="form-group">
          <label>Nama yang Ditampilkan *</label>
          <input type="text" name="nama"
                 value="<?= htmlspecialchars($_POST['nama'] ?? $_SESSION['pelanggan_nama']) ?>"
                 placeholder="Nama kamu" required />
        </div>

        <div class="star-group">
          <label>Rating *</label>
          <div class="star-row">
            <?php for ($i = 5; $i >= 1; $i--): ?>
            <input type="radio" name="rating" id="star<?= $i ?>" value="<?= $i ?>"
                   <?= (($_POST['rating'] ?? 5) == $i) ? 'checked' : '' ?> />
            <label for="star<?= $i ?>">&#9733;</label>
            <?php endfor; ?>
          </div>
        </div>

        <div class="form-group">
          <label>Pesan / Ulasan *</label>
          <textarea name="pesan" placeholder="Ceritakan pengalamanmu dengan produk kami..." required><?= htmlspecialchars($_POST['pesan'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn-submit">Kirim Ulasan</button>
      </form>
      <?php endif; ?>

      <div style="text-align:center;margin-top:1.5rem;font-size:.82rem;color:var(--muted);">
        <a href="katalog.php" style="color:var(--rose);text-decoration:none;">&larr; Kembali ke Katalog</a>
      </div>
    </div>

    <?php if (!empty($ulasan_saya)): ?>
    <div class="riwayat-wrap">
      <p class="riwayat-label">Ulasan Kamu Sebelumnya</p>
      <?php foreach ($ulasan_saya as $u): ?>
      <div class="riwayat-card">
        <div class="riwayat-top">
          <span class="riwayat-stars">
            <?php for ($i = 1; $i <= 5; $i++) echo $i <= $u['rating'] ? '&#9733;' : '&#9734;'; ?>
          </span>
          <?php if ($u['aktif']): ?>
            <span class="badge-approved">&#10003; Ditampilkan</span>
          <?php else: ?>
            <span class="badge-pending">&#9203; Menunggu Persetujuan</span>
          <?php endif; ?>
        </div>
        <p class="riwayat-pesan"><?= htmlspecialchars($u['pesan']) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

  <footer>
    <p>&copy; <?= date('Y') ?> <strong>Kios Bunga Talenta</strong> &middot; Kota Tomohon, Sulawesi Utara</p>
  </footer>

  <script src="js/main.js"></script>
</body>
</html>