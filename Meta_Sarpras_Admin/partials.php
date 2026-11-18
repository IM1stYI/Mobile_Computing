<?php
/** Kerangka halaman admin (View). APP = konfigurasi, DATA = data dari database. */
function admin_open(string $title, array $data = [], string $head = ''): void {
    $adm = admin_user();
    $menunggu = (int) db()->query("SELECT COUNT(*) FROM peminjaman WHERE status_peminjaman='Menunggu Konfirmasi'")->fetchColumn();
    $app = [
        'csrf' => csrf_token(), 'today' => date('Y-m-d'), 'date' => tgl(date('Y-m-d')),
        'admin' => ['nama' => h($adm['nama_admin']), 'username' => h($adm['username'])],
        'badges' => ['pengaduan' => Pengaduan::stat()['baru'] ?: null, 'peminjaman' => $menunggu ?: null, 'pengembalian' => Peminjaman::belumKembali() ?: null],
    ];
?><!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="icon" type="image/png" href="logo.png"><link rel="stylesheet" href="admin.css">
<title><?= h($title) ?> - Admin Meta Sarpras</title>
<?= $head ?>
</head>
<body>
<script>const APP = <?= json_safe($app) ?>; const DATA = <?= json_safe(h_deep($data)) ?>;</script>
<script src="admin.js"></script>
<?php }
