<?php
require __DIR__ . '/../app/bootstrap.php'; require __DIR__ . '/partials.php'; admin_require();
admin_open('Manajemen Peminjaman Alat', ['rows' => Peminjaman::semua()]); ?>
<script>
shell({ active: 'peminjaman', title: 'Manajemen Peminjaman Alat' });
const D = DATA.rows;
const ST = { menunggu: ['yl', 'Menunggu Konfirmasi'], disetujui: ['gr', 'Disetujui'], serah: ['bl', 'Serah Terima (Dipinjam)'], selesai: ['gr', 'Selesai (Dikembalikan)'], ditolak: ['rd', 'Ditolak'] };
const FL = [['Semua', ''], ['Menunggu', 'menunggu'], ['Disetujui', 'disetujui'], ['Serah Terima', 'serah'], ['Selesai', 'selesai'], ['Ditolak', 'ditolak']];
let f = '', q = '';
$('#content').innerHTML = `<section class="card"><div class="chead"><div><h2>📦 Manajemen Pengajuan Peminjaman Alat</h2><p>Verifikasi permohonan peminjaman alat, konfirmasi ketersediaan, tolak, dan lakukan serah terima fisik barang.</p></div><div class="fg" id="fg"></div></div>
<input class="search" id="q" placeholder="Cari peminjam, NIM, nama alat..." aria-label="Cari"><div class="tr"><table style="min-width:1150px"><thead><tr><th>No</th><th>Foto Fisik</th><th>Mahasiswa Peminjam</th><th>Nama Alat Praktikum</th><th>Qty</th><th>Tgl Pinjam &amp; Tempo</th><th>Status</th><th>Denda</th><th>Aksi Verifikasi</th></tr></thead><tbody id="tb"></tbody></table></div></section>`;
function acts(x, i) {
  const d = `<button class="b" data-a="detail" data-i="${i}">Detail</button>`, w = `<button class="b w" data-a="detail" data-i="${i}">Detail</button>`;
  if (x.s == 'menunggu') return `<button class="b g w" data-a="konfirmasi" data-i="${i}">✅ Konfirmasi</button><button class="b r" data-a="tolak" data-i="${i}">❌ Tolak</button>${d}`;
  if (x.s == 'disetujui') return `<button class="b p w" data-a="serah" data-i="${i}">🤝 Serah Terima</button>${w}`;
  if (x.s == 'serah') return `<button class="b p w" data-a="kembali" data-i="${i}">🔄 Proses Pengembalian</button>${w}`;
  if (x.s == 'selesai') return `<button class="b w" data-a="denda" data-i="${i}">✏️ Edit Denda</button>${w}`;
  return w;
}
const thumb = x => x.foto ? `<img src="${fotoUrl(x.foto)}" alt="" style="width:100%;height:100%;object-fit:cover">` : '📦';
function render() {
  $('#fg').innerHTML = FL.map(([l, k]) => `<button class="bf ${f == k ? 'on' : ''}" data-f="${k}">${l}</button>`).join('');
  const r = D.map((x, i) => ({ ...x, i })).filter(x => (!f || x.s == f) && (x.n + x.nim + x.a).toLowerCase().includes(q));
  $('#tb').innerHTML = r.length ? r.map((x, k) => `<tr><td>${k + 1}</td><td><div class="ph" style="overflow:hidden">${thumb(x)}</div></td><td><div class="stack"><span class="tm">${x.n}</span><span class="ts">NIM: ${x.nim}</span></div></td><td><div class="stack"><span class="tm">${x.a}</span><span class="ts">Ket: ${x.k || '-'}</span></div></td><td><div class="qty">${x.q}<br>unit</div></td><td><div class="stack"><span class="ts">Pinjam: ${fd(x.p)}</span><b style="color:#112a4f">Tempo: ${fd(x.t)}</b></div></td><td><span class="st ${ST[x.s][0]}">${ST[x.s][1]}</span></td><td>${x.den ? `<span class="den">${rp(x.den)}</span>` : '-'}</td><td><div class="acts">${acts(x, x.i)}</div></td></tr>`).join('') : '<tr><td colspan="9" class="empty">Tidak ada pengajuan yang cocok.</td></tr>';
}
async function ubah(i, to, extra = {}, pesan) {
  const j = await api('peminjaman.status', { id: D[i].id, to, ...extra }); if (!j) return false;
  D[i] = j.row; render(); toast(pesan);
}
document.addEventListener('click', e => {
  const fb = e.target.closest('[data-f]'); if (fb) { f = fb.dataset.f; render(); return }
  const b = e.target.closest('[data-a]'); if (!b) return; const i = +b.dataset.i, x = D[i], a = b.dataset.a;
  if (a == 'konfirmasi') ubah(i, 'disetujui', {}, 'Peminjaman ' + x.a + ' disetujui');
  else if (a == 'serah') ubah(i, 'serah', {}, 'Serah terima ' + x.a + ' dicatat');
  else if (a == 'tolak') openM({ icon: '❌', title: 'Tolak Pengajuan #' + x.id, w: 520, ok: 'Tolak Pengajuan', cls: 'r', body: `<p style="font-size:13px;color:#4b5563">Tolak peminjaman <b>${x.a}</b> oleh ${x.n}?</p><label class="fl" for="al">Alasan penolakan <i>*</i></label><textarea id="al"></textarea>`, onOk() { const v = $('#al').value.trim(); if (!v) { $('#al').classList.add('err'); toast('Alasan wajib diisi'); return false } return ubah(i, 'ditolak', { alasan: v }, 'Pengajuan ditolak') } });
  else if (a == 'kembali') returnModal(x, { icon: '🔍', title: 'Cek Kondisi & Konfirmasi Pengembalian', ok: '✅ Konfirmasi Pengembalian Selesai →', save: async p => { const j = await api('peminjaman.kembali', p); if (!j) return false; D[i] = j.row; render(); toast('Pengembalian dikonfirmasi') } });
  else if (a == 'denda') returnModal(x, { icon: '✏️', title: 'Edit Denda & Pengembalian', ok: '💾 Simpan Perubahan Denda', save: async p => { const j = await api('peminjaman.kembali', p); if (!j) return false; D[i] = j.row; render(); toast('Denda diperbarui') } });
  else {
    const late = x.ret ? dd(x.ret, x.t) : 0;
    openM({
      icon: '📄', title: 'Detail Peminjaman #' + x.id, w: 600, body: `<div class="g2"><div><div class="dl" style="margin-top:0">Mahasiswa:</div><div class="dv">${x.n} (${x.nim})</div></div><div><div class="dl" style="margin-top:0">Status:</div><span class="st ${ST[x.s][0]}" style="margin-top:2px">${ST[x.s][1]}</span></div><div><div class="dl">Tgl Pinjam:</div><div class="dv">${fd(x.p)}</div></div><div><div class="dl">Batas Tempo Kembali:</div><div class="dv">${fd(x.t)}</div></div></div>
 <div class="dl" style="margin-top:20px">Keperluan Penggunaan:</div><div class="dv" style="font-weight:600">${x.k || '-'}</div>
 <div class="dl" style="margin-top:20px">Foto Fisik Kondisi Barang:</div><div class="fotob" style="overflow:hidden">${x.foto ? `<img src="${fotoUrl(x.foto)}" alt="Foto fisik" style="width:100%;height:100%;object-fit:cover">` : '📦'}</div>
 ${x.den ? `<div class="dnd"><b>Denda Keterlambatan Tercatat</b><strong>${rpd(x.den)}</strong>${late >= 3 ? `Keterlambatan ${late} hari (≥ 3 hari dikenakan denda ${rpd(x.den)})` : (x.ket || '')}</div>` : ''}`
    });
  }
});
$('#q').oninput = e => { q = e.target.value.toLowerCase(); render() };
render();
</script></body></html>
