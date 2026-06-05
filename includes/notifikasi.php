<?php
// ============================================================
// includes/notifikasi.php
// Helper fungsi notifikasi — include di halaman yang perlu kirim notif
// ============================================================

/**
 * Kirim notifikasi ke admin atau pelanggan tertentu.
 *
 * @param string      $penerima    'admin' | 'pelanggan'
 * @param string      $pesan       Teks notifikasi
 * @param string|null $link        URL tujuan saat notif diklik (relatif, contoh: 'admin/pesanan.php')
 * @param int|null    $pelanggan_id Wajib diisi jika penerima = 'pelanggan'
 */
function kirim_notif(string $penerima, string $pesan, ?string $link = null, ?int $pelanggan_id = null): void
{
    try {
        $stmt = db()->prepare("
            INSERT INTO notifikasi (penerima, pelanggan_id, pesan, link)
            VALUES (:penerima, :pelanggan_id, :pesan, :link)
        ");
        $stmt->execute([
            ':penerima'    => $penerima,
            ':pelanggan_id'=> $pelanggan_id,
            ':pesan'       => $pesan,
            ':link'        => $link,
        ]);
    } catch (Exception $e) {
        // Jangan crash halaman hanya karena notif gagal
        error_log('[notifikasi] Gagal kirim: ' . $e->getMessage());
    }
}

/**
 * Ambil jumlah notifikasi belum dibaca.
 *
 * @param string   $penerima    'admin' | 'pelanggan'
 * @param int|null $pelanggan_id
 */
function hitung_notif_belum_dibaca(string $penerima, ?int $pelanggan_id = null): int
{
    try {
        if ($penerima === 'admin') {
            return (int) db()
                ->query("SELECT COUNT(*) FROM notifikasi WHERE penerima='admin' AND dibaca=0")
                ->fetchColumn();
        }
        $stmt = db()->prepare("
            SELECT COUNT(*) FROM notifikasi
            WHERE penerima='pelanggan' AND pelanggan_id=? AND dibaca=0
        ");
        $stmt->execute([$pelanggan_id]);
        return (int) $stmt->fetchColumn();
    } catch (Exception $e) {
        error_log('[notifikasi] Hitung gagal: ' . $e->getMessage());
        return 0;
    }
}

/**
 * Tandai semua notifikasi sebagai sudah dibaca.
 *
 * @param string   $penerima
 * @param int|null $pelanggan_id
 */
function tandai_semua_dibaca(string $penerima, ?int $pelanggan_id = null): void
{
    try {
        if ($penerima === 'admin') {
            db()->exec("UPDATE notifikasi SET dibaca=1 WHERE penerima='admin'");
        } else {
            $stmt = db()->prepare("
                UPDATE notifikasi SET dibaca=1
                WHERE penerima='pelanggan' AND pelanggan_id=?
            ");
            $stmt->execute([$pelanggan_id]);
        }
    } catch (Exception $e) {
        error_log('[notifikasi] Tandai dibaca gagal: ' . $e->getMessage());
    }
}

/**
 * (Opsional) Kirim notifikasi WhatsApp via Fonnte.
 * Aktifkan dengan mengisi FONNTE_TOKEN di config atau .env.
 *
 * @param string $nomor  Nomor tujuan, contoh: '0812xxxxxxxx'
 * @param string $pesan
 */
function kirim_wa(string $nomor, string $pesan): void
{
    $token = defined('FONNTE_TOKEN') ? FONNTE_TOKEN : '';
    if (!$token) return; // Lewati jika token belum diset

    // Normalisasi nomor: ganti awalan 0 dengan 62
    $nomor = preg_replace('/^0/', '62', $nomor);

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL            => 'https://api.fonnte.com/send',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => ['target' => $nomor, 'message' => $pesan],
        CURLOPT_HTTPHEADER     => ['Authorization: ' . $token],
        CURLOPT_TIMEOUT        => 10,
    ]);
    $response = curl_exec($curl);
    if (curl_errno($curl)) {
        error_log('[WA] Gagal kirim: ' . curl_error($curl));
    }
}