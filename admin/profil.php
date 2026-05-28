<?php
/**
 * admin/views/profil.view.php
 * Template HTML untuk halaman profil admin.
 * Variabel yang dibutuhkan dari profil.php:
 *   $admin      — array data admin (id, username, nama)
 *   $flash      — flash message (dari getFlash())
 *   $errorsInfo — array error form info
 *   $errorsPass — array error form password
 *   $adminName  — nama admin (string)
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Profil Admin – Talenta Florist</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../../css/admin.css" />
  <style>
    /* ── STAR STRENGTH INDICATOR ── */
    .strength-bar {
      height: 4px; border-radius: 2px;
      background: var(--sand); margin-top: .4rem;
      transition: all .3s;
    }
    .strength-fill {
      height: 100%; border-radius: 2px;
      width: 0; transition: width .3s, background .3s;
    }

    /* ── AVATAR ── */
    .profile-avatar {
      width: 88px; height: 88px; border-radius: 50%;
      background: linear-gradient(135deg, var(--blush), var(--rose));
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 1rem;
      font-family: 'Cormorant Garamond', serif;
      font-size: 2.2rem; font-weight: 600; color: #fff;
      overflow: hidden;
      box-shadow: 0 8px 24px rgba(201,122,106,.3);
    }
    .profile-avatar img {
      width: 100%; height: 100%; object-fit: cover;
    }

    /* ── INFO BOX ── */
    .info-row {
      display: flex; align-items: center; gap: .75rem;
      padding: .75rem 0;
      border-bottom: 1px solid var(--sand);
      font-size: .88rem;
    }
    .info-row:last-child  { border-bottom: none; }
    .info-row-icon        { font-size: 1.1rem; width: 24px; text-align: center; flex-shrink: 0; }
    .info-row-label       { font-size: .68rem; letter-spacing: .12em; text-transform: uppercase; color: var(--muted); min-width: 80px; }
    .info-row-value       { font-weight: 500; color: var(--deep); }

    /* ── SESSION INFO ── */
    .session-box {
      background: var(--cream); border-radius: 10px;
      padding: 1rem 1.25rem; margin-top: 1rem;
      font-size: .82rem; color: var(--muted);
      display: flex; flex-direction: column; gap: .35rem;
    }
    .session-box span { display: flex; align-items: center; gap: .5rem; }
  </style>
</head>
<body>
<div class="admin-wrapper">

  <!-- ── SIDEBAR ── -->
  <?php include __DIR__ . '/../../includes/sidebar.php'; ?>

  <!-- ── MAIN ── -->
  <div class="main-content">

    <!-- Topbar -->
    <div class="topbar">
      <button class="btn btn-sm btn-outline"
        style="border-radius:8px;"
        onclick="document.getElementById('sidebar').classList.toggle('open')"
        title="Toggle sidebar">☰</button>
      <div class="topbar-title">Profil Admin</div>
      <div class="topbar-right">
        <span class="topbar-user">Halo, <strong><?= htmlspecialchars($adminName) ?></strong></span>
        <a href="../../logout.php" class="btn btn-sm btn-outline">🚪 Logout</a>
      </div>
    </div>

    <!-- Konten -->
    <div class="page-content">

      <!-- Breadcrumb -->
      <p style="font-size:.78rem;color:var(--muted);margin-bottom:1.75rem;">
        <a href="../dashboard.php" style="color:var(--muted);text-decoration:none;">Dashboard</a>
        <span style="margin:0 .4rem;opacity:.4;">/</span>
        <span style="color:var(--deep);">Profil</span>
      </p>

      <!-- Flash message -->
      <?php if ($flash): ?>
      <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>" style="margin-bottom:1.5rem;">
        <?= htmlspecialchars($flash['msg']) ?>
      </div>
      <?php endif; ?>

      <div style="display:grid;grid-template-columns:300px 1fr 1fr;gap:1.5rem;align-items:start;">

        <!-- ── KOLOM 1: Info Akun ── -->
        <div style="display:flex;flex-direction:column;gap:1.25rem;">

          <!-- Card avatar & info singkat -->
          <div class="card">
            <div class="card-body" style="text-align:center;padding:2rem 1.5rem 1.5rem;">
              <div class="profile-avatar">
                <?= mb_strtoupper(mb_substr($admin['nama'], 0, 1)) ?>
              </div>
              <div style="font-family:'Cormorant Garamond',serif;font-size:1.4rem;font-weight:600;color:var(--deep);">
                <?= htmlspecialchars($admin['nama']) ?>
              </div>
              <div style="font-size:.78rem;color:var(--muted);margin-top:.2rem;">
                @<?= htmlspecialchars($admin['username']) ?>
              </div>
              <span class="badge badge-rose" style="margin-top:.75rem;">Administrator</span>
            </div>
            <div style="padding:0 1.5rem 1.5rem;">
              <div class="info-row">
                <span class="info-row-icon">👤</span>
                <span class="info-row-label">Username</span>
                <span class="info-row-value"><?= htmlspecialchars($admin['username']) ?></span>
              </div>
              <div class="info-row">
                <span class="info-row-icon">🪪</span>
                <span class="info-row-label">ID Admin</span>
                <span class="info-row-value">#<?= $admin['id'] ?></span>
              </div>
              <div class="info-row">
                <span class="info-row-icon">🔑</span>
                <span class="info-row-label">Role</span>
                <span class="info-row-value">Admin</span>
              </div>
            </div>
          </div>

          <!-- Session info -->
          <div class="card">
            <div class="card-header"><span class="card-title">📋 Info Sesi</span></div>
            <div class="card-body">
              <div class="session-box">
                <span>🕐 Login: <strong><?= date('d M Y, H:i') ?> WITA</strong></span>
                <span>⏱️ Timeout: <strong>30 menit tidak aktif</strong></span>
                <span>🌐 IP: <strong><?= htmlspecialchars($_SERVER['REMOTE_ADDR'] ?? '-') ?></strong></span>
              </div>
            </div>
          </div>

        </div>

        <!-- ── KOLOM 2: Edit Nama ── -->
        <div class="card">
          <div class="card-header">
            <span class="card-title">✏️ Edit Nama</span>
          </div>
          <div class="card-body">

            <?php if (!empty($errorsInfo)): ?>
            <div class="alert alert-error" style="flex-direction:column;align-items:flex-start;gap:.3rem;margin-bottom:1.25rem;">
              <?php foreach ($errorsInfo as $e): ?>
              <span>• <?= htmlspecialchars($e) ?></span>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="../profil.php" class="form-grid">
              <input type="hidden" name="form_type" value="info" />

              <div class="form-group">
                <label for="nama">
                  Nama Tampil <span style="color:var(--rose)">*</span>
                </label>
                <input class="form-control" type="text" id="nama" name="nama"
                  placeholder="Nama yang ditampilkan di panel"
                  value="<?= htmlspecialchars($admin['nama']) ?>"
                  required />
                <span class="form-hint">
                  Nama ini yang muncul di topbar panel admin.
                </span>
              </div>

              <div class="form-group">
                <label>Username</label>
                <input class="form-control" type="text"
                  value="<?= htmlspecialchars($admin['username']) ?>"
                  disabled
                  style="opacity:.55;cursor:not-allowed;background:var(--cream);" />
                <span class="form-hint">Username tidak dapat diubah.</span>
              </div>

              <div style="margin-top:.5rem;">
                <button type="submit" class="btn btn-primary">
                  💾 Simpan Nama
                </button>
              </div>
            </form>

          </div>
        </div>

        <!-- ── KOLOM 3: Ganti Password ── -->
        <div class="card">
          <div class="card-header">
            <span class="card-title">🔒 Ganti Password</span>
          </div>
          <div class="card-body">

            <?php if (!empty($errorsPass)): ?>
            <div class="alert alert-error" style="flex-direction:column;align-items:flex-start;gap:.3rem;margin-bottom:1.25rem;">
              <?php foreach ($errorsPass as $e): ?>
              <span>• <?= htmlspecialchars($e) ?></span>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="../profil.php" class="form-grid">
              <input type="hidden" name="form_type" value="password" />

              <!-- Password lama -->
              <div class="form-group">
                <label for="pass_lama">Password Lama <span style="color:var(--rose)">*</span></label>
                <div style="position:relative;">
                  <input class="form-control" type="password"
                    id="pass_lama" name="pass_lama"
                    placeholder="Masukkan password lama"
                    style="padding-right:2.75rem;"
                    required />
                  <button type="button"
                    onclick="togglePw('pass_lama','eye1')"
                    style="position:absolute;right:.9rem;top:50%;transform:translateY(-50%);
                           background:none;border:none;cursor:pointer;font-size:.9rem;color:var(--muted);">
                    <span id="eye1">👁️</span>
                  </button>
                </div>
              </div>

              <!-- Password baru -->
              <div class="form-group">
                <label for="pass_baru">Password Baru <span style="color:var(--rose)">*</span></label>
                <div style="position:relative;">
                  <input class="form-control" type="password"
                    id="pass_baru" name="pass_baru"
                    placeholder="Minimal 8 karakter"
                    style="padding-right:2.75rem;"
                    oninput="checkStrength(this.value)"
                    required />
                  <button type="button"
                    onclick="togglePw('pass_baru','eye2')"
                    style="position:absolute;right:.9rem;top:50%;transform:translateY(-50%);
                           background:none;border:none;cursor:pointer;font-size:.9rem;color:var(--muted);">
                    <span id="eye2">👁️</span>
                  </button>
                </div>
                <!-- Strength indicator -->
                <div class="strength-bar">
                  <div class="strength-fill" id="strength-fill"></div>
                </div>
                <span id="strength-text" class="form-hint"></span>
              </div>

              <!-- Konfirmasi password -->
              <div class="form-group">
                <label for="pass_ulang">Konfirmasi Password Baru <span style="color:var(--rose)">*</span></label>
                <input class="form-control" type="password"
                  id="pass_ulang" name="pass_ulang"
                  placeholder="Ulangi password baru"
                  oninput="checkMatch()"
                  required />
                <span id="match-msg" class="form-hint"></span>
              </div>

              <div style="margin-top:.5rem;">
                <button type="submit" class="btn btn-primary">
                  🔑 Perbarui Password
                </button>
              </div>
            </form>

          </div>
        </div>

      </div><!-- /grid -->
    </div><!-- /page-content -->
  </div><!-- /main-content -->
</div><!-- /admin-wrapper -->

<script src="../../js/admin.js"></script>
<script>
/* ── Password toggle ── */
function togglePw(inputId, eyeId) {
  var input = document.getElementById(inputId);
  var eye   = document.getElementById(eyeId);
  if (input.type === 'password') {
    input.type       = 'text';
    eye.textContent  = '🙈';
  } else {
    input.type       = 'password';
    eye.textContent  = '👁️';
  }
}

/* ── Password strength ── */
function checkStrength(val) {
  var fill = document.getElementById('strength-fill');
  var text = document.getElementById('strength-text');
  var score = 0;
  if (val.length >= 8)            score++;
  if (/[A-Z]/.test(val))          score++;
  if (/[0-9]/.test(val))          score++;
  if (/[^A-Za-z0-9]/.test(val))   score++;

  var levels = [
    { w: '0%',   c: '#dc2626', t: '' },
    { w: '25%',  c: '#dc2626', t: 'Lemah' },
    { w: '50%',  c: '#f59e0b', t: 'Sedang' },
    { w: '75%',  c: '#3b82f6', t: 'Kuat' },
    { w: '100%', c: '#16a34a', t: 'Sangat Kuat' },
  ];
  var lv             = levels[score] || levels[0];
  fill.style.width      = lv.w;
  fill.style.background = lv.c;
  text.textContent      = lv.t;
  text.style.color      = lv.c;
}

/* ── Cek kecocokan password ── */
function checkMatch() {
  var baru  = document.getElementById('pass_baru').value;
  var ulang = document.getElementById('pass_ulang').value;
  var msg   = document.getElementById('match-msg');
  if (!ulang) { msg.textContent = ''; return; }
  if (baru === ulang) {
    msg.textContent = '✅ Password cocok';
    msg.style.color = '#16a34a';
  } else {
    msg.textContent = '❌ Password tidak cocok';
    msg.style.color = '#dc2626';
  }
}
</script>
</body>
</html>