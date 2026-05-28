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
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cara Pesan – Talenta Florist</title>
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
    .petal:nth-child(1){width:65px;height:65px;background:var(--blush);top:-10%;left:20%;animation-duration:22s;animation-delay:2s;opacity:.06;}
    .petal:nth-child(2){width:45px;height:45px;background:var(--rose);top:5%;left:80%;animation-duration:19s;animation-delay:7s;opacity:.06;}
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
    .steps-bg{background:var(--deep);color:var(--cream);border-radius:24px;padding:4rem 5%;animation:fadeUp .9s .3s cubic-bezier(.22,1,.36,1) both;}
    .steps-bg .section-title{color:var(--cream);}
    .steps-bg .section-desc{color:rgba(250,246,240,.6);}
    .steps-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:2rem;margin-top:3rem;}
    .step{display:flex;flex-direction:column;gap:.75rem;position:relative;opacity:0;transform:translateY(28px);transition:opacity .6s cubic-bezier(.22,1,.36,1),transform .6s cubic-bezier(.22,1,.36,1);}
    .step.show{opacity:1;transform:translateY(0);}
    .step:not(:last-child)::after{content:'→';position:absolute;right:-1.2rem;top:.3rem;color:var(--rose);font-size:1.2rem;opacity:0;transition:opacity .4s;}
    .step.show:not(:last-child)::after{opacity:1;}
    .step-num-wrap{position:relative;display:inline-block;}
    .step-num{font-family:'Cormorant Garamond',serif;font-size:3rem;font-weight:300;color:var(--rose);line-height:1;opacity:.6;transition:opacity .3s,transform .3s;}
    .step:hover .step-num{opacity:1;transform:scale(1.1);}
    .step-title{font-family:'Cormorant Garamond',serif;font-size:1.1rem;font-weight:600;}
    .step-desc{font-size:.8rem;color:rgba(250,246,240,.55);line-height:1.7;}

    /* FAQ */
    .faq-section{padding:6rem 5vw;}
    .faq-list{max-width:700px;margin-top:2rem;}
    .faq-item{border-bottom:1px solid var(--sand);overflow:hidden;}
    .faq-q{width:100%;background:none;border:none;text-align:left;padding:1.2rem 0;font-size:.95rem;font-family:'DM Sans',sans-serif;font-weight:500;cursor:pointer;color:var(--text);display:flex;justify-content:space-between;align-items:center;transition:color .25s;}
    .faq-q:hover{color:var(--rose);}
    .faq-q::after{content:'+';font-size:1.3rem;color:var(--rose);transition:transform .3s;}
    .faq-item.open .faq-q::after{transform:rotate(45deg);}
    .faq-a{max-height:0;overflow:hidden;font-size:.875rem;color:var(--muted);line-height:1.9;transition:max-height .4s cubic-bezier(.22,1,.36,1),padding .3s;}
    .faq-item.open .faq-a{max-height:300px;padding-bottom:1.2rem;}

    .reveal{opacity:0;transform:translateY(38px);transition:opacity .75s cubic-bezier(.22,1,.36,1),transform .75s cubic-bezier(.22,1,.36,1);}
    .reveal.visible{opacity:1;transform:translateY(0);}
    footer{background:var(--deep);color:rgba(250,246,240,.5);text-align:center;padding:2rem 5vw;font-size:.78rem;letter-spacing:.04em;}
    footer a{color:var(--blush);text-decoration:none;}
    @media(max-width:768px){
      nav{flex-wrap:wrap;gap:1rem;}
      .nav-links{display:flex;width:100%;justify-content:center;gap:1.2rem;order:3;margin-top:.5rem;}
      .nav-links a{font-size:.75rem;}
      .step:not(:last-child)::after{display:none;}
      .steps-grid{gap:1.5rem;}
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
      <li><a href="logout.php" style="color: #dc2626; font-weight: 500;">Keluar</a></li>
    </ul>
    <a class="btn btn-primary" href="https://wa.me/6285233608339" target="_blank">💬 WhatsApp</a>
  </nav>

  <section id="pesan">
    <div class="steps-bg">
      <p class="section-label" style="color:var(--blush)">Mudah & Cepat</p>
      <h2 class="section-title">Cara <em>Pemesanan</em></h2>
      <p class="section-desc">Pesan bunga favorit Anda hanya dalam 4 langkah mudah melalui WhatsApp.</p>
      <div class="steps-grid">
        <div class="step"><div class="step-num-wrap"><div class="step-num">01</div></div><div class="step-title">Pilih Produk</div><div class="step-desc">Jelajahi katalog dan pilih rangkaian bunga yang Anda inginkan.</div></div>
        <div class="step"><div class="step-num-wrap"><div class="step-num">02</div></div><div class="step-title">Hubungi Kami</div><div class="step-desc">Chat via WhatsApp, sebutkan produk dan tanggal kebutuhan.</div></div>
        <div class="step"><div class="step-num-wrap"><div class="step-num">03</div></div><div class="step-title">Konfirmasi & Bayar</div><div class="step-desc">Setujui harga dan lakukan pembayaran via transfer bank.</div></div>
        <div class="step"><div class="step-num-wrap"><div class="step-num">04</div></div><div class="step-title">Terima Bunga</div><div class="step-desc">Bunga diantar ke lokasi Anda atau dapat diambil di kios.</div></div>
      </div>
    </div>
  </section>

  <!-- FAQ -->
  <section class="faq-section">
    <p class="section-label reveal">Pertanyaan Umum</p>
    <h2 class="section-title reveal">FAQ</h2>
    <div class="faq-list">
      <?php
      $faqs = [
        ['q'=>'Apakah bisa custom desain?',                  'a'=>'Tentu! Kami menerima custom order. Hubungi kami via WhatsApp dan ceritakan konsep yang Anda inginkan, kami akan membantu mewujudkannya.'],
        ['q'=>'Berapa lama proses pemesanan?',               'a'=>'Untuk pesanan reguler, kami membutuhkan minimal 1 hari. Untuk dekorasi acara besar, disarankan memesan 3–7 hari sebelum acara.'],
        ['q'=>'Apakah ada pengiriman ke luar Tomohon?',      'a'=>'Saat ini kami melayani pengiriman di dalam kota Tomohon. Untuk area lain silakan hubungi kami untuk konfirmasi ketersediaan.'],
        ['q'=>'Metode pembayaran apa yang tersedia?',        'a'=>'Kami menerima transfer bank (BRI, BNI, Mandiri) dan pembayaran tunai di kios.'],
        ['q'=>'Apakah bunga yang dijual selalu segar?',      'a'=>'Ya, kami mengutamakan kualitas. Bunga kami langsung dari sumber terpercaya di Tomohon dan dikerjakan fresh sesuai tanggal pesanan Anda.'],
      ];
      foreach ($faqs as $f): ?>
      <div class="faq-item reveal">
        <button class="faq-q"><?= htmlspecialchars($f['q']) ?></button>
        <div class="faq-a"><?= htmlspecialchars($f['a']) ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <footer class="reveal">
    <p>© <?= date('Y') ?> <strong style="color:var(--blush)">Kios Bunga Talenta</strong> · Kota Tomohon, Sulawesi Utara</p>
    <p style="margin-top:.4rem">Website ini dibuat sebagai bagian dari program digitalisasi UMKM · <a href="https://unsrat.ac.id" target="_blank">Universitas Sam Ratulangi</a></p>
  </footer>

  <script src="js/main.js"></script>
  <script>
    // Steps entrance
    const steps   = document.querySelectorAll('.step');
    const stepObs = new IntersectionObserver(entries => {
      if (entries[0].isIntersecting) {
        steps.forEach((s, i) => setTimeout(() => s.classList.add('show'), 300 + i * 180));
        stepObs.disconnect();
      }
    }, { threshold: .2 });
    if (steps.length) stepObs.observe(document.querySelector('.steps-grid'));

    // FAQ accordion
    document.querySelectorAll('.faq-q').forEach(btn => {
      btn.addEventListener('click', () => {
        const item = btn.closest('.faq-item');
        const isOpen = item.classList.contains('open');
        document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('open'));
        if (!isOpen) item.classList.add('open');
      });
    });
  </script>
</body>
</html>
