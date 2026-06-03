<?php
// admin/dashboard_view.php
$active_menu = 'dashboard';

$jml_produk    = db()->query('SELECT COUNT(*) FROM produk WHERE aktif=1')->fetchColumn();
$jml_kategori  = db()->query('SELECT COUNT(*) FROM kategori')->fetchColumn();
$jml_testimoni = db()->query('SELECT COUNT(*) FROM testimoni WHERE aktif=1')->fetchColumn();
$jml_variasi   = db()->query('SELECT COUNT(*) FROM variasi')->fetchColumn();

$produk_baru = db()->query("
  SELECT p.nama, k.nama AS kat, p.aktif, p.dibuat_pada,
         (SELECT v.gambar FROM variasi v WHERE v.produk_id=p.id ORDER BY v.urutan LIMIT 1) AS gambar,
         (SELECT v.harga  FROM variasi v WHERE v.produk_id=p.id ORDER BY v.urutan LIMIT 1) AS harga
  FROM produk p JOIN kategori k ON k.id=p.kategori_id
  ORDER BY p.dibuat_pada DESC LIMIT 5
")->fetchAll();

$testi_baru = db()->query("
  SELECT nama, pesan, rating, aktif, dibuat_pada
  FROM testimoni ORDER BY dibuat_pada DESC LIMIT 4
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard – Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?= base_url('admin/admin.css') ?>" />
  <style>
    .stats-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(140px,1fr)); gap:1rem; margin-bottom:2rem; }
    .stat-card { background:#fff; border:1px solid var(--sand); border-radius:12px; padding:1.4rem 1.6rem; position:relative; overflow:hidden; }
    .stat-card::before { content:''; position:absolute; top:0; left:0; width:4px; height:100%; background:var(--blush); }
    .stat-card.green::before { background:#7aab7a; }
    .stat-card.rose::before  { background:#c97b8a; }
    .stat-card.gold::before  { background:#c9a44a; }
    .stat-label { font-size:.75rem; letter-spacing:.08em; text-transform:uppercase; color:var(--muted); margin-bottom:.4rem; }
    .stat-value { font-family:'Cormorant Garamond',serif; font-size:2.4rem; font-weight:600; line-height:1; color:var(--deep); }
    .stat-sub   { font-size:.75rem; color:var(--muted); margin-top:.35rem; }

    .dash-grid { display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; }
    @media (max-width:840px) { .dash-grid { grid-template-columns:1fr; } }

    .produk-row { display:flex; align-items:center; gap:.9rem; padding:.75rem 0; border-bottom:1px solid var(--sand); }
    .produk-row:last-child { border-bottom:none; }
    .produk-thumb { width:46px; height:46px; border-radius:8px; background:var(--sand); display:flex; align-items:center; justify-content:center; font-size:1.3rem; flex-shrink:0; overflow:hidden; }
    .produk-thumb img { width:46px; height:46px; object-fit:cover; border-radius:8px; }
    .produk-info { flex:1; min-width:0; }
    .produk-nama { font-weight:500; font-size:.88rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .produk-meta { font-size:.75rem; color:var(--muted); }
    .produk-harga { font-family:'Cormorant Garamond',serif; font-size:1.05rem; font-weight:600; color:var(--deep); white-space:nowrap; }

    .testi-card { padding:.9rem 0; border-bottom:1px solid var(--sand); }
    .testi-card:last-child { border-bottom:none; }
    .testi-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:.4rem; }
    .testi-nama   { font-weight:500; font-size:.88rem; }
    .testi-rating { color:#c9a44a; font-size:.85rem; }
    .testi-pesan  { font-size:.82rem; color:var(--muted); display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }

    .welcome-banner { background:linear-gradient(135deg,#fdf8f3 0%,#f7ede2 100%); border:1px solid var(--sand); border-radius:16px; padding:2rem 2.4rem; margin-bottom:2rem; display:flex; align-items:center; justify-content:space-between; gap:1rem; }
    .welcome-title  { font-family:'Cormorant Garamond',serif; font-size:1.7rem; font-weight:600; color:var(--deep); margin-bottom:.3rem; }
    .welcome-sub    { font-size:.82rem; color:var(--muted); }
    .welcome-date   { font-family:'Cormorant Garamond',serif; font-size:1.1rem; color:var(--muted); text-align:right; white-space:nowrap; }

    @media (max-width: 768px) {
  .stats-grid {
    grid-template-columns: repeat(2, 1fr) !important;
  }
  .stat-card {
    min-width: 0;
    overflow: hidden;
  }
  .stat-label {
    font-size: .6rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .welcome-banner {
    flex-direction: column;
    align-items: flex-start;
  }
  .welcome-date {
    text-align: left;
  }
}

  </style>
</head>
<body>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<div class="admin-main">
  <div class="admin-topbar">
    <button id="sidebar-toggle">☰</button>
    <h1 class="topbar-title"><em>Dashboard</em></h1>
    <a href="<?= base_url('index.php') ?>" class="btn btn-outline btn-sm" target="_blank">↗ Lihat Website</a>
  </div>

  <div class="admin-body">

    <div class="welcome-banner">
      <div>
        <div class="welcome-title">Selamat Datang 👋</div>
        <div class="welcome-sub">Ini adalah ringkasan aktivitas toko Anda hari ini.</div>
      </div>
      <div class="welcome-date"><?= date('l, d F Y') ?></div>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-label">Produk Aktif</div>
        <div class="stat-value"><?= $jml_produk ?></div>
        <div class="stat-sub">produk tampil di katalog</div>
      </div>
      <div class="stat-card green">
        <div class="stat-label">Kategori</div>
        <div class="stat-value"><?= $jml_kategori ?></div>
        <div class="stat-sub">total kategori terdaftar</div>
      </div>
      <div class="stat-card rose">
        <div class="stat-label">Testimoni Aktif</div>
        <div class="stat-value"><?= $jml_testimoni ?></div>
        <div class="stat-sub">ulasan ditampilkan</div>
      </div>
      <div class="stat-card gold">
        <div class="stat-label">Total Variasi</div>
        <div class="stat-value"><?= $jml_variasi ?></div>
        <div class="stat-sub">varian produk tersedia</div>
      </div>
    </div>

    <div class="dash-grid">

      <div class="panel">
        <div class="panel-title" style="display:flex;justify-content:space-between;align-items:center;">
          Produk Terbaru
          <a href="<?= base_url('admin/produk/index.php') ?>" class="btn btn-outline btn-sm">Lihat Semua</a>
        </div>
        <?php if (empty($produk_baru)): ?>
        <p style="text-align:center;padding:1.5rem;color:var(--muted);">Belum ada produk.</p>
        <?php else: foreach ($produk_baru as $p): ?>
        <div class="produk-row">
          <div class="produk-thumb">
            <?php if ($p['gambar']): ?>
            <img src="<?= base_url($p['gambar']) ?>" alt="" />
            <?php else: ?>🌸<?php endif; ?>
          </div>
          <div class="produk-info">
            <div class="produk-nama"><?= htmlspecialchars($p['nama']) ?></div>
            <div class="produk-meta">
              <?= htmlspecialchars($p['kat']) ?> ·
              <?= $p['aktif'] ? '<span style="color:#7aab7a;">Aktif</span>' : '<span style="color:#c97b8a;">Nonaktif</span>' ?>
            </div>
          </div>
          <div class="produk-harga">Rp <?= number_format($p['harga'] ?? 0, 0, ',', '.') ?></div>
        </div>
        <?php endforeach; endif; ?>
      </div>

      <div class="panel">
        <div class="panel-title" style="display:flex;justify-content:space-between;align-items:center;">
          Testimoni Terbaru
          <a href="<?= base_url('admin/testimoni/index.php') ?>" class="btn btn-outline btn-sm">Lihat Semua</a>
        </div>
        <?php if (empty($testi_baru)): ?>
        <p style="text-align:center;padding:1.5rem;color:var(--muted);">Belum ada testimoni.</p>
        <?php else: foreach ($testi_baru as $t): ?>
        <div class="testi-card">
          <div class="testi-header">
            <div class="testi-nama"><?= htmlspecialchars($t['nama']) ?></div>
            <div class="testi-rating"><?= str_repeat('★', (int)$t['rating']) ?><?= str_repeat('☆', 5 - (int)$t['rating']) ?></div>
          </div>
          <div class="testi-pesan"><?= htmlspecialchars($t['pesan']) ?></div>
        </div>
        <?php endforeach; endif; ?>
      </div>

    </div>
  </div>
</div>

<script src="<?= base_url('js/admin.js') ?>"></script>
</body>
</html>