<?php

namespace App\Controllers;

use App\Models\UserModel;
use Exception;

class AuthController {
    private UserModel $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function loginForm() {
        if (isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }
        view('auth/login');
    }

    public function processLogin() {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Preencha todos os campos.';
            header('Location: /login');
            exit;
        }

        $user = $this->userModel->findByEmail($email);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'] ?? 'USER';
            header('Location: /');
            exit;
        }

        $_SESSION['error'] = 'E-mail ou senha incorretos.';
        header('Location: /login');
        exit;
    }

    public function registerForm() {
        if (isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }
        view('auth/register');
    }

    public function processRegister() {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($name) || empty($email) || empty($password)) {
            $_SESSION['error'] = 'Preencha todos os campos.';
            header('Location: /register');
            exit;
        }

        $existingUser = $this->userModel->findByEmail($email);
        if ($existingUser) {
            $_SESSION['error'] = 'Este e-mail já está em uso.';
            header('Location: /register');
            exit;
        }

        $hash = password_hash($password, PASSWORD_ARGON2ID);
        
        try {
            if ($this->userModel->create($name, $email, $hash)) {
                // Auto login após registro
                $user = $this->userModel->findByEmail($email);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'] ?? 'USER';
                header('Location: /');
                exit;
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Erro ao criar conta. Tente novamente.';
            header('Location: /register');
            exit;
        }
    }

    public function logout() {
        session_destroy();
        header('Location: /');
        exit;
    }
}
