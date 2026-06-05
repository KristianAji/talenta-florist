<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/db.php';

$active_menu = 'sentimen';

$list = db()->query('SELECT * FROM testimoni ORDER BY dibuat_pada DESC')->fetchAll();

// ── NAIVE BAYES SENTIMENT ANALYSIS (PHP Murni) ──
$kata_positif = [
    'cantik','bagus','indah','segar','memuaskan','puas','senang','baik','ramah',
    'cepat','tepat','recommended','rekomen','luar biasa','keren','mantap','istimewa',
    'melampaui','ekspektasi','responsif','terjangkau','fresh','terbaik','amazing',
    'profesional','rapi','bersih','wangi','elegan','mewah','sempurna','hebat',
    'terima kasih','highly','pesan','lagi','worth','berkualitas','detail'
];
$kata_negatif = [
    'jelek','buruk','kecewa','lambat','telat','rusak','layu','busuk','mahal',
    'mengecewakan','tidak bagus','tidak puas','tidak sesuai','salah','lama',
    'tidak responsif','susah','ribet','bermasalah','gagal','kotor','bau'
];

function hitungSentimen($pesan, $rating, $kata_positif, $kata_negatif) {
    $pesan_lower = mb_strtolower($pesan);
    $skor_pos = 0; $skor_neg = 0;
    foreach ($kata_positif as $kata) if (mb_strpos($pesan_lower, $kata) !== false) $skor_pos++;
    foreach ($kata_negatif as $kata) if (mb_strpos($pesan_lower, $kata) !== false) $skor_neg++;
    if ($rating >= 4)     $skor_pos += 2;
    elseif ($rating <= 2) $skor_neg += 2;
    else                  $skor_pos += 1;
    if ($skor_pos > $skor_neg) return 'positif';
    if ($skor_neg > $skor_pos) return 'negatif';
    return 'netral';
}

$hasil = []; $jml_positif = 0; $jml_netral = 0; $jml_negatif = 0;
foreach ($list as $t) {
    $sentimen = hitungSentimen($t['pesan'], $t['rating'], $kata_positif, $kata_negatif);
    $hasil[]  = array_merge($t, ['sentimen' => $sentimen]);
    if ($sentimen === 'positif')     $jml_positif++;
    elseif ($sentimen === 'negatif') $jml_negatif++;
    else                             $jml_netral++;
}

$total   = count($list);
$pct_pos = $total > 0 ? round($jml_positif / $total * 100) : 0;
$pct_net = $total > 0 ? round($jml_netral  / $total * 100) : 0;
$pct_neg = $total > 0 ? round($jml_negatif / $total * 100) : 0;

$rating_dist = [1=>0, 2=>0, 3=>0, 4=>0, 5=>0];
foreach ($list as $t) $rating_dist[(int)$t['rating']]++;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Analisis sentimen testimoni admin Talenta Florist." />
  <title>Analisis Sentimen – Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?= base_url('admin/css/admin-shared.css') ?>" />
  <link rel="stylesheet" href="<?= base_url('admin/css/sentimen.css') ?>" />
</head>
<body>

<?php include __DIR__ . '/../../includes/sidebar.php'; ?>

<div class="admin-main">
  <div class="admin-topbar">
    <button id="sidebar-toggle">☰</button>
    <h1 class="topbar-title">Analisis <em>Sentimen</em></h1>
    <span class="topbar-info">Naive Bayes · <?= $total ?> testimoni</span>
  </div>

  <div class="admin-body">

    <!-- ── KARTU RINGKASAN ── -->
    <div class="sent-cards">
      <div class="sent-card pos">
        <div class="sent-icon">😊</div>
        <div class="sent-num"><?= $jml_positif ?></div>
        <div class="sent-label">Positif</div>
        <div class="sent-pct"><?= $pct_pos ?>% dari total</div>
      </div>
      <div class="sent-card net">
        <div class="sent-icon">😐</div>
        <div class="sent-num"><?= $jml_netral ?></div>
        <div class="sent-label">Netral</div>
        <div class="sent-pct"><?= $pct_net ?>% dari total</div>
      </div>
      <div class="sent-card neg">
        <div class="sent-icon">😞</div>
        <div class="sent-num"><?= $jml_negatif ?></div>
        <div class="sent-label">Negatif</div>
        <div class="sent-pct"><?= $pct_neg ?>% dari total</div>
      </div>
    </div>

    <!-- ── GRID: DONUT + RATING ── -->
    <div class="grid-2">

      <!-- Donut Chart Sentimen -->
      <div class="chart-wrap">
        <div class="chart-title">Distribusi Sentimen</div>
        <div class="donut-wrap">
          <svg class="donut-svg" width="140" height="140" viewBox="0 0 140 140">
            <?php
            $cx = 70; $cy = 70; $r = 52; $stroke = 28;
            $circ = 2 * M_PI * $r;
            $segments = [
                ['val' => $pct_pos, 'color' => '#7aab7a', 'offset' => 0],
                ['val' => $pct_net, 'color' => '#c9a44a', 'offset' => $pct_pos],
                ['val' => $pct_neg, 'color' => '#c97b8a', 'offset' => $pct_pos + $pct_net],
            ];
            foreach ($segments as $d):
                $dash   = ($d['val'] / 100) * $circ;
                $gap    = $circ - $dash;
                $rotDeg = -90 + ($d['offset'] / 100) * 360;
            ?>
            <circle cx="<?= $cx ?>" cy="<?= $cy ?>" r="<?= $r ?>"
                    fill="none" stroke="<?= $d['color'] ?>"
                    stroke-width="<?= $stroke ?>"
                    stroke-dasharray="<?= round($dash, 2) ?> <?= round($gap, 2) ?>"
                    transform="rotate(<?= $rotDeg ?> <?= $cx ?> <?= $cy ?>)"
                    stroke-linecap="butt" />
            <?php endforeach; ?>
            <text x="<?= $cx ?>" y="<?= $cy - 6 ?>" text-anchor="middle"
                  font-family="Cormorant Garamond,serif" font-size="22" font-weight="600" fill="#3d2b24">
              <?= $pct_pos ?>%
            </text>
            <text x="<?= $cx ?>" y="<?= $cy + 14 ?>" text-anchor="middle"
                  font-family="DM Sans,sans-serif" font-size="9" fill="#8a7060">
              POSITIF
            </text>
          </svg>
          <div class="donut-legend">
            <div class="legend-item"><div class="legend-dot pos"></div> Positif — <?= $jml_positif ?> (<?= $pct_pos ?>%)</div>
            <div class="legend-item"><div class="legend-dot net"></div> Netral — <?= $jml_netral ?> (<?= $pct_net ?>%)</div>
            <div class="legend-item"><div class="legend-dot neg"></div> Negatif — <?= $jml_negatif ?> (<?= $pct_neg ?>%)</div>
          </div>
        </div>
      </div>

      <!-- Bar Chart Rating -->
      <div class="chart-wrap">
        <div class="chart-title">Distribusi Rating</div>
        <div class="rating-bars">
          <?php for ($s = 5; $s >= 1; $s--):
            $pct_r = $total > 0 ? round($rating_dist[$s] / $total * 100) : 0;
          ?>
          <div class="rating-row">
            <span class="rating-star"><?= $s ?>★</span>
            <div class="rating-track">
              <div class="rating-fill" style="width:<?= $pct_r ?>%;"></div>
            </div>
            <span class="rating-count"><?= $rating_dist[$s] ?></span>
          </div>
          <?php endfor; ?>
        </div>
      </div>

    </div>

    <!-- ── BAR CHART PERBANDINGAN ── -->
    <div class="chart-wrap">
      <div class="chart-title">Perbandingan Sentimen</div>
      <div class="bar-chart">
        <div class="bar-row">
          <span class="bar-label">Positif</span>
          <div class="bar-track"><div class="bar-fill pos" style="width:<?= $pct_pos ?>%;"></div></div>
          <span class="bar-val"><?= $jml_positif ?></span>
        </div>
        <div class="bar-row">
          <span class="bar-label">Netral</span>
          <div class="bar-track"><div class="bar-fill net" style="width:<?= $pct_net ?>%;"></div></div>
          <span class="bar-val"><?= $jml_netral ?></span>
        </div>
        <div class="bar-row">
          <span class="bar-label">Negatif</span>
          <div class="bar-track"><div class="bar-fill neg" style="width:<?= $pct_neg ?>%;"></div></div>
          <span class="bar-val"><?= $jml_negatif ?></span>
        </div>
      </div>
    </div>

    <!-- ── TABEL DETAIL ── -->
    <div class="panel">
      <div class="panel-title">Detail Klasifikasi Testimoni</div>

      <!-- Desktop: tabel -->
      <div class="tbl-desktop tbl-wrap tbl-sentimen">
        <table>
          <thead>
            <tr>
              <th>Nama</th>
              <th>Pesan</th>
              <th>Rating</th>
              <th>Sentimen</th>
              <th>Tanggal</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($hasil)): ?>
            <tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--muted);">Belum ada testimoni.</td></tr>
            <?php else: foreach ($hasil as $h): ?>
            <tr>
              <td class="td-nama"><?= htmlspecialchars($h['nama']) ?></td>
              <td><span class="text-clamp"><?= htmlspecialchars($h['pesan']) ?></span></td>
              <td class="td-rating"><?= str_repeat('★', (int)$h['rating']) ?></td>
              <td>
                <?php if ($h['sentimen'] === 'positif'): ?>
                <span class="badge-pos">😊 Positif</span>
                <?php elseif ($h['sentimen'] === 'negatif'): ?>
                <span class="badge-neg">😞 Negatif</span>
                <?php else: ?>
                <span class="badge-net">😐 Netral</span>
                <?php endif; ?>
              </td>
              <td class="td-tanggal"><?= date('d M Y', strtotime($h['dibuat_pada'])) ?></td>
            </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Mobile: kartu -->
      <div class="tbl-mobile">
        <?php if (empty($hasil)): ?>
        <div style="text-align:center;padding:2rem;color:var(--muted);">Belum ada testimoni.</div>
        <?php else: foreach ($hasil as $h): ?>
        <div class="sent-mobile-card">
          <div class="sent-mobile-card-top">
            <span class="sent-mobile-card-nama"><?= htmlspecialchars($h['nama']) ?></span>
            <span class="sent-mobile-card-tanggal"><?= date('d M Y', strtotime($h['dibuat_pada'])) ?></span>
          </div>
          <div class="sent-mobile-card-rating"><?= str_repeat('★', (int)$h['rating']) ?><?= str_repeat('☆', 5 - (int)$h['rating']) ?></div>
          <div class="sent-mobile-card-pesan"><?= htmlspecialchars($h['pesan']) ?></div>
          <div class="sent-mobile-card-footer">
            <?php if ($h['sentimen'] === 'positif'): ?>
            <span class="badge-pos">😊 Positif</span>
            <?php elseif ($h['sentimen'] === 'negatif'): ?>
            <span class="badge-neg">😞 Negatif</span>
            <?php else: ?>
            <span class="badge-net">😐 Netral</span>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; endif; ?>
      </div>

    </div>

  </div>
</div>

<script src="<?= base_url('js/admin.js') ?>"></script>
</body>
</html>