<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Jika pengguna belum login, paksa kembali ke halaman login
if (empty($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/config/db.php';

$page_title = 'Katalog';
$active_nav = 'katalog';

// Ambil semua kategori
$kategori_list = db()->query('SELECT * FROM kategori ORDER BY urutan')->fetchAll();

// Ambil semua produk aktif beserta variasi pertamanya
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

// Susun per produk
$produk_map = [];
foreach ($rows as $r) {
    $pid = $r['id'];
    if (!isset($produk_map[$pid])) {
        $produk_map[$pid] = [
            'id'       => $pid,
            'nama'     => $r['nama'],
            'deskripsi'=> $r['deskripsi'],
            'badge'    => $r['badge'],
            'kat_slug' => $r['kat_slug'],
            'kat_nama' => $r['kat_nama'],
            'variasi'  => [],
        ];
    }
    $produk_map[$pid]['variasi'][] = [
        'id'     => $r['vid'],
        'nama'   => $r['vnama'],
        'harga'  => $r['harga'],
        'gambar' => $r['gambar'],
    ];
}
$produk_list = array_values($produk_map);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Katalog Bunga – Talenta Florist</title>
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
    @keyframes fadeUp{from{opacity:0;transform:translateY(30px)}to{opacity:1;transform:translateY(0)}}
    @keyframes drift{0%{transform:translateY(-100px) rotate(0deg);}100%{transform:translateY(110vh) rotate(360deg);}}
    .petal{position:fixed;border-radius:50% 0 50% 0;pointer-events:none;animation:drift linear infinite;z-index:0;}
    .petal:nth-child(1){width:55px;height:55px;background:var(--rose);top:-5%;left:5%;animation-duration:21s;animation-delay:1s;opacity:.07;}
    .petal:nth-child(2){width:35px;height:35px;background:var(--blush);top:15%;left:92%;animation-duration:17s;animation-delay:6s;opacity:.06;}
    .petal:nth-child(3){width:70px;height:70px;background:var(--green);top:55%;left:2%;animation-duration:23s;animation-delay:3s;opacity:.05;}
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
    section{padding:6rem 5vw;position:relative;}
    .section-label{font-size:.7rem;letter-spacing:.22em;text-transform:uppercase;color:var(--rose);margin-bottom:.75rem;}
    .section-title{font-family:'Cormorant Garamond',serif;font-size:clamp(2rem,4vw,3rem);font-weight:300;line-height:1.2;margin-bottom:1rem;}
    .section-title em{font-style:italic;color:var(--rose);}
    .catalog-header{display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:3rem;flex-wrap:wrap;gap:1rem;animation:fadeUp .8s .3s cubic-bezier(.22,1,.36,1) both;}
    .filter-tabs{display:flex;gap:.5rem;flex-wrap:wrap;}
    .filter-btn{padding:.4rem 1rem;border-radius:2rem;font-size:.75rem;letter-spacing:.06em;text-transform:uppercase;cursor:pointer;border:1.5px solid var(--sand);background:transparent;color:var(--muted);transition:all .25s;}
    .filter-btn.active,.filter-btn:hover{background:var(--rose);border-color:var(--rose);color:#fff;}
    .catalog-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:2rem;}
    .product-card{background:#fff;border-radius:16px;overflow:hidden;transition:transform .35s cubic-bezier(.22,1,.36,1),box-shadow .35s;cursor:pointer;opacity:0;transform:translateY(32px);display:flex;flex-direction:column;}
    .product-card.show{opacity:1;transform:translateY(0);}
    .product-card:hover{transform:translateY(-8px) scale(1.01);box-shadow:0 24px 64px rgba(61,43,36,.15);}
    .product-img{width:100%;height:280px;position:relative;overflow:hidden;background:#fff;display:flex;align-items:center;justify-content:center;}
    .product-img img{width:100%;height:100%;object-fit:contain;display:block;transition:transform .45s cubic-bezier(.22,1,.36,1),opacity .25s ease;}
    .product-card:hover .product-img img{transform:scale(1.05);}
    .product-badge{position:absolute;top:.75rem;right:.75rem;background:var(--rose);color:#fff;font-size:.6rem;letter-spacing:.08em;text-transform:uppercase;padding:.25rem .6rem;border-radius:2rem;z-index:2;}
    .variant-label-img{position:absolute;bottom:.6rem;left:.75rem;background:rgba(61,43,36,.65);color:#fff;font-size:.65rem;letter-spacing:.1em;text-transform:uppercase;padding:.2rem .55rem;border-radius:2rem;z-index:2;max-width:calc(100% - 1.5rem);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .product-info{padding:1.2rem 1.4rem 1.4rem;background:#f7f3ef;flex:1;display:flex;flex-direction:column;}
    .product-cat{font-size:.65rem;letter-spacing:.14em;text-transform:uppercase;color:var(--muted);margin-bottom:.3rem;}
    .product-name{font-family:'Cormorant Garamond',serif;font-size:1.25rem;font-weight:600;margin-bottom:.3rem;}
    .product-desc{font-size:.78rem;color:var(--muted);line-height:1.6;margin-bottom:.9rem;flex:1;}
    .variant-lbl{font-size:.65rem;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);margin-bottom:.45rem;}
    .variant-selector{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:1rem;}
    .variant-btn{padding:.3rem .75rem;border-radius:2rem;border:1.5px solid var(--sand);background:#fff;font-size:.7rem;font-weight:500;cursor:pointer;color:var(--muted);transition:all .2s ease;white-space:nowrap;}
    .variant-btn:hover{border-color:var(--rose);color:var(--rose);}
    .variant-btn.active{background:var(--rose);border-color:var(--rose);color:#fff;box-shadow:0 2px 10px rgba(201,122,106,.35);}
    .product-footer{display:flex;justify-content:space-between;align-items:center;margin-top:auto;}
    .product-price{font-family:'Cormorant Garamond',serif;font-size:1.2rem;font-weight:600;color:var(--rose);}
    .wa-btn{display:inline-flex;align-items:center;gap:.35rem;padding:.45rem 1rem;background:#25d366;color:#fff;border-radius:2rem;font-size:.72rem;letter-spacing:.04em;text-decoration:none;transition:all .25s;}
    .wa-btn:hover{background:#128c4e;transform:translateY(-2px);box-shadow:0 4px 16px rgba(37,211,102,.4);}
    .detail-link{display:inline-flex;align-items:center;gap:.3rem;font-size:.72rem;color:var(--rose);text-decoration:none;margin-left:.5rem;opacity:.8;transition:opacity .2s;}
    .detail-link:hover{opacity:1;}
    .reveal{opacity:0;transform:translateY(38px);transition:opacity .75s cubic-bezier(.22,1,.36,1),transform .75s cubic-bezier(.22,1,.36,1);}
    .reveal.visible{opacity:1;transform:translateY(0);}
    footer{background:var(--deep);color:rgba(250,246,240,.5);text-align:center;padding:2rem 5vw;font-size:.78rem;letter-spacing:.04em;}
    footer a{color:var(--blush);text-decoration:none;}
    @media(max-width:768px){
      nav{flex-wrap:wrap;gap:1rem;}
      .nav-links{display:flex;width:100%;justify-content:center;gap:1.2rem;order:3;margin-top:.5rem;}
      .nav-links a{font-size:.75rem;}
      .catalog-grid{grid-template-columns:repeat(auto-fill,minmax(220px,1fr));}
      .product-img{height:220px;}
    }
  </style>
</head>
<body>
  <div class="petal"></div><div class="petal"></div><div class="petal"></div>

  <nav>
    <a class="logo" href="index.php">Talenta <span>Florist</span></a>
    <ul class="nav-links">
      <li><a href="tentang.php">Tentang</a></li>
      <li><a href="katalog.php">Katalog</a></li>
      <li><a href="pesan.php">Cara Pesan</a></li>
      <li><a href="kontak.php">Kontak</a></li>
      <li><a href="logout.php" style="color: #dc2626; font-weight: 500;">Keluar</a></li>
    </ul>
    <a class="btn btn-primary" href="https://wa.me/6285233608339" target="_blank">💬 WhatsApp</a>
  </nav>

  <section id="katalog">
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
            <span class="product-price">Rp <?= number_format($v0['harga'],0,',','.') ?></span>
            <div style="display:flex;align-items:center;gap:.3rem">
              <a class="detail-link" href="detail.php?id=<?= $p['id'] ?>">Detail →</a>
              <a class="wa-btn" href="https://wa.me/6285233608339?text=<?= $waText ?>" target="_blank">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Pesan
              </a>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <footer class="reveal">
    <p>© <?= date('Y') ?> <strong style="color:var(--blush)">Kios Bunga Talenta</strong> · Kota Tomohon, Sulawesi Utara</p>
    <p style="margin-top:.4rem">Website ini dibuat sebagai bagian dari program digitalisasi UMKM · <a href="https://unsrat.ac.id" target="_blank">Universitas Sam Ratulangi</a></p>
  </footer>

  <script src="js/main.js"></script>
  <script src="js/katalog.js"></script>
</body>
</html>
