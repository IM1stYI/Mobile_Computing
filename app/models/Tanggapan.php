<?php
/** Model: tabel tanggapan */
class Tanggapan {
    static function byPengaduan(int $idPengaduan): array {
        $s = db()->prepare('SELECT t.id_tanggapan id, t.isi_tanggapan isi, t.tanggal_tanggapan tgl, u.nama_user admin, u.nama_user nama_admin
                            FROM tanggapan t JOIN user u ON u.id_user=t.id_user
                            WHERE t.id_pengaduan=? ORDER BY t.tanggal_tanggapan DESC, t.id_tanggapan DESC');
        $s->execute([$idPengaduan]); return $s->fetchAll();
    }
    static function create(int $idUser, int $idPengaduan, string $isi, ?int $idPeminjaman = null): void {
        db()->prepare('INSERT INTO tanggapan (id_user, id_pengaduan, id_peminjaman, isi_tanggapan, tanggal_tanggapan) VALUES (?,?,?,?,CURDATE())')
            ->execute([$idUser, $idPengaduan, $idPeminjaman, $isi]);
    }
    /** Seluruh riwayat tanggapan (halaman admin) */
    static function semua(): array {
        return db()->query('SELECT t.id_tanggapan id, t.tanggal_tanggapan tgl, m.nama_user n, m.nama_user nama_mahasiswa, p.deskripsi_pengaduan d, t.isi_tanggapan isi, u.nama_user admin, u.nama_user nama_admin
                            FROM tanggapan t JOIN user u ON u.id_user=t.id_user
                            JOIN pengaduan p ON p.id_pengaduan=t.id_pengaduan
                            JOIN user m ON m.id_user=p.id_user
                            ORDER BY t.tanggal_tanggapan DESC, t.id_tanggapan DESC')->fetchAll();
    }
}
