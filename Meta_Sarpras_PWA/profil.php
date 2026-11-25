<?php
require __DIR__ . '/../app/bootstrap.php';
require __DIR__ . '/partials/layout.php';

$isAjax = (
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest'
    || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')
);

$jsonOut = static function (array $payload, int $status = 200): never {
    http_response_code($status);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_safe($payload);
    exit;
};

if ($isAjax) {
    $u = mhs_user();
    if (!$u) {
        $jsonOut(['ok' => false, 'msg' => 'Sesi berakhir. Silakan login ulang.', 'redirect' => 'login.php'], 401);
    }
} else {
    $u = mhs_require();
}

$id = (int)$u['id_mahasiswa'];
$err = '';
$sukses = '';
$fotoErr = '';
$fotoSukses = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = (string)($_POST['aksi'] ?? 'password');
    if ($aksi === 'foto') {
        try {
            if (!csrf_ok($_POST['csrf'] ?? null)) {
                throw new RuntimeException('Sesi kedaluwarsa. Muat ulang halaman lalu coba lagi.');
            }
            $fotoLama = $u['foto'] ?? null;
            if (!empty($_POST['hapus_foto'])) {
                User::updateFoto($id, null);
                if ($fotoLama && is_file(UPLOAD_DIR . $fotoLama)) {
                    @unlink(UPLOAD_DIR . $fotoLama);
                }
                $u = Mahasiswa::find($id);
                $fotoSukses = 'Foto profil berhasil dihapus.';
            } else {
                $fotoBaru = upload_foto('foto');
                if (!$fotoBaru) {
                    throw new RuntimeException('Pilih atau ambil foto terlebih dahulu.');
                }
                try {
                    User::updateFoto($id, $fotoBaru);
                } catch (Throwable $t) {
                    if (is_file(UPLOAD_DIR . $fotoBaru)) {
                        @unlink(UPLOAD_DIR . $fotoBaru);
                    }
                    throw $t;
                }
                if ($fotoLama && $fotoLama !== $fotoBaru && is_file(UPLOAD_DIR . $fotoLama)) {
                    @unlink(UPLOAD_DIR . $fotoLama);
                }
                $u = Mahasiswa::find($id);
                $fotoSukses = 'Foto profil berhasil diperbarui!';
            }
            if ($isAjax) {
                $hasFotoNow = !empty($u['foto']) && is_file(UPLOAD_DIR . $u['foto']);
                $jsonOut([
                    'ok' => true,
                    'msg' => $fotoSukses,
                    'has_foto' => $hasFotoNow,
                    'foto_url' => $hasFotoNow ? foto_url($u['foto']) : null,
                    'initials' => inisial($u['nama_mahasiswa']),
                    'nama' => $u['nama_mahasiswa'],
                ]);
            }
        } catch (Throwable $e) {
            $fotoErr = $e instanceof RuntimeException ? $e->getMessage() : 'Gagal menyimpan foto profil.';
            if ($isAjax) {
                $jsonOut(['ok' => false, 'msg' => $fotoErr], 400);
            }
        }
    } else {
        try {
            if (!csrf_ok($_POST['csrf'] ?? null)) {
                throw new RuntimeException('Sesi kedaluwarsa. Muat ulang halaman lalu coba lagi.');
            }
            $pwLama = (string)($_POST['pw_lama'] ?? '');
            $pwBaru = (string)($_POST['pw_baru'] ?? '');
            $pwKonf = (string)($_POST['pw_konf'] ?? '');

            if ($pwLama === '' || $pwBaru === '' || $pwKonf === '') {
                throw new RuntimeException('Semua kolom password wajib diisi.');
            }
            if (!password_verify($pwLama, $u['password'])) {
                throw new RuntimeException('Password saat ini (lama) tidak sesuai.');
            }
            if (mb_strlen($pwBaru) < 6) {
                throw new RuntimeException('Password baru minimal harus 6 karakter.');
            }
            if ($pwBaru !== $pwKonf) {
                throw new RuntimeException('Konfirmasi password baru tidak cocok.');
            }
            if ($pwBaru === $pwLama) {
                throw new RuntimeException('Password baru tidak boleh sama dengan password saat ini.');
            }

            Mahasiswa::resetPassword($id, $pwBaru);
            $u = Mahasiswa::find($id); // perbarui data user di memori
            $sukses = 'Password akun berhasil diperbarui! Gunakan password baru Anda saat login berikutnya.';
        } catch (RuntimeException $e) {
            $err = $e->getMessage();
        }
    }
}

$hasFoto = !empty($u['foto']) && is_file(UPLOAD_DIR . $u['foto']);

pwa_open('Profil Mahasiswa');
pwa_header($u, 'Profil & Keamanan', 'dark', 'AKUN MAHASISWA', 'beranda.php');
?>
    <main>
      <section class="welcome" style="margin-bottom:20px">
        <div style="display:flex;align-items:center;gap:16px">
          <button type="button" class="avatar-wrap" data-open-foto-sheet aria-label="Ganti foto profil">
            <div class="avatar js-user-avatar" style="width:62px;height:62px;font-size:22px;border-width:3px"><?= pwa_avatar_html($u) ?></div>
            <span class="avatar-badge" aria-hidden="true"><svg><use href="#cam" /></svg></span>
          </button>
          <div>
            <div class="eb">PORTAL MAHASISWA</div>
            <h2 style="font-size:18px;margin:2px 0 4px"><?= h($u['nama_mahasiswa']) ?></h2>
            <div class="aktif" style="margin-top:2px"><svg><use href="#grad" /></svg>Mahasiswa aktif</div>
          </div>
        </div>
      </section>

      <div class="card">
        <h3>👨‍🎓 Profil &amp; Keamanan Akun Mahasiswa</h3>

        <div class="info" style="margin-bottom:20px;line-height:1.55">
          <svg><use href="#info" /></svg>
          <span>Sesuai kebijakan akademik kampus, <b>NIM</b> dan <b>Nama Lengkap Mahasiswa</b> bersifat tetap dan terkunci. Mahasiswa hanya diizinkan untuk mengelola foto profil dan kata sandi (password). Untuk perbaikan data identitas, silakan hubungi Administrator Sarpras / BAAK.</span>
        </div>

        <div id="foto-feedback">
          <?php if ($fotoErr): ?>
          <div class="info red"><svg><use href="#info" /></svg><span><?= h($fotoErr) ?></span></div>
          <?php endif; ?>

          <?php if ($fotoSukses): ?>
          <div class="info" style="background:#e6f9f0;color:#12805c;border:1px solid #b7ecd4"><svg><use href="#chk" /></svg><span><?= h($fotoSukses) ?></span></div>
          <?php endif; ?>
        </div>

        <form method="post" enctype="multipart/form-data" id="form-foto" style="margin-bottom:18px;padding-bottom:18px;border-bottom:1px solid var(--line)">
          <?= csrf_field() ?>
          <input type="hidden" name="aksi" value="foto">
          <div class="field" style="margin-bottom:0">
            <label>Foto Profil (Kamera &amp; Galeri Android)</label>
            <div class="foto-row">
              <button type="button" class="avatar-wrap" data-open-foto-sheet aria-label="Pilih foto profil">
                <div class="avatar js-user-avatar foto-avatar-lg" id="preview_avatar"><?= pwa_avatar_html($u) ?></div>
                <span class="avatar-badge" aria-hidden="true"><svg><use href="#cam" /></svg></span>
              </button>
            </div>

            <!-- Input kamera Android (front/selfie default, bisa ganti kamera di perangkat) -->
            <input type="file" id="foto_kamera" accept="image/*" capture="user" hidden>
            <!-- Input galeri / pemilih foto Android -->
            <input type="file" id="foto_galeri" name="foto" accept="image/jpeg,image/png,image/webp,image/*" hidden>

            <noscript>
              <div style="margin-top:12px">
                <input type="file" name="foto" accept="image/jpeg,image/png,image/webp" style="width:100%;font-size:12px">
                <button class="btn main" type="submit" style="margin-top:8px">Simpan Foto Profil</button>
              </div>
            </noscript>

            <div class="foto-actions">
              <button class="btn main" type="button" data-open-foto-sheet style="width:100%">
                <svg style="width:17px;height:17px"><use href="#cam" /></svg>
                <span>Ganti Foto Profil</span>
              </button>
            </div>
          </div>
        </form>

        <div class="field">
          <label for="nim_mhs">Nomor Induk Mahasiswa (NIM)</label>
          <input class="inp" id="nim_mhs" type="text" value="<?= h($u['nim']) ?>" readonly disabled style="background:#edf1f7;color:#475569;cursor:not-allowed">
          <p class="hint">Terkunci • Dikelola langsung oleh Bagian Akademik &amp; Admin Sarpras.</p>
        </div>

        <div class="field">
          <label for="nama_mhs">Nama Lengkap Mahasiswa</label>
          <input class="inp" id="nama_mhs" type="text" value="<?= h($u['nama_mahasiswa']) ?>" readonly disabled style="background:#edf1f7;color:#475569;cursor:not-allowed">
          <p class="hint">Terkunci • Tidak dapat diubah oleh mahasiswa (data resmi identitas kampus).</p>
        </div>
      </div>

      <div class="card">
        <h3>🔐 Ganti Password Akun</h3>
        <p class="hint" style="margin-top:-4px;margin-bottom:16px">Masukkan password saat ini dan tentukan password baru yang aman.</p>

        <?php if ($err): ?>
        <div class="info red"><svg><use href="#info" /></svg><span><?= h($err) ?></span></div>
        <?php endif; ?>

        <?php if ($sukses): ?>
        <div class="info" style="background:#e6f9f0;color:#12805c;border:1px solid #b7ecd4"><svg><use href="#chk" /></svg><span><?= h($sukses) ?></span></div>
        <?php endif; ?>

        <form method="post" id="form-password"><?= csrf_field() ?>
          <input type="hidden" name="aksi" value="password">
          <div class="field">
            <label for="pw_lama">Password Saat Ini (Lama) <span style="color:var(--red)">*</span></label>
            <input class="inp" id="pw_lama" name="pw_lama" type="password" placeholder="Masukkan password saat ini" required autocomplete="current-password">
          </div>

          <div class="field">
            <label for="pw_baru">Password Baru <span style="color:var(--red)">*</span></label>
            <input class="inp" id="pw_baru" name="pw_baru" type="password" placeholder="Minimal 6 karakter" minlength="6" required autocomplete="new-password">
          </div>

          <div class="field">
            <label for="pw_konf">Konfirmasi Password Baru <span style="color:var(--red)">*</span></label>
            <input class="inp" id="pw_konf" name="pw_konf" type="password" placeholder="Ulangi password baru" minlength="6" required autocomplete="new-password">
          </div>

          <label class="consent" style="margin:14px 0 18px;display:flex;align-items:center;gap:8px;cursor:pointer">
            <input type="checkbox" id="show_pw" style="width:16px;height:16px;accent-color:var(--orange)">
            <span>Tampilkan karakter password</span>
          </label>

          <button class="btn main" type="submit" style="width:100%;display:flex;align-items:center;justify-content:center;gap:8px">
            <svg style="width:18px;height:18px"><use href="#key" /></svg>
            <span>Perbarui Password Akun →</span>
          </button>
        </form>
      </div>

      <div class="card" style="text-align:center">
        <h3 style="font-size:16px;margin-bottom:6px">Keluar dari Akun</h3>
        <p class="hint" style="margin-bottom:14px">Pastikan Anda keluar setelah menggunakan aplikasi jika menggunakan perangkat bersama.</p>
        <a class="btn" href="logout.php" data-logout style="color:var(--red);border:1px solid #fca5a5;background:#fff5f5;text-decoration:none;display:block">
          Keluar dari Akun (Logout)
        </a>
      </div>

      <p class="copy">© <?= date('Y') ?> Meta Sarpras • Politeknik Meta Industri Cikarang</p>
    </main>

    <!-- Bottom Sheet Pilihan Sumber Foto (Android UX) -->
    <div class="sheet-backdrop" id="sheet-foto" role="dialog" aria-modal="true" aria-labelledby="sheet-foto-title">
      <div class="sheet">
        <div class="sheet-handle"></div>
        <div class="sheet-title" id="sheet-foto-title">Ganti Foto Profil</div>
        <p class="sheet-sub">Pilih sumber foto dari perangkat Android Anda.</p>
        <div class="sheet-list">
          <button type="button" class="sheet-opt cam" id="opt-kamera">
            <span class="s-ico"><svg><use href="#cam" /></svg></span>
            <span><b>Ambil Foto (Kamera)</b><small>Buka kamera Android untuk foto langsung</small></span>
          </button>
          <button type="button" class="sheet-opt gal" id="opt-galeri">
            <span class="s-ico"><svg><use href="#img" /></svg></span>
            <span><b>Pilih dari Galeri</b><small>Pilih gambar dari Galeri / File Manager</small></span>
          </button>
          <button type="button" class="sheet-opt del" id="opt-hapus" <?= $hasFoto ? '' : 'hidden' ?>>
            <span class="s-ico"><svg><use href="#trash" /></svg></span>
            <span><b>Hapus Foto Profil</b><small>Kembali gunakan avatar inisial nama</small></span>
          </button>
        </div>
        <button type="button" class="btn" id="opt-batal" style="margin-bottom:0;height:46px;line-height:1">Batal</button>
      </div>
    </div>

    <!-- Bottom Sheet Crop & Preview Foto Profil -->
    <div class="sheet-backdrop" id="sheet-crop" role="dialog" aria-modal="true" aria-labelledby="sheet-crop-title">
      <div class="sheet">
        <div class="sheet-handle"></div>
        <div class="sheet-title" id="sheet-crop-title">Atur Posisi Foto</div>
        <p class="sheet-sub">Geser dan atur perbesaran agar wajah berada di tengah lingkaran.</p>
        <div class="crop-stage-wrap">
          <div class="crop-stage" id="crop-stage">
            <canvas id="crop-canvas" width="260" height="260"></canvas>
            <div class="crop-mask"></div>
          </div>
          <label class="crop-zoom" for="crop-zoom-range">
            <span>Perkecil</span>
            <input type="range" id="crop-zoom-range" min="1" max="3" step="0.02" value="1">
            <span>Perbesar</span>
          </label>
        </div>
        <div style="display:flex;gap:10px">
          <button type="button" class="btn" id="crop-ulang" style="flex:1;margin-bottom:0;height:46px;line-height:1">Pilih Ulang</button>
          <button type="button" class="btn main" id="crop-simpan" style="flex:1.4;margin-bottom:0;height:46px;line-height:1">Simpan Foto</button>
        </div>
      </div>
    </div>

    <div class="pwa-toast" id="pwa-toast" role="status" aria-live="polite"></div>

    <script>
      document.addEventListener('DOMContentLoaded', function () {
        var chk = document.getElementById('show_pw');
        var inputs = ['pw_lama', 'pw_baru', 'pw_konf'].map(function (id) {
          return document.getElementById(id);
        });
        if (chk) {
          chk.addEventListener('change', function () {
            var type = chk.checked ? 'text' : 'password';
            inputs.forEach(function (inp) {
              if (inp) inp.type = type;
            });
          });
        }

        var form = document.getElementById('form-password');
        if (form) {
          form.addEventListener('submit', function (e) {
            var p1 = document.getElementById('pw_baru');
            var p2 = document.getElementById('pw_konf');
            if (p1 && p2 && p1.value !== p2.value) {
              e.preventDefault();
              alert('Konfirmasi password baru tidak cocok!');
              p2.focus();
            }
          });
        }

        // ===== Android-Ready Foto Profil Controller =====
        var formFoto = document.getElementById('form-foto');
        var csrfInput = formFoto ? formFoto.querySelector('input[name="csrf"]') : null;
        var inputKamera = document.getElementById('foto_kamera');
        var inputGaleri = document.getElementById('foto_galeri');
        var sheetFoto = document.getElementById('sheet-foto');
        var sheetCrop = document.getElementById('sheet-crop');
        var optHapus = document.getElementById('opt-hapus');
        var feedbackBox = document.getElementById('foto-feedback');
        var toastEl = document.getElementById('pwa-toast');

        var cropStage = document.getElementById('crop-stage');
        var cropCanvas = document.getElementById('crop-canvas');
        var cropCtx = cropCanvas ? cropCanvas.getContext('2d') : null;
        var zoomRange = document.getElementById('crop-zoom-range');
        var btnSimpan = document.getElementById('crop-simpan');
        var btnUlang = document.getElementById('crop-ulang');

        var CROP_SIZE = 260;
        var EXPORT_SIZE = 600;
        var cropState = {
          img: null,
          w: 0,
          h: 0,
          zoom: 1,
          ox: 0,
          oy: 0,
          dragging: false,
          lastX: 0,
          lastY: 0,
          uploading: false
        };

        var toastTimer = null;
        function showToast(msg, type) {
          if (!toastEl) return;
          toastEl.textContent = msg;
          toastEl.className = 'pwa-toast show' + (type ? ' ' + type : '');
          clearTimeout(toastTimer);
          toastTimer = setTimeout(function () {
            toastEl.classList.remove('show');
          }, 2800);
        }

        function setFeedback(msg, isErr) {
          if (!feedbackBox) return;
          feedbackBox.textContent = '';
          if (!msg) return;
          var box = document.createElement('div');
          box.className = isErr ? 'info red' : 'info';
          if (!isErr) {
            box.style.background = '#e6f9f0';
            box.style.color = '#12805c';
            box.style.border = '1px solid #b7ecd4';
          }
          var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
          var use = document.createElementNS('http://www.w3.org/2000/svg', 'use');
          use.setAttribute('href', isErr ? '#info' : '#chk');
          svg.appendChild(use);
          var span = document.createElement('span');
          span.textContent = msg;
          box.appendChild(svg);
          box.appendChild(span);
          feedbackBox.appendChild(box);
        }

        function openSheet(el) {
          if (!el) return;
          el.classList.add('open');
        }

        function closeSheet(el) {
          if (!el) return;
          el.classList.remove('open');
        }

        function closeCropAndRelease() {
          closeSheet(sheetCrop);
          if (cropState.img && typeof cropState.img.close === 'function') {
            cropState.img.close();
          }
          cropState.img = null;
        }

        function updateAvatarUI(data) {
          var avatars = document.querySelectorAll('header .avatar, .js-user-avatar');
          avatars.forEach(function (av) {
            if (data.has_foto && data.foto_url) {
              var img = document.createElement('img');
              img.src = data.foto_url;
              img.alt = 'Foto ' + (data.nama || 'Mahasiswa');
              av.replaceChildren(img);
            } else {
              av.textContent = data.initials || '';
            }
          });
          if (optHapus) optHapus.hidden = !data.has_foto;
        }

        function uploadFormData(fd, onDone) {
          fetch('profil.php', {
            method: 'POST',
            headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'Accept': 'application/json'
            },
            body: fd,
            credentials: 'same-origin'
          })
            .then(function (res) {
              return res.json().catch(function () {
                return { ok: false, msg: 'Respon server tidak valid.' };
              });
            })
            .then(function (data) {
              if (data.redirect) {
                location.href = data.redirect;
                return;
              }
              if (!data.ok) {
                throw new Error(data.msg || 'Gagal memperbarui foto profil.');
              }
              updateAvatarUI(data);
              setFeedback(data.msg, false);
              showToast(data.msg, 'ok');
              if (onDone) onDone(true);
            })
            .catch(function (err) {
              var msg = err && err.message ? err.message : 'Gagal terhubung ke server.';
              setFeedback(msg, true);
              showToast(msg, 'err');
              if (onDone) onDone(false);
            });
        }

        function clampOffsets() {
          if (!cropState.img) return;
          var baseScale = Math.max(CROP_SIZE / cropState.w, CROP_SIZE / cropState.h);
          var scale = baseScale * cropState.zoom;
          var drawW = cropState.w * scale;
          var drawH = cropState.h * scale;
          var maxX = Math.max(0, (drawW - CROP_SIZE) / 2);
          var maxY = Math.max(0, (drawH - CROP_SIZE) / 2);
          cropState.ox = Math.max(-maxX, Math.min(maxX, cropState.ox));
          cropState.oy = Math.max(-maxY, Math.min(maxY, cropState.oy));
        }

        function drawCrop(ctx, targetSize) {
          if (!cropState.img || !ctx) return;
          var ratio = targetSize / CROP_SIZE;
          var baseScale = Math.max(CROP_SIZE / cropState.w, CROP_SIZE / cropState.h);
          var scale = baseScale * cropState.zoom;
          var drawW = cropState.w * scale * ratio;
          var drawH = cropState.h * scale * ratio;
          var dx = (CROP_SIZE / 2 + cropState.ox) * ratio - drawW / 2;
          var dy = (CROP_SIZE / 2 + cropState.oy) * ratio - drawH / 2;
          ctx.fillStyle = '#ffffff';
          ctx.fillRect(0, 0, targetSize, targetSize);
          ctx.drawImage(cropState.img, dx, dy, drawW, drawH);
        }

        function loadImageSource(file) {
          if (window.createImageBitmap) {
            return createImageBitmap(file, { imageOrientation: 'from-image' }).catch(function () {
              return createImageBitmap(file);
            });
          }
          return new Promise(function (resolve, reject) {
            var reader = new FileReader();
            reader.onload = function (ev) {
              var img = new Image();
              img.onload = function () { resolve(img); };
              img.onerror = function () { reject(new Error('File gambar tidak dapat dibaca.')); };
              img.src = ev.target.result;
            };
            reader.onerror = function () { reject(new Error('Gagal membaca file foto.')); };
            reader.readAsDataURL(file);
          });
        }

        function handleSelectedFile(file) {
          if (!file) return;
          if (file.type && file.type.indexOf('image/') !== 0) {
            showToast('Pilih file gambar (JPG, PNG, atau WEBP).', 'err');
            return;
          }
          closeSheet(sheetFoto);
          loadImageSource(file)
            .then(function (img) {
              if (cropState.img && typeof cropState.img.close === 'function') {
                cropState.img.close();
              }
              cropState.img = img;
              cropState.w = img.width || img.naturalWidth;
              cropState.h = img.height || img.naturalHeight;
              cropState.zoom = 1;
              cropState.ox = 0;
              cropState.oy = 0;
              if (zoomRange) zoomRange.value = '1';
              clampOffsets();
              drawCrop(cropCtx, CROP_SIZE);
              openSheet(sheetCrop);
            })
            .catch(function (err) {
              // Fallback langsung unggah bila file < 5 MB dan browser gagal decode preview
              if (file.size <= 5 * 1024 * 1024 && csrfInput) {
                var fd = new FormData();
                fd.append('csrf', csrfInput.value);
                fd.append('aksi', 'foto');
                fd.append('foto', file, file.name || 'profil.jpg');
                uploadFormData(fd);
              } else {
                showToast((err && err.message) || 'Foto tidak dapat diproses.', 'err');
              }
            });
        }

        function handleDeleteFoto() {
          closeSheet(sheetFoto);
          if (!confirm('Hapus foto profil saat ini?')) return;
          var fd = new FormData();
          if (csrfInput) fd.append('csrf', csrfInput.value);
          fd.append('aksi', 'foto');
          fd.append('hapus_foto', '1');
          uploadFormData(fd);
        }

        document.querySelectorAll('[data-open-foto-sheet]').forEach(function (btn) {
          btn.addEventListener('click', function () {
            openSheet(sheetFoto);
          });
        });

        [sheetFoto, sheetCrop].forEach(function (backdrop) {
          if (!backdrop) return;
          backdrop.addEventListener('click', function (e) {
            if (e.target === backdrop) {
              if (backdrop === sheetCrop) closeCropAndRelease();
              else closeSheet(backdrop);
            }
          });
        });

        var optKamera = document.getElementById('opt-kamera');
        var optGaleri = document.getElementById('opt-galeri');
        var optBatal = document.getElementById('opt-batal');

        if (optKamera && inputKamera) {
          optKamera.addEventListener('click', function () {
            inputKamera.value = '';
            inputKamera.click();
          });
        }
        if (optGaleri && inputGaleri) {
          optGaleri.addEventListener('click', function () {
            inputGaleri.value = '';
            inputGaleri.click();
          });
        }
        if (optBatal) {
          optBatal.addEventListener('click', function () {
            closeSheet(sheetFoto);
          });
        }
        if (optHapus) {
          optHapus.addEventListener('click', handleDeleteFoto);
        }

        if (inputKamera) {
          inputKamera.addEventListener('change', function () {
            if (inputKamera.files && inputKamera.files[0]) {
              handleSelectedFile(inputKamera.files[0]);
            }
          });
        }
        if (inputGaleri) {
          inputGaleri.addEventListener('change', function () {
            if (inputGaleri.files && inputGaleri.files[0]) {
              handleSelectedFile(inputGaleri.files[0]);
            }
          });
        }

        if (cropStage) {
          cropStage.addEventListener('pointerdown', function (e) {
            cropState.dragging = true;
            cropState.lastX = e.clientX;
            cropState.lastY = e.clientY;
            if (cropStage.setPointerCapture) cropStage.setPointerCapture(e.pointerId);
          });
          cropStage.addEventListener('pointermove', function (e) {
            if (!cropState.dragging) return;
            cropState.ox += e.clientX - cropState.lastX;
            cropState.oy += e.clientY - cropState.lastY;
            cropState.lastX = e.clientX;
            cropState.lastY = e.clientY;
            clampOffsets();
            drawCrop(cropCtx, CROP_SIZE);
          });
          var stopDrag = function () { cropState.dragging = false; };
          cropStage.addEventListener('pointerup', stopDrag);
          cropStage.addEventListener('pointercancel', stopDrag);
        }

        if (zoomRange) {
          zoomRange.addEventListener('input', function () {
            cropState.zoom = parseFloat(zoomRange.value) || 1;
            clampOffsets();
            drawCrop(cropCtx, CROP_SIZE);
          });
        }

        if (btnUlang) {
          btnUlang.addEventListener('click', function () {
            closeCropAndRelease();
            openSheet(sheetFoto);
          });
        }

        if (btnSimpan) {
          btnSimpan.addEventListener('click', function () {
            if (!cropState.img || cropState.uploading) return;
            cropState.uploading = true;
            var prevLabel = btnSimpan.textContent;
            btnSimpan.disabled = true;
            btnSimpan.textContent = 'Menyimpan...';

            var outCanvas = document.createElement('canvas');
            outCanvas.width = EXPORT_SIZE;
            outCanvas.height = EXPORT_SIZE;
            var outCtx = outCanvas.getContext('2d');
            drawCrop(outCtx, EXPORT_SIZE);

            outCanvas.toBlob(function (blob) {
              if (!blob) {
                cropState.uploading = false;
                btnSimpan.disabled = false;
                btnSimpan.textContent = prevLabel;
                showToast('Gagal memproses gambar.', 'err');
                return;
              }
              var fd = new FormData();
              if (csrfInput) fd.append('csrf', csrfInput.value);
              fd.append('aksi', 'foto');
              fd.append('foto', blob, 'profil.jpg');
              uploadFormData(fd, function (ok) {
                cropState.uploading = false;
                btnSimpan.disabled = false;
                btnSimpan.textContent = prevLabel;
                if (ok) closeCropAndRelease();
              });
            }, 'image/jpeg', 0.86);
          });
        }
      });
    </script>
<?php pwa_nav('profil');
