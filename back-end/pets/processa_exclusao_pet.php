<?php
/**
 * ============================================================
 * Petfy – BACK-END: Processamento de Exclusão de Pet
 * ============================================================
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/verifica_auth.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../front-end/pages/meus-pets.php');
    exit;
}

$idPet = (int)($_POST['id_pet'] ?? 0);
$idUsuario = (int)$_SESSION['usuario_id'];
$tipoUsuario = $_SESSION['usuario_tipo'] ?? 'usuario';

if ($idPet <= 0) {
    setFlashMessage('error', 'Pet inválido para exclusão.');
    header('Location: ../../front-end/pages/meus-pets.php');
    exit;
}

try {
    $pdo = db();

    $stmt = $pdo->prepare("SELECT id_pet, id_usuario_tutor, nome, foto_url FROM pet WHERE id_pet = :id LIMIT 1");
    $stmt->execute([':id' => $idPet]);
    $pet = $stmt->fetch();

    if (!$pet || ($pet['id_usuario_tutor'] != $idUsuario && $tipoUsuario !== 'admin')) {
        setFlashMessage('error', 'Você não possui permissão para excluir este pet.');
        header('Location: ../../front-end/pages/meus-pets.php');
        exit;
    }

    if (!empty($pet['foto_url']) && str_starts_with($pet['foto_url'], 'uploads/pets/')) {
        $filePath = __DIR__ . '/../../front-end/' . $pet['foto_url'];
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
    }

    $stmtDelete = $pdo->prepare("DELETE FROM pet WHERE id_pet = :id");
    $stmtDelete->execute([':id' => $idPet]);

    setFlashMessage('success', 'O cadastro do pet "' . htmlspecialchars($pet['nome']) . '" foi excluído com sucesso.');
    header('Location: ../../front-end/pages/meus-pets.php');
    exit;

} catch (PDOException $e) {
    setFlashMessage('error', 'Erro ao excluir o pet. Tente novamente.');
    header('Location: ../../front-end/pages/meus-pets.php');
    exit;
}
