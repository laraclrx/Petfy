<?php
/**
 * ============================================================
 * Petfy – BACK-END: Encerramento de Sessão (Logout)
 * ============================================================
 */

require_once __DIR__ . '/../config/database.php';

$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

session_start();
setFlashMessage('info', 'Você saiu da sua conta com segurança. Até breve!');

$isInsideFrontEnd = (strpos($_SERVER['REQUEST_URI'] ?? '', 'front-end') !== false);
$redirectUrl = $isInsideFrontEnd ? 'index.php' : 'front-end/index.php';

header('Location: ' . $redirectUrl);
exit;
