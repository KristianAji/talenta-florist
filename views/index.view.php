<?php
/**
 * views/index.view.php
 *
 * View layer untuk halaman beranda.
 * Dipanggil dari index.php setelah data siap.
 *
 * Variabel yang diharapkan:
 *   $jml_produk    – int
 *   $testimoni     – array of rows
 */
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
    /* ── Identik dengan index.php – lihat file tersebut ── */
    /* File view ini adalah versi terpisah yang bisa dipanggil oleh controller */
    :root{--cream:#faf6f0;--sand:#ede5d8;--blush:#e8c4b4;--rose:#c97a6a;--deep:#3d2b24;--green:#5a7a5a;--text:#2e1f19;--muted:#8a7060;}
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
    body{background:var(--cream);color:var(--text);font-family:'DM Sans',sans-serif;font-weight:300;overflow-x:hidden;}
    .section-label{font-size:.7rem;letter-spacing:.22em;text-transform:uppercase;color:var(--rose);margin-bottom:.75rem;}
    .testi-section{padding:6rem 5vw;background:var(--sand);}
    .testi-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:1.5rem;margin-top:2.5rem;}
    .testi-card{background:var(--cream);border-radius:16px;padding:1.8rem;}
    .testi-stars{color:var(--rose);font-size:1rem;margin-bottom:.75rem;}
    .testi-pesan{font-size:.9rem;color:var(--muted);line-height:1.8;margin-bottom:1rem;font-style:italic;}
    .testi-nama{font-size:.75rem;font-weight:500;letter-spacing:.06em;text-transform:uppercase;color:var(--deep);}
    footer{background:var(--deep);color:rgba(250,246,240,.5);text-align:center;padding:2rem 5vw;font-size:.78rem;letter-spacing:.04em;}
    footer a{color:var(--blush);text-decoration:none;}
  </style>
</head>
<body>

  <?php if (!empty($testimoni)): ?>
  <section class="testi-section">
    <p class="section-label">Apa Kata Mereka</p>
    <h2 style="font-family:'Cormorant Garamond',serif;font-size:clamp(1.8rem,3.5vw,2.6rem);font-weight:300;">
      Ulasan <em style="font-style:italic;color:var(--rose)">Pelanggan</em>
    </h2>
    <div class="testi-grid">
      <?php foreach ($testimoni as $t): ?>
      <div class="testi-card">
        <div class="testi-stars"><?= str_repeat('★', (int)$t['rating']) ?></div>
        <p class="testi-pesan">"<?= htmlspecialchars($t['pesan']) ?>"</p>
        <span class="testi-nama">— <?= htmlspecialchars($t['nama']) ?></span>
      </div>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

  <footer>
    <p>© <?= date('Y') ?> <strong style="color:var(--blush)">Kios Bunga Talenta</strong> · Kota Tomohon, Sulawesi Utara</p>
    <p style="margin-top:.4rem">Website ini dibuat sebagai bagian dari program digitalisasi UMKM ·
      <a href="https://unsrat.ac.id" target="_blank">Universitas Sam Ratulangi</a></p>
  </footer>

</body>
</html>