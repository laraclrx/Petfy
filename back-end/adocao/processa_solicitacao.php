<?php
/**
 * ============================================================
 * Petfy – BACK-END: Processamento de Solicitação de Adoção
 * ============================================================
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/verifica_auth.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../front-end/pages/pets.php');
    exit;
}

$idPet = (int)($_POST['id_pet'] ?? 0);
$idUsuario = (int)$_SESSION['usuario_id'];
$mensagem = trim($_POST['mensagem_motivo'] ?? '');
$telefone = trim($_POST['telefone_contato'] ?? ($_SESSION['usuario_telefone'] ?? ''));

if ($idPet <= 0) {
    setFlashMessage('error', 'Animal não especificado.');
    header('Location: ../../front-end/pages/pets.php');
    exit;
}

if (empty($mensagem)) {
    setFlashMessage('error', 'Escreva uma mensagem contando por que deseja adotar este animal.');
    header('Location: ../../front-end/pages/pet-detalhes.php?id=' . $idPet);
    exit;
}

if (empty($telefone)) {
    setFlashMessage('error', 'Informe seu telefone / WhatsApp para contato.');
    header('Location: ../../front-end/pages/pet-detalhes.php?id=' . $idPet);
    exit;
}

try {
    $pdo = db();

    $stmtPet = $pdo->prepare("SELECT id_pet, nome, id_usuario_tutor, status FROM pet WHERE id_pet = :id LIMIT 1");
    $stmtPet->execute([':id' => $idPet]);
    $pet = $stmtPet->fetch();

    if (!$pet) {
        setFlashMessage('error', 'Pet não encontrado.');
        header('Location: ../../front-end/pages/pets.php');
        exit;
    }

    if ($pet['id_usuario_tutor'] == $idUsuario) {
        setFlashMessage('warning', 'Você já é o tutor cadastrado deste pet.');
        header('Location: ../../front-end/pages/pet-detalhes.php?id=' . $idPet);
        exit;
    }

    if ($pet['status'] === 'Adotado') {
        setFlashMessage('info', 'Este pet já foi adotado!');
        header('Location: ../../front-end/pages/pet-detalhes.php?id=' . $idPet);
        exit;
    }

    $stmtCheck = $pdo->prepare("SELECT id_solicitacao FROM solicitacao_adocao WHERE id_pet = :id_pet AND id_usuario_adotante = :id_user AND status IN ('Pendente', 'Em Análise') LIMIT 1");
    $stmtCheck->execute([':id_pet' => $idPet, ':id_user' => $idUsuario]);
    if ($stmtCheck->fetch()) {
        setFlashMessage('info', 'Você já tem uma solicitação pendente para este animal.');
        header('Location: ../../front-end/pages/minhas-adocoes.php');
        exit;
    }

    $sqlInsert = "INSERT INTO solicitacao_adocao (id_pet, id_usuario_adotante, mensagem_motivo, telefone_contato, status) 
                  VALUES (:id_pet, :id_user, :mensagem, :telefone, 'Pendente')";
    $stmtInsert = $pdo->prepare($sqlInsert);
    $stmtInsert->execute([
        ':id_pet'   => $idPet,
        ':id_user'  => $idUsuario,
        ':mensagem' => $mensagem,
        ':telefone' => $telefone
    ]);

    setFlashMessage('success', 'Sua candidatura para adotar o pet ' . htmlspecialchars($pet['nome']) . ' foi enviada com sucesso!');
    header('Location: ../../front-end/pages/minhas-adocoes.php');
    exit;

} catch (PDOException $e) {
    setFlashMessage('error', 'Erro ao enviar o pedido de adoção.');
    header('Location: ../../front-end/pages/pet-detalhes.php?id=' . $idPet);
    exit;
}
