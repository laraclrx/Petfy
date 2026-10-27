<?php
/**
 * ============================================================
 * Petfy – BACK-END: Processamento de Cadastro de Usuário
 * Suporte completo a CEP e Endereço sem alterar o Banco de Dados
 * ============================================================
 */

require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../front-end/cadastro.php');
    exit;
}

$nome     = trim($_POST['nome'] ?? '');
$email    = trim($_POST['email'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');
$senha    = $_POST['senha'] ?? '';
$cep      = trim($_POST['cep'] ?? '');
$rua      = trim($_POST['rua'] ?? '');
$numero   = trim($_POST['numero'] ?? '');
$bairro   = trim($_POST['bairro'] ?? '');
$cidade   = trim($_POST['cidade'] ?? '');
$estado   = strtoupper(trim($_POST['estado'] ?? ''));

// Salva dados na sessão temporária para repovoar campos em caso de erro
$_SESSION['old_cadastro'] = [
    'nome'     => $nome,
    'email'    => $email,
    'telefone' => $telefone,
    'cep'      => $cep,
    'rua'      => $rua,
    'numero'   => $numero,
    'bairro'   => $bairro,
    'cidade'   => $cidade,
    'estado'   => $estado
];

$erros = [];

if (empty($nome) || mb_strlen($nome) < 3) {
    $erros[] = 'Informe seu nome completo (mínimo 3 letras).';
}
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erros[] = 'Informe um endereço de e-mail válido.';
}
if (empty($senha) || strlen($senha) < 6) {
    $erros[] = 'A senha deve conter no mínimo 6 caracteres.';
}

if (!empty($erros)) {
    setFlashMessage('error', implode('<br>', $erros));
    header('Location: ../../front-end/cadastro.php');
    exit;
}

try {
    $pdo = db();

    // Verifica se e-mail já existe na base
    $stmtCheck = $pdo->prepare("SELECT id_usuario FROM usuario WHERE email = :email LIMIT 1");
    $stmtCheck->execute([':email' => $email]);
    if ($stmtCheck->fetch()) {
        setFlashMessage('error', 'Este e-mail já está cadastrado. Faça login para continuar.');
        header('Location: ../../front-end/login.php');
        exit;
    }

    // Criptografia segura com Bcrypt
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuario (nome, email, senha, telefone, cep, rua, numero, bairro, cidade, estado, tipo_usuario) 
            VALUES (:nome, :email, :senha, :telefone, :cep, :rua, :numero, :bairro, :cidade, :estado, 'usuario')";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nome'     => $nome,
        ':email'    => $email,
        ':senha'    => $senhaHash,
        ':telefone' => $telefone ?: null,
        ':cep'      => $cep ?: null,
        ':rua'      => $rua ?: null,
        ':numero'   => $numero ?: null,
        ':bairro'   => $bairro ?: null,
        ':cidade'   => $cidade ?: null,
        ':estado'   => $estado ?: null
    ]);

    $idUsuario = (int)$pdo->lastInsertId();

    // Limpa os dados temporários de cadastro
    unset($_SESSION['old_cadastro']);

    session_regenerate_id(true);
    $_SESSION['usuario_id']       = $idUsuario;
    $_SESSION['usuario_nome']     = $nome;
    $_SESSION['usuario_email']    = $email;
    $_SESSION['usuario_telefone'] = $telefone;
    $_SESSION['usuario_tipo']     = 'usuario';

    setFlashMessage('success', 'Conta criada com sucesso! Bem-vindo(a) ao Petfy, ' . htmlspecialchars(explode(' ', $nome)[0]) . '!');
    
    $redirect = $_SESSION['redirect_after_login'] ?? '../../front-end/index.php';
    unset($_SESSION['redirect_after_login']);
    
    header('Location: ' . $redirect);
    exit;

} catch (PDOException $e) {
    setFlashMessage('error', 'Erro ao salvar o cadastro no banco de dados. Tente novamente.');
    header('Location: ../../front-end/cadastro.php');
    exit;
}
