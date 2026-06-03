<?php
// admin/pelanggan/index.php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/db.php';

$active_menu = 'pelanggan';
$flash       = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];

// Pencarian
$search = trim($_GET['q'] ?? '');
$params = [];
$where  = ['1=1'];

if ($search) {
    $where[]  = '(nama LIKE ? OR username LIKE ? OR email LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql  = 'SELECT * FROM pelanggan WHERE ' . implode(' AND ', $where) . ' ORDER BY id DESC';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$list = $stmt->fetchAll();

$total = db()->query('SELECT COUNT(*) FROM pelanggan')->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manajemen Pelanggan – Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?= base_url('admin/admin.css') ?>" />
  <link rel="stylesheet" href="<?= base_url('admin/css/pelanggan.css') ?>" />
</head>
<body>

<?php include __DIR__ . '/../../includes/sidebar.php'; ?>

<div class="admin-main">
  <div class="admin-topbar">
    <button id="sidebar-toggle">☰</button>
    <h1 class="topbar-title">Manajemen <em>Pelanggan</em></h1>
  </div>

  <div class="admin-body">

    <?php if ($flash): ?>
    <div class="flash-msg flash-<?= $flash['type'] ?>"><?= htmlspecialchars($flash['msg']) ?></div>
    <?php endif; ?>

    <!-- Statistik -->
    <div class="stat-row">
      <div class="stat-pill">
        <span>👥</span>
        <strong><?= $total ?></strong> total pelanggan terdaftar
      </div>
      <?php if ($search): ?>
      <div class="stat-pill">
        <span>🔍</span>
        <strong><?= count($list) ?></strong> hasil pencarian "<?= htmlspecialchars($search) ?>"
      </div>
      <?php endif; ?>
    </div>

    <!-- Search -->
    <div class="search-bar">
      <form method="get" class="search-bar-form">
        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>"
               placeholder="Cari nama, username, atau email…" />
        <button class="btn btn-primary btn-sm" type="submit">Cari</button>
        <?php if ($search): ?>
        <a href="<?= base_url('admin/pelanggan/index.php') ?>" class="btn btn-outline btn-sm">✕ Reset</a>
        <?php endif; ?>
      </form>
    </div>

    <!-- Tabel Pelanggan -->
    <div class="panel">
      <div class="tbl-wrap">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Pelanggan</th>
              <th>Email</th>
              <th>Telepon</th>
              <th>Alamat</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($list)): ?>
            <tr>
              <td colspan="6" class="tbl-empty-lg">
                <?= $search ? 'Tidak ada pelanggan yang cocok dengan pencarian.' : 'Belum ada pelanggan yang mendaftar.' ?>
              </td>
            </tr>
            <?php else: foreach ($list as $p): ?>
            <tr>
              <td class="col-id">#<?= $p['id'] ?></td>
              <td>
                <div class="pelanggan-nama">
                  <div class="avatar"><?= mb_strtoupper(mb_substr($p['nama'], 0, 1)) ?></div>
                  <div class="nama-info">
                    <strong><?= htmlspecialchars($p['nama']) ?></strong>
                    <span>@<?= htmlspecialchars($p['username']) ?></span>
                  </div>
                </div>
              </td>
              <td><?= htmlspecialchars($p['email']) ?></td>
              <td><?= $p['telepon'] ? htmlspecialchars($p['telepon']) : '<span class="col-muted">–</span>' ?></td>
              <td class="col-alamat">
                <?php if ($p['alamat']): ?>
                <span class="col-alamat-inner"><?= htmlspecialchars($p['alamat']) ?></span>
                <?php else: ?>
                <span class="col-muted">–</span>
                <?php endif; ?>
              </td>
              <td>
                <button type="button"
                        class="btn btn-hapus btn-sm"
                        data-modal-hapus
                        data-id="<?= $p['id'] ?>"
                        data-nama="<?= htmlspecialchars($p['nama'], ENT_QUOTES) ?>">Hapus</button>
              </td>
            </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

<!-- ── MODAL KONFIRMASI HAPUS ── -->
<div id="modal-hapus" class="modal-backdrop" hidden>
  <div class="modal-box">
    <p class="modal-msg">Hapus pelanggan <strong id="modal-nama"></strong>?<br>Tindakan ini tidak bisa dibatalkan.</p>
    <form method="post" action="<?= base_url('admin/pelanggan/hapus.php') ?>">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>" />
      <input type="hidden" name="id" id="modal-id" value="" />
      <div class="modal-actions">
        <button type="submit" class="btn btn-hapus">Ya, Hapus</button>
        <button type="button" class="btn btn-outline" id="modal-batal">Batal</button>
      </div>
    </form>
  </div>
</div>

<script src="<?= base_url('js/admin.js') ?>"></script>
<script>
(function () {
  const modal  = document.getElementById('modal-hapus');
  const namaEl = document.getElementById('modal-nama');
  const idEl   = document.getElementById('modal-id');

  document.querySelectorAll('[data-modal-hapus]').forEach(btn => {
    btn.addEventListener('click', () => {
      idEl.value         = btn.dataset.id;
      namaEl.textContent = btn.dataset.nama;
      modal.hidden       = false;
    });
  });

  document.getElementById('modal-batal').addEventListener('click', () => {
    modal.hidden = true;
  });

  modal.addEventListener('click', e => {
    if (e.target === modal) modal.hidden = true;
  });
})();
</script>
</body>
</html>