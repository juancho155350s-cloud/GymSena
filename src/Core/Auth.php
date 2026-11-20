<?php
namespace GymSena\Core;

class Auth
{
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $defaultSavePath = session_save_path() ?: sys_get_temp_dir();
            if (!is_writable($defaultSavePath)) {
                $customSessionDir = dirname(__DIR__, 2) . '/data/sessions';
                if (!is_dir($customSessionDir)) {
                    @mkdir($customSessionDir, 0777, true);
                }
                if (is_dir($customSessionDir) && is_writable($customSessionDir)) {
                    session_save_path($customSessionDir);
                } else {
                    session_save_path(sys_get_temp_dir());
                }
            }
            @session_start();
        }
    }

    public static function loginUser(array $user): void
    {
        self::startSession();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['nombre_completo'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['rol'];
        $_SESSION['user_sede_id'] = $user['sede_id'];
        $_SESSION['user_gym_id'] = $user['gimnasio_id'] ?? null;
    }

    public static function loginLearner(array $learner): void
    {
        self::startSession();
        $_SESSION['learner_id'] = $learner['id'];
        $_SESSION['learner_document'] = $learner['documento'];
        $_SESSION['learner_name'] = $learner['nombres'] . ' ' . $learner['apellidos'];
        $_SESSION['learner_ficha'] = $learner['ficha'];
        $_SESSION['learner_program'] = $learner['programa_formacion'];
        $_SESSION['learner_token'] = $learner['token_acceso'] ?? '';
    }

    public static function getUser(): ?array
    {
        self::startSession();
        if (empty($_SESSION['user_id'])) {
            return null;
        }
        return [
            'id' => $_SESSION['user_id'],
            'nombre' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'rol' => $_SESSION['user_role'],
            'sede_id' => $_SESSION['user_sede_id'],
            'gimnasio_id' => $_SESSION['user_gym_id']
        ];
    }

    public static function getLearner(): ?array
    {
        self::startSession();
        if (empty($_SESSION['learner_id'])) {
            return null;
        }
        return [
            'id' => $_SESSION['learner_id'],
            'documento' => $_SESSION['learner_document'],
            'nombre' => $_SESSION['learner_name'],
            'ficha' => $_SESSION['learner_ficha'],
            'programa' => $_SESSION['learner_program'],
            'token' => $_SESSION['learner_token']
        ];
    }

    public static function isUserLoggedIn(): bool
    {
        return self::getUser() !== null;
    }

    public static function isLearnerLoggedIn(): bool
    {
        return self::getLearner() !== null;
    }

    public static function hasRole(string $role): bool
    {
        $user = self::getUser();
        return $user && $user['rol'] === $role;
    }

    public static function requireRole(string $role): void
    {
        if (!self::hasRole($role) && !self::hasRole('ADMINISTRADOR')) {
            Response::redirect('/login?error=unauthorized');
        }
    }

    public static function logout(): void
    {
        self::startSession();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }
}

