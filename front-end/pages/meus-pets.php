<?php
/**
 * ============================================================
 * Petfy – FRONT-END: Painel do Tutor (Meus Pets Cadastrados)
 * Gestão de animais cadastrados e solicitações de adoção recebidas
 * ============================================================
 */

$pageTitle = 'Meus Pets Cadastrados';
require_once __DIR__ . '/../../back-end/config/database.php';
require_once __DIR__ . '/../../back-end/auth/verifica_auth.php';

requireAuth();

$pdo = db();
$usuario = getLoggedUser();
$idUsuario = (int)$usuario['id'];

// Busca todos os pets cadastrados pelo tutor logado
$sqlPets = "SELECT p.*, e.nome AS nome_especie, r.nome AS nome_raca,
            (SELECT COUNT(*) FROM solicitacao_adocao s WHERE s.id_pet = p.id_pet) AS total_pedidos
            FROM pet p
            LEFT JOIN especie e ON p.id_especie = e.id_especie
            LEFT JOIN raca r ON p.id_raca = r.id_raca
            WHERE p.id_usuario_tutor = :id
            ORDER BY p.id_pet DESC";
$stmtPets = $pdo->prepare($sqlPets);
$stmtPets->execute([':id' => $idUsuario]);
$meusPets = $stmtPets->fetchAll();

// Busca todas as solicitações de adoção recebidas para os pets do tutor
$sqlPedidos = "SELECT s.*, p.nome AS nome_pet, p.foto_url AS foto_pet, u.nome AS nome_adotante, u.email AS email_adotante
               FROM solicitacao_adocao s
               JOIN pet p ON s.id_pet = p.id_pet
               JOIN usuario u ON s.id_usuario_adotante = u.id_usuario
               WHERE p.id_usuario_tutor = :id
               ORDER BY s.id_solicitacao DESC";
$stmtPedidos = $pdo->prepare($sqlPedidos);
$stmtPedidos->execute([':id' => $idUsuario]);
$pedidosRecebidos = $stmtPedidos->fetchAll();

$rootPath = '../';
$extraCss = ['css/pets-grid.css', 'css/pet-form.css'];
include __DIR__ . '/../includes/header.php';
?>

<div class="container-petfy" style="padding-top: 30px; padding-bottom: 60px;">
    
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px; margin-bottom: 25px;">
        <div>
            <span class="catalog-badge"><i class="fas fa-user-shield"></i> Área do Tutor</span>
            <h1 style="font-family:var(--font-heading); color:var(--brand-teal-darker); font-size:2.2rem; margin-top:4px;">Meus Pets Cadastrados</h1>
            <p style="color:var(--text-muted); margin:0;">Gerencie os animais que você divulgou e acompanhe as mensagens de pessoas interessadas em adotar.</p>
        </div>
        <a href="cadastrar-pet.php" class="btn-petfy-primary" style="display:inline-flex; align-items:center; gap:8px;">
            <i class="fas fa-plus-circle"></i> Divulgar Novo Pet
        </a>
    </div>

    <!-- Lista dos Pets Cadastrados -->
    <?php if (!empty($meusPets)): ?>
        <div class="pets-cards-grid" style="margin-bottom: 50px;">
            <?php foreach ($meusPets as $pet): ?>
                <div class="pet-card" style="border: 1.5px solid #e2e8f0;">
                    <div class="pet-card-image-wrap">
                        <img src="<?= htmlspecialchars(getPetPhotoUrl($pet['foto_url'], $rootPath, $pet['nome_especie'] ?? 'Cachorro')) ?>" 
                             alt="<?= htmlspecialchars($pet['nome']) ?>" 
                             onerror="this.onerror=null; this.src='<?= $rootPath ?>img/default-pet.svg';">
                        
                        <span class="pet-card-badge-status <?= strtolower($pet['status']) === 'adotado' ? 'adotado' : (strtolower($pet['status']) === 'em processo' ? 'processo' : '') ?>">
                            <?= htmlspecialchars($pet['status']) ?>
                        </span>
                        
                        <span class="pet-card-badge-especie">
                            <?= htmlspecialchars($pet['nome_especie'] ?? 'Pet') ?>
                        </span>
                    </div>

                    <div class="pet-card-body">
                        <div class="pet-card-name-row">
                            <h3 class="pet-card-name"><?= htmlspecialchars($pet['nome']) ?></h3>
                            <span class="pet-card-sex <?= strtolower($pet['sexo']) === 'macho' ? 'macho' : 'femea' ?>">
                                <i class="fas <?= strtolower($pet['sexo']) === 'macho' ? 'fa-mars' : 'fa-venus' ?>"></i>
                            </span>
                        </div>

                        <p class="pet-card-breed"><?= htmlspecialchars($pet['nome_raca'] ?? 'SRD') ?></p>
                        
                        <div style="font-size:0.85rem; color:#4b5563; margin-bottom:12px;">
                            <i class="fas fa-envelope-open-text" style="color:var(--brand-teal);"></i>
                            <strong><?= $pet['total_pedidos'] ?></strong> pedido(s) de adoção recebidos
                        </div>

                        <div style="display:flex; flex-direction:column; gap:8px;">
                            <a href="pet-detalhes.php?id=<?= $pet['id_pet'] ?>" class="btn-petfy-outline" style="text-align:center; padding:8px 12px; font-size:0.9rem;">
                                <i class="fas fa-eye"></i> Visualizar Página
                            </a>

                            <!-- Formulário de Exclusão -->
                            <form action="../../back-end/pets/processa_exclusao_pet.php" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir o cadastro do pet <?= htmlspecialchars(addslashes($pet['nome'])) ?>?');">
                                <input type="hidden" name="id_pet" value="<?= $pet['id_pet'] ?>">
                                <button type="submit" style="width:100%; background:#fee2e2; color:#991b1b; border:none; padding:8px 12px; border-radius:10px; font-weight:700; cursor:pointer; font-size:0.85rem;">
                                    <i class="fas fa-trash-alt"></i> Excluir Pet
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-results-box" style="margin-bottom: 50px;">
            <i class="fas fa-paw" style="font-size: 3rem; color: var(--brand-teal); margin-bottom: 10px;"></i>
            <h3>Você ainda não cadastrou nenhum animal</h3>
            <p>Você resgatou ou conhece algum bichinho precisando de um novo lar? Divulgue agora mesmo!</p>
            <a href="cadastrar-pet.php" class="btn-petfy-primary" style="margin-top: 15px; display:inline-block;">Cadastrar Meu Primeiro Pet</a>
        </div>
    <?php endif; ?>

    <!-- Seção de Solicitações de Adoção Recebidas -->
    <div style="margin-top: 40px; border-top: 2px solid #e2e8f0; padding-top: 30px;">
        <h2 style="font-family:var(--font-heading); color:var(--brand-teal-darker); font-size:1.8rem; margin-bottom: 18px;">
            <i class="fas fa-heart" style="color:#ef4444;"></i> Candidaturas de Adoção Recebidas
        </h2>

        <?php if (!empty($pedidosRecebidos)): ?>
            <div style="display:flex; flex-direction:column; gap:16px;">
                <?php foreach ($pedidosRecebidos as $pedido): ?>
                    <div style="background:white; border-radius:16px; padding:20px; box-shadow:var(--shadow-xs); border:1.5px solid #e2e8f0; display:flex; flex-wrap:wrap; gap:16px; justify-content:space-between; align-items:center;">
                        <div style="display:flex; gap:14px; align-items:center;">
                            <img src="<?= htmlspecialchars(getPetPhotoUrl($pedido['foto_pet'], $rootPath)) ?>" 
                                 alt="Pet" 
                                 style="width:70px; height:70px; border-radius:12px; object-fit:cover;"
                                 onerror="this.onerror=null; this.src='<?= $rootPath ?>img/default-pet.svg';">
                            <div>
                                <h4 style="margin:0; font-size:1.1rem; color:var(--brand-teal-darker);">
                                    Interesse no pet: <strong><?= htmlspecialchars($pedido['nome_pet']) ?></strong>
                                </h4>
                                <p style="margin:4px 0 0 0; font-size:0.9rem; color:#4b5563;">
                                    Candidato(a): <strong><?= htmlspecialchars($pedido['nome_adotante']) ?></strong> • 
                                    <?php 
                                        $telAdotanteLimp = preg_replace('/\D/', '', (string)($pedido['telefone_contato'] ?? ''));
                                        if (!empty($telAdotanteLimp) && strlen($telAdotanteLimp) <= 11 && !str_starts_with($telAdotanteLimp, '55')) {
                                            $telAdotanteLimp = '55' . $telAdotanteLimp;
                                        }
                                    ?>
                                    WhatsApp: <a href="https://api.whatsapp.com/send?phone=<?= $telAdotanteLimp ?>" target="_blank" style="color:#059669; font-weight:bold;"><i class="fab fa-whatsapp"></i> <?= htmlspecialchars($pedido['telefone_contato'] ?? '') ?></a>
                                </p>
                                <p style="margin:6px 0 0 0; font-size:0.88rem; color:#6b7280; font-style:italic;">
                                    "<?= htmlspecialchars($pedido['mensagem_motivo']) ?>"
                                </p>
                            </div>
                        </div>

                        <!-- Status e Ações -->
                        <div style="display:flex; flex-direction:column; gap:8px; align-items:flex-end;">
                            <span class="pet-card-badge-status <?= strtolower($pedido['status']) === 'aprovada' ? 'adotado' : '' ?>" style="position:static;">
                                Status: <?= htmlspecialchars($pedido['status']) ?>
                            </span>

                            <form action="../../back-end/adocao/processa_status_adocao.php" method="POST" style="display:flex; gap:6px;">
                                <input type="hidden" name="id_solicitacao" value="<?= $pedido['id_solicitacao'] ?>">
                                
                                <select name="status" style="padding:6px 10px; border-radius:8px; border:1px solid #d1d5db; font-size:0.85rem;">
                                    <option value="Pendente" <?= $pedido['status'] === 'Pendente' ? 'selected' : '' ?>>Pendente</option>
                                    <option value="Em Análise" <?= $pedido['status'] === 'Em Análise' ? 'selected' : '' ?>>Em Análise</option>
                                    <option value="Aprovada" <?= $pedido['status'] === 'Aprovada' ? 'selected' : '' ?>>Aprovar Adoção</option>
                                    <option value="Recusada" <?= $pedido['status'] === 'Recusada' ? 'selected' : '' ?>>Recusar</option>
                                </select>

                                <button type="submit" class="btn-petfy-dark" style="padding:6px 14px; font-size:0.85rem; border-radius:8px;">
                                    Atualizar
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="color:var(--text-muted); font-size:0.95rem;">Nenhuma solicitação de adoção recebida até o momento.</p>
        <?php endif; ?>
    </div>

</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
