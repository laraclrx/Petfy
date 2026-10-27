<?php
/**
 * ============================================================
 * Petfy – BACK-END: Processamento de Edição de Perfil e CEP
 * ============================================================
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/verifica_auth.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../front-end/pages/perfil.php');
    exit;
}

$idUsuario = (int)$_SESSION['usuario_id'];
$nome      = trim($_POST['nome'] ?? '');
$telefone  = trim($_POST['telefone'] ?? '');
$cep       = trim($_POST['cep'] ?? '');
$rua       = trim($_POST['rua'] ?? '');
$numero    = trim($_POST['numero'] ?? '');
$bairro    = trim($_POST['bairro'] ?? '');
$cidade    = trim($_POST['cidade'] ?? '');
$estado    = strtoupper(trim($_POST['estado'] ?? ''));
$novaSenha = $_POST['nova_senha'] ?? '';

if (empty($nome) || mb_strlen($nome) < 3) {
    setFlashMessage('error', 'O nome deve conter ao menos 3 letras.');
    header('Location: ../../front-end/pages/perfil.php');
    exit;
}

try {
    $pdo = db();

    if (!empty($novaSenha)) {
        if (strlen($novaSenha) < 6) {
            setFlashMessage('error', 'A nova senha deve ter no mínimo 6 caracteres.');
            header('Location: ../../front-end/pages/perfil.php');
            exit;
        }

        $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
        $sql = "UPDATE usuario SET nome = :nome, telefone = :telefone, cep = :cep, rua = :rua,
                numero = :numero, bairro = :bairro, cidade = :cidade, estado = :estado, senha = :senha
                WHERE id_usuario = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome'     => $nome,
            ':telefone' => $telefone ?: null,
            ':cep'      => $cep ?: null,
            ':rua'      => $rua ?: null,
            ':numero'   => $numero ?: null,
            ':bairro'   => $bairro ?: null,
            ':cidade'   => $cidade ?: null,
            ':estado'   => $estado ?: null,
            ':senha'    => $senhaHash,
            ':id'       => $idUsuario
        ]);
        setFlashMessage('success', 'Perfil e nova senha atualizados com sucesso!');
    } else {
        $sql = "UPDATE usuario SET nome = :nome, telefone = :telefone, cep = :cep, rua = :rua,
                numero = :numero, bairro = :bairro, cidade = :cidade, estado = :estado
                WHERE id_usuario = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome'     => $nome,
            ':telefone' => $telefone ?: null,
            ':cep'      => $cep ?: null,
            ':rua'      => $rua ?: null,
            ':numero'   => $numero ?: null,
            ':bairro'   => $bairro ?: null,
            ':cidade'   => $cidade ?: null,
            ':estado'   => $estado ?: null,
            ':id'       => $idUsuario
        ]);
        setFlashMessage('success', 'Dados do perfil atualizados com sucesso!');
    }

    $_SESSION['usuario_nome']     = $nome;
    $_SESSION['usuario_telefone'] = $telefone;

    header('Location: ../../front-end/pages/perfil.php');
    exit;

} catch (PDOException $e) {
    setFlashMessage('error', 'Erro ao salvar alterações do perfil.');
    header('Location: ../../front-end/pages/perfil.php');
    exit;
}
