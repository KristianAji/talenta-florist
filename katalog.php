<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth_pelanggan.php';

$kategori_list = db()->query('SELECT * FROM kategori ORDER BY urutan')->fetchAll();

$sql = "
  SELECT p.id, p.nama, p.deskripsi, p.badge, k.slug AS kat_slug, k.nama AS kat_nama,
         v.id AS vid, v.nama AS vnama, v.harga, v.gambar
  FROM produk p
  JOIN kategori k ON k.id = p.kategori_id
  JOIN variasi  v ON v.produk_id = p.id
  WHERE p.aktif = 1
  ORDER BY k.urutan, p.id, v.urutan
";
$rows = db()->query($sql)->fetchAll();

$produk_map = [];
foreach ($rows as $r) {
    $pid = $r['id'];
    if (!isset($produk_map[$pid])) {
        $produk_map[$pid] = [
            'id'        => $pid,
            'nama'      => $r['nama'],
            'deskripsi' => $r['deskripsi'],
            'badge'     => $r['badge'],
            'kat_slug'  => $r['kat_slug'],
            'kat_nama'  => $r['kat_nama'],
            'variasi'   => [],
        ];
    }
    $produk_map[$pid]['variasi'][] = ['id'=>$r['vid'],'nama'=>$r['vnama'],'harga'=>$r['harga'],'gambar'=>$r['gambar']];
}
$produk_list = array_values($produk_map);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Katalog Bunga – Talenta Florist</title>
  <link rel="stylesheet" href="css/katalog.css" />
</head>
<body>
  <div class="petal"></div><div class="petal"></div><div class="petal"></div>

  <nav>
    <a class="logo" href="index.php">Talenta <span>Florist</span></a>
    <ul class="nav-links">
      <li><a href="tentang.php">Tentang</a></li>
      <li><a href="katalog.php" class="active">Katalog</a></li>
      <li><a href="pesan.php">Cara Pesan</a></li>
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

  <section class="catalog-section">
    <div class="catalog-header">
      <div>
        <p class="section-label">Produk Kami</p>
        <h2 class="section-title">Katalog <em>Bunga</em></h2>
      </div>
      <div class="filter-tabs">
        <button class="filter-btn active" data-kat="semua">Semua</button>
        <?php foreach ($kategori_list as $k): ?>
        <button class="filter-btn" data-kat="<?= htmlspecialchars($k['slug']) ?>">
          <?= htmlspecialchars($k['nama']) ?>
        </button>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="catalog-grid">
      <?php foreach ($produk_list as $p):
        $v0 = $p['variasi'][0];
        $waText = urlencode("Halo Kios Bunga Talenta, saya tertarik dengan produk *{$p['nama']}* variasi *{$v0['nama']}* (Rp " . number_format($v0['harga'],0,',','.') . "). Apakah masih tersedia?");
        $variantsJson = htmlspecialchars(json_encode($p['variasi']), ENT_QUOTES);
      ?>
      <div class="product-card" data-kat="<?= htmlspecialchars($p['kat_slug']) ?>" data-variants="<?= $variantsJson ?>">
        <div class="product-img">
          <img src="<?= htmlspecialchars($v0['gambar'] ?? '') ?>" alt="<?= htmlspecialchars($p['nama']) ?>" loading="lazy" />
          <?php if ($p['badge']): ?>
          <span class="product-badge"><?= htmlspecialchars($p['badge']) ?></span>
          <?php endif; ?>
          <span class="variant-label-img"><?= htmlspecialchars($v0['nama']) ?></span>
        </div>
        <div class="product-info">
          <p class="product-cat"><?= htmlspecialchars($p['kat_nama']) ?></p>
          <h3 class="product-name"><?= htmlspecialchars($p['nama']) ?></h3>
          <p class="product-desc"><?= htmlspecialchars($p['deskripsi']) ?></p>

          <?php if (count($p['variasi']) > 1): ?>
          <p class="variant-lbl">Pilih Variasi</p>
          <div class="variant-selector">
            <?php foreach ($p['variasi'] as $idx => $v): ?>
            <button class="variant-btn <?= $idx===0?'active':'' ?>"
                    onclick="selectVariant(this,<?= $p['id'] ?>,<?= $idx ?>)">
              <?= htmlspecialchars($v['nama']) ?>
            </button>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>

<div class="product-footer">
  <span class="product-price" id="price-<?= $p['id'] ?>">Rp <?= number_format($v0['harga'],0,',','.') ?></span>
  <div class="product-actions">
    <a class="detail-link" href="detail.php?id=<?= $p['id'] ?>">Detail →</a>
    <a class="wa-btn" id="wa-btn-<?= $p['id'] ?>"
       href="https://wa.me/6285233608339?text=<?= $waText ?>" target="_blank">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
      Pesan Via WA
    </a>
    <a class="web-btn" id="web-btn-<?= $p['id'] ?>"
       href="pesan_produk.php?produk=<?= $p['id'] ?>&variasi=<?= $v0['id'] ?>">
      🌸 Pesan Via Web
    </a>
  </div>
</div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <footer class="reveal">
    <p>&copy; <?= date('Y') ?> <strong>Talenta Florist</strong> &middot; Kota Tomohon, Sulawesi Utara</p>
  </footer>

  <script src="js/main.js"></script>
  <script src="js/katalog.js"></script>
</body>
</html>