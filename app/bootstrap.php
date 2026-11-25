<?php
declare(strict_types=1);
/**
 * Bootstrap bersama (dipakai PWA mahasiswa & Admin).
 * - session, koneksi PDO, helper, autentikasi, upload foto
 * - memuat Model (folder app/models)
 */
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax', 'path' => '/']);
    session_start();
}
date_default_timezone_set('Asia/Jakarta');
define('UPLOAD_DIR', dirname(__DIR__) . '/uploads/');

const STATUS_PENGADUAN = ['Menunggu', 'Sedang diproses', 'Selesai'];
// kode (dipakai tampilan admin) => nilai di database
const STATUS_PINJAM = [
    'menunggu'  => 'Menunggu Konfirmasi',
    'disetujui' => 'Disetujui',
    'serah'     => 'Dipinjam',
    'selesai'   => 'Telah Dikembalikan',
    'ditolak'   => 'Ditolak',
];

function cfg(): array {
    static $c;
    if ($c === null) {
        $f = __DIR__ . '/config.php';
        if (!is_file($f)) { http_response_code(500); exit('config.php belum ada. Salin app/config.example.php menjadi app/config.php.'); }
        $c = require $f;
    }
    return $c;
}

function db(): PDO {
    static $pdo;
    if ($pdo === null) {
        $c = cfg();
        try {
            $pdo = new PDO(
                "mysql:host={$c['db_host']};port={$c['db_port']};dbname={$c['db_name']};charset=utf8mb4",
                $c['db_user'], $c['db_pass'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false]
            );
        } catch (PDOException $e) {
            http_response_code(500);
            exit('<p style="font-family:sans-serif">Gagal terhubung ke database. Pastikan MySQL menyala lalu buka <a href="/setup.php">/setup.php</a> sekali untuk membuat database.</p>');
        }
    }
    return $pdo;
}

// ---------- helper tampilan ----------
function h($s): string { return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function h_deep($v) {
    if (is_array($v)) return array_map('h_deep', $v);
    return is_string($v) ? h($v) : $v;
}
function json_safe($v): string { return json_encode($v, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_INVALID_UTF8_SUBSTITUTE); }
const BLN = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
function tgl(?string $iso, bool $short = false): string {
    if (!$iso) return '-';
    [$y, $m, $d] = array_map('intval', explode('-', substr($iso, 0, 10)));
    $b = BLN[$m - 1];
    return $d . ' ' . ($short ? substr($b, 0, 3) : $b) . ' ' . $y;
}
function rupiah($n): string { return 'Rp' . number_format((int)$n, 0, ',', '.'); }
function redirect(string $u): never { header('Location: ' . $u); exit; }
function inisial(string $nama): string {
    $p = preg_split('/\s+/', trim($nama));
    return strtoupper(mb_substr($p[0] ?? '', 0, 1) . mb_substr($p[1] ?? '', 0, 1));
}
function foto_url(?string $f): ?string { return $f ? '../uploads/' . rawurlencode($f) : null; }
function selisih_hari(string $a, string $b): int { return (int) round((strtotime($a) - strtotime($b)) / 86400); }

// ---------- CSRF ----------
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
    return $_SESSION['csrf'];
}
function csrf_field(): string { return '<input type="hidden" name="csrf" value="' . h(csrf_token()) . '">'; }
function csrf_ok(?string $t): bool { return is_string($t) && hash_equals($_SESSION['csrf'] ?? '', $t); }

// ---------- autentikasi ----------
function mhs_user(): ?array {
    static $u = false;
    if ($u === false) {
        $id = isset($_SESSION['mhs_id']) ? (int)$_SESSION['mhs_id'] : 0;
        $u = $id ? User::find($id) : null;
        if ($u && $u['role'] !== 'mahasiswa') $u = null;
    }
    return $u;
}
function mhs_require(): array { $u = mhs_user(); if (!$u) redirect('login.php'); return $u; }
function admin_user(): ?array {
    static $u = false;
    if ($u === false) {
        $id = isset($_SESSION['admin_id']) ? (int)$_SESSION['admin_id'] : 0;
        $u = $id ? User::find($id) : null;
        if ($u && $u['role'] !== 'admin') $u = null;
    }
    return $u;
}
function admin_require(): array { $u = admin_user(); if (!$u) redirect('login.php'); return $u; }

// ---------- upload foto ----------
/** @return string|null nama file tersimpan, atau null bila tidak ada file. Melempar RuntimeException bila tidak valid. */
function upload_foto(string $field): ?string {
    if (empty($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) return null;
    $f = $_FILES[$field];
    if ($f['error'] !== UPLOAD_ERR_OK) throw new RuntimeException('Upload foto gagal. Coba lagi.');
    if ($f['size'] > 5 * 1024 * 1024) throw new RuntimeException('Ukuran foto maksimal 5 MB.');
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
    $ext = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'][$mime] ?? null;
    if (!$ext) throw new RuntimeException('Format foto harus JPG, PNG, atau WEBP.');
    if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0775, true);
    $name = date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    if (!move_uploaded_file($f['tmp_name'], UPLOAD_DIR . $name)) throw new RuntimeException('Foto tidak dapat disimpan.');
    return $name;
}

foreach (['User', 'Pengaduan', 'Tanggapan', 'Peminjaman'] as $m) require_once __DIR__ . "/models/$m.php";
