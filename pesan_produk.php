<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/base_path.php';
require_once __DIR__ . '/includes/auth_pelanggan.php';
require_once __DIR__ . '/includes/notifikasi.php'; // ← TAMBAHAN

if (!empty($_SESSION['admin_id']))    { header('Location: admin/dashboard.php'); exit; }
if (empty($_SESSION['pelanggan_id'])){ header('Location: login.php?ref='.urlencode($_SERVER['REQUEST_URI'])); exit; }

$produk_id  = isset($_GET['produk'])  ? (int)$_GET['produk']  : 0;
$variasi_id = isset($_GET['variasi']) ? (int)$_GET['variasi'] : 0;

$stmt = db()->prepare("SELECT p.*,k.nama AS kat_nama FROM produk p JOIN kategori k ON k.id=p.kategori_id WHERE p.id=? AND p.aktif=1");
$stmt->execute([$produk_id]);
$produk = $stmt->fetch();
if (!$produk) { header('Location: katalog.php'); exit; }

$stmt = db()->prepare("SELECT * FROM variasi WHERE id=? AND produk_id=?");
$stmt->execute([$variasi_id,$produk_id]);
$variasi = $stmt->fetch();
if (!$variasi) { header('Location: detail.php?id='.$produk_id); exit; }

$all_variasi = db()->prepare("SELECT * FROM variasi WHERE produk_id=? ORDER BY urutan");
$all_variasi->execute([$produk_id]);
$all_variasi = $all_variasi->fetchAll();

$pel = db()->prepare("SELECT * FROM pelanggan WHERE id=?");
$pel->execute([$_SESSION['pelanggan_id']]);
$pel = $pel->fetch();

$error = ''; $success = false; $pesanan_id = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_penerima = trim($_POST['nama_penerima'] ?? '');
    $telepon       = trim($_POST['telepon']       ?? '');
    $alamat        = trim($_POST['alamat']        ?? '');
    $catatan       = trim($_POST['catatan']       ?? '');
    $jumlah        = max(1,(int)($_POST['jumlah'] ?? 1));
    $var_id        = (int)($_POST['variasi_id']   ?? $variasi_id);

    $stmtV = db()->prepare("SELECT * FROM variasi WHERE id=? AND produk_id=?");
    $stmtV->execute([$var_id,$produk_id]);
    $var_dipilih = $stmtV->fetch();

    if (!$nama_penerima || !$telepon || !$alamat) {
        $error = 'Nama penerima, telepon, dan alamat wajib diisi.';
    } elseif (!$var_dipilih) {
        $error = 'Variasi tidak valid.';
    } else {
        $total = $var_dipilih['harga'] * $jumlah;
        db()->prepare("INSERT INTO pesanan (pelanggan_id,produk_id,variasi_id,jumlah,total_harga,nama_penerima,telepon,alamat,catatan) VALUES (?,?,?,?,?,?,?,?,?)")
           ->execute([$_SESSION['pelanggan_id'],$produk_id,$var_id,$jumlah,$total,$nama_penerima,$telepon,$alamat,$catatan]);
        $success    = true;
        $pesanan_id = db()->lastInsertId();

        // ── NOTIFIKASI: Beritahu admin ada pesanan baru ──
        kirim_notif(
            'admin',
            '🌸 Pesanan baru dari ' . $_SESSION['pelanggan_nama'] .
            ' — ' . $produk['nama'] . ' (' . $var_dipilih['nama'] . ')' .
            ' x' . $jumlah . ' | #' . $pesanan_id,
            'admin/pesanan/index.php'
        );
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pesan – <?= htmlspecialchars($produk['nama']) ?></title>
  <link rel="stylesheet" href="css/pesan_produk.css" />
  <script>window.BASE_URL = '<?= base_url('') ?>';</script>
</head>
<body style="display:flex;flex-direction:column;min-height:100vh;">
  <div class="petal"></div><div class="petal"></div>

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
      <a class="btn btn-outline btn-sm" href="<?= base_url('riwayat_pesanan.php') ?>">📋 Riwayat Pesanan</a>
      <a class="btn btn-outline btn-sm" href="<?= base_url('notifikasi.php') ?>" style="position:relative">
        🔔
        <span id="notif-badge" class="notif-badge-nav" style="display:none">0</span>
      </a>
      <a class="btn btn-outline btn-sm" href="logout.php">Keluar</a>
    </div>
  </nav>

  <div class="order-wrap">
    <?php if ($success): ?>
    <div class="success-wrap">
      <span class="success-icon">🌸</span>
      <h1 class="success-title">Pesanan <em>Berhasil!</em></h1>
      <span class="success-id">ID Pesanan #<?= $pesanan_id ?></span>
      <p class="success-desc">Pesanan kamu sudah kami terima. Selanjutnya konfirmasi pembayaran via WhatsApp dan admin akan segera memproses pesananmu.</p>
      <a class="wa-confirm"
         href="https://wa.me/6285233608339?text=<?= urlencode("Halo Kios Bunga Talenta, saya ingin konfirmasi pesanan dengan ID #{$pesanan_id} atas nama {$_SESSION['pelanggan_nama']}. Mohon info pembayarannya.") ?>"
         target="_blank">💬 Konfirmasi via WhatsApp</a>
      <div class="btn-group">
        <a class="btn btn-outline" href="riwayat_pesanan.php">📋 Lihat Riwayat Pesanan</a>
        <a class="btn btn-outline" href="katalog.php">← Kembali ke Katalog</a>
      </div>
    </div>

    <?php else: ?>
    <div class="page-header">
      <p class="page-eyebrow">Form Pemesanan</p>
      <h1 class="page-title">Pesan <em>Sekarang</em></h1>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-error">⚠ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" id="form-pesanan">
      <div class="order-grid">
        <div class="form-card">
          <div class="form-section-title">Data Penerima</div>
          <div class="form-group">
            <label>Nama Penerima *</label>
            <input type="text" name="nama_penerima" value="<?= htmlspecialchars($_POST['nama_penerima'] ?? $pel['nama']) ?>" placeholder="Nama lengkap penerima" required />
          </div>
          <div class="form-group">
            <label>No. Telepon / WhatsApp *</label>
            <input type="text" name="telepon" value="<?= htmlspecialchars($_POST['telepon'] ?? $pel['telepon'] ?? '') ?>" placeholder="cth: 08123456789" required />
          </div>
          <div class="form-group">
            <label>Alamat Pengiriman *</label>
            <textarea name="alamat" placeholder="Alamat lengkap pengiriman…" required><?= htmlspecialchars($_POST['alamat'] ?? $pel['alamat'] ?? '') ?></textarea>
          </div>

          <div class="form-section-title" style="margin-top:1.5rem;">Detail Pesanan</div>
          <div class="form-group">
            <label>Pilih Variasi *</label>
            <select name="variasi_id" id="sel-variasi" required>
              <?php foreach ($all_variasi as $v): ?>
              <option value="<?= $v['id'] ?>" data-harga="<?= $v['harga'] ?>" data-gambar="<?= htmlspecialchars($v['gambar']??'') ?>" <?= $v['id']==$variasi_id?'selected':'' ?>>
                <?= htmlspecialchars($v['nama']) ?> — Rp <?= number_format($v['harga'],0,',','.') ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Jumlah</label>
            <div class="qty-wrap">
              <button type="button" class="qty-btn" id="qty-min">−</button>
              <input type="number" name="jumlah" id="qty-input" class="qty-input" value="<?= (int)($_POST['jumlah']??1) ?>" min="1" max="99" />
              <button type="button" class="qty-btn" id="qty-plus">+</button>
            </div>
          </div>
          <div class="form-group">
            <label>Catatan (opsional)</label>
            <textarea name="catatan" placeholder="cth: tolong tambahkan kartu ucapan…"><?= htmlspecialchars($_POST['catatan']??'') ?></textarea>
          </div>
          <div class="info-note">
            <span>ℹ️</span>
            <div><strong>Cara Pembayaran:</strong> Setelah pesanan diterima, admin akan menghubungi kamu via WhatsApp untuk konfirmasi dan info rekening pembayaran.</div>
          </div>
          <button type="submit" class="btn-submit">🌸 Buat Pesanan</button>
        </div>

        <div class="summary-card">
          <div class="summary-title">Ringkasan Pesanan</div>
          <?php if ($variasi['gambar']): ?>
          <img class="summary-img" id="sum-img" src="<?= htmlspecialchars($variasi['gambar']) ?>" alt="<?= htmlspecialchars($produk['nama']) ?>" />
          <?php else: ?>
          <div class="summary-img-placeholder">🌸</div>
          <?php endif; ?>
          <div class="summary-nama"><?= htmlspecialchars($produk['nama']) ?></div>
          <div class="summary-kat"><?= htmlspecialchars($produk['kat_nama']) ?></div>
          <div class="summary-row"><span class="label">Variasi</span><span class="val" id="sum-variasi"><?= htmlspecialchars($variasi['nama']) ?></span></div>
          <div class="summary-row"><span class="label">Harga Satuan</span><span class="val" id="sum-harga">Rp <?= number_format($variasi['harga'],0,',','.') ?></span></div>
          <div class="summary-row"><span class="label">Jumlah</span><span class="val" id="sum-jumlah">1</span></div>
          <div class="summary-total">
            <span class="label">Total</span>
            <span class="val" id="sum-total">Rp <?= number_format($variasi['harga'],0,',','.') ?></span>
          </div>
        </div>
      </div>
    </form>
    <?php endif; ?>
  </div>

  <footer>
    <p>&copy; <?= date('Y') ?> <strong>Talenta Florist</strong> &middot; Kota Tomohon, Sulawesi Utara</p>
  </footer>

  <script src="js/main.js"></script>
  <script src="js/pesan_produk.js"></script>
  <script src="<?= base_url('js/notif-pelanggan.js') ?>"></script>
</body>
</html>