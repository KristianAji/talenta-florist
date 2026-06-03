<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/db.php';

$active_menu = 'pesanan';
$flash       = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Update status jika ada POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'], $_POST['id'])) {
    $allowed = ['menunggu','dikonfirmasi','dikirim','selesai','dibatalkan'];
    $status  = in_array($_POST['status'], $allowed) ? $_POST['status'] : 'menunggu';
    db()->prepare('UPDATE pesanan SET status=? WHERE id=?')->execute([$status, (int)$_POST['id']]);
    $_SESSION['flash'] = ['type'=>'success','msg'=>'Status pesanan berhasil diperbarui.'];
    header('Location: ' . base_url('admin/pesanan/index.php')); exit;
}

$filter = $_GET['status'] ?? 'semua';
$where  = $filter !== 'semua' ? 'WHERE ps.status = ' . db()->quote($filter) : '';

$list = db()->query("
    SELECT ps.*, pl.nama AS pelanggan_nama, pl.username,
           p.nama AS produk_nama, v.nama AS variasi_nama, v.gambar
    FROM pesanan ps
    JOIN pelanggan pl ON pl.id = ps.pelanggan_id
    JOIN produk p     ON p.id  = ps.produk_id
    JOIN variasi v    ON v.id  = ps.variasi_id
    $where
    ORDER BY ps.dibuat_pada DESC
")->fetchAll();

$total_semua      = db()->query("SELECT COUNT(*) FROM pesanan")->fetchColumn();
$total_menunggu   = db()->query("SELECT COUNT(*) FROM pesanan WHERE status='menunggu'")->fetchColumn();
$total_konfirmasi = db()->query("SELECT COUNT(*) FROM pesanan WHERE status='dikonfirmasi'")->fetchColumn();
$total_selesai    = db()->query("SELECT COUNT(*) FROM pesanan WHERE status='selesai'")->fetchColumn();

$status_options = [
    'menunggu'     => 'Menunggu',
    'dikonfirmasi' => 'Dikonfirmasi',
    'dikirim'      => 'Dikirim',
    'selesai'      => 'Selesai',
    'dibatalkan'   => 'Dibatalkan',
];
$status_colors = [
    'menunggu'     => ['bg'=>'#faeeda','color'=>'#7a5010'],
    'dikonfirmasi' => ['bg'=>'#eaf3de','color'=>'#3b6d11'],
    'dikirim'      => ['bg'=>'#e6f1fb','color'=>'#185fa5'],
    'selesai'      => ['bg'=>'#eaf3de','color'=>'#3b6d11'],
    'dibatalkan'   => ['bg'=>'#fcebeb','color'=>'#a32d2d'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manajemen Pesanan – Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?= base_url('admin/admin.css') ?>" />
  <style>
    .filter-tabs{display:flex;gap:.5rem;margin-bottom:1.5rem;flex-wrap:wrap;}
    .filter-tab{padding:.4rem 1.1rem;border-radius:2rem;font-size:.75rem;letter-spacing:.06em;text-transform:uppercase;text-decoration:none;border:1.5px solid var(--sand);color:var(--muted);transition:all .2s;}
    .filter-tab:hover,.filter-tab.active{background:var(--rose);border-color:var(--rose);color:#fff;}
    .stat-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:1rem;margin-bottom:1.5rem;}
    .stat-box{background:#fff;border:1px solid var(--sand);border-radius:12px;padding:1rem 1.2rem;text-align:center;}
    .stat-box-num{font-family:'Cormorant Garamond',serif;font-size:2rem;font-weight:600;color:var(--rose);}
    .stat-box-lbl{font-size:.68rem;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);}
    .status-badge{display:inline-block;padding:.2rem .75rem;border-radius:2rem;font-size:.68rem;font-weight:500;letter-spacing:.05em;text-transform:uppercase;}
    .order-img{width:48px;height:48px;object-fit:cover;border-radius:8px;background:var(--sand);}
    .order-img-ph{width:48px;height:48px;border-radius:8px;background:var(--sand);display:flex;align-items:center;justify-content:center;font-size:1.4rem;}
    .produk-info strong{display:block;font-weight:500;font-size:.88rem;}
    .produk-info span{font-size:.75rem;color:var(--muted);}
    select.status-sel{padding:.35rem .7rem;border:1.5px solid var(--sand);border-radius:8px;font-size:.78rem;font-family:'DM Sans',sans-serif;background:#fff;color:var(--deep);outline:none;cursor:pointer;}
    select.status-sel:focus{border-color:var(--rose);}
  </style>
</head>
<body>
<?php include __DIR__ . '/../../includes/sidebar.php'; ?>

<div class="admin-main">
  <div class="admin-topbar">
    <button id="sidebar-toggle">☰</button>
    <h1 class="topbar-title">Manajemen <em>Pesanan</em></h1>
    <span style="font-size:.78rem;color:var(--muted);"><?= $total_menunggu ?> menunggu konfirmasi</span>
  </div>

  <div class="admin-body">

    <?php if ($flash): ?>
    <div class="flash-msg flash-<?= $flash['type'] ?>"><?= htmlspecialchars($flash['msg']) ?></div>
    <?php endif; ?>

    <!-- Statistik -->
    <div class="stat-row">
      <div class="stat-box">
        <div class="stat-box-num"><?= $total_semua ?></div>
        <div class="stat-box-lbl">Total Pesanan</div>
      </div>
      <div class="stat-box">
        <div class="stat-box-num" style="color:#c9a44a;"><?= $total_menunggu ?></div>
        <div class="stat-box-lbl">Menunggu</div>
      </div>
      <div class="stat-box">
        <div class="stat-box-num" style="color:#185fa5;"><?= $total_konfirmasi ?></div>
        <div class="stat-box-lbl">Dikonfirmasi</div>
      </div>
      <div class="stat-box">
        <div class="stat-box-num" style="color:#3b6d11;"><?= $total_selesai ?></div>
        <div class="stat-box-lbl">Selesai</div>
      </div>
    </div>

    <!-- Filter -->
    <div class="filter-tabs">
      <a href="?status=semua"      class="filter-tab <?= $filter==='semua'      ?'active':'' ?>">Semua (<?= $total_semua ?>)</a>
      <a href="?status=menunggu"   class="filter-tab <?= $filter==='menunggu'   ?'active':'' ?>">Menunggu (<?= $total_menunggu ?>)</a>
      <a href="?status=dikonfirmasi" class="filter-tab <?= $filter==='dikonfirmasi'?'active':'' ?>">Dikonfirmasi</a>
      <a href="?status=dikirim"    class="filter-tab <?= $filter==='dikirim'    ?'active':'' ?>">Dikirim</a>
      <a href="?status=selesai"    class="filter-tab <?= $filter==='selesai'    ?'active':'' ?>">Selesai</a>
      <a href="?status=dibatalkan" class="filter-tab <?= $filter==='dibatalkan' ?'active':'' ?>">Dibatalkan</a>
    </div>

    <!-- Tabel -->
    <div class="panel">
      <div class="tbl-wrap">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Produk</th>
              <th>Pelanggan</th>
              <th>Penerima</th>
              <th>Jml</th>
              <th>Total</th>
              <th>Status</th>
              <th>Tanggal</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($list)): ?>
            <tr><td colspan="9" style="text-align:center;padding:2.5rem;color:var(--muted);">Tidak ada pesanan.</td></tr>
            <?php else: foreach ($list as $ps):
              $sc = $status_colors[$ps['status']] ?? $status_colors['menunggu'];
            ?>
            <tr>
              <td style="color:var(--muted);font-size:.8rem;">#<?= $ps['id'] ?></td>
              <td>
                <div style="display:flex;align-items:center;gap:.75rem;">
                  <?php if ($ps['gambar']): ?>
                  <img class="order-img" src="<?= base_url($ps['gambar']) ?>" alt="" />
                  <?php else: ?>
                  <div class="order-img-ph">🌸</div>
                  <?php endif; ?>
                  <div class="produk-info">
                    <strong><?= htmlspecialchars($ps['produk_nama']) ?></strong>
                    <span><?= htmlspecialchars($ps['variasi_nama']) ?></span>
                  </div>
                </div>
              </td>
              <td>
                <div style="font-weight:500;font-size:.85rem;"><?= htmlspecialchars($ps['pelanggan_nama']) ?></div>
                <div style="font-size:.72rem;color:var(--muted);">@<?= htmlspecialchars($ps['username']) ?></div>
              </td>
              <td>
                <div style="font-size:.85rem;"><?= htmlspecialchars($ps['nama_penerima']) ?></div>
                <div style="font-size:.72rem;color:var(--muted);"><?= htmlspecialchars($ps['telepon']) ?></div>
              </td>
              <td style="text-align:center;"><?= $ps['jumlah'] ?></td>
              <td style="font-weight:500;white-space:nowrap;">Rp <?= number_format($ps['total_harga'],0,',','.') ?></td>
              <td>
                <span class="status-badge" style="background:<?= $sc['bg'] ?>;color:<?= $sc['color'] ?>;">
                  <?= $status_options[$ps['status']] ?>
                </span>
              </td>
              <td style="font-size:.78rem;color:var(--muted);white-space:nowrap;"><?= date('d M Y', strtotime($ps['dibuat_pada'])) ?></td>
              <td style="white-space:nowrap;">
                <form method="POST" style="display:inline-flex;gap:.4rem;align-items:center;">
                  <input type="hidden" name="id" value="<?= $ps['id'] ?>" />
                  <select name="status" class="status-sel">
                    <?php foreach ($status_options as $k => $v): ?>
                    <option value="<?= $k ?>" <?= $ps['status']===$k?'selected':'' ?>><?= $v ?></option>
                    <?php endforeach; ?>
                  </select>
                  <button class="btn btn-outline btn-sm" type="submit">Simpan</button>
                </form>
                <a href="https://wa.me/<?= preg_replace('/[^0-9]/','',$ps['telepon']) ?>?text=<?= urlencode("Halo {$ps['nama_penerima']}, pesanan #{$ps['id']} kamu sudah kami terima. Silakan transfer ke rekening BRI: 1234567890 a/n Talenta Florist. Total: Rp " . number_format($ps['total_harga'],0,',','.')) ?>"
                   target="_blank"
                   class="btn btn-sm"
                   style="background:#25d366;color:#fff;margin-left:.3rem;">
                  💬
                </a>
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