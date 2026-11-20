<?php
namespace GymSena\Controllers;

use GymSena\Core\Request;
use GymSena\Core\Response;
use GymSena\Core\Auth;
use GymSena\Models\User;

class AuthController
{
    public function showLogin(Request $request): void
    {
        if (Auth::isUserLoggedIn()) {
            $user = Auth::getUser();
            if ($user['rol'] === 'ADMINISTRADOR') {
                Response::redirect('/admin');
            } else {
                Response::redirect('/monitor');
            }
        }

        Response::view('auth/login', [
            'title' => 'Inicio de Sesión - GymSENA',
            'error' => $request->get('error')
        ], 'auth');
    }

    public function login(Request $request): void
    {
        Auth::startSession();

        $csrf = $request->post('csrf_token');
        if (!empty($csrf) && !Auth::validateCsrfToken($csrf)) {
            Response::redirect('/login?error=sesion_expirada');
            return;
        }

        $ip = $request->getIp();
        $attemptKey = 'login_attempts_' . md5($ip);
        $attempts = $_SESSION[$attemptKey] ?? ['count' => 0, 'last_attempt' => time()];

        // Bloqueo temporal tras 5 intentos fallidos por IP (Protección anti-fuerza bruta)
        if ($attempts['count'] >= 5) {
            $timePassed = time() - $attempts['last_attempt'];
            if ($timePassed < 60) {
                Response::redirect('/login?error=demasiados_intentos');
            } else {
                // Reiniciar ventana de intentos tras expirar el bloqueo
                $attempts = ['count' => 0, 'last_attempt' => time()];
            }
        }

        $email = trim($request->post('email') ?: '');
        $password = trim($request->post('password') ?: '');

        if (empty($email) || empty($password)) {
            Response::redirect('/login?error=campos_requeridos');
        }

        $user = User::findByEmail($email);
        if (!$user || !User::verifyPassword($user, $password)) {
            $attempts['count']++;
            $attempts['last_attempt'] = time();
            $_SESSION[$attemptKey] = $attempts;
            Response::redirect('/login?error=credenciales_invalidas');
        }

        // Login exitoso: restablecer intentos
        unset($_SESSION[$attemptKey]);

        User::updateLastLogin($user['id']);
        Auth::loginUser($user);

        if ($user['rol'] === 'ADMINISTRADOR') {
            Response::redirect('/admin');
        } else {
            Response::redirect('/monitor');
        }
    }

    public function logout(Request $request): void
    {
        Auth::logout();
        Response::redirect('/login?msg=sesion_cerrada');
    }
}

