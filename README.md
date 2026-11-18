# Meta Sarpras (PHP + MySQL)

Aplikasi pengaduan fasilitas & peminjaman alat Politeknik Meta Industri Cikarang.
Struktur database mengikuti file `MVC_Pengaduan_Peminjaman_Barang_Kampus.xlsx`.

## Menjalankan (tanpa htdocs)
1. Buka XAMPP Control Panel, **Start MySQL** (Apache tidak perlu).
2. Klik dua kali `start.bat` (atau jalankan `php -S localhost:8000` dari folder ini).
3. Buka <http://localhost:8000/setup.php> **sekali** untuk membuat database `meta_sarpras` + data contoh.
4. Buka aplikasi:
   - Mahasiswa (PWA): <http://localhost:8000/Meta_Sarpras_PWA/splash.php>
   - Admin: <http://localhost:8000/Meta_Sarpras_Admin/login.php>

| Peran | Login | Password |
|---|---|---|
| Admin | `zakiajja` | `zaki123` |
| Mahasiswa | NIM `2425010` | `mahasiswa` |

Jika user/password MySQL berbeda, ubah `app/config.php` (file ini tidak ikut ke GitHub; contohnya `app/config.example.php`).

## Struktur
```
app/            bootstrap.php (session, PDO, helper) + models/ (Mahasiswa, Admin, Pengaduan, Tanggapan, Peminjaman)
database/       schema.sql (tabel + data contoh, aman dijalankan berulang)
Meta_Sarpras_PWA/    halaman mahasiswa (view + controller per halaman)
Meta_Sarpras_Admin/  halaman admin + api.php (controller aksi JSON)
uploads/        foto yang diunggah (tidak ikut ke GitHub)
```

## Tabel
`mahasiswa`, `admin`, `pengaduan`, `tanggapan`, `peminjaman` (sesuai file MVC).
Kolom tambahan di luar file MVC: `mahasiswa.password`, `peminjaman.jumlah`, `peminjaman.catatan_admin`.
