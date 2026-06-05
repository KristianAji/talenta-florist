<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/base_path.php';
require_once __DIR__ . '/includes/auth_pelanggan.php';

// Redirect ke login hanya jika mencoba POST (kirim ulasan)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_SESSION['pelanggan_id'])) {
    header('Location: login.php?ref=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$is_login = !empty($_SESSION['pelanggan_id']);

// Ambil pesanan_id dari query string (jika datang dari riwayat pesanan)
$pesanan_id   = isset($_GET['pesanan_id']) ? (int)$_GET['pesanan_id'] : null;
$produk_hint  = isset($_GET['produk'])     ? trim($_GET['produk'])     : '';

// Validasi: pesanan harus milik pelanggan ini dan statusnya selesai
if ($pesanan_id) {
    $cek = db()->prepare("
        SELECT ps.id, p.nama AS produk_nama FROM pesanan ps
        JOIN produk p ON p.id = ps.produk_id
        WHERE ps.id = ? AND ps.pelanggan_id = ? AND ps.status = 'selesai'
    ");
    $cek->execute([$pesanan_id, $_SESSION['pelanggan_id']]);
    $pesanan_ref = $cek->fetch();
    if (!$pesanan_ref) $pesanan_id = null;
}

$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama   = trim($_POST['nama']   ?? '');
    $pesan  = trim($_POST['pesan']  ?? '');
    $rating = min(5, max(1, (int)($_POST['rating'] ?? 5)));
    $pid    = isset($_POST['pesanan_id']) ? (int)$_POST['pesanan_id'] : null;

    if (!$nama || !$pesan) {
        $error = 'Nama dan pesan wajib diisi.';
    } else {
        if ($pid) {
            $duplikat = db()->prepare("SELECT id FROM testimoni WHERE pelanggan_id = ? AND pesanan_id = ?");
            $duplikat->execute([$_SESSION['pelanggan_id'], $pid]);
            if ($duplikat->fetch()) {
                $error = 'Kamu sudah memberikan ulasan untuk pesanan ini.';
            }
        }

        if (!$error) {
            db()->prepare('
                INSERT INTO testimoni (nama, pesan, rating, aktif, pelanggan_id, pesanan_id)
                VALUES (?,?,?,0,?,?)
            ')->execute([$nama, $pesan, $rating, $_SESSION['pelanggan_id'], $pid ?: null]);
            $success = 'Terima kasih! Ulasan kamu sedang menunggu persetujuan admin.';
        }
    }
}

// Ambil semua ulasan milik pelanggan ini
$ulasan_saya = [];
if ($is_login) {
    $q = db()->prepare("SELECT * FROM testimoni WHERE pelanggan_id = ? ORDER BY testimoni.id DESC");
    $q->execute([$_SESSION['pelanggan_id']]);
    $ulasan_saya = $q->fetchAll();
}

// Ambil semua ulasan publik yang sudah disetujui admin (selain milik sendiri)
$param_publik = [];
$extra_where  = '';
if ($is_login) {
    $extra_where  = 'AND (t.pelanggan_id IS NULL OR t.pelanggan_id != ?)';
    $param_publik = [$_SESSION['pelanggan_id']];
}
$ulasan_publik = db()->prepare("
    SELECT t.id, t.nama, t.pesan, t.rating
    FROM testimoni t
    WHERE t.aktif = 1 $extra_where
    ORDER BY t.id DESC
");
$ulasan_publik->execute($param_publik);
$ulasan_publik = $ulasan_publik->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Beri Ulasan – Talenta Florist</title>
  <link rel="stylesheet" href="<?= base_url('css/testimoni.css') ?>" />
  <script>window.BASE_URL = '<?= base_url('') ?>';</script>
</head>
<body style="display:flex;flex-direction:column;min-height:100vh;">
  <div class="petal"></div>
  <div class="petal"></div>
  <div class="petal"></div>

  <nav>
    <a class="logo" href="<?= base_url('index.php') ?>">Talenta <span>Florist</span></a>
    <ul class="nav-links">
      <li><a href="<?= base_url('tentang.php') ?>">Tentang</a></li>
      <li><a href="<?= base_url('katalog.php') ?>">Katalog</a></li>
      <li><a href="<?= base_url('pesan.php') ?>">Cara Pesan</a></li>
      <li><a href="<?= base_url('kontak.php') ?>">Kontak</a></li>
      <li><a href="<?= base_url('testimoni.php') ?>" class="active">Ulasan</a></li>
    </ul>
    <div style="display:flex;gap:.75rem;align-items:center;">
      <?php if (is_admin_browsing()): ?>
        <span class="nav-user">Admin: <strong><?= htmlspecialchars($_SESSION['admin_nama'] ?? 'Admin') ?></strong></span>
      <?php elseif ($is_login): ?>
        <span class="nav-user">Halo, <strong><?= htmlspecialchars($_SESSION['pelanggan_nama']) ?></strong></span>
        <a class="btn btn-outline btn-sm" href="<?= base_url('riwayat_pesanan.php') ?>">📋 Riwayat Pesanan</a>
        <a class="btn btn-outline btn-sm" href="<?= base_url('notifikasi.php') ?>" style="position:relative;">
          🔔
          <span id="notif-badge" class="notif-badge-nav" style="display:none;">0</span>
        </a>
        <a class="btn btn-outline btn-sm" href="<?= base_url('logout.php') ?>">Keluar</a>
      <?php else: ?>
        <a class="btn btn-outline btn-sm" href="<?= base_url('login.php') ?>">Masuk</a>
      <?php endif; ?>
    </div>
  </nav>

  <div class="ulasan-section">
    <div class="card" style="max-width:560px;">
      <p class="card-eyebrow">Bagikan Pengalamanmu</p>
      <h1 class="card-title">Tulis <em>Ulasan</em></h1>

      <?php if ($pesanan_id && !empty($pesanan_ref)): ?>
      <div style="background:#fff8f0;border:1.5px solid #f0d5b0;border-radius:.75rem;padding:.75rem 1rem;margin-bottom:1.25rem;font-size:.85rem;color:#7a4a1a;display:flex;align-items:center;gap:.6rem;">
        🛍️ Mengulas pesanan <strong>#<?= $pesanan_id ?></strong>
        <?php if ($produk_hint): ?> — <?= htmlspecialchars($produk_hint) ?><?php endif; ?>
      </div>
      <?php else: ?>
      <p class="card-sub">Ulasanmu sangat berarti bagi kami dan membantu pelanggan lain dalam memilih produk terbaik.</p>
      <?php endif; ?>

      <?php if ($is_login): ?>
      <div class="user-info">
        <div class="user-avatar"><?= mb_strtoupper(mb_substr($_SESSION['pelanggan_nama'], 0, 1)) ?></div>
        <div>
          <div class="user-name"><?= htmlspecialchars($_SESSION['pelanggan_nama']) ?></div>
          <div class="user-sub">Menulis sebagai pelanggan terdaftar</div>
        </div>
      </div>
      <?php else: ?>
      <div style="background:#fff8f0;border:1.5px solid #f0d5b0;border-radius:.75rem;padding:.75rem 1rem;margin-bottom:1.25rem;font-size:.85rem;color:#7a4a1a;text-align:center;">
        <a href="<?= base_url('login.php') ?>?ref=testimoni.php" style="color:#c06b8a;font-weight:600;text-decoration:none;">Masuk</a> untuk menulis ulasan
      </div>
      <?php endif; ?>

      <?php if (!empty($error)): ?>
      <div class="alert alert-error">&#9888; <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <?php if (!empty($success)): ?>
      <div class="alert alert-success">&#10003; <?= $success ?></div>
      <?php endif; ?>

      <?php if (empty($success)): ?>
      <form method="POST">
        <?php if ($pesanan_id): ?>
        <input type="hidden" name="pesanan_id" value="<?= $pesanan_id ?>" />
        <?php endif; ?>

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
        <?php if ($pesanan_id): ?>
        <a href="<?= base_url('riwayat_pesanan.php') ?>" style="color:var(--rose);text-decoration:none;">&larr; Kembali ke Riwayat Pesanan</a>
        <?php else: ?>
        <a href="<?= base_url('katalog.php') ?>" style="color:var(--rose);text-decoration:none;">&larr; Kembali ke Katalog</a>
        <?php endif; ?>
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
        <?php if (!empty($u['pesanan_id'])): ?>
        <div style="font-size:.75rem;color:#999;margin-bottom:.3rem;">Pesanan #<?= $u['pesanan_id'] ?></div>
        <?php endif; ?>
        <p class="riwayat-pesan"><?= htmlspecialchars($u['pesan']) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

  <hr class="section-divider" />

  <div class="publik-section">
    <div class="publik-header">
      <div>
        <p class="page-eyebrow">Dari Pelanggan Kami</p>
        <h2 class="publik-title">Apa Kata <em>Mereka?</em></h2>
      </div>
      <?php if (!empty($ulasan_publik)): ?>
      <button class="toggle-ulasan-btn" id="toggleBtn" onclick="toggleUlasan()">
        🌸 Lihat Ulasan (<?= count($ulasan_publik) ?>)
      </button>
      <?php endif; ?>
    </div>

    <?php if (empty($ulasan_publik)): ?>
    <p style="text-align:center;color:#aaa;font-size:.9rem;padding:2rem 0;">Belum ada ulasan publik saat ini.</p>
    <?php else: ?>
    <div class="publik-grid" id="publik-grid" style="display:none;">
      <?php foreach ($ulasan_publik as $j => $u): ?>
      <div class="publik-card" style="animation-delay:<?= $j * .07 ?>s;">
        <div class="publik-card-top">
          <div class="publik-avatar"><?= mb_strtoupper(mb_substr($u['nama'], 0, 1)) ?></div>
          <div>
            <div class="publik-nama"><?= htmlspecialchars($u['nama']) ?></div>
            <div class="publik-stars">
              <?php for ($i = 1; $i <= 5; $i++) echo $i <= $u['rating'] ? '★' : '☆'; ?>
            </div>
          </div>
        </div>
        <p class="publik-pesan">"<?= htmlspecialchars($u['pesan']) ?>"</p>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

  <footer>
    <p>&copy; <?= date('Y') ?> <strong>Talenta Florist</strong> &middot; Kota Tomohon, Sulawesi Utara</p>
  </footer>

  <script src="<?= base_url('js/main.js') ?>"></script>
  <script src="<?= base_url('js/notif-pelanggan.js') ?>"></script>
</body>
</html>