<?php
// register.php
session_start();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/base_path.php';

// Jika sudah login (baik admin maupun user), lempar ke beranda
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: ' . base_url('index.php'));
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $telepon  = trim($_POST['telepon'] ?? '');
    $alamat   = trim($_POST['alamat'] ?? '');

    // Validasi backend
    if (empty($nama) || empty($username) || empty($email) || empty($password)) {
        $error = 'Field bertanda bintang (*) wajib diisi!';
    } else {
        try {
            $stmtCek = db()->prepare("SELECT id FROM pelanggan WHERE username = ? OR email = ? LIMIT 1");
            $stmtCek->execute([$username, $email]);
            
            if ($stmtCek->fetch()) {
                $error = 'Username atau Email sudah terdaftar! Silakan gunakan yang lain.';
            } else {
                $password_terenkripsi = password_hash($password, PASSWORD_BCRYPT);

                $sqlInsert = "INSERT INTO pelanggan (nama, username, email, password, telepon, alamat) VALUES (?, ?, ?, ?, ?, ?)";
                $stmtInsert = db()->prepare($sqlInsert);
                $stmtInsert->execute([$nama, $username, $email, $password_terenkripsi, $telepon, $alamat]);

                $success = 'Pendaftaran berhasil! Silakan <a href="'. base_url('login.php') .'">login di sini</a>.';
            }
        } catch (PDOException $e) {
            $error = 'Kesalahan sistem: ' . $e->getMessage();
        }
    }
}

// Persiapan untuk header
$page_title = 'Daftar Akun';
$active_nav = 'register';
require_once __DIR__ . '/includes/header.php';
?>

<main class="auth-wrap">
    <div class="auth-container">
        <h2>Daftar <span>Akun</span></h2>

        <?php if (!empty($error)): ?>
            <div class="auth-alert error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="auth-alert success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form action="" method="POST" id="formRegister">
            <div class="auth-form-group">
                <label for="nama">Nama Lengkap *</label>
                <input type="text" id="nama" name="nama" required>
            </div>
            
            <div class="auth-form-group">
                <label for="username">Username *</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="auth-form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="auth-form-group">
                <label for="password">Password * (Min. 6 karakter)</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="auth-form-group">
                <label for="telepon">No. Telepon / WA</label>
                <input type="text" id="telepon" name="telepon">
            </div>
            
            <div class="auth-form-group">
                <label for="alamat">Alamat Lengkap</label>
                <textarea id="alamat" name="alamat" rows="2"></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary auth-btn">Daftar Sekarang</button>
        </form>

        <div class="auth-link">
            Sudah memiliki akun? <a href="<?= base_url('login.php') ?>">Masuk di sini</a>
        </div>
    </div>
</main>

<script src="<?= base_url('js/register.js') ?>"></script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
