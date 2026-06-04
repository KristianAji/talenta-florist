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

$max_kat   = max(array_column($per_kat,    'jml')       ?: [1]);
$max_bulan = max(array_column($per_bulan,  'jml')       ?: [1]);
$max_harga = max(array_column($harga_kat,  'avg_harga') ?: [1]);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Laporan dan statistik admin Talenta Florist." />
  <title>Laporan &amp; Statistik – Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?= base_url('admin/css/admin-shared.css') ?>" />
  <link rel="stylesheet" href="<?= base_url('admin/css/laporan.css') ?>" />
</head>
<body>

<?php include __DIR__ . '/../../includes/sidebar.php'; ?>

<div class="admin-main">
  <div class="admin-topbar">
    <button id="sidebar-toggle">☰</button>
    <h1 class="topbar-title">Laporan <em>&amp; Statistik</em></h1>
    <span class="topbar-info"><?= date('d M Y') ?></span>
  </div>

  <div class="admin-body">

    <!-- ── STATISTIK UMUM ── -->
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

    <!-- ── GRID: PRODUK PER KATEGORI + PELANGGAN PER BULAN ── -->
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
        <p class="chart-empty">Belum ada data pelanggan.</p>
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

    <!-- ── RATA-RATA HARGA PER KATEGORI ── -->
    <div class="chart-panel">
      <div class="chart-head">Rata-rata Harga per Kategori</div>
      <div class="hbar-list">
        <?php foreach ($harga_kat as $h):
          $pct = $max_harga > 0 ? round($h['avg_harga'] / $max_harga * 100) : 0;
        ?>
        <div class="hbar-row">
          <span class="hbar-lbl" title="<?= htmlspecialchars($h['nama']) ?>"><?= htmlspecialchars($h['nama']) ?></span>
          <div class="hbar-track">
            <div class="hbar-fill" style="width:<?= $pct ?>%;"></div>
          </div>
          <span class="hbar-val">Rp <?= number_format($h['avg_harga'], 0, ',', '.') ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- ── TOP PRODUK DENGAN VARIASI TERBANYAK ── -->
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
            <tr><td colspan="6" class="td-empty">Belum ada data.</td></tr>
            <?php else: foreach ($top_variasi as $i => $p): ?>
            <tr>
              <td><span class="rank-num"><?= $i + 1 ?></span></td>
              <td class="td-nama"><?= htmlspecialchars($p['nama']) ?></td>
              <td class="td-center">
                <span class="badge-variasi"><?= $p['jml_variasi'] ?> variasi</span>
              </td>
              <td>Rp <?= number_format($p['harga_min'], 0, ',', '.') ?></td>
              <td>Rp <?= number_format($p['harga_max'], 0, ',', '.') ?></td>
              <td class="td-rentang">
                Rp <?= number_format($p['harga_min'], 0, ',', '.') ?> – <?= number_format($p['harga_max'], 0, ',', '.') ?>
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