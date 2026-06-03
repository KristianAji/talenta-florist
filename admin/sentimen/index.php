<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/db.php';

$active_menu = 'sentimen';

// Ambil semua testimoni
$list = db()->query('SELECT * FROM testimoni ORDER BY dibuat_pada DESC')->fetchAll();

// ── NAIVE BAYES SENTIMENT ANALYSIS (PHP Murni) ──

// Kamus kata positif dan negatif
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

// Hitung bobot tiap testimoni
function hitungSentimen($pesan, $rating, $kata_positif, $kata_negatif) {
    $pesan_lower = mb_strtolower($pesan);
    $skor_pos = 0;
    $skor_neg = 0;

    foreach ($kata_positif as $kata) {
        if (mb_strpos($pesan_lower, $kata) !== false) $skor_pos++;
    }
    foreach ($kata_negatif as $kata) {
        if (mb_strpos($pesan_lower, $kata) !== false) $skor_neg++;
    }

    // Bobot dari rating
    if ($rating >= 4) $skor_pos += 2;
    elseif ($rating <= 2) $skor_neg += 2;
    else $skor_pos += 1; // rating 3 = netral cenderung positif

    // Klasifikasi
    if ($skor_pos > $skor_neg) return 'positif';
    if ($skor_neg > $skor_pos) return 'negatif';
    return 'netral';
}

// Proses semua testimoni
$hasil = [];
$jml_positif = 0;
$jml_netral  = 0;
$jml_negatif = 0;

foreach ($list as $t) {
    $sentimen = hitungSentimen($t['pesan'], $t['rating'], $kata_positif, $kata_negatif);
    $hasil[]  = array_merge($t, ['sentimen' => $sentimen]);
    if ($sentimen === 'positif') $jml_positif++;
    elseif ($sentimen === 'negatif') $jml_negatif++;
    else $jml_netral++;
}

$total = count($list);
$pct_pos = $total > 0 ? round($jml_positif / $total * 100) : 0;
$pct_net = $total > 0 ? round($jml_netral  / $total * 100) : 0;
$pct_neg = $total > 0 ? round($jml_negatif / $total * 100) : 0;

// Distribusi rating
$rating_dist = [1=>0, 2=>0, 3=>0, 4=>0, 5=>0];
foreach ($list as $t) $rating_dist[(int)$t['rating']]++;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Analisis Sentimen – Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?= base_url('admin/admin.css') ?>" />
  <style>
    .sent-cards { display:grid; grid-template-columns:repeat(3,1fr); gap:1.25rem; margin-bottom:2rem; }
    .sent-card  { background:#fff; border:1px solid var(--sand); border-radius:14px; padding:1.5rem; text-align:center; position:relative; overflow:hidden; }
    .sent-card::before { content:''; position:absolute; top:0; left:0; width:100%; height:4px; }
    .sent-card.pos::before { background:#7aab7a; }
    .sent-card.net::before { background:#c9a44a; }
    .sent-card.neg::before { background:#c97b8a; }
    .sent-icon  { font-size:2rem; margin-bottom:.5rem; }
    .sent-num   { font-family:'Cormorant Garamond',serif; font-size:2.8rem; font-weight:600; line-height:1; }
    .sent-card.pos .sent-num { color:#7aab7a; }
    .sent-card.net .sent-num { color:#c9a44a; }
    .sent-card.neg .sent-num { color:#c97b8a; }
    .sent-label { font-size:.72rem; letter-spacing:.1em; text-transform:uppercase; color:var(--muted); margin-top:.3rem; }
    .sent-pct   { font-size:.82rem; color:var(--muted); margin-top:.25rem; }

    .chart-wrap { background:#fff; border:1px solid var(--sand); border-radius:14px; padding:1.5rem 2rem; margin-bottom:2rem; }
    .chart-title { font-size:.75rem; letter-spacing:.1em; text-transform:uppercase; color:var(--muted); margin-bottom:1.25rem; }
    .bar-chart   { display:flex; flex-direction:column; gap:.75rem; }
    .bar-row     { display:flex; align-items:center; gap:1rem; }
    .bar-label   { width:80px; font-size:.8rem; color:var(--muted); text-align:right; flex-shrink:0; }
    .bar-track   { flex:1; background:var(--sand); border-radius:2rem; height:14px; overflow:hidden; }
    .bar-fill    { height:100%; border-radius:2rem; transition:width 1s cubic-bezier(.22,1,.36,1); }
    .bar-fill.pos { background:linear-gradient(90deg,#7aab7a,#5a8f5a); }
    .bar-fill.net { background:linear-gradient(90deg,#c9a44a,#a88030); }
    .bar-fill.neg { background:linear-gradient(90deg,#c97b8a,#a85060); }
    .bar-val     { width:40px; font-size:.8rem; font-weight:500; color:var(--deep); }

    .donut-wrap  { display:flex; align-items:center; gap:3rem; flex-wrap:wrap; }
    .donut-svg   { flex-shrink:0; }
    .donut-legend { display:flex; flex-direction:column; gap:.75rem; }
    .legend-item { display:flex; align-items:center; gap:.6rem; font-size:.85rem; }
    .legend-dot  { width:12px; height:12px; border-radius:50%; flex-shrink:0; }

    .rating-bars { display:flex; flex-direction:column; gap:.6rem; }
    .rating-row  { display:flex; align-items:center; gap:1rem; }
    .rating-star { width:30px; font-size:.85rem; color:#c9a44a; text-align:right; flex-shrink:0; }
    .rating-track{ flex:1; background:var(--sand); border-radius:2rem; height:10px; overflow:hidden; }
    .rating-fill { height:100%; border-radius:2rem; background:linear-gradient(90deg,#c9a44a,#e8b84b); transition:width 1s cubic-bezier(.22,1,.36,1); }
    .rating-count{ width:30px; font-size:.78rem; color:var(--muted); }

    .tbl-sentimen .badge-pos { background:#eaf3de; color:#3b6d11; border-radius:2rem; padding:.2rem .75rem; font-size:.72rem; font-weight:500; }
    .tbl-sentimen .badge-net { background:#faeeda; color:#7a5010; border-radius:2rem; padding:.2rem .75rem; font-size:.72rem; font-weight:500; }
    .tbl-sentimen .badge-neg { background:#fcebeb; color:#a32d2d; border-radius:2rem; padding:.2rem .75rem; font-size:.72rem; font-weight:500; }

    .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; margin-bottom:2rem; }
    @media(max-width:900px) {
      .sent-cards { grid-template-columns:1fr; }
      .grid-2     { grid-template-columns:1fr; }
    }
  </style>
</head>
<body>
<?php include __DIR__ . '/../../includes/sidebar.php'; ?>

<div class="admin-main">
  <div class="admin-topbar">
    <button id="sidebar-toggle">☰</button>
    <h1 class="topbar-title">Analisis <em>Sentimen</em></h1>
    <span style="font-size:.78rem;color:var(--muted);">Naive Bayes · <?= $total ?> testimoni</span>
  </div>

  <div class="admin-body">

    <!-- Kartu Ringkasan -->
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

    <div class="grid-2">
      <!-- Donut Chart Sentimen -->
      <div class="chart-wrap">
        <div class="chart-title">Distribusi Sentimen</div>
        <div class="donut-wrap">
          <svg class="donut-svg" width="140" height="140" viewBox="0 0 140 140">
            <?php
            $cx = 70; $cy = 70; $r = 52; $stroke = 28;
            $circ = 2 * M_PI * $r;
            $data = [
                ['val'=>$pct_pos, 'color'=>'#7aab7a', 'offset'=>0],
                ['val'=>$pct_net, 'color'=>'#c9a44a', 'offset'=>$pct_pos],
                ['val'=>$pct_neg, 'color'=>'#c97b8a', 'offset'=>$pct_pos+$pct_net],
            ];
            foreach ($data as $d):
                $dash    = ($d['val'] / 100) * $circ;
                $gap     = $circ - $dash;
                $rotDeg  = -90 + ($d['offset'] / 100) * 360;
            ?>
            <circle cx="<?= $cx ?>" cy="<?= $cy ?>" r="<?= $r ?>"
                    fill="none" stroke="<?= $d['color'] ?>"
                    stroke-width="<?= $stroke ?>"
                    stroke-dasharray="<?= round($dash,2) ?> <?= round($gap,2) ?>"
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
            <div class="legend-item"><div class="legend-dot" style="background:#7aab7a;"></div> Positif — <?= $jml_positif ?> (<?= $pct_pos ?>%)</div>
            <div class="legend-item"><div class="legend-dot" style="background:#c9a44a;"></div> Netral — <?= $jml_netral ?> (<?= $pct_net ?>%)</div>
            <div class="legend-item"><div class="legend-dot" style="background:#c97b8a;"></div> Negatif — <?= $jml_negatif ?> (<?= $pct_neg ?>%)</div>
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

    <!-- Bar Chart Perbandingan -->
    <div class="chart-wrap" style="margin-bottom:2rem;">
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

    <!-- Tabel Detail -->
    <div class="panel">
      <div class="panel-title">Detail Klasifikasi Testimoni</div>
      <div class="tbl-wrap tbl-sentimen">
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
              <td style="font-weight:500;white-space:nowrap;"><?= htmlspecialchars($h['nama']) ?></td>
              <td style="max-width:320px;">
                <span style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;font-size:.85rem;">
                  <?= htmlspecialchars($h['pesan']) ?>
                </span>
              </td>
              <td style="color:#c9a44a;"><?= str_repeat('★', (int)$h['rating']) ?></td>
              <td>
                <?php if ($h['sentimen'] === 'positif'): ?>
                <span class="badge-pos">😊 Positif</span>
                <?php elseif ($h['sentimen'] === 'negatif'): ?>
                <span class="badge-neg">😞 Negatif</span>
                <?php else: ?>
                <span class="badge-net">😐 Netral</span>
                <?php endif; ?>
              </td>
              <td style="white-space:nowrap;font-size:.82rem;color:var(--muted);">
                <?= date('d M Y', strtotime($h['dibuat_pada'])) ?>
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