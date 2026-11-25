<?php
require __DIR__ . '/../app/bootstrap.php'; require __DIR__ . '/partials.php'; admin_require();
ob_start(); ?>
<style>
.pgrid{display:grid;grid-template-columns:320px 1fr;gap:24px;align-items:start}
.av-wrap{position:relative;width:104px;height:104px;margin:0 auto 14px}
.av-btn{width:104px;height:104px;border-radius:50%;border:0;background:none;padding:0;position:relative;display:block}
.av-btn:hover .av-cam{transform:scale(1.08);background:#ea6d00}
.big{width:104px;height:104px;border-radius:50%;background:#1e3a8a;border:3px solid #ef4444;display:grid;place-items:center;font-size:44px;overflow:hidden}
.big img{width:100%;height:100%;object-fit:cover;display:block}
.av-cam{position:absolute;right:2px;bottom:2px;width:30px;height:30px;border-radius:50%;background:#ff7b00;color:#fff;border:2px solid #fff;display:grid;place-items:center;font-size:14px;box-shadow:0 2px 8px rgba(0,0,0,.18);transition:.15s}
.av-acts{display:flex;gap:8px;justify-content:center;flex-wrap:wrap;margin:12px 0 6px}
.av-acts .b{padding:8px 13px;font-size:12px;border-radius:8px}
.av-acts [hidden]{display:none}
.av-hint{font-size:11px;color:#6b7280;margin-bottom:16px}
.pc{text-align:center}.pc h2{font-size:18px;color:#112a4f;margin-bottom:8px}
.il{text-align:left;display:flex;flex-direction:column;gap:14px;margin-top:6px;padding-top:16px;border-top:1px solid #eef0f3}.il small{display:block;font-size:11px;color:#6b7280;margin-bottom:2px}.il b{font-size:13px;color:#112a4f;word-break:break-word}
.pf label{display:block;font-size:13px;font-weight:700;color:#112a4f;margin:16px 0 8px}.pf label i{color:#ef4444;font-style:normal}
.pf input{width:100%;padding:12px 16px;border:1px solid #d1d5db;border-radius:8px;font:inherit;font-size:13px;outline:0;background:#fff}
.pf input:focus{border-color:#1e3a8a;box-shadow:0 0 0 3px #1e3a8a1a}.pf input.err{border-color:#ef4444}.pf input[readonly]{background:#f3f4f6;color:#6b7280}
.pf h2{font-size:16px;color:#112a4f}.pf>div>p{font-size:12px;color:#6b7280;margin-top:4px}
.pf .row2{display:grid;grid-template-columns:1fr 1fr;gap:0 20px}.pf .ft{display:flex;gap:10px;justify-content:flex-end;margin-top:22px;flex-wrap:wrap}
.pf .ft .b{padding:11px 22px;font-size:13px;border-radius:8px}.chk{display:flex;align-items:center;gap:8px;font-size:12px;color:#4b5563;margin-top:14px}.chk input{width:auto}
.crop-wrap{display:flex;flex-direction:column;align-items:center;gap:14px}
.crop-stage{position:relative;width:260px;height:260px;border-radius:14px;overflow:hidden;background:#0b1a30;cursor:grab;touch-action:none;user-select:none}
.crop-stage:active{cursor:grabbing}
.crop-stage canvas{width:260px;height:260px;display:block}
.crop-mask{position:absolute;inset:0;pointer-events:none;border-radius:50%;box-shadow:0 0 0 999px rgba(11,26,48,.58);border:2px solid rgba(255,255,255,.85)}
.crop-zoom{display:flex;align-items:center;gap:10px;width:100%;max-width:280px;font-size:12px;color:#4b5563}
.crop-zoom input{flex:1;padding:0;border:0;accent-color:#1e3a8a}
@media(max-width:1000px){.pgrid{grid-template-columns:1fr}}@media(max-width:560px){.pf .row2{grid-template-columns:1fr}}
</style>
<?php $css = ob_get_clean(); admin_open('Profil Akun', [], $css); ?>

<script>
shell({ active: 'profil', title: 'Profil Akun' });
const P = APP.admin;
$('#content').innerHTML = `<div class="pgrid">
<section class="card pc">
<div class="av-wrap"><button type="button" class="av-btn" id="btnPickFoto" title="Ganti foto profil" aria-label="Ganti foto profil"><div class="big" id="bigAv">${avatarHtml(P.foto_url, P.nama)}</div><span class="av-cam" aria-hidden="true">📷</span></button></div>
<h2 id="pn">${P.nama}</h2><span class="role">@ADMIN</span>
<div class="av-acts"><button type="button" class="b p" id="btnGantiFoto">📷 Ganti Foto</button><button type="button" class="b r" id="btnHapusFoto" ${P.foto_url ? '' : 'hidden'}>🗑️ Hapus</button></div>
<p class="av-hint">Format JPG, PNG, atau WEBP • Maks. 5 MB</p>
<input type="file" id="inpFoto" accept="image/jpeg,image/png,image/webp" hidden>
<div class="il"><div><small>Username</small><b>${P.username}</b></div><div><small>Hak akses</small><b>Administrator Sarpras (penuh)</b></div></div></section>
<div style="display:flex;flex-direction:column;gap:24px">
<form class="card pf" id="fp" novalidate><div><h2>👤 Informasi Profil</h2><p>Perbarui nama petugas administrator. Username tidak dapat diubah.</p></div>
<div class="row2"><div><label for="n">Nama Lengkap <i>*</i></label><input id="n" value="${P.nama}" maxlength="100"></div><div><label for="u">Username</label><input id="u" value="${P.username}" readonly></div></div>
<div class="ft"><button class="b p" type="submit">💾 Simpan Perubahan</button></div></form>
<form class="card pf" id="fw" novalidate><div><h2> Ubah Password</h2><p>Gunakan minimal 6 karakter dan jangan bagikan password kepada siapa pun.</p></div>
<label for="w0">Password Saat Ini <i>*</i></label><input id="w0" type="password" autocomplete="current-password">
<div class="row2"><div><label for="w1">Password Baru <i>*</i></label><input id="w1" type="password" autocomplete="new-password"></div><div><label for="w2">Konfirmasi Password Baru <i>*</i></label><input id="w2" type="password" autocomplete="new-password"></div></div>
<label class="chk"><input type="checkbox" id="sh"> Tampilkan password</label><div class="ft"><button class="b bl" type="submit">🔑 Ubah Password</button></div></form></div></div>`;

function syncAvatar(url) {
  P.foto_url = url || null;
  const html = avatarHtml(P.foto_url, P.nama);
  $('#bigAv').innerHTML = html;
  const hdr = $('#hdrAvatar'); if (hdr) hdr.innerHTML = html;
  $('#btnHapusFoto').hidden = !P.foto_url;
}

async function uploadFotoBlob(blob, filename = 'profil.jpg') {
  const fd = new FormData();
  fd.append('foto', blob, filename);
  const j = await api('profil.foto', fd);
  if (!j) return false;
  syncAvatar(j.foto_url);
  toast(j.msg || 'Foto profil berhasil diperbarui');
  return true;
}

function loadImg(file) {
  if (window.createImageBitmap) {
    return createImageBitmap(file, { imageOrientation: 'from-image' }).catch(() => createImageBitmap(file));
  }
  return new Promise((res, rej) => {
    const fr = new FileReader();
    fr.onload = ev => {
      const im = new Image();
      im.onload = () => res(im);
      im.onerror = () => rej(new Error('File gambar tidak dapat dibaca.'));
      im.src = ev.target.result;
    };
    fr.onerror = () => rej(new Error('Gagal membaca file gambar.'));
    fr.readAsDataURL(file);
  });
}

function openCropModal(img) {
  const SIZE = 260, OUT = 600;
  const st = { w: img.width || img.naturalWidth, h: img.height || img.naturalHeight, zoom: 1, ox: 0, oy: 0, drag: false, lx: 0, ly: 0 };
  const clamp = () => {
    const s = Math.max(SIZE / st.w, SIZE / st.h) * st.zoom;
    const mx = Math.max(0, (st.w * s - SIZE) / 2), my = Math.max(0, (st.h * s - SIZE) / 2);
    st.ox = Math.max(-mx, Math.min(mx, st.ox)); st.oy = Math.max(-my, Math.min(my, st.oy));
  };
  const draw = (ctx, target) => {
    const r = target / SIZE, s = Math.max(SIZE / st.w, SIZE / st.h) * st.zoom;
    const dw = st.w * s * r, dh = st.h * s * r;
    const dx = (SIZE / 2 + st.ox) * r - dw / 2, dy = (SIZE / 2 + st.oy) * r - dh / 2;
    ctx.fillStyle = '#fff'; ctx.fillRect(0, 0, target, target);
    ctx.drawImage(img, dx, dy, dw, dh);
  };

  openM({
    icon: '🖼️', title: 'Atur Foto Profil', w: 460, ok: '💾 Simpan Foto', cls: 'p',
    body: `<p style="font-size:12px;color:#6b7280;margin-bottom:14px">Geser dan atur perbesaran agar posisi foto pas di dalam lingkaran.</p>
      <div class="crop-wrap">
        <div class="crop-stage" id="cropStage"><canvas id="cropCanvas" width="260" height="260"></canvas><div class="crop-mask"></div></div>
        <label class="crop-zoom"><span>Perkecil</span><input type="range" id="cropZoom" min="1" max="3" step="0.02" value="1"><span>Perbesar</span></label>
      </div>`,
    async onOk() {
      const out = document.createElement('canvas'); out.width = out.height = OUT;
      draw(out.getContext('2d'), OUT);
      const blob = await new Promise(res => out.toBlob(res, 'image/jpeg', 0.88));
      if (!blob) { toast('Gagal memproses gambar'); return false; }
      const ok = await uploadFotoBlob(blob, 'profil.jpg');
      if (ok && typeof img.close === 'function') img.close();
      return ok;
    }
  });

  const stage = $('#cropStage'), ctx = $('#cropCanvas').getContext('2d'), zoom = $('#cropZoom');
  clamp(); draw(ctx, SIZE);
  stage.onpointerdown = e => { st.drag = true; st.lx = e.clientX; st.ly = e.clientY; stage.setPointerCapture?.(e.pointerId); };
  stage.onpointermove = e => {
    if (!st.drag) return;
    st.ox += e.clientX - st.lx; st.oy += e.clientY - st.ly; st.lx = e.clientX; st.ly = e.clientY;
    clamp(); draw(ctx, SIZE);
  };
  stage.onpointerup = stage.onpointercancel = () => { st.drag = false; };
  zoom.oninput = () => { st.zoom = parseFloat(zoom.value) || 1; clamp(); draw(ctx, SIZE); };
}

const pickFoto = () => { const inp = $('#inpFoto'); inp.value = ''; inp.click(); };
$('#btnPickFoto').onclick = $('#btnGantiFoto').onclick = pickFoto;
$('#inpFoto').onchange = async e => {
  const file = e.target.files?.[0];
  if (!file) return;
  if (!/^image\/(jpeg|png|webp)$/i.test(file.type) && !file.type.startsWith('image/')) {
    toast('Format foto harus JPG, PNG, atau WEBP'); return;
  }
  try {
    const img = await loadImg(file);
    openCropModal(img);
  } catch {
    if (file.size <= 5 * 1024 * 1024) await uploadFotoBlob(file, file.name || 'profil.jpg');
    else toast('Ukuran foto maksimal 5 MB');
  }
};

$('#btnHapusFoto').onclick = () => openM({
  icon: '🗑️', title: 'Hapus Foto Profil', w: 420, ok: 'Ya, Hapus Foto', cls: 'r',
  body: '<p style="font-size:13px;color:#4b5563;line-height:1.6">Hapus foto profil saat ini dan kembali menggunakan ikon bawaan?</p>',
  async onOk() {
    const fd = new FormData(); fd.append('hapus', '1');
    const j = await api('profil.foto', fd); if (!j) return false;
    syncAvatar(null); toast(j.msg || 'Foto profil berhasil dihapus');
  }
});

$('#sh').onchange = e => ['w0', 'w1', 'w2'].forEach(i => $('#' + i).type = e.target.checked ? 'text' : 'password');
$('#fp').onsubmit = async e => {
  e.preventDefault(); const n = $('#n').value.trim(); $('#n').classList.toggle('err', !n); if (!n) { toast('Nama lengkap wajib diisi'); return }
  const j = await api('profil.nama', { nama: n }); if (!j) return;
  P.nama = j.nama; $('#pn').innerHTML = j.nama;
  const un = $('.user-name'); if (un) un.innerHTML = j.nama;
  toast('Profil berhasil diperbarui');
};
$('#fw').onsubmit = async e => {
  e.preventDefault(); const a = $('#w0'), b = $('#w1'), c = $('#w2');[a, b, c].forEach(x => x.classList.remove('err'));
  const er = (x, m) => { x.classList.add('err'); toast(m) };
  if (!a.value) return er(a, 'Password saat ini wajib diisi');
  if (b.value.length < 6) return er(b, 'Password baru minimal 6 karakter');
  if (c.value !== b.value) return er(c, 'Konfirmasi password tidak cocok');
  const j = await api('profil.password', { lama: a.value, baru: b.value }); if (!j) return er(a, 'Periksa kembali password Anda');
  a.value = b.value = c.value = ''; toast('Password berhasil diubah');
};
</script></body></html>
