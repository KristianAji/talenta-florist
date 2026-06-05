<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/db.php';

$active_menu = 'laporan';

// ── FILTER RENTANG TANGGAL ───────────────────────────────────
$range   = $_GET['range'] ?? '7';   // 1, 7, 30, bulan_ini, custom
$date_from = $_GET['dari'] ?? '';
$date_to   = $_GET['sampai'] ?? '';

switch ($range) {
    case '1':
        $sql_from = 'CURDATE()';
        $label_range = 'Hari Ini';
        break;
    case 'kemarin':
        $sql_from = 'DATE_SUB(CURDATE(), INTERVAL 1 DAY)';
        $sql_to   = 'DATE_SUB(CURDATE(), INTERVAL 1 DAY)';
        $label_range = 'Kemarin';
        break;
    case '30':
        $sql_from = 'DATE_SUB(CURDATE(), INTERVAL 30 DAY)';
        $label_range = '30 Hari Terakhir';
        break;
    case 'bulan_ini':
        $sql_from = 'DATE_FORMAT(NOW(), \'%Y-%m-01\')';
        $label_range = 'Bulan Ini';
        break;
    case 'custom':
        $sql_from = db()->quote($date_from ?: date('Y-m-01'));
        $sql_to   = db()->quote($date_to   ?: date('Y-m-d'));
        $label_range = 'Custom';
        break;
    default: // 7
        $range = '7';
        $sql_from = 'DATE_SUB(CURDATE(), INTERVAL 7 DAY)';
        $label_range = '7 Hari Terakhir';
}
$where_from = "DATE(ps.dibuat_pada) >= {$sql_from}";
$where_to   = isset($sql_to) ? " AND DATE(ps.dibuat_pada) <= {$sql_to}" : '';
$where_penjualan = "WHERE {$where_from}{$where_to} AND ps.status != 'dibatalkan'";

// ── STATISTIK PENJUALAN ──────────────────────────────────────
$stat_penjualan = db()->query("
    SELECT
        COUNT(*)                          AS total_transaksi,
        COALESCE(SUM(total_harga), 0)     AS total_pendapatan,
        COALESCE(AVG(total_harga), 0)     AS avg_transaksi,
        COALESCE(SUM(jumlah), 0)          AS total_item,
        COUNT(DISTINCT pelanggan_id)      AS jml_pembeli
    FROM pesanan ps
    {$where_penjualan}
")->fetch();

// Kemarin (untuk perbandingan delta)
$stat_kemarin = db()->query("
    SELECT
        COUNT(*)                      AS total_transaksi,
        COALESCE(SUM(total_harga), 0) AS total_pendapatan
    FROM pesanan
    WHERE DATE(dibuat_pada) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
      AND status != 'dibatalkan'
")->fetch();

// Penjualan per hari (dalam rentang)
$penjualan_harian = db()->query("
    SELECT
        DATE(ps.dibuat_pada)          AS tgl,
        COUNT(*)                      AS transaksi,
        COALESCE(SUM(ps.total_harga), 0) AS pendapatan
    FROM pesanan ps
    {$where_penjualan}
    GROUP BY tgl
    ORDER BY tgl ASC
")->fetchAll();

// Top produk terlaris (dalam rentang)
$top_produk = db()->query("
    SELECT p.nama, SUM(ps.jumlah) AS terjual,
           SUM(ps.total_harga)    AS pendapatan,
           COUNT(ps.id)           AS transaksi
    FROM pesanan ps
    JOIN produk p ON p.id = ps.produk_id
    {$where_penjualan}
    GROUP BY p.id
    ORDER BY terjual DESC
    LIMIT 5
")->fetchAll();

// Distribusi status pesanan (dalam rentang)
$dist_status = db()->query("
    SELECT status, COUNT(*) AS jml
    FROM pesanan ps
    WHERE {$where_from}{$where_to}
    GROUP BY status
")->fetchAll();

// Pesanan per jam hari ini
$per_jam = db()->query("
    SELECT HOUR(dibuat_pada) AS jam, COUNT(*) AS jml,
           COALESCE(SUM(total_harga), 0) AS pendapatan
    FROM pesanan
    WHERE DATE(dibuat_pada) = CURDATE()
      AND status != 'dibatalkan'
    GROUP BY jam ORDER BY jam ASC
")->fetchAll();

// ── STATISTIK UMUM ───────────────────────────────────────────
$jml_produk    = db()->query('SELECT COUNT(*) FROM produk WHERE aktif=1')->fetchColumn();
$jml_kategori  = db()->query('SELECT COUNT(*) FROM kategori')->fetchColumn();
$jml_testimoni = db()->query('SELECT COUNT(*) FROM testimoni')->fetchColumn();
$jml_pelanggan = db()->query('SELECT COUNT(*) FROM pelanggan')->fetchColumn();
$jml_variasi   = db()->query('SELECT COUNT(*) FROM variasi')->fetchColumn();
$avg_rating    = db()->query('SELECT ROUND(AVG(rating),1) FROM testimoni')->fetchColumn();

$per_kat = db()->query("
  SELECT k.nama, COUNT(p.id) AS jml
  FROM kategori k
  LEFT JOIN produk p ON p.kategori_id = k.id AND p.aktif = 1
  GROUP BY k.id ORDER BY k.urutan
")->fetchAll();

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

$per_bulan = db()->query("
  SELECT DATE_FORMAT(dibuat_pada, '%b %Y') AS bulan,
         DATE_FORMAT(dibuat_pada, '%Y-%m') AS sort_key,
         COUNT(*) AS jml
  FROM pelanggan
  WHERE dibuat_pada >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
  GROUP BY sort_key, bulan
  ORDER BY sort_key ASC
")->fetchAll();

$harga_kat = db()->query("
  SELECT k.nama, ROUND(AVG(v.harga)) AS avg_harga,
         MIN(v.harga) AS min_harga, MAX(v.harga) AS max_harga
  FROM kategori k
  JOIN produk p ON p.kategori_id = k.id
  JOIN variasi v ON v.produk_id = p.id
  GROUP BY k.id ORDER BY avg_harga DESC
")->fetchAll();

$max_kat     = max(array_column($per_kat,         'jml')       ?: [1]);
$max_bulan   = max(array_column($per_bulan,       'jml')       ?: [1]);
$max_harga   = max(array_column($harga_kat,       'avg_harga') ?: [1]);
$max_harian  = max(array_column($penjualan_harian,'pendapatan') ?: [1]);
$max_produk  = max(array_column($top_produk,      'terjual')   ?: [1]);

// Helper delta
function delta($now, $prev) {
    if ($prev == 0) return $now > 0 ? '+100%' : '–';
    $d = round(($now - $prev) / $prev * 100);
    return ($d >= 0 ? '+' : '') . $d . '%';
}
function delta_class($now, $prev) {
    if ($prev == 0) return $now > 0 ? 'up' : 'neutral';
    return $now >= $prev ? 'up' : 'down';
}

$status_label = [
    'menunggu'     => ['lbl' => 'Menunggu',     'cls' => 'menunggu'],
    'dikonfirmasi' => ['lbl' => 'Dikonfirmasi', 'cls' => 'dikonfirmasi'],
    'dikirim'      => ['lbl' => 'Dikirim',      'cls' => 'dikirim'],
    'selesai'      => ['lbl' => 'Selesai',      'cls' => 'selesai'],
    'dibatalkan'   => ['lbl' => 'Dibatalkan',   'cls' => 'dibatalkan'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
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

    <!-- ════════════════════════════════════════════════════════
         SEKSI 1 — LAPORAN PENJUALAN
    ════════════════════════════════════════════════════════ -->
    <div class="section-label">Laporan <em>Penjualan</em></div>

    <!-- Filter rentang -->
    <form method="GET" class="range-filter">
      <div class="range-tabs">
        <?php foreach ([
          '1'        => 'Hari Ini',
          'kemarin'  => 'Kemarin',
          '7'        => '7 Hari',
          '30'       => '30 Hari',
          'bulan_ini'=> 'Bulan Ini',
          'custom'   => 'Custom',
        ] as $val => $lbl): ?>
        <a href="?range=<?= $val ?>"
           class="range-tab <?= $range === $val ? 'active' : '' ?>"><?= $lbl ?></a>
        <?php endforeach; ?>
      </div>
      <?php if ($range === 'custom'): ?>
      <div class="range-custom">
        <input type="date" name="dari"    value="<?= htmlspecialchars($date_from) ?>" max="<?= date('Y-m-d') ?>" />
        <span>–</span>
        <input type="date" name="sampai"  value="<?= htmlspecialchars($date_to) ?>"   max="<?= date('Y-m-d') ?>" />
        <input type="hidden" name="range" value="custom" />
        <button type="submit" class="btn btn-primary btn-sm">Terapkan</button>
      </div>
      <?php endif; ?>
    </form>

    <!-- Stat penjualan -->
    <div class="penjualan-grid">
      <?php
      $d_trx = delta($stat_penjualan['total_transaksi'], $stat_kemarin['total_transaksi']);
      $d_rev = delta($stat_penjualan['total_pendapatan'], $stat_kemarin['total_pendapatan']);
      $dc_trx = delta_class($stat_penjualan['total_transaksi'], $stat_kemarin['total_transaksi']);
      $dc_rev = delta_class($stat_penjualan['total_pendapatan'], $stat_kemarin['total_pendapatan']);
      ?>
      <div class="penjualan-card penjualan-card--primary">
        <div class="penjualan-card-icon">💰</div>
        <div class="penjualan-card-val">Rp <?= number_format($stat_penjualan['total_pendapatan'], 0, ',', '.') ?></div>
        <div class="penjualan-card-lbl">Total Pendapatan</div>
        <div class="penjualan-card-delta <?= $dc_rev ?>"><?= $d_rev ?> vs kemarin</div>
      </div>
      <div class="penjualan-card">
        <div class="penjualan-card-icon">🧾</div>
        <div class="penjualan-card-val"><?= $stat_penjualan['total_transaksi'] ?></div>
        <div class="penjualan-card-lbl">Total Transaksi</div>
        <div class="penjualan-card-delta <?= $dc_trx ?>"><?= $d_trx ?> vs kemarin</div>
      </div>
      <div class="penjualan-card">
        <div class="penjualan-card-icon">🛍️</div>
        <div class="penjualan-card-val"><?= $stat_penjualan['total_item'] ?></div>
        <div class="penjualan-card-lbl">Item Terjual</div>
      </div>
      <div class="penjualan-card">
        <div class="penjualan-card-icon">👤</div>
        <div class="penjualan-card-val"><?= $stat_penjualan['jml_pembeli'] ?></div>
        <div class="penjualan-card-lbl">Pembeli Unik</div>
      </div>
      <div class="penjualan-card">
        <div class="penjualan-card-icon">📊</div>
        <div class="penjualan-card-val">Rp <?= number_format(round($stat_penjualan['avg_transaksi']), 0, ',', '.') ?></div>
        <div class="penjualan-card-lbl">Rata-rata / Transaksi</div>
      </div>
    </div>

    <!-- Grafik harian + distribusi status -->
    <div class="grid-2">

      <!-- Grafik pendapatan harian -->
      <div class="chart-panel">
        <div class="chart-head">
          Pendapatan Harian
          <span><?= $label_range ?></span>
        </div>
        <?php if (empty($penjualan_harian)): ?>
        <p class="chart-empty">Tidak ada transaksi pada periode ini.</p>
        <?php else: ?>
        <div class="vchart">
          <?php foreach ($penjualan_harian as $hd):
            $h = $max_harian > 0 ? round($hd['pendapatan'] / $max_harian * 130) : 0;
            $tgl_lbl = date('d/m', strtotime($hd['tgl']));
          ?>
          <div class="vbar-wrap">
            <span class="vbar-val" style="font-size:.58rem;">
              <?= $hd['pendapatan'] >= 1000000
                  ? round($hd['pendapatan']/1000000, 1).'jt'
                  : round($hd['pendapatan']/1000).'rb' ?>
            </span>
            <div class="vbar" style="height:<?= $h ?>px;background:var(--rose);"></div>
            <span class="vbar-lbl"><?= $tgl_lbl ?></span>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

      <!-- Distribusi status -->
      <div class="chart-panel">
        <div class="chart-head">Distribusi Status Pesanan</div>
        <?php if (empty($dist_status)): ?>
        <p class="chart-empty">Tidak ada pesanan pada periode ini.</p>
        <?php else: ?>
        <div class="dist-status-list">
          <?php
          $total_dist = array_sum(array_column($dist_status, 'jml'));
          foreach ($dist_status as $ds):
            $pct = $total_dist > 0 ? round($ds['jml'] / $total_dist * 100) : 0;
            $info = $status_label[$ds['status']] ?? ['lbl' => $ds['status'], 'cls' => 'menunggu'];
          ?>
          <div class="dist-row">
            <span class="status-badge status-badge--<?= $info['cls'] ?>"><?= $info['lbl'] ?></span>
            <div class="dist-track">
              <div class="dist-fill dist-fill--<?= $info['cls'] ?>" style="width:<?= $pct ?>%;"></div>
            </div>
            <span class="dist-num"><?= $ds['jml'] ?> <small>(<?= $pct ?>%)</small></span>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

    </div>

    <!-- Top produk terlaris -->
    <div class="panel" style="margin-bottom:1.5rem;">
      <div class="panel-title">Top Produk Terlaris — <?= $label_range ?></div>

      <!-- Desktop -->
      <div class="tbl-desktop tbl-wrap">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Nama Produk</th>
              <th class="td-center">Terjual</th>
              <th class="td-center">Transaksi</th>
              <th>Pendapatan</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($top_produk)): ?>
            <tr><td colspan="5" class="td-empty">Tidak ada data penjualan.</td></tr>
            <?php else: foreach ($top_produk as $i => $tp): ?>
            <tr>
              <td><span class="rank-num"><?= $i + 1 ?></span></td>
              <td class="td-nama"><?= htmlspecialchars($tp['nama']) ?></td>
              <td class="td-center"><span class="badge-variasi"><?= $tp['terjual'] ?> pcs</span></td>
              <td class="td-center"><?= $tp['transaksi'] ?>×</td>
              <td>Rp <?= number_format($tp['pendapatan'], 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Mobile -->
      <div class="tbl-mobile">
        <?php if (empty($top_produk)): ?>
        <div style="text-align:center;padding:2rem;color:var(--muted);">Tidak ada data penjualan.</div>
        <?php else: foreach ($top_produk as $i => $tp): ?>
        <div class="laporan-card">
          <div class="laporan-card-rank"><?= $i + 1 ?></div>
          <div class="laporan-card-body">
            <div class="laporan-card-nama"><?= htmlspecialchars($tp['nama']) ?></div>
            <div class="laporan-card-meta">
              <span class="laporan-card-meta-item">Terjual: <strong><?= $tp['terjual'] ?> pcs</strong></span>
              <span class="laporan-card-meta-item">Transaksi: <strong><?= $tp['transaksi'] ?>×</strong></span>
              <span class="laporan-card-meta-item">Pendapatan: <strong>Rp <?= number_format($tp['pendapatan'], 0, ',', '.') ?></strong></span>
            </div>
          </div>
        </div>
        <?php endforeach; endif; ?>
      </div>
    </div>

    <!-- Aktivitas per jam hari ini -->
    <?php if (!empty($per_jam)): ?>
    <div class="chart-panel" style="margin-bottom:1.5rem;">
      <div class="chart-head">Aktivitas Pesanan Per Jam — Hari Ini</div>
      <div class="hbar-list">
        <?php
        $max_jam = max(array_column($per_jam, 'jml') ?: [1]);
        foreach ($per_jam as $pj):
          $pct = $max_jam > 0 ? round($pj['jml'] / $max_jam * 100) : 0;
        ?>
        <div class="hbar-row">
          <span class="hbar-lbl"><?= str_pad($pj['jam'], 2, '0', STR_PAD_LEFT) ?>:00</span>
          <div class="hbar-track">
            <div class="hbar-fill" style="width:<?= $pct ?>%;background:linear-gradient(90deg,#7a9abb,#a8c4d8);"></div>
          </div>
          <span class="hbar-val"><?= $pj['jml'] ?> pesanan</span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- ════════════════════════════════════════════════════════
         SEKSI 2 — STATISTIK PRODUK & KATALOG
    ════════════════════════════════════════════════════════ -->
    <div class="section-label">Statistik <em>Produk &amp; Katalog</em></div>

    <!-- Statistik umum -->
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

    <div class="panel">
      <div class="panel-title">Top Produk dengan Variasi Terbanyak</div>
      <div class="tbl-desktop tbl-wrap">
        <table>
          <thead>
            <tr>
              <th>#</th><th>Nama Produk</th><th>Jml Variasi</th>
              <th>Harga Min</th><th>Harga Max</th><th>Rentang Harga</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($top_variasi)): ?>
            <tr><td colspan="6" class="td-empty">Belum ada data.</td></tr>
            <?php else: foreach ($top_variasi as $i => $p): ?>
            <tr>
              <td><span class="rank-num"><?= $i + 1 ?></span></td>
              <td class="td-nama"><?= htmlspecialchars($p['nama']) ?></td>
              <td class="td-center"><span class="badge-variasi"><?= $p['jml_variasi'] ?> variasi</span></td>
              <td>Rp <?= number_format($p['harga_min'], 0, ',', '.') ?></td>
              <td>Rp <?= number_format($p['harga_max'], 0, ',', '.') ?></td>
              <td class="td-rentang">Rp <?= number_format($p['harga_min'], 0, ',', '.') ?> – <?= number_format($p['harga_max'], 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
      <div class="tbl-mobile">
        <?php if (empty($top_variasi)): ?>
        <div style="text-align:center;padding:2rem;color:var(--muted);">Belum ada data.</div>
        <?php else: foreach ($top_variasi as $i => $p): ?>
        <div class="laporan-card">
          <div class="laporan-card-rank"><?= $i + 1 ?></div>
          <div class="laporan-card-body">
            <div class="laporan-card-nama"><?= htmlspecialchars($p['nama']) ?></div>
            <div class="laporan-card-meta">
              <span class="laporan-card-meta-item">Min: <strong>Rp <?= number_format($p['harga_min'], 0, ',', '.') ?></strong></span>
              <span class="laporan-card-meta-item">Max: <strong>Rp <?= number_format($p['harga_max'], 0, ',', '.') ?></strong></span>
            </div>
          </div>
          <span class="laporan-card-badge"><?= $p['jml_variasi'] ?> variasi</span>
        </div>
        <?php endforeach; endif; ?>
      </div>
    </div>

  </div>
</div>

<script src="<?= base_url('js/admin.js') ?>"></script>
</body>
</html>