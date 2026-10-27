<?php
/**
 * ============================================================
 * Petfy – BACK-END: Atualização de Status da Adoção
 * ============================================================
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/verifica_auth.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../front-end/pages/meus-pets.php');
    exit;
}

$idSolicitacao = (int)($_POST['id_solicitacao'] ?? 0);
$novoStatus    = trim($_POST['status'] ?? '');
$respostaTutor = trim($_POST['resposta_tutor'] ?? '');
$idUsuario     = (int)$_SESSION['usuario_id'];
$tipoUsuario   = $_SESSION['usuario_tipo'] ?? 'usuario';

$statusPermitidos = ['Pendente', 'Em Análise', 'Aprovada', 'Recusada', 'Finalizada'];

if ($idSolicitacao <= 0 || !in_array($novoStatus, $statusPermitidos)) {
    setFlashMessage('error', 'Dados inválidos.');
    header('Location: ../../front-end/pages/meus-pets.php');
    exit;
}

try {
    $pdo = db();

    $sql = "SELECT s.id_solicitacao, s.id_pet, s.id_usuario_adotante, p.id_usuario_tutor, p.nome AS nome_pet
            FROM solicitacao_adocao s
            JOIN pet p ON s.id_pet = p.id_pet
            WHERE s.id_solicitacao = :id_solicitacao
            LIMIT 1";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id_solicitacao' => $idSolicitacao]);
    $solicitacao = $stmt->fetch();

    if (!$solicitacao || ($solicitacao['id_usuario_tutor'] != $idUsuario && $tipoUsuario !== 'admin')) {
        setFlashMessage('error', 'Sem permissão para alterar este pedido.');
        header('Location: ../../front-end/pages/meus-pets.php');
        exit;
    }

    $sqlUpdate = "UPDATE solicitacao_adocao 
                  SET status = :status, resposta_tutor = :resposta, data_atualizacao = NOW() 
                  WHERE id_solicitacao = :id";
    $stmtUpdate = $pdo->prepare($sqlUpdate);
    $stmtUpdate->execute([
        ':status'   => $novoStatus,
        ':resposta' => $respostaTutor ?: null,
        ':id'       => $idSolicitacao
    ]);

    if (in_array($novoStatus, ['Aprovada', 'Finalizada'])) {
        $stmtPet = $pdo->prepare("UPDATE pet SET status = 'Adotado' WHERE id_pet = :id_pet");
        $stmtPet->execute([':id_pet' => $solicitacao['id_pet']]);
    }

    setFlashMessage('success', 'Status da solicitação atualizado para: ' . $novoStatus);
    header('Location: ../../front-end/pages/meus-pets.php');
    exit;

} catch (PDOException $e) {
    setFlashMessage('error', 'Erro ao atualizar o status.');
    header('Location: ../../front-end/pages/meus-pets.php');
    exit;
}
