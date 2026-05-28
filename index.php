<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// PENJAGA PINTU: Jika belum login, paksa pindah ke halaman login
if (empty($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/config/db.php';

$page_title = 'Beranda';
$active_nav = '';

// Ambil statistik dari DB
$jml_produk    = db()->query('SELECT COUNT(*) FROM produk WHERE aktif=1')->fetchColumn();
$jml_testimoni = db()->query('SELECT COUNT(*) FROM testimoni WHERE aktif=1')->fetchColumn();
$testimoni     = db()->query('SELECT * FROM testimoni WHERE aktif=1 ORDER BY dibuat_pada DESC LIMIT 3')->fetchAll();
?>
<!DOCTYPE html>
  <html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Talenta Florist – Kota Tomohon</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
    <style>
      :root{--cream:#faf6f0;--sand:#ede5d8;--blush:#e8c4b4;--rose:#c97a6a;--deep:#3d2b24;--green:#5a7a5a;--text:#2e1f19;--muted:#8a7060;}
      *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
      html{scroll-behavior:smooth;}
      body{background:var(--cream);color:var(--text);font-family:'DM Sans',sans-serif;font-weight:300;overflow-x:hidden;animation:pageIn .55s ease both;}

      @keyframes pageIn {from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
      @keyframes pageOut{to{opacity:0;transform:translateY(-14px)}}
      @keyframes navSlide{from{opacity:0;transform:translateY(-22px)}to{opacity:1;transform:translateY(0)}}
      @keyframes fadeUp{from{opacity:0;transform:translateY(30px)}to{opacity:1;transform:translateY(0)}}
      @keyframes drift{0%{transform:translateY(-120px) rotate(0deg);opacity:.08;}50%{opacity:.13;}100%{transform:translateY(110vh) rotate(360deg);opacity:.04;}}
      @keyframes statPop{from{opacity:0;transform:scale(.6)}to{opacity:1;transform:scale(1)}}

      /* HERO */
      .hero{min-height:100svh;display:grid;grid-template-rows:auto 1fr auto;position:relative;overflow:hidden;}
      .hero::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 60% at 70% 30%,rgba(201,122,106,.18) 0%,transparent 65%),radial-gradient(ellipse 50% 70% at 20% 80%,rgba(90,122,90,.12) 0%,transparent 60%);pointer-events:none;}
      .petal{position:absolute;border-radius:50% 0 50% 0;opacity:.08;animation:drift linear infinite;}
      .petal:nth-child(1){width:80px;height:80px;background:var(--rose);top:-10%;left:15%;animation-duration:18s;}
      .petal:nth-child(2){width:50px;height:50px;background:var(--blush);top:5%;left:70%;animation-duration:22s;animation-delay:3s;}
      .petal:nth-child(3){width:100px;height:100px;background:var(--rose);top:20%;left:85%;animation-duration:16s;animation-delay:7s;}
      .petal:nth-child(4){width:60px;height:60px;background:var(--green);top:60%;left:5%;animation-duration:25s;animation-delay:2s;}
      .petal:nth-child(5){width:40px;height:40px;background:var(--blush);top:80%;left:90%;animation-duration:20s;animation-delay:5s;}

      /* NAV */
      nav{display:flex;justify-content:space-between;align-items:center;padding:1.5rem 5vw;position:relative;z-index:10;animation:navSlide .7s .1s cubic-bezier(.22,1,.36,1) both;}
      .logo{font-family:'Cormorant Garamond',serif;font-size:1.5rem;font-weight:600;letter-spacing:.04em;color:var(--deep);text-decoration:none;transition:opacity .3s;}
      .logo:hover{opacity:.7;}
      .logo span{color:var(--rose);font-style:italic;}
      .nav-links{display:flex;gap:2rem;list-style:none;}
      .nav-links a{text-decoration:none;font-size:.85rem;letter-spacing:.08em;text-transform:uppercase;color:var(--muted);transition:color .3s;position:relative;}
      .nav-links a::after{content:'';position:absolute;bottom:-3px;left:0;width:0;height:1px;background:var(--rose);transition:width .3s;}
      .nav-links a:hover::after{width:100%;}
      .nav-links a:hover{color:var(--rose);}
      .btn{display:inline-flex;align-items:center;gap:.5rem;padding:.8rem 1.8rem;border-radius:2rem;font-size:.85rem;letter-spacing:.06em;text-transform:uppercase;text-decoration:none;transition:all .3s;cursor:pointer;border:none;}
      .btn-primary{background:var(--rose);color:#fff;box-shadow:0 4px 24px rgba(201,122,106,.35);}
      .btn-primary:hover{background:var(--deep);box-shadow:0 6px 32px rgba(61,43,36,.3);transform:translateY(-2px);}
      .btn-outline{background:transparent;color:var(--deep);border:1.5px solid var(--blush);}
      .btn-outline:hover{border-color:var(--rose);color:var(--rose);transform:translateY(-2px);}

      /* HERO CENTER */
      .hero-center{display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:0 5vw;gap:1.5rem;position:relative;z-index:2;}
      .hero-eyebrow{font-size:.75rem;letter-spacing:.25em;text-transform:uppercase;color:var(--muted);display:flex;align-items:center;gap:.75rem;animation:fadeUp .85s .35s cubic-bezier(.22,1,.36,1) both;}
      .hero-eyebrow::before,.hero-eyebrow::after{content:'';display:block;width:40px;height:1px;background:var(--muted);opacity:.5;}
      h1{font-family:'Cormorant Garamond',serif;font-size:clamp(3rem,9vw,7rem);font-weight:300;line-height:1.05;letter-spacing:-.01em;animation:fadeUp .9s .5s cubic-bezier(.22,1,.36,1) both;}
      h1 em{font-style:italic;color:var(--rose);}
      .hero-sub{max-width:480px;font-size:1rem;line-height:1.8;color:var(--muted);animation:fadeUp .9s .65s cubic-bezier(.22,1,.36,1) both;}
      .hero-btns{display:flex;gap:1rem;flex-wrap:wrap;justify-content:center;margin-top:.5rem;animation:fadeUp .9s .8s cubic-bezier(.22,1,.36,1) both;}

      /* HERO STRIP */
      .hero-strip{display:flex;justify-content:center;gap:3rem;padding:1.5rem 5vw 2.5rem;position:relative;z-index:2;animation:fadeUp .9s .95s cubic-bezier(.22,1,.36,1) both;}
      .stat{text-align:center;}
      .stat-num{font-family:'Cormorant Garamond',serif;font-size:2rem;font-weight:600;color:var(--rose);animation:statPop .6s 1.2s cubic-bezier(.34,1.56,.64,1) both;}
      .stat-label{font-size:.7rem;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);}

      .divider{height:1px;background:linear-gradient(90deg,transparent,var(--sand),transparent);margin:0 5vw;}

      /* TESTIMONI */
      .testi-section{padding:6rem 5vw;background:var(--sand);}
      .testi-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:1.5rem;margin-top:2.5rem;}
      .testi-card{background:var(--cream);border-radius:16px;padding:1.8rem;opacity:0;transform:translateY(28px);transition:opacity .6s cubic-bezier(.22,1,.36,1),transform .6s cubic-bezier(.22,1,.36,1);}
      .testi-card.visible{opacity:1;transform:translateY(0);}
      .testi-stars{color:var(--rose);font-size:1rem;margin-bottom:.75rem;}
      .testi-pesan{font-size:.9rem;color:var(--muted);line-height:1.8;margin-bottom:1rem;font-style:italic;}
      .testi-nama{font-size:.75rem;font-weight:500;letter-spacing:.06em;text-transform:uppercase;color:var(--deep);}

      /* SCROLL REVEAL */
      .reveal{opacity:0;transform:translateY(38px);transition:opacity .75s cubic-bezier(.22,1,.36,1),transform .75s cubic-bezier(.22,1,.36,1);}
      .reveal.visible{opacity:1;transform:translateY(0);}

      footer{background:var(--deep);color:rgba(250,246,240,.5);text-align:center;padding:2rem 5vw;font-size:.78rem;letter-spacing:.04em;}
      footer a{color:var(--blush);text-decoration:none;}

      @media(max-width:768px){
        nav{flex-wrap:wrap;gap:1rem;}
        .nav-links{display:flex;width:100%;justify-content:center;gap:1.2rem;order:3;margin-top:.5rem;}
        .nav-links a{font-size:.75rem;}
        .hero-strip{gap:1.5rem;flex-wrap:wrap;}
      }
    </style>
  </head>
  <body>
    <section class="hero">
      <div class="petal"></div><div class="petal"></div><div class="petal"></div>
      <div class="petal"></div><div class="petal"></div>

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

      <div class="hero-center">
        <p class="hero-eyebrow">Kota Tomohon, Sulawesi Utara</p>
        <h1>Rangkaian Bunga<br><em>Terbaik</em> untuk<br>Setiap Momen</h1>
        <p class="hero-sub">Dari pernikahan hingga wisuda, kami hadirkan keindahan bunga segar pilihan dengan harga terjangkau dan pengiriman ke seluruh Tomohon.</p>
        <div class="hero-btns">
          <a class="btn btn-primary" href="katalog.php">🌸 Lihat Katalog</a>
          <a class="btn btn-outline" href="pesan.php">Cara Pemesanan</a>
        </div>
      </div>

      <div class="hero-strip">
        <div class="stat">
          <div class="stat-num">100+</div>
          <div class="stat-label">Pelanggan Puas</div>
        </div>
        <div class="stat">
          <div class="stat-num"><?= $jml_produk ?>+</div>
          <div class="stat-label">Jenis Produk</div>
        </div>
        <div class="stat">
          <div class="stat-num">5★</div>
          <div class="stat-label">Rating Layanan</div>
        </div>
      </div>
    </section>

    <div class="divider"></div>

    <!-- TESTIMONI DINAMIS -->
    <?php if ($testimoni): ?>
    <section class="testi-section">
      <p class="section-label reveal" style="font-size:.7rem;letter-spacing:.22em;text-transform:uppercase;color:var(--rose);margin-bottom:.75rem;">Apa Kata Mereka</p>
      <h2 class="reveal" style="font-family:'Cormorant Garamond',serif;font-size:clamp(1.8rem,3.5vw,2.6rem);font-weight:300;">Ulasan <em style="font-style:italic;color:var(--rose)">Pelanggan</em></h2>
      <div class="testi-grid">
        <?php foreach ($testimoni as $t): ?>
        <div class="testi-card reveal">
          <div class="testi-stars"><?= str_repeat('★', (int)$t['rating']) ?></div>
          <p class="testi-pesan">"<?= htmlspecialchars($t['pesan']) ?>"</p>
          <span class="testi-nama">— <?= htmlspecialchars($t['nama']) ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endif; ?>

    <footer class="reveal">
      <p>© <?= date('Y') ?> <strong style="color:var(--blush)">Kios Bunga Talenta</strong> · Kota Tomohon, Sulawesi Utara</p>
      <p style="margin-top:.4rem">Website ini dibuat sebagai bagian dari program digitalisasi UMKM · <a href="https://unsrat.ac.id" target="_blank">Universitas Sam Ratulangi</a></p>
    </footer>

    <script src="js/main.js"></script>
  </body>
  </html>
