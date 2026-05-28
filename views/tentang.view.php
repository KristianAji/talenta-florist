<?php
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}
$page_title = 'Kontak';
?>
require_once __DIR__ . '/config/db.php';

$anggota = db()->query('SELECT * FROM anggota ORDER BY urutan')->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tentang Kami – Talenta Florist</title>
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
    @keyframes badgeSpin{from{opacity:0;transform:scale(.4) rotate(-30deg)}to{opacity:1;transform:scale(1) rotate(0deg)}}
    .petal{position:fixed;border-radius:50% 0 50% 0;pointer-events:none;animation:drift linear infinite;z-index:0;}
    .petal:nth-child(1){width:60px;height:60px;background:var(--blush);top:-8%;left:8%;animation-duration:20s;opacity:.06;}
    .petal:nth-child(2){width:40px;height:40px;background:var(--rose);top:10%;left:88%;animation-duration:25s;animation-delay:4s;opacity:.06;}
    .petal:nth-child(3){width:80px;height:80px;background:var(--blush);top:40%;left:95%;animation-duration:18s;animation-delay:9s;opacity:.05;}
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
    .section-desc{max-width:520px;color:var(--muted);line-height:1.9;font-size:.95rem;}

    /* ABOUT GRID */
    .about-grid{display:grid;grid-template-columns:1fr 1fr;gap:4rem;align-items:center;}
    .about-img-wrap{position:relative;}
    .about-img-wrap img{width:100%;height:auto;max-height:550px;object-fit:cover;border-radius:2px 60px 2px 2px;box-shadow:0 12px 48px rgba(61,43,36,.1);transition:transform .4s cubic-bezier(.22,1,.36,1);display:block;}
    .about-img-wrap img:hover{transform:rotate(-1.5deg) scale(1.02);}
    .about-badge{position:absolute;bottom:-1.5rem;right:-1.5rem;background:var(--deep);color:var(--cream);border-radius:50%;width:110px;height:110px;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;font-family:'Cormorant Garamond',serif;z-index:2;animation:badgeSpin 1.2s 1s cubic-bezier(.34,1.56,.64,1) both;}
    .about-badge-num{font-size:2rem;font-weight:600;line-height:1;}
    .about-badge-txt{font-size:.6rem;letter-spacing:.1em;text-transform:uppercase;opacity:.75;}
    .about-features{display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-top:2rem;}
    .feature-card{background:var(--sand);border-radius:12px;padding:1.2rem;transition:transform .3s,box-shadow .3s,background .3s;}
    .feature-card:hover{transform:translateY(-4px);box-shadow:0 12px 32px rgba(61,43,36,.1);background:var(--blush);}
    .feature-icon{font-size:1.4rem;margin-bottom:.4rem;transition:transform .3s;}
    .feature-card:hover .feature-icon{transform:scale(1.25) rotate(-5deg);}
    .feature-title{font-size:.8rem;font-weight:500;letter-spacing:.04em;margin-bottom:.3rem;}
    .feature-desc{font-size:.75rem;color:var(--muted);line-height:1.6;}

    /* ANGGOTA */
    .anggota-section{background:var(--sand);padding:6rem 5vw;}
    .anggota-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:2rem;margin-top:2.5rem;}
    .anggota-card{background:var(--cream);border-radius:16px;padding:2rem 1.5rem;text-align:center;transition:transform .3s,box-shadow .3s;}
    .anggota-card:hover{transform:translateY(-6px);box-shadow:0 16px 40px rgba(61,43,36,.12);}
    .anggota-foto{width:80px;height:80px;border-radius:50%;object-fit:cover;margin:0 auto 1rem;border:3px solid var(--blush);background:var(--sand);}
    .anggota-foto-placeholder{width:80px;height:80px;border-radius:50%;margin:0 auto 1rem;background:linear-gradient(135deg,var(--blush),var(--rose));display:flex;align-items:center;justify-content:center;font-family:'Cormorant Garamond',serif;font-size:1.8rem;color:#fff;font-weight:600;}
    .anggota-nama{font-family:'Cormorant Garamond',serif;font-size:1.1rem;font-weight:600;margin-bottom:.25rem;}
    .anggota-nim{font-size:.7rem;letter-spacing:.1em;color:var(--muted);margin-bottom:.4rem;}
    .anggota-peran{font-size:.72rem;background:var(--blush);color:var(--deep);padding:.2rem .6rem;border-radius:2rem;display:inline-block;}

    .reveal{opacity:0;transform:translateY(38px);transition:opacity .75s cubic-bezier(.22,1,.36,1),transform .75s cubic-bezier(.22,1,.36,1);}
    .reveal.visible{opacity:1;transform:translateY(0);}
    .reveal-left{opacity:0;transform:translateX(-48px);transition:opacity .75s cubic-bezier(.22,1,.36,1),transform .75s cubic-bezier(.22,1,.36,1);}
    .reveal-left.visible{opacity:1;transform:translateX(0);}
    .reveal-right{opacity:0;transform:translateX(48px);transition:opacity .75s cubic-bezier(.22,1,.36,1),transform .75s cubic-bezier(.22,1,.36,1);}
    .reveal-right.visible{opacity:1;transform:translateX(0);}
    .divider{height:1px;background:linear-gradient(90deg,transparent,var(--sand),transparent);margin:0 5vw;}
    footer{background:var(--deep);color:rgba(250,246,240,.5);text-align:center;padding:2rem 5vw;font-size:.78rem;letter-spacing:.04em;}
    footer a{color:var(--blush);text-decoration:none;}
    @media(max-width:768px){
      nav{flex-wrap:wrap;gap:1rem;}
      .nav-links{display:flex;width:100%;justify-content:center;gap:1.2rem;order:3;margin-top:.5rem;}
      .nav-links a{font-size:.75rem;}
      .about-grid{grid-template-columns:1fr;gap:2.5rem;}
      .about-badge{right:1rem;bottom:-1rem;width:90px;height:90px;}
      .about-badge-num{font-size:1.6rem;}
    }
  </style>
</head>
<body>
  <div class="petal"></div><div class="petal"></div><div class="petal"></div>

  <nav>
    <a class="logo" href="index.php">Talenta <span>Florist</span></a>
    <ul class="nav-links">
      <li><a href="tentang.php" class="active">Tentang</a></li>
      <li><a href="katalog.php">Katalog</a></li>
      <li><a href="pesan.php">Cara Pesan</a></li>
      <li><a href="kontak.php">Kontak</a></li>
    </ul>
    <a class="btn btn-primary" href="https://wa.me/6285233608339" target="_blank">💬 WhatsApp</a>
  </nav>

  <!-- TENTANG -->
  <section id="tentang">
    <div class="about-grid">
      <div class="about-img-wrap reveal-left">
        <img src="image/Talenta.jpeg" alt="Kios Bunga Talenta" />
        <div class="about-badge">
          <span class="about-badge-num">★</span>
          <span class="about-badge-txt">Tomohon<br>Florist</span>
        </div>
      </div>
      <div class="reveal-right">
        <p class="section-label">Tentang Kami</p>
        <h2 class="section-title">Kios Bunga <em>Talenta</em></h2>
        <p class="section-desc">Kios Bunga Talenta adalah usaha florist lokal yang berlokasi di Kota Tomohon, kota bunga terbaik di Sulawesi Utara. Kami menyediakan berbagai rangkaian bunga segar untuk kebutuhan personal maupun acara seremonial.</p>
        <div class="about-features">
          <div class="feature-card reveal" style="transition-delay:.05s">
            <div class="feature-icon">🌷</div>
            <div class="feature-title">Bunga Segar</div>
            <div class="feature-desc">Langsung dari sumber terpercaya di Tomohon</div>
          </div>
          <div class="feature-card reveal" style="transition-delay:.15s">
            <div class="feature-icon">🚚</div>
            <div class="feature-title">Antar ke Lokasi</div>
            <div class="feature-desc">Pengiriman ke seluruh area Tomohon</div>
          </div>
          <div class="feature-card reveal" style="transition-delay:.25s">
            <div class="feature-icon">💌</div>
            <div class="feature-title">Custom Order</div>
            <div class="feature-desc">Desain sesuai keinginan dan budget Anda</div>
          </div>
          <div class="feature-card reveal" style="transition-delay:.35s">
            <div class="feature-icon">⚡</div>
            <div class="feature-title">Respons Cepat</div>
            <div class="feature-desc">Balas WhatsApp dalam hitungan menit</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <div class="divider"></div>

  <!-- ANGGOTA KELOMPOK (halaman statis dari DB) -->
  <section class="anggota-section">
    <p class="section-label reveal">Tim Kami</p>
    <h2 class="section-title reveal">Anggota <em>Kelompok</em></h2>
    <p class="section-desc reveal">Website ini merupakan proyek kelompok dalam mata kuliah Pemrograman Web, Universitas Sam Ratulangi.</p>
    <div class="anggota-grid">
      <?php foreach ($anggota as $a): ?>
      <div class="anggota-card reveal">
        <?php if ($a['foto']): ?>
        <img class="anggota-foto" src="uploads/<?= htmlspecialchars($a['foto']) ?>" alt="<?= htmlspecialchars($a['nama']) ?>" />
        <?php else: ?>
        <div class="anggota-foto-placeholder"><?= mb_substr($a['nama'], 0, 1) ?></div>
        <?php endif; ?>
        <div class="anggota-nama"><?= htmlspecialchars($a['nama']) ?></div>
        <div class="anggota-nim"><?= htmlspecialchars($a['nim']) ?></div>
        <span class="anggota-peran"><?= htmlspecialchars($a['peran']) ?></span>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <footer class="reveal">
    <p>© <?= date('Y') ?> <strong style="color:var(--blush)">Kios Bunga Talenta</strong> · Kota Tomohon, Sulawesi Utara</p>
    <p style="margin-top:.4rem">Website ini dibuat sebagai bagian dari program digitalisasi UMKM · <a href="https://unsrat.ac.id" target="_blank">Universitas Sam Ratulangi</a></p>
  </footer>

  <script src="js/main.js"></script>
</body>
</html>
