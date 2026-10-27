<?php
/**
 * ============================================================
 * Petfy – BACK-END: Processamento de Login
 * ============================================================
 */

require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../front-end/login.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

if (empty($email) || empty($senha)) {
    setFlashMessage('error', 'Por favor, preencha o e-mail e a senha.');
    header('Location: ../../front-end/login.php');
    exit;
}

try {
    $pdo = db();

    $stmt = $pdo->prepare("SELECT * FROM usuario WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    $senhaValida = false;
    if ($user) {
        if (password_verify($senha, $user['senha'])) {
            $senhaValida = true;
        } elseif ($user['senha'] === $senha) {
            // Suporte de segurança caso alguém tenha inserido senha em texto puro manualmente no phpMyAdmin
            $senhaValida = true;
            // Atualiza automaticamente para hash seguro Bcrypt
            $novoHash = password_hash($senha, PASSWORD_DEFAULT);
            $stmtUp = $pdo->prepare("UPDATE usuario SET senha = :senha WHERE id_usuario = :id");
            $stmtUp->execute([':senha' => $novoHash, ':id' => $user['id_usuario']]);
        }
    }

    if ($senhaValida) {
        session_regenerate_id(true);

        $_SESSION['usuario_id']       = $user['id_usuario'];
        $_SESSION['usuario_nome']     = $user['nome'];
        $_SESSION['usuario_email']    = $user['email'];
        $_SESSION['usuario_telefone'] = $user['telefone'];
        $_SESSION['usuario_tipo']     = $user['tipo_usuario'];

        setFlashMessage('success', 'Olá, ' . htmlspecialchars(explode(' ', $user['nome'])[0]) . '! Seja muito bem-vindo(a) de volta ao Petfy.');

        $redirect = $_SESSION['redirect_after_login'] ?? '../../front-end/index.php';
        unset($_SESSION['redirect_after_login']);

        header('Location: ' . $redirect);
        exit;
    } else {
        setFlashMessage('error', 'E-mail ou senha incorretos. Verifique suas credenciais e tente novamente.');
        header('Location: ../../front-end/login.php');
        exit;
    }

} catch (PDOException $e) {
    setFlashMessage('error', 'Erro interno ao realizar login no banco de dados.');
    header('Location: ../../front-end/login.php');
    exit;
}
