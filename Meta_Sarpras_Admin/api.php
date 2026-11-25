<?php
/** Controller aksi admin (AJAX/JSON). Semua perubahan data lewat Model. */
require __DIR__ . '/../app/bootstrap.php';
header('Content-Type: application/json; charset=UTF-8');
function out(array $a, int $code = 200): never { http_response_code($code); echo json_safe($a); exit; }
function fail(string $m, int $code = 400): never { out(['ok' => false, 'msg' => $m], $code); }

$adm = admin_user();
if (!$adm) fail('Sesi berakhir. Silakan login ulang.', 401);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') fail('Metode tidak diizinkan.', 405);
if (!csrf_ok($_SERVER['HTTP_X_CSRF'] ?? ($_POST['csrf'] ?? null))) fail('Token keamanan tidak valid. Muat ulang halaman.', 403);
$isMultipart = str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'multipart/form-data');
$in = $isMultipart ? $_POST : json_decode(file_get_contents('php://input') ?: '', true);
if (!is_array($in)) fail('Data tidak valid.');
$idA = (int)$adm['id_user'];
$str = fn(string $k) => trim((string)($in[$k] ?? ''));
$int = fn(string $k) => (int)($in[$k] ?? 0);

try {
    switch ($in['action'] ?? '') {

    case 'pengaduan.tindak': {
        $id = $int('id'); $s = $str('status'); $isi = $str('tanggapan');
        $map = ['Menunggu' => 'Menunggu', 'Diproses' => 'Sedang diproses', 'Selesai' => 'Selesai'];
        if (!isset($map[$s])) fail('Status tidak valid.');
        if ($isi === '') fail('Tanggapan resmi wajib diisi.');
        if (!Pengaduan::baca($id)) fail('Pengaduan tidak ditemukan.', 404);
        $pdo = db(); $pdo->beginTransaction();
        Pengaduan::tindak($id, $idA, $map[$s]); Tanggapan::create($idA, $id, $isi);
        $pdo->commit();
        out(['ok' => true, 'row' => h_deep(Pengaduan::baca($id))]);
    }

    case 'peminjaman.status': {
        $id = $int('id'); $to = $str('to'); $p = Peminjaman::baca($id);
        if (!$p) fail('Peminjaman tidak ditemukan.', 404);
        $alur = ['disetujui' => 'menunggu', 'ditolak' => 'menunggu', 'serah' => 'disetujui'];
        if (!isset($alur[$to]) || $p['s'] !== $alur[$to]) fail('Perubahan status tidak diizinkan dari status saat ini.');
        $alasan = null;
        if ($to === 'ditolak') { $alasan = $str('alasan'); if ($alasan === '') fail('Alasan penolakan wajib diisi.'); }
        Peminjaman::setStatus($id, $idA, STATUS_PINJAM[$to], $alasan);
        out(['ok' => true, 'row' => h_deep(Peminjaman::baca($id))]);
    }

    case 'peminjaman.kembali': {
        $id = $int('id'); $p = Peminjaman::baca($id);
        if (!$p) fail('Peminjaman tidak ditemukan.', 404);
        if (!in_array($p['s'], ['serah', 'selesai'], true)) fail('Alat belum berstatus dipinjam.');
        $ret = $str('ret');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $ret)) fail('Tanggal pengembalian tidak valid.');
        $den = max(0, $int('den'));
        Peminjaman::kembalikan($id, $idA, $ret, mb_substr($str('kond'), 0, 100), $den, mb_substr($str('ket'), 0, 255), $str('cat'));
        out(['ok' => true, 'row' => h_deep(Peminjaman::baca($id))]);
    }

    case 'akun.mhs.tambah': {
        $nim = $str('nim'); $nama = $str('nama'); $pw = (string)($in['password'] ?? '');
        if (!preg_match('/^\d{3,20}$/', $nim)) fail('NIM harus berupa angka (3–20 digit).');
        if ($nama === '' || mb_strlen($nama) > 100) fail('Nama lengkap wajib diisi (maksimal 100 karakter).');
        if (strlen($pw) < 6) fail('Password minimal 6 karakter.');
        if (User::nimAda($nim)) fail('NIM sudah terdaftar.');
        $id = User::createMahasiswa($nim, $nama, $pw);
        out(['ok' => true, 'row' => h_deep(['id' => $id, 'nim' => $nim, 'nama' => $nama])]);
    }

    case 'akun.mhs.hapus': {
        $id = $int('id');
        try { User::delete($id); }
        catch (PDOException $e) { fail('Akun tidak dapat dihapus karena sudah memiliki data pengaduan atau peminjaman.'); }
        out(['ok' => true]);
    }

    case 'akun.mhs.reset': {
        $id = $int('id'); if (!User::find($id)) fail('Akun tidak ditemukan.', 404);
        User::resetPassword($id, 'password123');
        out(['ok' => true]);
    }

    case 'akun.admin.tambah': {
        $u = $str('username'); $nama = $str('nama'); $pw = (string)($in['password'] ?? '');
        if (!preg_match('/^[A-Za-z0-9_.-]{3,50}$/', $u)) fail('Username 3–50 karakter (huruf, angka, titik, strip, garis bawah).');
        if ($nama === '' || mb_strlen($nama) > 100) fail('Nama lengkap petugas wajib diisi.');
        if (strlen($pw) < 6) fail('Password minimal 6 karakter.');
        if (User::usernameAda($u)) fail('Username sudah dipakai.');
        $id = User::createAdmin($u, $nama, $pw);
        out(['ok' => true, 'row' => h_deep(['id' => $id, 'username' => $u, 'nama' => $nama])]);
    }

    case 'profil.nama': {
        $nama = $str('nama'); if ($nama === '' || mb_strlen($nama) > 100) fail('Nama lengkap wajib diisi (maksimal 100 karakter).');
        User::updateNama($idA, $nama); out(['ok' => true, 'nama' => h($nama)]);
    }

    case 'profil.password': {
        $lama = (string)($in['lama'] ?? ''); $baru = (string)($in['baru'] ?? '');
        if (!password_verify($lama, $adm['password'])) fail('Password saat ini salah.');
        if (strlen($baru) < 6) fail('Password baru minimal 6 karakter.');
        if ($baru === $lama) fail('Password baru harus berbeda dari yang lama.');
        User::updatePassword($idA, $baru); out(['ok' => true]);
    }

    case 'profil.foto': {
        $fotoLama = $adm['foto'] ?? null;
        if (!empty($in['hapus'])) {
            User::updateFoto($idA, null);
            if ($fotoLama && is_file(UPLOAD_DIR . $fotoLama)) @unlink(UPLOAD_DIR . $fotoLama);
            out(['ok' => true, 'foto_url' => null, 'msg' => 'Foto profil berhasil dihapus.']);
        }
        try {
            $fotoBaru = upload_foto('foto');
        } catch (RuntimeException $e) {
            fail($e->getMessage(), 400);
        }
        if (!$fotoBaru) fail('Pilih file foto terlebih dahulu.');
        try {
            User::updateFoto($idA, $fotoBaru);
        } catch (Throwable $t) {
            if (is_file(UPLOAD_DIR . $fotoBaru)) @unlink(UPLOAD_DIR . $fotoBaru);
            throw $t;
        }
        if ($fotoLama && $fotoLama !== $fotoBaru && is_file(UPLOAD_DIR . $fotoLama)) {
            @unlink(UPLOAD_DIR . $fotoLama);
        }
        out(['ok' => true, 'foto_url' => foto_url($fotoBaru), 'msg' => 'Foto profil berhasil diperbarui.']);
    }

    default: fail('Aksi tidak dikenal.');
    }
} catch (Throwable $e) {
    if (db()->inTransaction()) db()->rollBack();
    fail('Terjadi kesalahan pada server.', 500);
}
