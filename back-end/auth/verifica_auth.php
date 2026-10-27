<?php
/**
 * ============================================================
 * Petfy – BACK-END: Middleware de Autenticação e Controle de Sessão
 * ============================================================
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn(): bool {
    return !empty($_SESSION['usuario_id']);
}

function getLoggedUser(): ?array {
    if (!isLoggedIn()) {
        return null;
    }
    return [
        'id'       => $_SESSION['usuario_id'],
        'nome'     => $_SESSION['usuario_nome'] ?? 'Usuário',
        'email'    => $_SESSION['usuario_email'] ?? '',
        'telefone' => $_SESSION['usuario_telefone'] ?? '',
        'tipo'     => $_SESSION['usuario_tipo'] ?? 'usuario'
    ];
}

function requireAuth(?string $redirectUrl = null): void {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $redirectUrl ?? ($_SERVER['REQUEST_URI'] ?? '');
        
        $_SESSION['flash'] = [
            'type' => 'warning',
            'message' => 'Você precisa estar conectado à sua conta para acessar esta área.'
        ];

        // Determina o caminho relativo correto até a tela de login
        if (file_exists(__DIR__ . '/../../front-end/login.php')) {
            $prefix = file_exists('login.php') ? '' : (file_exists('../login.php') ? '../' : '../../front-end/');
            header("Location: " . $prefix . "login.php");
        } else {
            header("Location: login.php");
        }
        exit;
    }
}
