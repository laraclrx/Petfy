<?php
/**
 * ============================================================
 * Petfy – FRONT-END: Minhas Adoções
 * Acompanhamento das candidaturas de adoção enviadas pelo usuário
 * ============================================================
 */

$pageTitle = 'Minhas Adoções';
require_once __DIR__ . '/../../back-end/config/database.php';
require_once __DIR__ . '/../../back-end/auth/verifica_auth.php';

requireAuth();

$pdo = db();
$usuario = getLoggedUser();
$idUsuario = (int)$usuario['id'];

$sql = "SELECT s.*, p.nome AS nome_pet, p.foto_url AS foto_pet, p.cidade, p.estado,
               e.nome AS nome_especie, r.nome AS nome_raca,
               u.nome AS nome_tutor, u.telefone AS telefone_tutor
        FROM solicitacao_adocao s
        JOIN pet p ON s.id_pet = p.id_pet
        LEFT JOIN especie e ON p.id_especie = e.id_especie
        LEFT JOIN raca r ON p.id_raca = r.id_raca
        JOIN usuario u ON p.id_usuario_tutor = u.id_usuario
        WHERE s.id_usuario_adotante = :id
        ORDER BY s.id_solicitacao DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $idUsuario]);
$minhasCandidaturas = $stmt->fetchAll();

$rootPath = '../';
$extraCss = ['css/pets-grid.css'];
include __DIR__ . '/../includes/header.php';
?>

<div class="container-petfy" style="padding-top: 30px; padding-bottom: 60px;">
    
    <div style="margin-bottom: 25px;">
        <span class="catalog-badge"><i class="fas fa-heart"></i> Minhas Candidaturas</span>
        <h1 style="font-family:var(--font-heading); color:var(--brand-teal-darker); font-size:2.2rem; margin-top:4px;">Minhas Adoções</h1>
        <p style="color:var(--text-muted); margin:0;">Acompanhe o andamento dos animais que você manifestou interesse em adotar.</p>
    </div>

    <?php if (!empty($minhasCandidaturas)): ?>
        <div style="display:flex; flex-direction:column; gap:18px;">
            <?php foreach ($minhasCandidaturas as $item): ?>
                <div style="background:white; border-radius:18px; padding:22px; box-shadow:var(--shadow-xs); border:1.5px solid #e2e8f0; display:flex; flex-wrap:wrap; gap:20px; align-items:center; justify-content:space-between;">
                    
                    <div style="display:flex; gap:16px; align-items:center;">
                        <img src="<?= htmlspecialchars(getPetPhotoUrl($item['foto_pet'], $rootPath, $item['nome_especie'] ?? 'Cachorro')) ?>" 
                             alt="<?= htmlspecialchars($item['nome_pet']) ?>" 
                             style="width:85px; height:85px; border-radius:14px; object-fit:cover;"
                             onerror="this.onerror=null; this.src='<?= $rootPath ?>img/default-pet.svg';">
                        
                        <div>
                            <h3 style="margin:0; font-size:1.25rem; color:var(--brand-teal-darker); font-family:var(--font-heading);">
                                <?= htmlspecialchars($item['nome_pet']) ?>
                            </h3>
                            <p style="margin:4px 0; font-size:0.88rem; color:#4b5563;">
                                <?= htmlspecialchars($item['nome_especie'] ?? 'Pet') ?> • <?= htmlspecialchars($item['nome_raca'] ?? 'SRD') ?> • 
                                <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($item['cidade']) ?> - <?= htmlspecialchars($item['estado']) ?>
                            </p>
                            <p style="margin:4px 0 0 0; font-size:0.85rem; color:#6b7280;">
                                Enviada em: <?= date('d/m/Y \à\s H:i', strtotime($item['data_solicitacao'])) ?>
                            </p>
                        </div>
                    </div>

                    <!-- Status e Contato -->
                    <div style="display:flex; flex-direction:column; gap:8px; align-items:flex-end;">
                        <?php
                            $badgeClass = 'bg-gray';
                            $status = $item['status'];
                            if ($status === 'Aprovada') $badgeColor = '#059669; background:#ecfdf5;';
                            elseif ($status === 'Recusada') $badgeColor = '#dc2626; background:#fef2f2;';
                            elseif ($status === 'Em Análise') $badgeColor = '#d97706; background:#fffbeb;';
                            else $badgeColor = '#2563eb; background:#eff6ff;';
                        ?>
                        <span style="font-weight:800; font-size:0.85rem; padding:6px 14px; border-radius:20px; color:<?= $badgeColor ?>">
                            <i class="fas fa-circle" style="font-size:0.6rem;"></i> <?= htmlspecialchars($status) ?>
                        </span>

                        <div style="display:flex; gap:10px; align-items:center;">
                            <?php if (!empty($item['telefone_tutor'])): ?>
                                <?php 
                                    $telTutorLimp = preg_replace('/\D/', '', (string)$item['telefone_tutor']);
                                    if (strlen($telTutorLimp) <= 11 && !str_starts_with($telTutorLimp, '55')) {
                                        $telTutorLimp = '55' . $telTutorLimp;
                                    }
                                ?>
                                <a href="https://api.whatsapp.com/send?phone=<?= $telTutorLimp ?>" 
                                   target="_blank" 
                                   rel="noopener" 
                                   style="color:#059669; font-size:0.9rem; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:5px;">
                                    <i class="fab fa-whatsapp"></i> Falar com Tutor
                                </a>
                            <?php endif; ?>

                            <a href="pet-detalhes.php?id=<?= $item['id_pet'] ?>" class="btn-petfy-outline" style="padding:6px 14px; font-size:0.85rem;">
                                Ver Pet
                            </a>
                        </div>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-results-box">
            <i class="fas fa-heart-broken" style="font-size: 3rem; color: #9ca3af; margin-bottom: 12px;"></i>
            <h3>Você ainda não enviou nenhum pedido de adoção</h3>
            <p>Conheça os animais disponíveis e envie uma mensagem para o tutor responsável quando se apaixonar por algum!</p>
            <a href="pets.php" class="btn-petfy-primary" style="margin-top:15px; display:inline-block;">Conhecer Pets para Adoção</a>
        </div>
    <?php endif; ?>

</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
