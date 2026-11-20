<?php
namespace GymSena\Models;

use GymSena\Core\Database;
use PDO;

class Learner
{
    public static function findById(int $id): ?array
    {
        if (Database::isFallback()) {
            $learners = Database::getFallbackData('aprendices');
            foreach ($learners as $l) {
                if ($l['id'] == $id) {
                    return $l;
                }
            }
            return null;
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM aprendices WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function findByDocument(string $document): ?array
    {
        $cleanDoc = trim($document);
        if (Database::isFallback()) {
            $learners = Database::getFallbackData('aprendices');
            foreach ($learners as $l) {
                if ($l['documento'] === $cleanDoc) {
                    return $l;
                }
            }
            return null;
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM aprendices WHERE documento = ?");
        $stmt->execute([$cleanDoc]);
        return $stmt->fetch() ?: null;
    }

    public static function findByToken(string $token): ?array
    {
        if (empty($token)) {
            return null;
        }

        if (Database::isFallback()) {
            $learners = Database::getFallbackData('aprendices');
            foreach ($learners as $l) {
                if (!empty($l['token_acceso']) && $l['token_acceso'] === $token) {
                    return $l;
                }
            }
            return null;
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM aprendices WHERE token_acceso = ?");
        $stmt->execute([$token]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): array
    {
        $data['tipo_usuario'] = strtoupper(trim($data['tipo_usuario'] ?? 'APRENDIZ'));
        if (!in_array($data['tipo_usuario'], ['APRENDIZ', 'INSTRUCTOR', 'FUNCIONARIO', 'INVITADO'])) {
            $data['tipo_usuario'] = 'APRENDIZ';
        }

        $token = 'tok_' . bin2hex(random_bytes(16));
        $data['token_acceso'] = $token;
        $data['creado_en'] = date('Y-m-d H:i:s');
        $data['activo'] = 1;
        $data['sede_id'] = $data['sede_id'] ?? 1;

        if (Database::isFallback()) {
            $learners = Database::getFallbackData('aprendices');
            $newId = count($learners) > 0 ? max(array_column($learners, 'id')) + 1 : 1;
            $data['id'] = $newId;
            $learners[] = $data;
            Database::saveFallbackData('aprendices', $learners);
            return $data;
        }

        $pdo = Database::getConnection();
        try {
            $stmt = $pdo->prepare("
                INSERT INTO aprendices 
                (sede_id, tipo_documento, documento, nombres, apellidos, tipo_usuario, ficha, programa_formacion, correo, telefono, contacto_emergencia_nombre, contacto_emergencia_telefono, estado_formacion, activo, token_acceso, creado_en)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $data['sede_id'],
                $data['tipo_documento'] ?? 'CC',
                $data['documento'],
                $data['nombres'],
                $data['apellidos'],
                $data['tipo_usuario'],
                $data['ficha'],
                $data['programa_formacion'],
                $data['correo'],
                $data['telefono'],
                $data['contacto_emergencia_nombre'],
                $data['contacto_emergencia_telefono'],
                $data['estado_formacion'] ?? 'LECTIVA',
                $data['activo'],
                $data['token_acceso'],
                $data['creado_en']
            ]);
        } catch (\PDOException $e) {
            // Fallback en caso de que la tabla aún no tenga la columna tipo_usuario
            $stmt = $pdo->prepare("
                INSERT INTO aprendices 
                (sede_id, tipo_documento, documento, nombres, apellidos, ficha, programa_formacion, correo, telefono, contacto_emergencia_nombre, contacto_emergencia_telefono, estado_formacion, activo, token_acceso, creado_en)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $data['sede_id'],
                $data['tipo_documento'] ?? 'CC',
                $data['documento'],
                $data['nombres'],
                $data['apellidos'],
                $data['ficha'],
                $data['programa_formacion'],
                $data['correo'],
                $data['telefono'],
                $data['contacto_emergencia_nombre'],
                $data['contacto_emergencia_telefono'],
                $data['estado_formacion'] ?? 'LECTIVA',
                $data['activo'],
                $data['token_acceso'],
                $data['creado_en']
            ]);
        }
        $data['id'] = (int)$pdo->lastInsertId();
        return $data;
    }

    public static function recordConsent(int $learnerId, string $ip, string $userAgent, string $version = 'v1.0-2026'): void
    {
        $fecha = date('Y-m-d H:i:s');
        if (Database::isFallback()) {
            $cons = Database::getFallbackData('consentimientos_ley1581');
            $cons[] = [
                'id' => count($cons) + 1,
                'aprendiz_id' => $learnerId,
                'acepto_politica' => 1,
                'ip_registro' => $ip,
                'user_agent' => substr($userAgent, 0, 250),
                'version_politica' => $version,
                'fecha_hora_consentimiento' => $fecha
            ];
            Database::saveFallbackData('consentimientos_ley1581', $cons);
            return;
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            INSERT INTO consentimientos_ley1581 
            (aprendiz_id, acepto_politica, ip_registro, user_agent, version_politica, fecha_hora_consentimiento)
            VALUES (?, 1, ?, ?, ?, ?)
        ");
        $stmt->execute([$learnerId, $ip, substr($userAgent, 0, 250), $version, $fecha]);
    }

    public static function hasActiveSanction(int $learnerId): ?array
    {
        $hoy = date('Y-m-d');
        if (Database::isFallback()) {
            $novedades = Database::getFallbackData('novedades_sanciones');
            foreach ($novedades as $n) {
                if ($n['aprendiz_id'] == $learnerId && !empty($n['bloquea_ingreso']) && empty($n['resuelto'])) {
                    if (empty($n['fecha_fin']) || $n['fecha_fin'] >= $hoy) {
                        return $n;
                    }
                }
            }
            return null;
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT * FROM novedades_sanciones 
            WHERE aprendiz_id = ? AND bloquea_ingreso = 1 AND resuelto = 0 
              AND (fecha_fin IS NULL OR fecha_fin >= ?)
            LIMIT 1
        ");
        $stmt->execute([$learnerId, $hoy]);
        return $stmt->fetch() ?: null;
    }

    public static function getAll(int $limit = 50): array
    {
        if (Database::isFallback()) {
            return array_slice(Database::getFallbackData('aprendices'), 0, $limit);
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM aprendices ORDER BY id DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll() ?: [];
    }
}

