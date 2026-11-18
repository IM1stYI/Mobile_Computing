<?php
declare(strict_types=1);
/** Jalankan SEKALI di browser: http://localhost:8000/setup.php  -> membuat database, tabel, dan data contoh. */
$cfgFile = __DIR__ . '/app/config.php';
if (!is_file($cfgFile)) copy(__DIR__ . '/app/config.example.php', $cfgFile);
$c = require $cfgFile;
$ok = false; $msg = '';
try {
    $pdo = new PDO("mysql:host={$c['db_host']};port={$c['db_port']};charset=utf8mb4", $c['db_user'], $c['db_pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $pdo->exec(file_get_contents(__DIR__ . '/database/schema.sql'));
    $ok = true; $msg = 'Database <b>meta_sarpras</b> beserta tabel dan data contoh siap dipakai.';
} catch (Throwable $e) {
    $msg = 'Gagal: ' . htmlspecialchars($e->getMessage()) . '<br><br>Pastikan <b>MySQL</b> di XAMPP sudah Start dan user/password di <code>app/config.php</code> benar.';
}
?><!DOCTYPE html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Setup Meta Sarpras</title>
<style>body{font-family:system-ui,sans-serif;background:#0b1f3a;color:#12233f;display:grid;place-items:center;min-height:100vh;margin:0;padding:20px}
.c{background:#fff;border-radius:16px;padding:28px;max-width:520px;width:100%}h1{font-size:20px;margin:0 0 12px}a{color:#c25a00;font-weight:600}
.ok{color:#12805c}.er{color:#d93b3b}code{background:#f1f3f7;padding:2px 6px;border-radius:4px}table{border-collapse:collapse;margin:14px 0;width:100%}td,th{border:1px solid #e3e8ef;padding:8px;font-size:13px;text-align:left}</style></head><body><div class="c">
<h1 class="<?= $ok ? 'ok' : 'er' ?>"><?= $ok ? '✅ Setup berhasil' : '❌ Setup gagal' ?></h1><p><?= $msg ?></p>
<?php if ($ok): ?>
<table><tr><th>Peran</th><th>Login</th><th>Password</th></tr>
<tr><td>Admin</td><td>zakiajja</td><td>zaki123</td></tr>
<tr><td>Mahasiswa</td><td>NIM 2425010</td><td>mahasiswa</td></tr></table>
<p><a href="Meta_Sarpras_PWA/splash.php">→ Buka aplikasi mahasiswa (PWA)</a><br><a href="Meta_Sarpras_Admin/login.php">→ Buka panel admin</a></p>
<p style="font-size:12px;color:#7b8798">Setup aman diulang: data yang sudah ada tidak ditimpa.</p>
<?php endif; ?></div></body></html>
