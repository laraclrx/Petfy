<?php
/**
 * ============================================================
 * Petfy – BACK-END: Processamento de Cadastro de Pet
 * ============================================================
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/verifica_auth.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../front-end/pages/cadastrar-pet.php');
    exit;
}

$idUsuario = (int)$_SESSION['usuario_id'];

$nome            = trim($_POST['nome'] ?? '');
$idEspecie       = !empty($_POST['id_especie']) ? (int)$_POST['id_especie'] : 1;
$idRaca          = !empty($_POST['id_raca']) ? (int)$_POST['id_raca'] : null;
$racaOutra       = trim($_POST['raca_outra'] ?? '');
$sexo            = in_array($_POST['sexo'] ?? '', ['Macho', 'Fêmea']) ? $_POST['sexo'] : 'Macho';
$porte           = in_array($_POST['porte'] ?? '', ['P', 'M', 'G']) ? $_POST['porte'] : 'M';
$idade           = trim($_POST['idade'] ?? '');
$descricao       = trim($_POST['descricao'] ?? '');
$origem          = trim($_POST['origem'] ?? '');
$nivelEnergia    = trim($_POST['nivel_energia'] ?? '');

$personalidadesInput = $_POST['personalidades'] ?? [];
if (is_array($personalidadesInput)) {
    $personalidades = implode(', ', array_map('trim', $personalidadesInput));
} else {
    $personalidades = trim($personalidadesInput);
}

if (!empty($nivelEnergia) && stripos($personalidades, $nivelEnergia) === false) {
    $personalidades = $personalidades ? $personalidades . ', Energia: ' . $nivelEnergia : 'Energia: ' . $nivelEnergia;
}

$detalhesExtras = [];
if (!empty($origem)) {
    $detalhesExtras[] = "📌 Origem / Resgate: " . $origem;
}
if (!empty($nivelEnergia)) {
    $detalhesExtras[] = "⚡ Nível de Energia: " . $nivelEnergia;
}

if (!empty($detalhesExtras)) {
    $blocoExtras = implode("\n", $detalhesExtras);
    $descricao = !empty($descricao) ? $descricao . "\n\n" . $blocoExtras : $blocoExtras;
}

$vacinado        = isset($_POST['vacinado']) && $_POST['vacinado'] === '1' ? 1 : 0;
$castrado        = isset($_POST['castrado']) && $_POST['castrado'] === '1' ? 1 : 0;
$vermifugado     = isset($_POST['vermifugado']) && $_POST['vermifugado'] === '1' ? 1 : 0;
$necessidades    = trim($_POST['necessidades_especiais'] ?? '');

$contatoNome     = trim($_POST['contato_nome'] ?? ($_SESSION['usuario_nome'] ?? ''));
$contatoTelefone = trim($_POST['contato_telefone'] ?? ($_SESSION['usuario_telefone'] ?? ''));
$contatoEmail    = trim($_POST['contato_email'] ?? ($_SESSION['usuario_email'] ?? ''));
$cidade          = trim($_POST['cidade'] ?? '');
$estado          = strtoupper(trim($_POST['estado'] ?? ''));

if (empty($nome) || empty($idade) || empty($cidade) || empty($estado)) {
    setFlashMessage('error', 'Preencha os campos obrigatórios (nome, idade, cidade e estado).');
    header('Location: ../../front-end/pages/cadastrar-pet.php');
    exit;
}

$fotoUrl = null;

if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['foto']['tmp_name'];
    $fileName    = $_FILES['foto']['name'];
    $fileSize    = $_FILES['foto']['size'];
    $extension   = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
    $maxFileSize = 6 * 1024 * 1024; // 6MB

    if (!in_array($extension, $allowedExtensions)) {
        setFlashMessage('error', 'Formato de foto inválido! Utilize JPG, PNG ou WEBP.');
        header('Location: ../../front-end/pages/cadastrar-pet.php');
        exit;
    }

    if ($fileSize > $maxFileSize) {
        setFlashMessage('error', 'A foto enviada ultrapassa o limite de 6MB.');
        header('Location: ../../front-end/pages/cadastrar-pet.php');
        exit;
    }

    $uploadDir = __DIR__ . '/../../front-end/uploads/pets/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $newFileName = 'pet_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
    $destPath = $uploadDir . $newFileName;

    if (move_uploaded_file($fileTmpPath, $destPath)) {
        $fotoUrl = 'uploads/pets/' . $newFileName;
    } else {
        setFlashMessage('error', 'Erro ao processar o upload da imagem.');
        header('Location: ../../front-end/pages/cadastrar-pet.php');
        exit;
    }
} else {
    // Caso o usuário não envie foto no momento, usa fallback adequado conforme espécie
    $fotoUrl = ($idEspecie === 2) ? 'img/default-cat.svg' : 'img/default-dog.svg';
}

try {
    $pdo = db();

    if (!empty($racaOutra) && empty($idRaca)) {
        $stmtRaca = $pdo->prepare("SELECT id_raca FROM raca WHERE LOWER(nome) = LOWER(:nome) AND id_especie = :especie LIMIT 1");
        $stmtRaca->execute([':nome' => $racaOutra, ':especie' => $idEspecie]);
        $racaExistente = $stmtRaca->fetch();

        if ($racaExistente) {
            $idRaca = (int)$racaExistente['id_raca'];
        } else {
            $stmtNovaRaca = $pdo->prepare("INSERT INTO raca (nome, id_especie) VALUES (:nome, :especie)");
            $stmtNovaRaca->execute([':nome' => $racaOutra, ':especie' => $idEspecie]);
            $idRaca = (int)$pdo->lastInsertId();
        }
    }

    $sql = "INSERT INTO pet (
                id_usuario_tutor, id_especie, id_raca, nome, sexo, porte, idade,
                personalidades, descricao, vacinado, castrado, vermifugado,
                necessidades_especiais, foto_url, contato_nome, contato_telefone,
                contato_email, cidade, estado, status
            ) VALUES (
                :id_tutor, :id_especie, :id_raca, :nome, :sexo, :porte, :idade,
                :personalidades, :descricao, :vacinado, :castrado, :vermifugado,
                :necessidades, :foto_url, :contato_nome, :contato_telefone,
                :contato_email, :cidade, :estado, 'Disponível'
            )";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id_tutor'          => $idUsuario,
        ':id_especie'        => $idEspecie,
        ':id_raca'           => $idRaca ?: null,
        ':nome'              => $nome,
        ':sexo'              => $sexo,
        ':porte'             => $porte,
        ':idade'             => $idade,
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
        ':estado'            => $estado
    ]);

    $petId = (int)$pdo->lastInsertId();

    setFlashMessage('success', '🎉 Maravilha! O pet "' . htmlspecialchars($nome) . '" foi cadastrado para adoção com sucesso!');
    header('Location: ../../front-end/pages/pet-detalhes.php?id=' . $petId);
    exit;

} catch (PDOException $e) {
    setFlashMessage('error', 'Erro ao salvar os dados do pet no banco de dados: ' . $e->getMessage());
    header('Location: ../../front-end/pages/cadastrar-pet.php');
    exit;
}
