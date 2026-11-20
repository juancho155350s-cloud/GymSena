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
        $email = trim($request->post('email') ?: '');
        $password = trim($request->post('password') ?: '');

        if (empty($email) || empty($password)) {
            Response::redirect('/login?error=campos_requeridos');
        }

        $user = User::findByEmail($email);
        if (!$user || !User::verifyPassword($user, $password)) {
            Response::redirect('/login?error=credenciales_invalidas');
        }

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

