<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/db.php';

$active_menu = 'pesanan';
$flash       = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];

// Update status jika ada POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'], $_POST['id'])) {
    // Verifikasi CSRF
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Akses tidak sah.'];
        header('Location: ' . base_url('admin/pesanan/index.php')); exit;
    }
    $allowed = ['menunggu', 'dikonfirmasi', 'dikirim', 'selesai', 'dibatalkan'];
    $status  = in_array($_POST['status'], $allowed) ? $_POST['status'] : 'menunggu';
    db()->prepare('UPDATE pesanan SET status=? WHERE id=?')->execute([$status, (int)$_POST['id']]);
    $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Status pesanan berhasil diperbarui.'];
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
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manajemen Pesanan – Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?= base_url('admin/css/admin-shared.css') ?>" />
  <link rel="stylesheet" href="<?= base_url('admin/css/pesanan.css') ?>" />
</head>
<body>
<?php include __DIR__ . '/../../includes/sidebar.php'; ?>

<div class="admin-main">
  <div class="admin-topbar">
    <button id="sidebar-toggle">☰</button>
    <h1 class="topbar-title">Manajemen <em>Pesanan</em></h1>
    <span class="topbar-info"><?= $total_menunggu ?> menunggu konfirmasi</span>
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
        <div class="stat-box-num stat-box-num--warning"><?= $total_menunggu ?></div>
        <div class="stat-box-lbl">Menunggu</div>
      </div>
      <div class="stat-box">
        <div class="stat-box-num stat-box-num--info"><?= $total_konfirmasi ?></div>
        <div class="stat-box-lbl">Dikonfirmasi</div>
      </div>
      <div class="stat-box">
        <div class="stat-box-num stat-box-num--success"><?= $total_selesai ?></div>
        <div class="stat-box-lbl">Selesai</div>
      </div>
    </div>

    <!-- Filter -->
    <div class="filter-tabs">
      <a href="?status=semua"        class="filter-tab <?= $filter==='semua'        ? 'active' : '' ?>">Semua (<?= $total_semua ?>)</a>
      <a href="?status=menunggu"     class="filter-tab <?= $filter==='menunggu'     ? 'active' : '' ?>">Menunggu (<?= $total_menunggu ?>)</a>
      <a href="?status=dikonfirmasi" class="filter-tab <?= $filter==='dikonfirmasi' ? 'active' : '' ?>">Dikonfirmasi</a>
      <a href="?status=dikirim"      class="filter-tab <?= $filter==='dikirim'      ? 'active' : '' ?>">Dikirim</a>
      <a href="?status=selesai"      class="filter-tab <?= $filter==='selesai'      ? 'active' : '' ?>">Selesai</a>
      <a href="?status=dibatalkan"   class="filter-tab <?= $filter==='dibatalkan'   ? 'active' : '' ?>">Dibatalkan</a>
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
            <tr><td colspan="9" class="tbl-empty-lg">Tidak ada pesanan.</td></tr>
            <?php else: foreach ($list as $ps): ?>
            <tr>
              <td class="col-id">#<?= $ps['id'] ?></td>
              <td>
                <div class="produk-col">
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
                <div class="col-nama-utama"><?= htmlspecialchars($ps['pelanggan_nama']) ?></div>
                <div class="col-nama-sub">@<?= htmlspecialchars($ps['username']) ?></div>
              </td>
              <td>
                <div class="col-nama-utama"><?= htmlspecialchars($ps['nama_penerima']) ?></div>
                <div class="col-nama-sub"><?= htmlspecialchars($ps['telepon']) ?></div>
              </td>
              <td class="col-center"><?= $ps['jumlah'] ?></td>
              <td class="col-total">Rp <?= number_format($ps['total_harga'], 0, ',', '.') ?></td>
              <td>
                <span class="status-badge status-badge--<?= $ps['status'] ?>">
                  <?= $status_options[$ps['status']] ?? $ps['status'] ?>
                </span>
              </td>
              <td class="col-tanggal"><?= date('d M Y', strtotime($ps['dibuat_pada'])) ?></td>
              <td class="col-aksi">
                <form method="POST" class="status-form">
                  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>" />
                  <input type="hidden" name="id" value="<?= $ps['id'] ?>" />
                  <div class="status-form-row">
                    <select name="status" class="status-sel">
                      <?php foreach ($status_options as $k => $v): ?>
                      <option value="<?= $k ?>" <?= $ps['status'] === $k ? 'selected' : '' ?>><?= $v ?></option>
                      <?php endforeach; ?>
                    </select>
                    <button class="btn btn-outline btn-sm" type="submit">Simpan</button>
                  </div>
                  <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $ps['telepon']) ?>?text=<?= urlencode("Halo {$ps['nama_penerima']}, pesanan #{$ps['id']} kamu sudah kami terima. Silakan transfer ke rekening BRI: 1234567890 a/n Talenta Florist. Total: Rp " . number_format($ps['total_harga'], 0, ',', '.')) ?>"
                     target="_blank"
                     class="btn btn-sm btn-wa">💬 WhatsApp</a>
                </form>
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