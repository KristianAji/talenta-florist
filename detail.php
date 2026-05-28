<?php
require_once __DIR__ . '/config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil produk
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

// Variasi
$variasi = db()->prepare("SELECT * FROM variasi WHERE produk_id = ? ORDER BY urutan");
$variasi->execute([$id]);
$variasi = $variasi->fetchAll();

$v0 = $variasi[0] ?? null;
$page_title = $produk['nama'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($produk['nama']) ?> – Talenta Florist</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <style>
    :root{--cream:#faf6f0;--sand:#ede5d8;--blush:#e8c4b4;--rose:#c97a6a;--deep:#3d2b24;--green:#5a7a5a;--text:#2e1f19;--muted:#8a7060;}
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
    html{scroll-behavior:smooth;}
    body{background:var(--cream);color:var(--text);font-family:'DM Sans',sans-serif;font-weight:300;overflow-x:hidden;animation:pageIn .55s ease both;}
    @keyframes pageIn{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
    @keyframes pageOut{to{opacity:0;transform:translateY(-14px)}}
    @keyframes navSlide{from{opacity:0;transform:translateY(-22px)}to{opacity:1;transform:translateY(0)}}
    @keyframes drift{0%{transform:translateY(-100px) rotate(0deg);}100%{transform:translateY(110vh) rotate(360deg);}}
    .petal{position:fixed;border-radius:50% 0 50% 0;pointer-events:none;animation:drift linear infinite;z-index:0;}
    .petal:nth-child(1){width:55px;height:55px;background:var(--rose);top:-5%;left:5%;animation-duration:21s;animation-delay:1s;opacity:.07;}
    .petal:nth-child(2){width:45px;height:45px;background:var(--blush);top:30%;left:93%;animation-duration:18s;animation-delay:4s;opacity:.06;}
    nav{display:flex;justify-content:space-between;align-items:center;padding:1.5rem 5vw;background:var(--cream);border-bottom:1px solid var(--sand);position:relative;z-index:10;animation:navSlide .7s .1s cubic-bezier(.22,1,.36,1) both;}
    .logo{font-family:'Cormorant Garamond',serif;font-size:1.5rem;font-weight:600;letter-spacing:.04em;color:var(--deep);text-decoration:none;transition:opacity .3s;}
    .logo:hover{opacity:.7;}
    .logo span{color:var(--rose);font-style:italic;}
    .nav-links{display:flex;gap:2rem;list-style:none;}
    .nav-links a{text-decoration:none;font-size:.85rem;letter-spacing:.08em;text-transform:uppercase;color:var(--muted);transition:color .3s;position:relative;}
    .nav-links a::after{content:'';position:absolute;bottom:-3px;left:0;width:0;height:1px;background:var(--rose);transition:width .3s;}
    .nav-links a:hover::after,.nav-links a.active::after{width:100%;}
    .nav-links a:hover,.nav-links a.active{color:var(--rose);}
    .btn{display:inline-flex;align-items:center;gap:.5rem;padding:.8rem 1.8rem;border-radius:2rem;font-size:.85rem;letter-spacing:.06em;text-transform:uppercase;text-decoration:none;transition:all .3s;cursor:pointer;border:none;}
    .btn-primary{background:var(--rose);color:#fff;box-shadow:0 4px 24px rgba(201,122,106,.35);}
    .btn-primary:hover{background:var(--deep);transform:translateY(-2px);}

    /* DETAIL LAYOUT */
    .detail-wrap{max-width:1100px;margin:0 auto;padding:5rem 5vw;display:grid;grid-template-columns:1fr 1fr;gap:5rem;align-items:start;}
    .img-box{position:relative;background:#fff;border-radius:20px;overflow:hidden;box-shadow:0 12px 48px rgba(61,43,36,.1);}
    #main-img{width:100%;max-height:520px;object-fit:contain;display:block;transition:opacity .3s,transform .3s;}
    .badge-box{position:absolute;top:1rem;right:1rem;background:var(--rose);color:#fff;font-size:.65rem;letter-spacing:.08em;text-transform:uppercase;padding:.3rem .75rem;border-radius:2rem;}
    .breadcrumb{font-size:.72rem;color:var(--muted);margin-bottom:1.5rem;}
    .breadcrumb a{color:var(--rose);text-decoration:none;}
    .kat-tag{font-size:.65rem;letter-spacing:.16em;text-transform:uppercase;color:var(--rose);margin-bottom:.5rem;}
    #product-name{font-family:'Cormorant Garamond',serif;font-size:clamp(1.8rem,4vw,2.8rem);font-weight:300;line-height:1.2;margin-bottom:1rem;}
    .desc{color:var(--muted);line-height:1.9;font-size:.95rem;margin-bottom:2rem;}
    .variant-heading{font-size:.65rem;letter-spacing:.14em;text-transform:uppercase;color:var(--muted);margin-bottom:.5rem;}
    #active-variant-name{display:inline-block;font-size:.75rem;color:var(--rose);margin-left:.5rem;font-style:italic;}
    .variant-row{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:2rem;}
    .variant-btn{padding:.35rem .85rem;border-radius:2rem;border:1.5px solid var(--sand);background:#fff;font-size:.72rem;cursor:pointer;color:var(--muted);transition:all .2s;}
    .variant-btn:hover{border-color:var(--rose);color:var(--rose);}
    .variant-btn.active{background:var(--rose);border-color:var(--rose);color:#fff;box-shadow:0 2px 10px rgba(201,122,106,.35);}
    #detail-price{font-family:'Cormorant Garamond',serif;font-size:2rem;font-weight:600;color:var(--rose);display:block;margin-bottom:1.5rem;}
    .cta-row{display:flex;gap:1rem;flex-wrap:wrap;}
    .wa-big{display:inline-flex;align-items:center;gap:.5rem;padding:.9rem 2rem;background:#25d366;color:#fff;border-radius:2rem;font-size:.85rem;letter-spacing:.04em;text-decoration:none;transition:all .25s;font-weight:500;}
    .wa-big:hover{background:#128c4e;transform:translateY(-2px);box-shadow:0 6px 20px rgba(37,211,102,.4);}
    .back-btn{display:inline-flex;align-items:center;gap:.4rem;padding:.9rem 1.6rem;border-radius:2rem;border:1.5px solid var(--blush);color:var(--deep);text-decoration:none;font-size:.82rem;transition:all .25s;}
    .back-btn:hover{border-color:var(--rose);color:var(--rose);}

    footer{background:var(--deep);color:rgba(250,246,240,.5);text-align:center;padding:2rem 5vw;font-size:.78rem;letter-spacing:.04em;}
    footer a{color:var(--blush);text-decoration:none;}
    @media(max-width:768px){
      nav{flex-wrap:wrap;gap:1rem;}
      .nav-links{display:flex;width:100%;justify-content:center;gap:1.2rem;order:3;margin-top:.5rem;}
      .nav-links a{font-size:.75rem;}
      .detail-wrap{grid-template-columns:1fr;gap:2.5rem;padding:3rem 5vw;}
    }
  </style>
</head>
<body>
  <div class="petal"></div><div class="petal"></div>

  <nav>
    <a class="logo" href="index.php">Talenta <span>Florist</span></a>
    <ul class="nav-links">
      <li><a href="tentang.php">Tentang</a></li>
      <li><a href="katalog.php" class="active">Katalog</a></li>
      <li><a href="pesan.php">Cara Pesan</a></li>
      <li><a href="kontak.php">Kontak</a></li>
    </ul>
    <a class="btn btn-primary" href="https://wa.me/6285233608339" target="_blank">💬 WhatsApp</a>
  </nav>

  <!-- Variants JSON untuk JS -->
  <script id="variants-data" type="application/json">
    <?= json_encode(array_map(fn($v) => ['nama'=>$v['nama'],'harga'=>$v['harga'],'gambar'=>$v['gambar']], $variasi)) ?>
  </script>

  <div class="detail-wrap">
    <!-- Gambar -->
    <div class="img-box">
      <img id="main-img" src="<?= htmlspecialchars($v0['gambar'] ?? '') ?>" alt="<?= htmlspecialchars($produk['nama']) ?>" />
      <?php if ($produk['badge']): ?>
      <span class="badge-box"><?= htmlspecialchars($produk['badge']) ?></span>
      <?php endif; ?>
    </div>

    <!-- Info -->
    <div>
      <p class="breadcrumb"><a href="katalog.php">Katalog</a> › <?= htmlspecialchars($produk['kat_nama']) ?> › <?= htmlspecialchars($produk['nama']) ?></p>
      <p class="kat-tag"><?= htmlspecialchars($produk['kat_nama']) ?></p>
      <h1 id="product-name"><?= htmlspecialchars($produk['nama']) ?></h1>
      <p class="desc"><?= nl2br(htmlspecialchars($produk['deskripsi'])) ?></p>

      <?php if (count($variasi) > 1): ?>
      <p class="variant-heading">Pilih Variasi <span id="active-variant-name"><?= htmlspecialchars($v0['nama']) ?></span></p>
      <div class="variant-row">
        <?php foreach ($variasi as $idx => $v): ?>
        <button class="variant-btn <?= $idx===0?'active':'' ?>"><?= htmlspecialchars($v['nama']) ?></button>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
      <script>document.getElementById && (document.getElementById('active-variant-name') || 0);</script>
      <?php endif; ?>

      <span id="detail-price">Rp <?= number_format($v0['harga'] ?? 0, 0, ',', '.') ?></span>

      <div class="cta-row">
        <?php
          $waInit = urlencode("Halo Kios Bunga Talenta, saya tertarik dengan produk *{$produk['nama']}* variasi *{$v0['nama']}* (Rp " . number_format($v0['harga'],0,',','.') . "). Apakah masih tersedia?");
        ?>
        <a class="wa-big" id="wa-link" href="https://wa.me/6285233608339?text=<?= $waInit ?>" target="_blank">
          💬 Pesan via WhatsApp
        </a>
        <a class="back-btn" href="katalog.php">← Kembali ke Katalog</a>
      </div>
    </div>
  </div>

  <footer>
    <p>© <?= date('Y') ?> <strong style="color:var(--blush)">Kios Bunga Talenta</strong> · Kota Tomohon, Sulawesi Utara</p>
  </footer>

  <script src="js/main.js"></script>
  <script src="js/detail.js"></script>
</body>
</html>