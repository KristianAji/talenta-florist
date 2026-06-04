<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/base_path.php';
require_once __DIR__ . '/includes/auth_pelanggan.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = db()->prepare("
  SELECT p.*, k.nama AS kat_nama, k.slug AS kat_slug
  FROM produk p JOIN kategori k ON k.id = p.kategori_id
  WHERE p.id = ? AND p.aktif = 1
");
$stmt->execute([$id]);
$produk = $stmt->fetch();

if (!$produk) {
    http_response_code(404);
    include __DIR__ . '/views/404.view.php';
    exit;
}

$variasi = db()->prepare("SELECT * FROM variasi WHERE produk_id = ? ORDER BY urutan");
$variasi->execute([$id]);
$variasi = $variasi->fetchAll();
$v0 = $variasi[0] ?? null;

$harga_aktif = $v0['harga'] ?? 0;
$waInit = urlencode("Halo Kios Bunga Talenta, saya tertarik dengan produk *{$produk['nama']}* variasi *{$v0['nama']}* (Rp " . number_format($harga_aktif,0,',','.') . "). Apakah masih tersedia?");

$rek = db()->prepare("
  SELECT p.id, p.nama, p.badge, k.nama AS kat_nama,
         v.harga, v.gambar, v.nama AS vnama,
         ABS(v.harga - ?) AS selisih_harga
  FROM produk p
  JOIN kategori k ON k.id = p.kategori_id
  JOIN variasi  v ON v.id = (
    SELECT id FROM variasi WHERE produk_id = p.id ORDER BY urutan LIMIT 1
  )
  WHERE p.kategori_id = ? AND p.id != ? AND p.aktif = 1
  ORDER BY selisih_harga ASC
  LIMIT 4
");
$rek->execute([$harga_aktif, $produk['kategori_id'], $id]);
$rekomendasi = $rek->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($produk['nama']) ?> – Talenta Florist</title>
  <link rel="stylesheet" href="<?= base_url('css/style.css') ?>" />
</head>
<body>
  <div class="petal"></div><div class="petal"></div>

  <?php if (is_admin_browsing()): ?>
  <div class="admin-bar">
    🔍 Anda sedang melihat tampilan publik sebagai Admin
    <a href="<?= base_url('admin/dashboard.php') ?>">← Kembali ke Dashboard</a>
  </div>
  <div class="admin-bar-spacer"></div>
  <?php endif; ?>

  <nav>
    <a class="logo" href="<?= base_url('index.php') ?>">Talenta <span>Florist</span></a>
    <ul class="nav-links">
      <li><a href="<?= base_url('tentang.php') ?>">Tentang</a></li>
      <li><a href="<?= base_url('katalog.php') ?>" class="active">Katalog</a></li>
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

  <!-- Variants JSON untuk JS -->
  <script id="variants-data" type="application/json">
    <?= json_encode(array_map(fn($v) => ['nama'=>$v['nama'],'harga'=>$v['harga'],'gambar'=>$v['gambar']], $variasi)) ?>
  </script>

  <div class="detail-wrap">
    <!-- Gambar -->
    <div class="img-box">
      <img id="main-img"
           src="<?= base_url($v0['gambar'] ?? '') ?>"
           alt="<?= htmlspecialchars($produk['nama']) ?>" />
      <?php if ($produk['badge']): ?>
      <span class="badge-box"><?= htmlspecialchars($produk['badge']) ?></span>
      <?php endif; ?>
    </div>

    <!-- Info -->
    <div class="detail-info-col">
      <p class="breadcrumb">
        <a href="<?= base_url('katalog.php') ?>">Katalog</a> ›
        <?= htmlspecialchars($produk['kat_nama']) ?> ›
        <?= htmlspecialchars($produk['nama']) ?>
      </p>
      <p class="kat-tag"><?= htmlspecialchars($produk['kat_nama']) ?></p>
      <h1 class="detail-name" id="product-name"><?= htmlspecialchars($produk['nama']) ?></h1>
      <p class="detail-desc"><?= nl2br(htmlspecialchars($produk['deskripsi'])) ?></p>

      <?php if (count($variasi) > 1): ?>
      <p class="variant-heading">Pilih Variasi <span id="active-variant-name"><?= htmlspecialchars($v0['nama']) ?></span></p>
      <div class="variant-row">
        <?php foreach ($variasi as $idx => $v): ?>
        <button class="variant-btn <?= $idx===0?'active':'' ?>"><?= htmlspecialchars($v['nama']) ?></button>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <span id="detail-price">Rp <?= number_format($v0['harga'] ?? 0, 0, ',', '.') ?></span>

      <div class="cta-row">
        <a class="wa-big" id="wa-link"
           href="https://wa.me/6285233608339?text=<?= $waInit ?>"
           target="_blank">💬 Pesan via WhatsApp</a>

        <?php if (!empty($_SESSION['pelanggan_id'])): ?>
        <a class="btn btn-primary" id="btn-pesan-web"
           href="pesan_produk.php?produk=<?= $produk['id'] ?>&variasi=<?= $v0['id'] ?>">
          🌸 Pesan via Web
        </a>
        <?php else: ?>
        <a class="btn btn-outline"
           href="login.php?ref=<?= urlencode('detail.php?id='.$produk['id']) ?>">
          Masuk untuk Pesan
        </a>
        <?php endif; ?>

        <a class="back-btn" href="katalog.php">← Kembali</a>
      </div>
    </div>
  </div>

  <!-- REKOMENDASI PRODUK -->
  <?php if (!empty($rekomendasi)): ?>
  <section class="rek-section">
    <div class="rek-wrap">
      <div style="margin-bottom:2.5rem;">
        <p class="rek-label">Mungkin Kamu Suka</p>
        <h2 class="rek-title">Produk <em>Serupa</em></h2>
        <p class="rek-sub">Dari kategori <?= htmlspecialchars($produk['kat_nama']) ?> dengan harga terdekat</p>
      </div>
      <div class="rek-grid">
        <?php foreach ($rekomendasi as $r):
          $waRek = urlencode("Halo Kios Bunga Talenta, saya tertarik dengan produk *{$r['nama']}* variasi *{$r['vnama']}* (Rp " . number_format($r['harga'],0,',','.') . "). Apakah masih tersedia?");
        ?>
        <a class="rek-card" href="<?= base_url('detail.php?id='.$r['id']) ?>">
          <div class="rek-img">
            <?php if ($r['gambar']): ?>
            <img src="<?= base_url($r['gambar']) ?>" alt="<?= htmlspecialchars($r['nama']) ?>" loading="lazy" />
            <?php else: ?>
            <span>🌸</span>
            <?php endif; ?>
            <?php if ($r['badge']): ?>
            <span class="rek-badge"><?= htmlspecialchars($r['badge']) ?></span>
            <?php endif; ?>
          </div>
          <div class="rek-info">
            <p class="rek-kat"><?= htmlspecialchars($r['kat_nama']) ?></p>
            <p class="rek-nama"><?= htmlspecialchars($r['nama']) ?></p>
            <p class="rek-harga">Rp <?= number_format($r['harga'],0,',','.') ?></p>
            <div class="rek-actions">
              <span class="rek-detail">Lihat Detail →</span>
              <a class="rek-wa"
                 href="https://wa.me/6285233608339?text=<?= $waRek ?>"
                 target="_blank"
                 onclick="event.stopPropagation()">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                Pesan
              </a>
            </div>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <footer>
    <p>&copy; <?= date('Y') ?> <strong>Talenta Florist</strong> &middot; Kota Tomohon, Sulawesi Utara</p>
  </footer>

  <script src="<?= base_url('js/main.js') ?>"></script>
  <script src="<?= base_url('js/detail.js') ?>"></script>
</body>
</html>