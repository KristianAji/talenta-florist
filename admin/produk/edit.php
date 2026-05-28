<?php
// admin/produk/edit.php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/db.php';

$active_menu = 'produk';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$errors = [];

$produk = db()->prepare('SELECT * FROM produk WHERE id=?');
$produk->execute([$id]);
$produk = $produk->fetch();
if (!$produk) { header('Location: ' . base_url('admin/produk/index.php')); exit; }

$variasi_list  = db()->prepare('SELECT * FROM variasi WHERE produk_id=? ORDER BY urutan');
$variasi_list->execute([$id]);
$variasi_list  = $variasi_list->fetchAll();

$kategori_list = db()->query('SELECT * FROM kategori ORDER BY urutan')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama        = trim($_POST['nama'] ?? '');
    $deskripsi   = trim($_POST['deskripsi'] ?? '');
    $badge       = trim($_POST['badge'] ?? '');
    $kategori_id = (int)($_POST['kategori_id'] ?? 0);
    $aktif       = isset($_POST['aktif']) ? 1 : 0;

    $var_id     = $_POST['var_id']    ?? [];
    $var_nama   = $_POST['var_nama']  ?? [];
    $var_harga  = $_POST['var_harga'] ?? [];

    if (!$nama)        $errors[] = 'Nama produk wajib diisi.';
    if (!$kategori_id) $errors[] = 'Kategori wajib dipilih.';

    if (empty($errors)) {
        db()->beginTransaction();
        try {
            db()->prepare("UPDATE produk SET kategori_id=?, nama=?, deskripsi=?, badge=?, aktif=? WHERE id=?")
               ->execute([$kategori_id, $nama, $deskripsi, $badge ?: null, $aktif, $id]);

            $keep_ids = array_filter(array_map('intval', $var_id));
            if ($keep_ids) {
                $in = implode(',', $keep_ids);
                db()->exec("DELETE FROM variasi WHERE produk_id=$id AND id NOT IN ($in)");
            } else {
                db()->exec("DELETE FROM variasi WHERE produk_id=$id");
            }

            $stmtU = db()->prepare("UPDATE variasi SET nama=?, harga=?, gambar=COALESCE(?,gambar), urutan=? WHERE id=?");
            $stmtI = db()->prepare("INSERT INTO variasi (produk_id, nama, harga, gambar, urutan) VALUES (?,?,?,?,?)");

            foreach ($var_nama as $i => $vn) {
                $vn = trim($vn);
                if (!$vn) continue;
                $vh  = (int)($var_harga[$i] ?? 0);
                $vid = (int)($var_id[$i]    ?? 0);

                $gambar = null;
                $file_key = "var_gambar_$i";
                if (!empty($_FILES[$file_key]['tmp_name'])) {
                    $ext     = strtolower(pathinfo($_FILES[$file_key]['name'], PATHINFO_EXTENSION));
                    $allowed = ['jpg','jpeg','png','webp'];
                    if (in_array($ext, $allowed)) {
                        $dir  = __DIR__ . '/../../uploads/';
                        $fname = uniqid('img_') . '.' . $ext;
                        if (move_uploaded_file($_FILES[$file_key]['tmp_name'], $dir . $fname)) {
                            $gambar = 'uploads/' . $fname;
                        }
                    }
                }

                if ($vid) {
                    $stmtU->execute([$vn, $vh, $gambar, $i + 1, $vid]);
                } else {
                    $stmtI->execute([$id, $vn, $vh, $gambar, $i + 1]);
                }
            }

            db()->commit();
            $_SESSION['flash'] = ['type'=>'success','msg'=>'Produk berhasil diperbarui.'];
            header('Location: ' . base_url('admin/produk/index.php'));
            exit;
        } catch (\Exception $e) {
            db()->rollBack();
            $errors[] = 'Gagal menyimpan: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Produk – Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?= base_url('admin/admin.css') ?>" />
</head>
<body>

<?php include __DIR__ . '/../../includes/sidebar.php'; ?>

<div class="admin-main">
  <div class="admin-topbar">
    <button id="sidebar-toggle">☰</button>
    <h1 class="topbar-title">Edit <em>Produk</em></h1>
    <a href="<?= base_url('admin/produk/index.php') ?>" class="btn btn-outline btn-sm">← Kembali</a>
  </div>

  <div class="admin-body">

    <?php if ($errors): ?>
    <div class="flash-msg flash-error"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
      <div class="panel">
        <div class="panel-title">Informasi Produk</div>
        <div class="form-row">
          <div class="form-group">
            <label>Nama Produk *</label>
            <input type="text" name="nama" value="<?= htmlspecialchars($produk['nama']) ?>" required />
          </div>
          <div class="form-group">
            <label>Kategori *</label>
            <select name="kategori_id" required>
              <option value="">– Pilih Kategori –</option>
              <?php foreach ($kategori_list as $k): ?>
              <option value="<?= $k['id'] ?>" <?= $produk['kategori_id']==$k['id']?'selected':'' ?>>
                <?= htmlspecialchars($k['nama']) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label>Deskripsi</label>
          <textarea name="deskripsi"><?= htmlspecialchars($produk['deskripsi'] ?? '') ?></textarea>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Badge (opsional)</label>
            <input type="text" name="badge" value="<?= htmlspecialchars($produk['badge'] ?? '') ?>" />
          </div>
          <div class="form-group" style="display:flex;align-items:center;padding-top:1.6rem;">
            <label class="form-check">
              <input type="checkbox" name="aktif" value="1" <?= $produk['aktif']?'checked':'' ?> />
              Produk Aktif
            </label>
          </div>
        </div>
      </div>

      <div class="panel">
        <div class="panel-title">Variasi Produk</div>
        <div class="variasi-list" id="variasi-list">
          <?php foreach ($variasi_list as $i => $v): ?>
          <div class="variasi-item">
            <input type="hidden" name="var_id[]" value="<?= $v['id'] ?>" />
            <span class="remove-variasi" title="Hapus variasi">✕</span>
            <div class="form-row">
              <div class="form-group">
                <label>Nama Variasi</label>
                <input type="text" name="var_nama[]" value="<?= htmlspecialchars($v['nama']) ?>" />
              </div>
              <div class="form-group">
                <label>Harga (Rp)</label>
                <input type="number" name="var_harga[]" value="<?= $v['harga'] ?>" min="0" />
              </div>
            </div>
            <div class="form-group">
              <label>Gambar Variasi (kosongkan jika tidak diubah)</label>
              <?php if ($v['gambar']): ?>
              <img src="<?= base_url($v['gambar']) ?>" style="width:80px;height:80px;object-fit:cover;border-radius:8px;margin-bottom:.5rem;display:block;" />
              <?php endif; ?>
              <input type="file" name="var_gambar_<?= $i ?>" accept="image/*" data-preview="prev_<?= $i ?>" />
              <img id="prev_<?= $i ?>" class="img-preview" src="" alt="" />
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <button type="button" id="btn-add-variasi">+ Tambah Variasi</button>
      </div>

      <div style="display:flex;gap:.8rem;">
        <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
        <a href="<?= base_url('admin/produk/index.php') ?>" class="btn btn-outline">Batal</a>
      </div>
    </form>
  </div>
</div>

<script src="<?= base_url('js/admin.js') ?>"></script>
<script>
let idx = <?= count($variasi_list) ?>;

document.getElementById('btn-add-variasi').addEventListener('click', () => {
  const list = document.getElementById('variasi-list');
  const div  = document.createElement('div');
  div.className = 'variasi-item';
  div.innerHTML = `
    <input type="hidden" name="var_id[]" value="0" />
    <span class="remove-variasi">✕</span>
    <div class="form-row">
      <div class="form-group">
        <label>Nama Variasi</label>
        <input type="text" name="var_nama[]" placeholder="cth: Variasi ${idx + 1}" />
      </div>
      <div class="form-group">
        <label>Harga (Rp)</label>
        <input type="number" name="var_harga[]" min="0" placeholder="0" />
      </div>
    </div>
    <div class="form-group">
      <label>Gambar Variasi</label>
      <input type="file" name="var_gambar_${idx}" accept="image/*" data-preview="prev_new_${idx}" />
      <img id="prev_new_${idx}" class="img-preview" />
    </div>`;
  list.appendChild(div);
  div.querySelector('input[type=file]').addEventListener('change', function () {
    const p = document.getElementById(this.dataset.preview);
    if (!p) return;
    const r = new FileReader();
    r.onload = e => { p.src = e.target.result; p.style.display = 'block'; };
    r.readAsDataURL(this.files[0]);
  });
  div.querySelector('.remove-variasi').addEventListener('click', () => div.remove());
  idx++;
});

document.querySelectorAll('.remove-variasi').forEach(btn => {
  btn.addEventListener('click', function () {
    const items = document.querySelectorAll('.variasi-item');
    if (items.length > 1) this.closest('.variasi-item').remove();
  });
});
</script>
</body>
</html>