<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/db.php';

$active_menu = 'laporan';

// Statistik umum
$jml_produk    = db()->query('SELECT COUNT(*) FROM produk WHERE aktif=1')->fetchColumn();
$jml_kategori  = db()->query('SELECT COUNT(*) FROM kategori')->fetchColumn();
$jml_testimoni = db()->query('SELECT COUNT(*) FROM testimoni')->fetchColumn();
$jml_pelanggan = db()->query('SELECT COUNT(*) FROM pelanggan')->fetchColumn();
$jml_variasi   = db()->query('SELECT COUNT(*) FROM variasi')->fetchColumn();
$avg_rating    = db()->query('SELECT ROUND(AVG(rating),1) FROM testimoni')->fetchColumn();

// Produk per kategori
$per_kat = db()->query("
  SELECT k.nama, COUNT(p.id) AS jml
  FROM kategori k
  LEFT JOIN produk p ON p.kategori_id = k.id AND p.aktif = 1
  GROUP BY k.id ORDER BY k.urutan
")->fetchAll();

// Top 5 produk dengan variasi terbanyak
$top_variasi = db()->query("
  SELECT p.nama, COUNT(v.id) AS jml_variasi,
         MIN(v.harga) AS harga_min, MAX(v.harga) AS harga_max
  FROM produk p
  JOIN variasi v ON v.produk_id = p.id
  WHERE p.aktif = 1
  GROUP BY p.id
  ORDER BY jml_variasi DESC
  LIMIT 5
")->fetchAll();

// Pelanggan per bulan (6 bulan terakhir)
$per_bulan = db()->query("
  SELECT DATE_FORMAT(dibuat_pada, '%b %Y') AS bulan,
         DATE_FORMAT(dibuat_pada, '%Y-%m') AS sort_key,
         COUNT(*) AS jml
  FROM pelanggan
  WHERE dibuat_pada >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
  GROUP BY sort_key, bulan
  ORDER BY sort_key ASC
")->fetchAll();

// Harga rata-rata per kategori
$harga_kat = db()->query("
  SELECT k.nama, ROUND(AVG(v.harga)) AS avg_harga,
         MIN(v.harga) AS min_harga, MAX(v.harga) AS max_harga
  FROM kategori k
  JOIN produk p ON p.kategori_id = k.id
  JOIN variasi v ON v.produk_id = p.id
  GROUP BY k.id ORDER BY avg_harga DESC
")->fetchAll();

$max_kat = max(array_column($per_kat, 'jml') ?: [1]);
$max_bulan = max(array_column($per_bulan, 'jml') ?: [1]);
$max_harga = max(array_column($harga_kat, 'avg_harga') ?: [1]);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Laporan & Statistik – Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?= base_url('admin/admin.css') ?>" />
  <style>
    .stats-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:1rem; margin-bottom:2rem; }
    .stat-box   { background:#fff; border:1px solid var(--sand); border-radius:12px; padding:1.25rem 1.4rem; position:relative; overflow:hidden; }
    .stat-box::before { content:''; position:absolute; top:0; left:0; width:4px; height:100%; }
    .stat-box:nth-child(1)::before { background:var(--blush); }
    .stat-box:nth-child(2)::before { background:#7aab7a; }
    .stat-box:nth-child(3)::before { background:#c9a44a; }
    .stat-box:nth-child(4)::before { background:#c97b8a; }
    .stat-box:nth-child(5)::before { background:#7a9abb; }
    .stat-box:nth-child(6)::before { background:var(--rose); }
    .stat-lbl { font-size:.68rem; letter-spacing:.1em; text-transform:uppercase; color:var(--muted); margin-bottom:.4rem; }
    .stat-val { font-family:'Cormorant Garamond',serif; font-size:2.2rem; font-weight:600; color:var(--deep); line-height:1; }
    .stat-sub { font-size:.72rem; color:var(--muted); margin-top:.3rem; }

    .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; margin-bottom:1.5rem; }
    .chart-panel { background:#fff; border:1px solid var(--sand); border-radius:14px; padding:1.5rem 1.75rem; }
    .chart-head  { font-size:.72rem; letter-spacing:.1em; text-transform:uppercase; color:var(--muted); margin-bottom:1.5rem; display:flex; justify-content:space-between; align-items:center; }
    .chart-head span { font-family:'Cormorant Garamond',serif; font-size:1rem; font-weight:600; color:var(--deep); letter-spacing:0; text-transform:none; }

    /* Bar chart vertikal */
    .vchart { display:flex; align-items:flex-end; gap:.75rem; height:160px; padding-bottom:1.5rem; position:relative; }
    .vchart::before { content:''; position:absolute; bottom:1.5rem; left:0; right:0; height:1px; background:var(--sand); }
    .vbar-wrap { display:flex; flex-direction:column; align-items:center; gap:.4rem; flex:1; height:100%; justify-content:flex-end; }
    .vbar { width:100%; border-radius:6px 6px 0 0; transition:height 1s cubic-bezier(.22,1,.36,1); min-height:4px; }
    .vbar-val { font-size:.72rem; font-weight:500; color:var(--deep); }
    .vbar-lbl { font-size:.65rem; color:var(--muted); text-align:center; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:60px; }

    /* Bar chart horisontal */
    .hbar-list { display:flex; flex-direction:column; gap:.85rem; }
    .hbar-row  { display:flex; align-items:center; gap:.85rem; }
    .hbar-lbl  { width:90px; font-size:.78rem; color:var(--muted); text-align:right; flex-shrink:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .hbar-track{ flex:1; background:var(--sand); border-radius:2rem; height:12px; overflow:hidden; }
    .hbar-fill { height:100%; border-radius:2rem; transition:width 1.2s cubic-bezier(.22,1,.36,1); }
    .hbar-val  { width:45px; font-size:.78rem; font-weight:500; color:var(--deep); }

    /* Tabel top variasi */
    .rank-num { font-family:'Cormorant Garamond',serif; font-size:1.3rem; font-weight:600; color:var(--blush); width:2rem; }

    @media(max-width:900px) { .grid-2 { grid-template-columns:1fr; } }
  </style>
</head>
<body>
<?php include __DIR__ . '/../../includes/sidebar.php'; ?>

<div class="admin-main">
  <div class="admin-topbar">
    <button id="sidebar-toggle">☰</button>
    <h1 class="topbar-title">Laporan <em>&amp; Statistik</em></h1>
    <span style="font-size:.78rem;color:var(--muted);"><?= date('d M Y') ?></span>
  </div>

  <div class="admin-body">

    <!-- Statistik Umum -->
    <div class="stats-grid">
      <div class="stat-box">
        <div class="stat-lbl">Produk Aktif</div>
        <div class="stat-val"><?= $jml_produk ?></div>
        <div class="stat-sub"><?= $jml_variasi ?> total variasi</div>
      </div>
      <div class="stat-box">
        <div class="stat-lbl">Kategori</div>
        <div class="stat-val"><?= $jml_kategori ?></div>
        <div class="stat-sub">kategori produk</div>
      </div>
      <div class="stat-box">
        <div class="stat-lbl">Testimoni</div>
        <div class="stat-val"><?= $jml_testimoni ?></div>
        <div class="stat-sub">ulasan masuk</div>
      </div>
      <div class="stat-box">
        <div class="stat-lbl">Pelanggan</div>
        <div class="stat-val"><?= $jml_pelanggan ?></div>
        <div class="stat-sub">akun terdaftar</div>
      </div>
      <div class="stat-box">
        <div class="stat-lbl">Rata-rata Rating</div>
        <div class="stat-val"><?= $avg_rating ?: '–' ?></div>
        <div class="stat-sub">dari 5 bintang</div>
      </div>
      <div class="stat-box">
        <div class="stat-lbl">Total Variasi</div>
        <div class="stat-val"><?= $jml_variasi ?></div>
        <div class="stat-sub">varian produk</div>
      </div>
    </div>

    <div class="grid-2">

      <!-- Produk per Kategori (Vertikal) -->
      <div class="chart-panel">
        <div class="chart-head">
          Produk per Kategori
          <span><?= $jml_produk ?> produk aktif</span>
        </div>
        <div class="vchart">
          <?php
          $colors = ['#c97a6a','#e8c4b4','#7aab7a','#c9a44a'];
          foreach ($per_kat as $i => $k):
            $h = $max_kat > 0 ? round($k['jml'] / $max_kat * 130) : 0;
            $c = $colors[$i % count($colors)];
          ?>
          <div class="vbar-wrap">
            <span class="vbar-val"><?= $k['jml'] ?></span>
            <div class="vbar" style="height:<?= $h ?>px;background:<?= $c ?>;"></div>
            <span class="vbar-lbl" title="<?= htmlspecialchars($k['nama']) ?>"><?= htmlspecialchars($k['nama']) ?></span>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Pelanggan per Bulan -->
      <div class="chart-panel">
        <div class="chart-head">
          Pelanggan Baru (6 Bulan)
          <span><?= $jml_pelanggan ?> total</span>
        </div>
        <?php if (empty($per_bulan)): ?>
        <p style="text-align:center;padding:2rem;color:var(--muted);font-size:.85rem;">Belum ada data pelanggan.</p>
        <?php else: ?>
        <div class="vchart">
          <?php foreach ($per_bulan as $b):
            $h = $max_bulan > 0 ? round($b['jml'] / $max_bulan * 130) : 0;
          ?>
          <div class="vbar-wrap">
            <span class="vbar-val"><?= $b['jml'] ?></span>
            <div class="vbar" style="height:<?= $h ?>px;background:#7a9abb;"></div>
            <span class="vbar-lbl"><?= $b['bulan'] ?></span>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

    </div>

    <!-- Rata-rata Harga per Kategori -->
    <div class="chart-panel" style="margin-bottom:1.5rem;">
      <div class="chart-head">Rata-rata Harga per Kategori</div>
      <div class="hbar-list">
        <?php foreach ($harga_kat as $h):
          $pct = $max_harga > 0 ? round($h['avg_harga'] / $max_harga * 100) : 0;
        ?>
        <div class="hbar-row">
          <span class="hbar-lbl" title="<?= htmlspecialchars($h['nama']) ?>"><?= htmlspecialchars($h['nama']) ?></span>
          <div class="hbar-track">
            <div class="hbar-fill" style="width:<?= $pct ?>%;background:linear-gradient(90deg,var(--rose),var(--blush));"></div>
          </div>
          <span class="hbar-val">Rp <?= number_format($h['avg_harga'],0,',','.') ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Top Produk dengan Variasi Terbanyak -->
    <div class="panel">
      <div class="panel-title">Top Produk dengan Variasi Terbanyak</div>
      <div class="tbl-wrap">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Nama Produk</th>
              <th>Jml Variasi</th>
              <th>Harga Min</th>
              <th>Harga Max</th>
              <th>Rentang Harga</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($top_variasi)): ?>
            <tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--muted);">Belum ada data.</td></tr>
            <?php else: foreach ($top_variasi as $i => $p): ?>
            <tr>
              <td><span class="rank-num"><?= $i+1 ?></span></td>
              <td style="font-weight:500;"><?= htmlspecialchars($p['nama']) ?></td>
              <td style="text-align:center;">
                <span style="background:var(--sand);padding:.2rem .75rem;border-radius:2rem;font-size:.78rem;">
                  <?= $p['jml_variasi'] ?> variasi
                </span>
              </td>
              <td>Rp <?= number_format($p['harga_min'],0,',','.') ?></td>
              <td>Rp <?= number_format($p['harga_max'],0,',','.') ?></td>
              <td style="font-size:.82rem;color:var(--muted);">
                Rp <?= number_format($p['harga_min'],0,',','.') ?> – <?= number_format($p['harga_max'],0,',','.') ?>
              </td>
            </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

<script src="<?= base_url('js/admin.js') ?>"></script>
</body>
</html>