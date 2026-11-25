<?php
/** Model: tabel user (menggabungkan admin & mahasiswa) */
class User {
    static function find(int $id): ?array {
        $s = db()->prepare('SELECT * FROM user WHERE id_user=?');
        $s->execute([$id]);
        $u = $s->fetch();
        if ($u) {
            $u['nama_mahasiswa'] = $u['nama_user'];
            $u['nama_admin']     = $u['nama_user'];
            $u['id_mahasiswa']   = $u['id_user'];
            $u['id_admin']       = $u['id_user'];
        }
        return $u ?: null;
    }

    static function login(string $identifier, string $pw): ?array {
        $s = db()->prepare('SELECT * FROM user WHERE username=? OR nim=?');
        $s->execute([$identifier, $identifier]);
        $u = $s->fetch();
        if ($u && password_verify($pw, $u['password'])) {
            $u['nama_mahasiswa'] = $u['nama_user'];
            $u['nama_admin']     = $u['nama_user'];
            $u['id_mahasiswa']   = $u['id_user'];
            $u['id_admin']       = $u['id_user'];
            return $u;
        }
        return null;
    }

    static function loginAdmin(string $username, string $pw): ?array {
        $s = db()->prepare("SELECT * FROM user WHERE username=? AND role='admin'");
        $s->execute([$username]);
        $u = $s->fetch();
        if ($u && password_verify($pw, $u['password'])) {
            $u['nama_mahasiswa'] = $u['nama_user'];
            $u['nama_admin']     = $u['nama_user'];
            $u['id_mahasiswa']   = $u['id_user'];
            $u['id_admin']       = $u['id_user'];
            return $u;
        }
        return null;
    }

    static function loginMahasiswa(string $nimOrUser, string $pw): ?array {
        $s = db()->prepare("SELECT * FROM user WHERE (nim=? OR username=?) AND role='mahasiswa'");
        $s->execute([$nimOrUser, $nimOrUser]);
        $u = $s->fetch();
        if ($u && password_verify($pw, $u['password'])) {
            $u['nama_mahasiswa'] = $u['nama_user'];
            $u['nama_admin']     = $u['nama_user'];
            $u['id_mahasiswa']   = $u['id_user'];
            $u['id_admin']       = $u['id_user'];
            return $u;
        }
        return null;
    }

    static function allMahasiswa(): array {
        return db()->query("SELECT id_user id, id_user id_mahasiswa, COALESCE(nim, username) nim, nama_user nama, nama_user nama_mahasiswa FROM user WHERE role='mahasiswa' ORDER BY nama_user")->fetchAll();
    }

    static function allAdmin(): array {
        return db()->query("SELECT id_user id, id_user id_admin, username, nama_user nama, nama_user nama_admin FROM user WHERE role='admin' ORDER BY id_user")->fetchAll();
    }

    static function countMahasiswa(): int {
        return (int) db()->query("SELECT COUNT(*) FROM user WHERE role='mahasiswa'")->fetchColumn();
    }

    static function countAdmin(): int {
        return (int) db()->query("SELECT COUNT(*) FROM user WHERE role='admin'")->fetchColumn();
    }

    static function usernameAda(string $u, ?int $excludeId = null): bool {
        $sql = 'SELECT 1 FROM user WHERE username=?';
        $params = [$u];
        if ($excludeId !== null) {
            $sql .= ' AND id_user!=?';
            $params[] = $excludeId;
        }
        $s = db()->prepare($sql);
        $s->execute($params);
        return (bool) $s->fetchColumn();
    }

    static function nimAda(string $nim, ?int $excludeId = null): bool {
        $sql = 'SELECT 1 FROM user WHERE nim=?';
        $params = [$nim];
        if ($excludeId !== null) {
            $sql .= ' AND id_user!=?';
            $params[] = $excludeId;
        }
        $s = db()->prepare($sql);
        $s->execute($params);
        return (bool) $s->fetchColumn();
    }

    static function createMahasiswa(string $nim, string $nama, string $pw): int {
        $s = db()->prepare("INSERT INTO user (username, password, nama_user, nim, role) VALUES (?,?,?,?, 'mahasiswa')");
        $s->execute([$nim, password_hash($pw, PASSWORD_DEFAULT), $nama, $nim]);
        return (int) db()->lastInsertId();
    }

    static function createAdmin(string $username, string $nama, string $pw): int {
        $s = db()->prepare("INSERT INTO user (username, password, nama_user, nim, role) VALUES (?,?,?,NULL, 'admin')");
        $s->execute([$username, password_hash($pw, PASSWORD_DEFAULT), $nama]);
        return (int) db()->lastInsertId();
    }

    static function delete(int $id): void {
        db()->prepare('DELETE FROM user WHERE id_user=?')->execute([$id]);
    }

    static function resetPassword(int $id, string $pw): void {
        db()->prepare('UPDATE user SET password=? WHERE id_user=?')->execute([password_hash($pw, PASSWORD_DEFAULT), $id]);
    }

    static function updateNama(int $id, string $nama): void {
        db()->prepare('UPDATE user SET nama_user=? WHERE id_user=?')->execute([$nama, $id]);
    }

    static function updatePassword(int $id, string $pw): void {
        db()->prepare('UPDATE user SET password=? WHERE id_user=?')->execute([password_hash($pw, PASSWORD_DEFAULT), $id]);
    }

    static function updateFoto(int $id, ?string $foto): void {
        db()->prepare('UPDATE user SET foto=? WHERE id_user=?')->execute([$foto, $id]);
    }

    // Kompatibilitas untuk kode lama yang mungkin masih memanggil Mahasiswa::... atau Admin::...
    static function all(): array { return self::allMahasiswa(); }
    static function count(): int { return self::countMahasiswa(); }
    static function create(string ...$args): int {
        if (count($args) === 3) {
            // Jika argumen pertama berupa angka NIM -> createMahasiswa
            if (preg_match('/^\d+$/', $args[0])) {
                return self::createMahasiswa($args[0], $args[1], $args[2]);
            }
            return self::createAdmin($args[0], $args[1], $args[2]);
        }
        return 0;
    }
}

// Alias agar backward-compatible
class_alias('User', 'Admin');
class_alias('User', 'Mahasiswa');
