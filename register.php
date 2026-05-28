<?php
session_start();
// Memanggil koneksi MySQL (PHP murni/PDO)
require_once __DIR__ . '/config/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mengambil data dari form
    $nama     = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $telepon  = trim($_POST['telepon'] ?? '');
    $alamat   = trim($_POST['alamat'] ?? '');

    // Validasi backend (PHP)
    if (empty($nama) || empty($username) || empty($email) || empty($password)) {
        $error = 'Field bertanda bintang (*) wajib diisi!';
    } else {
        try {
            // Cek apakah username atau email sudah ada di database
            $stmtCek = $pdo->prepare("SELECT id FROM pelanggan WHERE username = ? OR email = ? LIMIT 1");
            $stmtCek->execute([$username, $email]);
            
            if ($stmtCek->fetch()) {
                $error = 'Username atau Email sudah terdaftar! Silakan gunakan yang lain.';
            } else {
                // Enkripsi password untuk keamanan
                $password_terenkripsi = password_hash($password, PASSWORD_BCRYPT);

                // Insert ke tabel pelanggan (TANPA kolom dibuat_pada)
                $sqlInsert = "INSERT INTO pelanggan (nama, username, email, password, telepon, alamat) VALUES (?, ?, ?, ?, ?, ?)";
                $stmtInsert = $pdo->prepare($sqlInsert);
                $stmtInsert->execute([$nama, $username, $email, $password_terenkripsi, $telepon, $alamat]);

                $success = 'Pendaftaran berhasil! Silakan login untuk melanjutkan.';
            }
        } catch (PDOException $e) {
            $error = 'Kesalahan sistem: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pelanggan - Kios Bunga Talenta</title>
    
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f7f9fc;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .form-container {
            background-color: #ffffff;
            padding: 30px 40px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 450px;
        }
        .form-container h2 {
            text-align: center;
            color: #333333;
            margin-bottom: 25px;
        }
        .input-group {
            margin-bottom: 15px;
        }
        .input-group label {
            display: block;
            margin-bottom: 5px;
            color: #555555;
            font-size: 14px;
        }
        .input-group input, 
        .input-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #cccccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-family: inherit;
        }
        .input-group input:focus, 
        .input-group textarea:focus {
            border-color: #28a745;
            outline: none;
        }
        .btn-submit {
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.3s;
        }
        .btn-submit:hover {
            background-color: #218838;
        }
        .pesan-error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 14px;
            border: 1px solid #f5c6cb;
        }
        .pesan-sukses {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 14px;
            border: 1px solid #c3e6cb;
        }
        .pesan-sukses a {
            color: #155724;
            font-weight: bold;
        }
        .link-bawah {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }
        .link-bawah a {
            color: #007bff;
            text-decoration: none;
        }
        .link-bawah a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Pendaftaran Pelanggan</h2>

    <?php if (!empty($error)): ?>
        <div class="pesan-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="pesan-sukses"><?php echo $success; ?></div>
    <?php endif; ?>

    <form action="" method="POST" id="formRegister" onsubmit="return validasiForm()">
        <div class="input-group">
            <label for="nama">Nama Lengkap *</label>
            <input type="text" id="nama" name="nama" required>
        </div>
        <div class="input-group">
            <label for="username">Username *</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="input-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="input-group">
            <label for="password">Password * (Minimal 6 karakter)</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="input-group">
            <label for="telepon">No. Telepon / WA</label>
            <input type="text" id="telepon" name="telepon">
        </div>
        <div class="input-group">
            <label for="alamat">Alamat Lengkap</label>
            <textarea id="alamat" name="alamat" rows="3"></textarea>
        </div>
        
        <button type="submit" class="btn-submit">Daftar Sekarang</button>
    </form>

    <div class="link-bawah">
        Sudah memiliki akun? <a href="login.php">Masuk di sini</a>
    </div>
</div>

<script>
    function validasiForm() {
        // Mengambil nilai dari input password
        var password = document.getElementById("password").value;
        var username = document.getElementById("username").value;

        // Cek panjang password
        if (password.length < 6) {
            alert("Pendaftaran Gagal: Password harus memiliki minimal 6 karakter!");
            return false; // Mencegah form dikirim ke PHP
        }
        
        // Cek agar username tidak mengandung spasi
        if (username.indexOf(' ') >= 0) {
            alert("Pendaftaran Gagal: Username tidak boleh menggunakan spasi!");
            return false;
        }

        // Jika semua lolos, izinkan form dikirim
        return true;
    }
</script>

</body>
</html>
