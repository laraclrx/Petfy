<?php
/**
 * ============================================================
 * Petfy – BACK-END: Processamento de Edição de Pet
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
    setFlashMessage('error', 'Pet inválido.');
    header('Location: ../../front-end/pages/meus-pets.php');
    exit;
}

try {
    $pdo = db();

    $stmtCheck = $pdo->prepare("SELECT id_pet, id_usuario_tutor, foto_url FROM pet WHERE id_pet = :id LIMIT 1");
    $stmtCheck->execute([':id' => $idPet]);
    $petExistente = $stmtCheck->fetch();

    if (!$petExistente || ($petExistente['id_usuario_tutor'] != $idUsuario && $tipoUsuario !== 'admin')) {
        setFlashMessage('error', 'Você não tem permissão para editar este pet.');
        header('Location: ../../front-end/pages/meus-pets.php');
        exit;
    }

    $nome            = trim($_POST['nome'] ?? '');
    $idEspecie       = (int)($_POST['id_especie'] ?? 1);
    $idRaca          = !empty($_POST['id_raca']) ? (int)$_POST['id_raca'] : null;
    $sexo            = in_array($_POST['sexo'] ?? '', ['Macho', 'Fêmea']) ? $_POST['sexo'] : 'Macho';
    $porte           = in_array($_POST['porte'] ?? '', ['P', 'M', 'G']) ? $_POST['porte'] : 'M';
    $idade           = trim($_POST['idade'] ?? '');
    $status          = in_array($_POST['status'] ?? '', ['Disponível', 'Em Processo', 'Adotado']) ? $_POST['status'] : 'Disponível';
    $descricao       = trim($_POST['descricao'] ?? '');
    $personalidades  = trim($_POST['personalidades'] ?? '');
    $vacinado        = isset($_POST['vacinado']) && $_POST['vacinado'] === '1' ? 1 : 0;
    $castrado        = isset($_POST['castrado']) && $_POST['castrado'] === '1' ? 1 : 0;
    $vermifugado     = isset($_POST['vermifugado']) && $_POST['vermifugado'] === '1' ? 1 : 0;
    $necessidades    = trim($_POST['necessidades_especiais'] ?? '');
    $cidade          = trim($_POST['cidade'] ?? '');
    $estado          = strtoupper(trim($_POST['estado'] ?? ''));
    $contatoNome     = trim($_POST['contato_nome'] ?? '');
    $contatoTelefone = trim($_POST['contato_telefone'] ?? '');
    $contatoEmail    = trim($_POST['contato_email'] ?? '');

    $fotoUrl = $petExistente['foto_url'];

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['foto']['tmp_name'];
        $fileName    = $_FILES['foto']['name'];
        $fileSize    = $_FILES['foto']['size'];
        $extension   = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($extension, $allowedExtensions) && $fileSize <= 5 * 1024 * 1024) {
            $uploadDir = __DIR__ . '/../../front-end/uploads/pets/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $newFileName = 'pet_' . uniqid('', true) . '.' . $extension;
            $destPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                if (!empty($petExistente['foto_url']) && str_starts_with($petExistente['foto_url'], 'uploads/pets/')) {
                    $oldPath = __DIR__ . '/../../front-end/' . $petExistente['foto_url'];
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                }
                $fotoUrl = 'uploads/pets/' . $newFileName;
            }
        }
    }

    $sqlUpdate = "UPDATE pet SET 
                    nome = :nome, id_especie = :id_especie, id_raca = :id_raca,
                    sexo = :sexo, porte = :porte, idade = :idade, status = :status,
                    personalidades = :personalidades, descricao = :descricao,
                    vacinado = :vacinado, castrado = :castrado, vermifugado = :vermifugado,
                    necessidades_especiais = :necessidades, foto_url = :foto_url,
                    contato_nome = :contato_nome, contato_telefone = :contato_telefone,
                    contato_email = :contato_email, cidade = :cidade, estado = :estado
                  WHERE id_pet = :id_pet";

    $stmtUpdate = $pdo->prepare($sqlUpdate);
    $stmtUpdate->execute([
        ':nome'              => $nome,
        ':id_especie'        => $idEspecie,
        ':id_raca'           => $idRaca ?: null,
        ':sexo'              => $sexo,
        ':porte'             => $porte,
        ':idade'             => $idade,
        ':status'            => $status,
        ':personalidades'    => $personalidades ?: null,
        ':descricao'         => $descricao ?: null,
        ':vacinado'          => $vacinado,
        ':castrado'          => $castrado,
        ':vermifugado'       => $vermifugado,
        ':necessidades'      => $necessidades ?: null,
        ':foto_url'          => $fotoUrl,
        ':contato_nome'      => $contatoNome ?: null,
        ':contato_telefone'  => $contatoTelefone ?: null,
        ':contato_email'     => $contatoEmail ?: null,
        ':cidade'            => $cidade,
        ':estado'            => $estado,
        ':id_pet'            => $idPet
    ]);

    setFlashMessage('success', 'Informações do pet atualizadas com sucesso!');
    header('Location: ../../front-end/pages/pet-detalhes.php?id=' . $idPet);
    exit;

} catch (PDOException $e) {
    setFlashMessage('error', 'Erro ao atualizar dados do pet.');
    header('Location: ../../front-end/pages/meus-pets.php');
    exit;
}
