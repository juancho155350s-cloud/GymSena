<?php
namespace GymSena\Models;

use GymSena\Core\Database;
use PDO;

class User
{
    public static function findByEmail(string $email): ?array
    {
        $cleanEmail = strtolower(trim($email));

        if (Database::isFallback()) {
            $users = Database::getFallbackData('usuarios_sistema');
            foreach ($users as $u) {
                if (strtolower($u['email']) === $cleanEmail && $u['estado'] === 'ACTIVO') {
                    return $u;
                }
            }
            return null;
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM usuarios_sistema WHERE LOWER(email) = ? AND estado = 'ACTIVO'");
        $stmt->execute([$cleanEmail]);
        return $stmt->fetch() ?: null;
    }

    public static function verifyPassword(array $user, string $plainPassword): bool
    {
        return password_verify($plainPassword, $user['password_hash']);
    }

    public static function updateLastLogin(int $userId): void
    {
        $now = date('Y-m-d H:i:s');
        if (Database::isFallback()) {
            $users = Database::getFallbackData('usuarios_sistema');
            foreach ($users as &$u) {
                if ($u['id'] == $userId) {
                    $u['ultimo_login'] = $now;
                    break;
                }
            }
            Database::saveFallbackData('usuarios_sistema', $users);
            return;
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE usuarios_sistema SET ultimo_login = ? WHERE id = ?");
        $stmt->execute([$now, $userId]);
    }

    public static function findById(int $id): ?array
    {
        if (Database::isFallback()) {
            $users = Database::getFallbackData('usuarios_sistema');
            foreach ($users as $u) {
                if ((int)$u['id'] === $id) {
                    return $u;
                }
            }
            return null;
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM usuarios_sistema WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function getAllStaff(): array
    {
        if (Database::isFallback()) {
            $users = Database::getFallbackData('usuarios_sistema');
            $staff = [];
            foreach ($users as $u) {
                if ($u['rol'] === 'MONITOR') {
                    $staff[] = $u;
                }
            }
            return array_reverse($staff);
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT * FROM usuarios_sistema WHERE rol = 'MONITOR' ORDER BY id DESC");
        return $stmt->fetchAll() ?: [];
    }

    public static function create(array $data): array
    {
        $hashed = password_hash($data['password'], PASSWORD_DEFAULT);
        $record = [
            'sede_id' => (int)($data['sede_id'] ?? 1),
            'gimnasio_id' => (int)($data['gimnasio_id'] ?? 1),
            'documento' => trim($data['documento']),
            'nombre_completo' => trim($data['nombre_completo']),
            'email' => strtolower(trim($data['email'])),
            'password_hash' => $hashed,
            'rol' => $data['rol'] ?? 'MONITOR',
            'estado' => $data['estado'] ?? 'ACTIVO',
            'ultimo_login' => null,
            'creado_en' => date('Y-m-d H:i:s')
        ];

        if (Database::isFallback()) {
            $users = Database::getFallbackData('usuarios_sistema');
            $newId = count($users) > 0 ? max(array_column($users, 'id')) + 1 : 1;
            $record['id'] = $newId;
            $users[] = $record;
            Database::saveFallbackData('usuarios_sistema', $users);
            return $record;
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            INSERT INTO usuarios_sistema (sede_id, gimnasio_id, documento, nombre_completo, email, password_hash, rol, estado, creado_en)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $record['sede_id'],
            $record['gimnasio_id'],
            $record['documento'],
            $record['nombre_completo'],
            $record['email'],
            $record['password_hash'],
            $record['rol'],
            $record['estado'],
            $record['creado_en']
        ]);

        $record['id'] = (int)$pdo->lastInsertId();
        return $record;
    }

    public static function toggleStatus(int $userId): bool
    {
        if (Database::isFallback()) {
            $users = Database::getFallbackData('usuarios_sistema');
            $found = false;
            foreach ($users as &$u) {
                if ((int)$u['id'] === $userId) {
                    $u['estado'] = ($u['estado'] === 'ACTIVO') ? 'INACTIVO' : 'ACTIVO';
                    $found = true;
                    break;
                }
            }
            if ($found) {
                Database::saveFallbackData('usuarios_sistema', $users);
            }
            return $found;
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            UPDATE usuarios_sistema 
            SET estado = IF(estado = 'ACTIVO', 'INACTIVO', 'ACTIVO') 
            WHERE id = ?
        ");
        return $stmt->execute([$userId]);
    }

    public static function delete(int $userId): bool
    {
        if (Database::isFallback()) {
            $users = Database::getFallbackData('usuarios_sistema');
            $filtered = array_values(array_filter($users, fn($u) => (int)$u['id'] !== $userId));
            Database::saveFallbackData('usuarios_sistema', $filtered);
            return true;
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM usuarios_sistema WHERE id = ? AND rol = 'MONITOR'");
        return $stmt->execute([$userId]);
    }
}

