<?php
// includes/sidebar.php
// Sertakan di semua halaman admin.
// Variabel yang harus di-set sebelum include:
//   $active_menu – slug menu aktif: 'dashboard','produk','kategori','testimoni','pengaturan'

if (!isset($active_menu)) $active_menu = 'dashboard';

if (!defined('BASE_PATH')) {
    require_once __DIR__ . '/../config/base_path.php';
}

$menus = [
    ['slug' => 'dashboard',  'icon' => '◈', 'label' => 'Dashboard',  'href' => base_url('admin/dashboard.php')],
    ['slug' => 'produk',     'icon' => '🌸', 'label' => 'Produk',     'href' => base_url('admin/produk/index.php')],
    ['slug' => 'kategori',   'icon' => '🏷', 'label' => 'Kategori',   'href' => base_url('admin/kategori/index.php')],
    ['slug' => 'testimoni',  'icon' => '⭐', 'label' => 'Testimoni',  'href' => base_url('admin/testimoni/index.php')],
    ['slug' => 'pengaturan', 'icon' => '⚙', 'label' => 'Pengaturan', 'href' => base_url('admin/pengaturan.php')],
];
?>
<aside id="admin-sidebar">

  <div class="sidebar-logo">
    <a href="<?= base_url('index.php') ?>" class="s-logo">Talenta <span>Florist</span></a>
    <span class="s-tag">Admin Panel</span>
  </div>

  <nav class="sidebar-nav">
    <?php foreach ($menus as $m): ?>
    <a href="<?= $m['href'] ?>"
       class="s-link <?= $active_menu === $m['slug'] ? 'active' : '' ?>">
      <span class="s-icon"><?= $m['icon'] ?></span>
      <span class="s-label"><?= $m['label'] ?></span>
    </a>
    <?php endforeach; ?>
  </nav>

  <div class="sidebar-footer">
    <div class="s-admin-info">
      <span class="s-admin-avatar"><?= mb_strtoupper(mb_substr($_SESSION['admin_nama'] ?? 'A', 0, 1)) ?></span>
      <span class="s-admin-name"><?= htmlspecialchars($_SESSION['admin_nama'] ?? 'Admin') ?></span>
    </div>
    <a href="<?= base_url('logout.php') ?>" class="s-logout" title="Keluar">⏻</a>
  </div>

</aside>