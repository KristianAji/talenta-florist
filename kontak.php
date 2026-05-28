<?php $page_title = 'Kontak'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Kontak – Talenta Florist</title>
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
    @keyframes shimmer{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
    @keyframes pinBounce{0%,100%{transform:translateY(0)}50%{transform:translateY(-8px)}}
    .petal{position:fixed;border-radius:50% 0 50% 0;pointer-events:none;animation:drift linear infinite;z-index:0;}
    .petal:nth-child(1){width:50px;height:50px;background:var(--rose);top:-8%;left:30%;animation-duration:20s;animation-delay:1s;opacity:.06;}
    .petal:nth-child(2){width:70px;height:70px;background:var(--blush);top:20%;left:85%;animation-duration:26s;animation-delay:5s;opacity:.05;}
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
    .contact-grid{display:grid;grid-template-columns:1fr 1fr;gap:4rem;align-items:start;}
    .contact-cards{display:flex;flex-direction:column;gap:1rem;margin-top:2rem;}
    .contact-card{display:flex;align-items:center;gap:1rem;padding:1rem 1.2rem;background:var(--sand);border-radius:12px;text-decoration:none;color:var(--text);transition:background .25s,transform .3s cubic-bezier(.22,1,.36,1),box-shadow .3s;opacity:0;transform:translateX(-32px);}
    .contact-card.show{opacity:1;transform:translateX(0);}
    .contact-card:hover{background:var(--blush);transform:translateX(6px);box-shadow:4px 0 20px rgba(201,122,106,.2);}
    .contact-icon{font-size:1.5rem;transition:transform .35s cubic-bezier(.34,1.56,.64,1);}
    .contact-card:hover .contact-icon{transform:scale(1.25) rotate(-8deg);}
    .contact-label{font-size:.65rem;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);}
    .contact-value{font-size:.9rem;font-weight:500;}
    .map-placeholder{background:linear-gradient(135deg,var(--sand) 0%,var(--blush) 50%,var(--sand) 100%);background-size:200% 200%;animation:shimmer 4s ease infinite;border-radius:16px;height:300px;display:flex;align-items:center;justify-content:center;font-family:'Cormorant Garamond',serif;font-size:1.1rem;color:var(--muted);text-align:center;padding:2rem;position:relative;overflow:hidden;opacity:0;transform:translateY(24px);transition:opacity .75s .4s cubic-bezier(.22,1,.36,1),transform .75s .4s cubic-bezier(.22,1,.36,1);}
    .map-placeholder.show{opacity:1;transform:translateY(0);}
    .map-placeholder::before{content:'📍';font-size:3rem;display:block;margin-bottom:.75rem;animation:pinBounce 2s ease-in-out infinite;}
    .reveal{opacity:0;transform:translateY(38px);transition:opacity .75s cubic-bezier(.22,1,.36,1),transform .75s cubic-bezier(.22,1,.36,1);}
    .reveal.visible{opacity:1;transform:translateY(0);}
    .reveal-left{opacity:0;transform:translateX(-48px);transition:opacity .75s cubic-bezier(.22,1,.36,1),transform .75s cubic-bezier(.22,1,.36,1);}
    .reveal-left.visible{opacity:1;transform:translateX(0);}
    .reveal-right{opacity:0;transform:translateX(48px);transition:opacity .75s cubic-bezier(.22,1,.36,1),transform .75s cubic-bezier(.22,1,.36,1);}
    .reveal-right.visible{opacity:1;transform:translateX(0);}
    footer{background:var(--deep);color:rgba(250,246,240,.5);text-align:center;padding:2rem 5vw;font-size:.78rem;letter-spacing:.04em;}
    footer a{color:var(--blush);text-decoration:none;}
    @media(max-width:768px){
      nav{flex-wrap:wrap;gap:1rem;}
      .nav-links{display:flex;width:100%;justify-content:center;gap:1.2rem;order:3;margin-top:.5rem;}
      .nav-links a{font-size:.75rem;}
      .contact-grid{grid-template-columns:1fr;gap:2.5rem;}
    }
  </style>
</head>
<body>
  <div class="petal"></div><div class="petal"></div>

  <nav>
    <a class="logo" href="index.php">Talenta <span>Florist</span></a>
    <ul class="nav-links">
      <li><a href="tentang.php">Tentang</a></li>
      <li><a href="katalog.php">Katalog</a></li>
      <li><a href="pesan.php">Cara Pesan</a></li>
      <li><a href="kontak.php">Kontak</a></li>
      <li><a href="register.php">Daftar</a></li>
      <li><a href="login.php">Login</a></li>
    </ul>
    <a class="btn btn-primary" href="https://wa.me/6285233608339" target="_blank">💬 WhatsApp</a>
  </nav>

  <section id="kontak">
    <div class="contact-grid">
      <div class="reveal-left">
        <p class="section-label">Hubungi Kami</p>
        <h2 class="section-title">Temukan <em>Kami</em></h2>
        <p class="section-desc">Kami siap melayani Anda setiap hari. Hubungi kami melalui salah satu channel di bawah ini.</p>
        <div class="contact-cards">
          <a class="contact-card" href="https://wa.me/6285233608339" target="_blank">
            <span class="contact-icon">💬</span>
            <div><div class="contact-label">WhatsApp</div><div class="contact-value">0852-3360-8339</div></div>
          </a>
          <a class="contact-card" href="https://maps.app.goo.gl/4P4Kyo859XVRhHW58" target="_blank">
            <span class="contact-icon">📍</span>
            <div><div class="contact-label">Lokasi</div><div class="contact-value">Kota Tomohon, Sulawesi Utara</div></div>
          </a>
          <a class="contact-card" href="https://www.instagram.com/kbtalenta" target="_blank">
            <span class="contact-icon">📸</span>
            <div><div class="contact-label">Instagram</div><div class="contact-value">@kbtalenta</div></div>
          </a>
          <div class="contact-card" style="cursor:default;">
            <span class="contact-icon">🕐</span>
            <div><div class="contact-label">Jam Operasional</div><div class="contact-value">Setiap Hari, 07.00 – 20.00 WITA</div></div>
          </div>
        </div>
      </div>
      <div class="reveal-right">
        <div class="map-placeholder" id="mapBox">
          <div>
            Kios Bunga Talenta<br>
            <small style="opacity:.7">Kota Tomohon, Sulawesi Utara</small><br><br>
            <a class="btn btn-primary" href="https://maps.app.goo.gl/4P4Kyo859XVRhHW58" target="_blank" style="font-size:.75rem">Buka di Google Maps</a>
          </div>
        </div>
        <!-- Untuk embed Google Maps nyata, ganti div di atas dengan iframe:
        <iframe src="EMBED_URL" width="100%" height="300"
          style="border:0;border-radius:16px;" allowfullscreen loading="lazy"></iframe>
        -->
      </div>
    </div>
  </section>

  <footer class="reveal">
    <p>© <?= date('Y') ?> <strong style="color:var(--blush)">Kios Bunga Talenta</strong> · Kota Tomohon, Sulawesi Utara</p>
    <p style="margin-top:.4rem">Website ini dibuat sebagai bagian dari program digitalisasi UMKM · <a href="https://unsrat.ac.id" target="_blank">Universitas Sam Ratulangi</a></p>
  </footer>

  <script src="js/main.js"></script>
  <script src="js/kontak.js"></script>
</body>
</html>
