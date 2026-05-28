<?php
// admin/testimoni__view.php  —  dipakai oleh testimoni/index.php
// Variabel yang harus tersedia:
//   $list        : array semua testimoni
//   $flash       : flash message atau null
//   $active_menu = 'testimoni'
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Testimoni – Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/admin/admin.css" />
  <style>
    .testi-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 1.1rem;
    }
    .testi-card {
      background: #fff;
      border: 1px solid var(--sand, #f0ece4);
      border-radius: 14px;
      padding: 1.3rem 1.4rem;
      display: flex;
      flex-direction: column;
      gap: .75rem;
      transition: box-shadow .2s, transform .2s;
      position: relative;
    }
    .testi-card:hover {
      box-shadow: 0 6px 24px rgba(0,0,0,.08);
      transform: translateY(-2px);
    }
    .testi-card-quote {
      position: absolute;
      top: 1rem; right: 1.2rem;
      font-family: 'Cormorant Garamond', serif;
      font-size: 3rem;
      line-height: 1;
      color: var(--sand, #e0d9d0);
      pointer-events: none;
      user-select: none;
    }
    .testi-header {
      display: flex;
      align-items: center;
      gap: .75rem;
    }
    .testi-avatar {
      width: 40px; height: 40px;
      border-radius: 50%;
      background: linear-gradient(135deg, #f0e8dd, #e0d0c0);
      display: flex; align-items: center; justify-content: center;
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.1rem;
      font-weight: 600;
      color: var(--dark, #2a2420);
      flex-shrink: 0;
    }
    .testi-nama {
      font-weight: 500;
      font-size: .9rem;
      color: var(--dark, #2a2420);
    }
    .testi-date {
      font-size: .72rem;
      color: var(--muted, #9a9087);
    }
    .testi-rating {
      display: flex;
      gap: .15rem;
    }
    .star { font-size: .9rem; }
    .star.filled { color: #c9a44a; }
    .star.empty  { color: var(--sand, #d0cac2); }
    .testi-pesan {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1rem;
      line-height: 1.6;
      color: var(--dark, #3a332e);
      font-style: italic;
    }
    .testi-footer {
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-top: 1px solid var(--sand, #f0ece4);
      padding-top: .75rem;
      margin-top: auto;
    }
    .testi-actions { display: flex; gap: .4rem; }

    .testi-stats {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1rem;
      margin-bottom: 1.5rem;
    }
    .testi-stat {
      background: #fff;
      border: 1px solid var(--sand, #f0ece4);
      border-radius: 12px;
      padding: 1rem 1.2rem;
      text-align: center;
    }
    .testi-stat-val {
      font-family: 'Cormorant Garamond', serif;
      font-size: 2rem;
      font-weight: 600;
      color: var(--dark, #2a2420);
    }
    .testi-stat-label {
      font-size: .72rem;
      color: var(--muted, #9a9087);
      text-transform: uppercase;
      letter-spacing: .06em;
    }
    .testi-stat-stars { color: #c9a44a; font-size: 1.1rem; }

    .filter-tabs {
      display: flex;
      gap: .4rem;
      margin-bottom: 1rem;
    }
    .filter-tab {
      padding: .35rem .9rem;
      border-radius: 20px;
      border: 1px solid var(--sand, #d0cac2);
      font-size: .78rem;
      cursor: pointer;
      background: none;
      color: var(--muted, #9a9087);
      transition: all .15s;
    }
    .filter-tab.active,
    .filter-tab:hover {
      background: var(--dark, #2a2420);
      color: #fff;
      border-color: var(--dark, #2a2420);
    }

    .empty-state {
      grid-column: 1 / -1;
      text-align: center;
      padding: 3rem;
      color: var(--muted, #9a9087);
    }
  </style>
</head>
<body>
<?php include __DIR__ . '/../../includes/sidebar.php'; ?>

<div class="admin-main">
  <div class="admin-topbar">
    <button id="sidebar-toggle">☰</button>
    <h1 class="topbar-title">Manajemen <em>Testimoni</em></h1>
    <a href="/admin/testimoni/tambah.php" class="btn btn-primary btn-sm">+ Tambah</a>
  </div>

  <div class="admin-body">

    <?php if ($flash): ?>
    <div class="flash-msg flash-<?= $flash['type'] ?>"><?= htmlspecialchars($flash['msg']) ?></div>
    <?php endif; ?>

    <?php
    // Hitung statistik
    $total      = count($list);
    $aktif      = array_filter($list, fn($t) => $t['aktif']);
    $avg_rating = $total > 0 ? round(array_sum(array_column($list, 'rating')) / $total, 1) : 0;
    ?>

    <!-- Stats -->
    <div class="testi-stats">
      <div class="testi-stat">
        <div class="testi-stat-val"><?= $total ?></div>
        <div class="testi-stat-label">Total Testimoni</div>
      </div>
      <div class="testi-stat">
        <div class="testi-stat-val"><?= count($aktif) ?></div>
        <div class="testi-stat-label">Tampil di Website</div>
      </div>
      <div class="testi-stat">
        <div class="testi-stat-stars"><?= str_repeat('★', round($avg_rating)) ?><?= str_repeat('☆', 5 - round($avg_rating)) ?></div>
        <div class="testi-stat-val" style="font-size:1.5rem;"><?= number_format($avg_rating, 1) ?></div>
        <div class="testi-stat-label">Rata-rata Rating</div>
      </div>
    </div>

    <!-- Filter Tabs -->
    <div class="filter-tabs">
      <button class="filter-tab active" data-filter="all">Semua</button>
      <button class="filter-tab" data-filter="aktif">Ditampilkan</button>
      <button class="filter-tab" data-filter="nonaktif">Disembunyikan</button>
    </div>

    <!-- Cards -->
    <div class="testi-grid" id="testi-grid">
      <?php if (empty($list)): ?>
      <div class="empty-state">
        <div style="font-size:2.8rem;margin-bottom:.8rem;opacity:.5;">💬</div>
        <p>Belum ada testimoni.<br>Tambahkan testimoni pertama Anda.</p>
      </div>
      <?php else: foreach ($list as $t): ?>
      <div class="testi-card" data-aktif="<?= $t['aktif'] ? 'aktif' : 'nonaktif' ?>">
        <span class="testi-card-quote">"</span>
        <div class="testi-header">
          <div class="testi-avatar"><?= mb_strtoupper(mb_substr($t['nama'], 0, 1)) ?></div>
          <div>
            <div class="testi-nama"><?= htmlspecialchars($t['nama']) ?></div>
            <div class="testi-date"><?= date('d M Y', strtotime($t['dibuat_pada'])) ?></div>
          </div>
        </div>
        <div class="testi-rating">
          <?php for ($s = 1; $s <= 5; $s++): ?>
          <span class="star <?= $s <= $t['rating'] ? 'filled' : 'empty' ?>">★</span>
          <?php endfor; ?>
        </div>
        <div class="testi-pesan"><?= htmlspecialchars($t['pesan']) ?></div>
        <div class="testi-footer">
          <span class="badge <?= $t['aktif'] ? 'badge-aktif' : 'badge-nonaktif' ?>">
            <?= $t['aktif'] ? 'Tampil' : 'Disembunyikan' ?>
          </span>
          <div class="testi-actions">
            <a href="/admin/testimoni/edit.php?id=<?= $t['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
            <a href="/admin/testimoni/hapus.php?id=<?= $t['id'] ?>" class="btn btn-hapus btn-sm"
               onclick="return confirm('Hapus testimoni dari <?= htmlspecialchars(addslashes($t['nama'])) ?>?')">Hapus</a>
          </div>
        </div>
      </div>
      <?php endforeach; endif; ?>
    </div>

    <!-- Tabel ringkasan (tetap tersedia sebagai referensi) -->
    <div class="panel" style="margin-top:2rem;">
      <div class="panel-title">Semua Testimoni (Tabel)</div>
      <div class="tbl-wrap">
        <table>
          <thead>
            <tr>
              <th>Nama</th>
              <th>Pesan</th>
              <th>Rating</th>
              <th>Status</th>
              <th>Tanggal</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($list)): ?>
            <tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--muted);">Belum ada testimoni.</td></tr>
            <?php else: foreach ($list as $t): ?>
            <tr>
              <td style="font-weight:500;white-space:nowrap;"><?= htmlspecialchars($t['nama']) ?></td>
              <td style="max-width:280px;">
                <span style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                  <?= htmlspecialchars($t['pesan']) ?>
                </span>
              </td>
              <td style="color:#c9a44a;"><?= str_repeat('★', (int)$t['rating']) ?></td>
              <td><span class="badge <?= $t['aktif'] ? 'badge-aktif' : 'badge-nonaktif' ?>"><?= $t['aktif'] ? 'Tampil' : 'Disembunyikan' ?></span></td>
              <td style="white-space:nowrap;"><?= date('d M Y', strtotime($t['dibuat_pada'])) ?></td>
              <td style="white-space:nowrap;">
                <a href="/admin/testimoni/edit.php?id=<?= $t['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
                <a href="/admin/testimoni/hapus.php?id=<?= $t['id'] ?>" class="btn btn-hapus btn-sm"
                   onclick="return confirm('Hapus?')">Hapus</a>
              </td>
            </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

<script src="/js/admin.js"></script>
<script>
document.querySelectorAll('.filter-tab').forEach(tab => {
  tab.addEventListener('click', function() {
    document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
    this.classList.add('active');
    const f = this.dataset.filter;
    document.querySelectorAll('.testi-card').forEach(card => {
      if (f === 'all' || card.dataset.aktif === f) {
        card.style.display = '';
      } else {
        card.style.display = 'none';
      }
    });
  });
});
</script>
</body>
</html>