<?php
require __DIR__ . '/../app/bootstrap.php'; require __DIR__ . '/partials.php'; admin_require();
admin_open('Manajemen Pengaduan Fasilitas', ['rows' => Pengaduan::semua()]); ?>
<script>
shell({ active: 'pengaduan', title: 'Manajemen Pengaduan Fasilitas Kampus' });
const D = DATA.rows;
const CL = { Menunggu: 'yl', Diproses: 'or', Selesai: 'gr' }, OPT = { Menunggu: 'Menunggu (Belum Diproses)', Diproses: 'Diproses (Sedang Diperbaiki Teknisi Sarpras)', Selesai: 'Selesai (Perbaikan Tuntas)' };
let f = 'Semua', q = '';
$('#content').innerHTML = `<section class="card"><div class="chead"><div><h2>📢 Pengaduan Fasilitas Kampus (Use Case: Melihat Pengaduan Mahasiswa)</h2><p>Kelola laporan kerusakan sarana, verifikasi bukti foto, ubah status, dan berikan tanggapan resmi.</p></div><div class="fg" id="fg"></div></div>
<input class="search" id="q" placeholder="Cari nama mahasiswa, NIM, atau deskripsi..." aria-label="Cari"><div class="tr"><table style="min-width:1100px"><thead><tr><th>No</th><th>Bukti Foto</th><th>Mahasiswa Pelapor</th><th>Tanggal</th><th>Rincian Kerusakan Fasilitas</th><th>Status Saat Ini</th><th>Petugas</th><th>Aksi</th></tr></thead><tbody id="tb"></tbody></table></div></section>`;
const short = t => t.length > 72 ? t.slice(0, 70) + ' ...' : t;
function render() {
  $('#fg').innerHTML = ['Semua', 'Menunggu', 'Diproses', 'Selesai'].map(k => `<button class="bf ${f == k ? 'on' : ''}" data-k="${k}">${k}</button>`).join('');
  const r = D.map((x, i) => ({ ...x, i })).filter(x => (f == 'Semua' || x.s == f) && (x.n + x.nim + x.d).toLowerCase().includes(q));
  $('#tb').innerHTML = r.length ? r.map((x, k) => `<tr><td>${k + 1}</td><td><div class="ph" style="overflow:hidden">${x.foto ? `<img src="${fotoUrl(x.foto)}" alt="" style="width:100%;height:100%;object-fit:cover">` : '📷'}</div></td><td><div class="stack"><span class="tm">${x.n}</span><span class="ts">NIM: ${x.nim}</span></div></td><td>${fd(x.tgl)}</td><td><div class="td">${short(x.d)}</div></td><td><span class="st ${CL[x.s]}">${x.s}</span></td><td>${x.admin || '-'}</td><td><button class="b p" data-i="${x.i}">⚙️ Tindak Lanjut →</button></td></tr>`).join('') : '<tr><td colspan="8" class="empty">Tidak ada pengaduan yang cocok.</td></tr>';
}
const bukti = x => x.foto ? `<img src="${fotoUrl(x.foto)}" alt="Bukti foto" style="max-width:100%;max-height:100%;object-fit:contain;border-radius:8px">` : `<svg width="190" height="130" viewBox="0 0 190 130"><rect x="20" y="10" width="160" height="78" rx="8" fill="#fff" stroke="#112a4f" stroke-width="2.5"/><circle cx="48" cy="49" r="15" fill="#fff" stroke="#ff7b00" stroke-width="4"/><circle cx="48" cy="49" r="8" fill="#3b82f6"/><path d="M76 43h88M76 55h88" stroke="#cbd5e1" stroke-width="4" stroke-linecap="round"/><text x="95" y="112" font-size="9" font-weight="700" fill="#9ca3af" text-anchor="middle" font-family="Inter,sans-serif">Tanpa foto</text></svg>`;
document.addEventListener('click', e => {
  const k = e.target.closest('.bf'); if (k) { f = k.dataset.k; render(); return }
  const b = e.target.closest('[data-i]'); if (!b) return; const x = D[b.dataset.i];
  openM({
    icon: '⚙️', title: `Tindak Lanjut Pengaduan #${x.id}`, w: 650, ok: 'Simpan Perubahan &amp; Kirim Tanggapan →', body: `
 <div class="ib" style="line-height:2">Pelapor: <b>${x.n}</b> (NIM: ${x.nim})<br>Tanggal: <b>${fd(x.tgl)}</b></div>
 <label class="fl">Deskripsi Fasilitas yang Dilaporkan:</label><div class="box">${x.d}</div>
 <label class="fl">Bukti Foto Kerusakan:</label><div class="bukti" style="width:100%;max-width:340px;height:190px">${bukti(x)}</div><hr class="s">
 <label class="fl" for="ss" style="margin-top:24px">Status Penanganan Pengaduan</label><select id="ss">${Object.keys(OPT).map(s => `<option value="${s}" ${s == x.s ? 'selected' : ''}>${OPT[s]}</option>`).join('')}</select>
 <label class="fl" for="tg">Berikan Tanggapan Resmi ke Mahasiswa</label><textarea id="tg" placeholder="Tuliskan catatan tindak lanjut, misalnya: 'Teknisi pendingin ruangan sedang menuju lokasi lab' atau 'Fasilitas proyektor telah diganti dengan unit baru'..."></textarea>
 ${x.t.length ? `<label class="fl">Riwayat Tanggapan Sebelumnya:</label>${x.t.map(t => `<div class="hist"><b>${t[0]} (${fd(t[1])}):</b>${t[2]}</div>`).join('')}` : ''}`,
    async onOk() {
      const v = $('#tg').value.trim(); if (!v) { $('#tg').classList.add('err'); toast('Tanggapan resmi wajib diisi'); return false }
      const j = await api('pengaduan.tindak', { id: x.id, status: $('#ss').value, tanggapan: v }); if (!j) return false;
      D[b.dataset.i] = j.row; render(); toast('Status diubah menjadi ' + j.row.s + ' &amp; tanggapan terkirim');
    }
  });
});
$('#q').oninput = e => { q = e.target.value.toLowerCase(); render() };
render();
</script></body></html>
