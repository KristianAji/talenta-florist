<?php
// ============================================================
// config/db.php – Koneksi ke MySQL
// Ganti nilai di bawah sesuai server hosting Anda
// ============================================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'talenta_florist');
define('DB_USER', 'root');       // ganti di hosting
define('DB_PASS', '');           // ganti di hosting
define('DB_CHAR', 'utf8mb4');

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHAR;
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            // Tampilkan pesan umum agar kredensial tidak bocor
            die('<p style="font-family:sans-serif;color:red;padding:2rem">Koneksi database gagal. Periksa konfigurasi di config/db.php</p>');
        }
    }
    return $pdo;
}