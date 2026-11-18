<?php
require __DIR__ . '/../app/bootstrap.php'; require __DIR__ . '/partials.php'; admin_require();
$sp = Pengaduan::stat(); $sl = Peminjaman::stat();
admin_open('Dashboard Admin Sarpras', [
    'stat' => ['pengaduan' => $sp['total'], 'baru' => $sp['baru'], 'pinjam' => $sl['total'], 'jalan' => $sl['jalan'], 'denda' => $sl['denda'], 'akun' => Mahasiswa::count()],
    'pengaduan' => array_slice(Pengaduan::semua(), 0, 2), 'peminjaman' => array_slice(Peminjaman::semua(), 0, 4),
]); ?>
<script>
shell({ active: 'dashboard', title: 'Dashboard Admin Sarpras' });
const S = DATA.stat;
const ST = { menunggu: ['yl', 'Menunggu Konfirmasi'], disetujui: ['gr', 'Disetujui'], serah: ['bl', 'Serah Terima (Dipinjam)'], selesai: ['gr', 'Selesai (Dikembalikan)'], ditolak: ['rd', 'Ditolak'] };
const CLP = { Menunggu: 'yl', Diproses: 'or', Selesai: 'gr' };
const short = t => t.length > 60 ? t.slice(0, 58) + '...' : t;
$('#content').innerHTML = `<div class="stats">
<div class="stat" style="border-color:#ff7b00"><div class="ic" style="background:#fff4ed">📢</div><div><b>${S.pengaduan}</b><span>Total Pengaduan (${S.baru} Baru)</span></div></div>
<div class="stat" style="border-color:#3b82f6"><div class="ic" style="background:#eff6ff">📦</div><div><b>${S.pinjam}</b><span>Peminjaman (${S.jalan} Berjalan)</span></div></div>
<div class="stat" style="border-color:#ef4444"><div class="ic" style="background:#fef2f2">💵</div><div><b>${rpd(S.denda)}</b><span>Total Denda Terkumpul</span></div></div>
<div class="stat" style="border-color:#10b981"><div class="ic" style="background:#ecfdf5">👥</div><div><b>${S.akun}</b><span>Akun Mahasiswa Terdaftar</span></div></div></div>
<div class="two"><section class="card"><div class="chead"><h2>📢 Laporan Pengaduan Fasilitas Terkini</h2><a class="b" href="pengaduan.php">Kelola Semua →</a></div>
<div class="tr"><table style="min-width:520px"><thead><tr><th>Mahasiswa</th><th>Fasilitas Kerusakan</th><th>Status</th><th>Aksi</th></tr></thead><tbody>${DATA.pengaduan.length ? DATA.pengaduan.map(r => `<tr><td><div class="stack"><span class="tm">${r.n}</span><span class="ts">NIM: ${r.nim}</span></div></td><td><div class="stack"><span class="td">${short(r.d)}</span><span class="ts">${fd(r.tgl)}</span></div></td><td><span class="st ${CLP[r.s]}">${r.s}</span></td><td><a class="b" href="pengaduan.php">Proses</a></td></tr>`).join('') : '<tr><td colspan="4" class="empty">Belum ada pengaduan.</td></tr>'}</tbody></table></div></section>
<section class="card"><div class="chead"><h2>📦 Pengajuan Peminjaman Alat Terkini</h2><a class="b" href="peminjaman.php">Kelola Semua →</a></div>
<div class="tr"><table style="min-width:560px"><thead><tr><th>Mahasiswa</th><th>Nama Alat</th><th>Status</th><th>Aksi</th></tr></thead><tbody>${DATA.peminjaman.length ? DATA.peminjaman.map(r => `<tr><td><div class="stack"><span class="tm">${r.n}</span><span class="ts">NIM: ${r.nim}</span></div></td><td><div class="stack"><span class="tm">${r.a}<span class="chip">Qty: ${r.q} unit</span></span><span class="ts">Tempo: ${fd(r.t)}</span></div></td><td><span class="st ${ST[r.s][0]}">${ST[r.s][1]}</span></td><td><a class="b" href="peminjaman.php">Kelola</a></td></tr>`).join('') : '<tr><td colspan="4" class="empty">Belum ada peminjaman.</td></tr>'}</tbody></table></div></section></div>`;
</script></body></html>
