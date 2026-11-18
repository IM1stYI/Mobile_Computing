<?php
/** Model: tabel tanggapan */
class Tanggapan {
    static function byPengaduan(int $idPengaduan): array {
        $s = db()->prepare('SELECT t.id_tanggapan id, t.isi_tanggapan isi, t.tanggal_tanggapan tgl, a.nama_admin admin
                            FROM tanggapan t JOIN admin a ON a.id_admin=t.id_admin
                            WHERE t.id_pengaduan=? ORDER BY t.tanggal_tanggapan DESC, t.id_tanggapan DESC');
        $s->execute([$idPengaduan]); return $s->fetchAll();
    }
    static function create(int $idAdmin, int $idPengaduan, string $isi): void {
        db()->prepare('INSERT INTO tanggapan (id_admin, id_pengaduan, isi_tanggapan, tanggal_tanggapan) VALUES (?,?,?,CURDATE())')
            ->execute([$idAdmin, $idPengaduan, $isi]);
    }
    /** Seluruh riwayat tanggapan (halaman admin) */
    static function semua(): array {
        return db()->query('SELECT t.id_tanggapan id, t.tanggal_tanggapan tgl, m.nama_mahasiswa n, p.deskripsi_pengaduan d, t.isi_tanggapan isi, a.nama_admin admin
                            FROM tanggapan t JOIN admin a ON a.id_admin=t.id_admin
                            JOIN pengaduan p ON p.id_pengaduan=t.id_pengaduan
                            JOIN mahasiswa m ON m.id_mahasiswa=p.id_mahasiswa
                            ORDER BY t.tanggal_tanggapan DESC, t.id_tanggapan DESC')->fetchAll();
    }
}
