<?php
/**
 * ============================================================
 * Petfy – FRONT-END: Detalhes do Pet (Perfil Completo para Adoção)
 * Conhecer o animal, falar no WhatsApp ou solicitar adoção
 * ============================================================
 */

require_once __DIR__ . '/../../back-end/config/database.php';
require_once __DIR__ . '/../../back-end/auth/verifica_auth.php';

$pdo = db();
$idPet = (int)($_GET['id'] ?? 0);

if ($idPet <= 0) {
    setFlashMessage('error', 'Animal não especificado.');
    header('Location: pets.php');
    exit;
}

$sql = "SELECT p.*, e.nome AS nome_especie, r.nome AS nome_raca, u.nome AS nome_tutor_usuario, u.telefone AS telefone_tutor_usuario, u.email AS email_tutor_usuario
        FROM pet p
        LEFT JOIN especie e ON p.id_especie = e.id_especie
        LEFT JOIN raca r ON p.id_raca = r.id_raca
        LEFT JOIN usuario u ON p.id_usuario_tutor = u.id_usuario
        WHERE p.id_pet = :id
        LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $idPet]);
$pet = $stmt->fetch();

if (!$pet) {
    setFlashMessage('error', 'Animal não encontrado no sistema.');
    header('Location: pets.php');
    exit;
}

$pageTitle = 'Conhecer ' . $pet['nome'] . ' para Adoção';
$usuarioLogado = getLoggedUser();
$isTutor = $usuarioLogado && ($usuarioLogado['id'] == $pet['id_usuario_tutor'] || $usuarioLogado['tipo'] === 'admin');

$tagsPersonalidade = !empty($pet['personalidades']) ? array_map('trim', explode(',', $pet['personalidades'])) : [];

// Busca pets semelhantes disponíveis
$stmtSimilares = $pdo->prepare("SELECT p.*, e.nome AS nome_especie, r.nome AS nome_raca 
                                FROM pet p
                                LEFT JOIN especie e ON p.id_especie = e.id_especie
                                LEFT JOIN raca r ON p.id_raca = r.id_raca
                                WHERE p.id_especie = :especie AND p.id_pet != :id AND p.status = 'Disponível'
                                ORDER BY p.id_pet DESC LIMIT 3");
$stmtSimilares->execute([':especie' => $pet['id_especie'], ':id' => $idPet]);
$petsSimilares = $stmtSimilares->fetchAll();

// Contato para WhatsApp
$telefoneContato = !empty($pet['contato_telefone']) ? $pet['contato_telefone'] : ($pet['telefone_tutor_usuario'] ?? '');
$telefoneWhatsapp = preg_replace('/\D/', '', (string)$telefoneContato);
if (!empty($telefoneWhatsapp)) {
    if (strlen($telefoneWhatsapp) <= 11 && !str_starts_with($telefoneWhatsapp, '55')) {
        $telefoneWhatsapp = '55' . $telefoneWhatsapp;
    }
}
$mensagemWhatsapp = rawurlencode("Olá! Vi o pet " . $pet['nome'] . " anunciado no Petfy e gostaria de mais informações sobre o processo de adoção!");

$rootPath = '../';
$extraCss = ['css/pets-grid.css'];
include __DIR__ . '/../includes/header.php';
?>

<div class="pet-details-container">
    
    <a href="pets.php" class="btn-back-catalog">
        <i class="fas fa-arrow-left"></i> Voltar para a lista de pets
    </a>

    <div class="pet-details-card">
        
        <!-- Coluna da Foto com Fallback -->
        <div class="pet-details-photo-col">
            <img src="<?= htmlspecialchars(getPetPhotoUrl($pet['foto_url'], $rootPath, $pet['nome_especie'] ?? 'Cachorro')) ?>" 
                 alt="<?= htmlspecialchars($pet['nome']) ?>" 
                 onerror="this.onerror=null; this.src='<?= $rootPath ?>img/default-pet.svg';">
            
            <span class="pet-card-badge-status <?= strtolower($pet['status']) === 'adotado' ? 'adotado' : (strtolower($pet['status']) === 'em processo' ? 'processo' : '') ?>" style="top: 20px; right: 20px; font-size: 0.95rem; padding: 7px 18px;">
                <?= htmlspecialchars($pet['status']) ?>
            </span>
        </div>

        <!-- Coluna de Informações e Adoção -->
        <div class="pet-details-content-col">
            
            <div class="details-header-row">
                <div>
                    <h1 class="details-pet-name"><?= htmlspecialchars($pet['nome']) ?></h1>
                    <p class="details-pet-breed">
                        <i class="fas fa-paw" style="color:var(--brand-teal);"></i>
                        <?= htmlspecialchars($pet['nome_especie'] ?? 'Pet') ?> • <?= htmlspecialchars($pet['nome_raca'] ?? 'Sem raça definida') ?>
                    </p>
                </div>
                <span class="pet-card-sex <?= strtolower($pet['sexo']) === 'macho' ? 'macho' : 'femea' ?>" style="font-size: 2rem;">
                    <i class="fas <?= strtolower($pet['sexo']) === 'macho' ? 'fa-mars' : 'fa-venus' ?>"></i>
                </span>
            </div>

            <!-- Ficha Técnica Rápida -->
            <div class="details-info-grid">
                <div class="info-chip-card">
                    <small>Sexo</small>
                    <strong><?= htmlspecialchars($pet['sexo']) ?></strong>
                </div>
                <div class="info-chip-card">
                    <small>Porte</small>
                    <strong>Porte <?= htmlspecialchars($pet['porte']) ?></strong>
                </div>
                <div class="info-chip-card">
                    <small>Idade Estimada</small>
                    <strong><?= htmlspecialchars($pet['idade']) ?></strong>
                </div>
                <div class="info-chip-card">
                    <small>Localização</small>
                    <strong><?= htmlspecialchars($pet['cidade']) ?> - <?= htmlspecialchars($pet['estado']) ?></strong>
                </div>
            </div>

            <!-- Cuidados de Saúde -->
            <div class="details-health-box">
                <h4><i class="fas fa-shield-alt"></i> Condições de Saúde</h4>
                <div class="health-checks-row">
                    <span class="health-badge <?= $pet['vacinado'] ? 'checked' : 'unchecked' ?>">
                        <i class="fas <?= $pet['vacinado'] ? 'fa-check-circle' : 'fa-times-circle' ?>"></i>
                        <?= $pet['vacinado'] ? 'Vacinado' : 'Vacinação pendente' ?>
                    </span>
                    <span class="health-badge <?= $pet['castrado'] ? 'checked' : 'unchecked' ?>">
                        <i class="fas <?= $pet['castrado'] ? 'fa-check-circle' : 'fa-times-circle' ?>"></i>
                        <?= $pet['castrado'] ? 'Castrado' : 'Não castrado' ?>
                    </span>
                    <span class="health-badge <?= $pet['vermifugado'] ? 'checked' : 'unchecked' ?>">
                        <i class="fas <?= $pet['vermifugado'] ? 'fa-check-circle' : 'fa-times-circle' ?>"></i>
                        <?= $pet['vermifugado'] ? 'Vermifugado' : 'Vermifugação pendente' ?>
                    </span>
                </div>
                <?php if (!empty($pet['necessidades_especiais'])): ?>
                    <p style="margin-top: 10px; font-size: 0.88rem; color: #92400e; background: #fef3c7; padding: 8px 12px; border-radius: 8px;">
                        <strong><i class="fas fa-heartbeat"></i> Cuidados especiais:</strong> <?= htmlspecialchars($pet['necessidades_especiais']) ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- Temperamento / Personalidades -->
            <?php if (!empty($tagsPersonalidade)): ?>
                <div style="margin-top: 18px;">
                    <h4 style="font-size: 0.92rem; color: var(--text-muted); font-weight: 700; margin-bottom: 8px;">
                        <i class="fas fa-smile"></i> Personalidade & Temperamento
                    </h4>
                    <div style="display:flex; flex-wrap:wrap; gap:8px;">
                        <?php foreach ($tagsPersonalidade as $tag): ?>
                            <span class="tag-chip"><?= htmlspecialchars($tag) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- História / Descrição -->
            <?php if (!empty($pet['descricao'])): ?>
                <div style="margin-top: 20px;">
                    <h4 style="font-size: 1.05rem; color: var(--brand-teal-darker); font-weight: 800; margin-bottom: 8px;">
                        Sobre <?= htmlspecialchars($pet['nome']) ?>
                    </h4>
                    <p style="color: var(--text-secondary); line-height: 1.6; white-space: pre-line; background: #f8fafc; padding: 16px; border-radius: 14px; border: 1px solid #e2e8f0;">
                        <?= htmlspecialchars($pet['descricao']) ?>
                    </p>
                </div>
            <?php endif; ?>

            <!-- Ações de Adoção e Contato -->
            <div class="adoption-cta-box" style="margin-top: 25px; padding-top: 20px; border-top: 2px dashed #e2e8f0;">
                <?php if ($pet['status'] === 'Adotado'): ?>
                    <div class="alert-adopted-box" style="background:#ecfdf5; border:1px solid #a7f3d0; padding:15px; border-radius:12px; text-align:center; color:#065f46;">
                        <i class="fas fa-check-circle" style="font-size:1.5rem;"></i>
                        <h4 style="margin:5px 0;">Este pet já encontrou um lar amoroso!</h4>
                        <p style="margin:0; font-size:0.9rem;">Veja outros companheiros que ainda estão aguardando adoção.</p>
                        <a href="pets.php" class="btn-petfy-primary" style="margin-top:10px; display:inline-block;">Explorar Outros Pets</a>
                    </div>
                <?php else: ?>
                    <div class="contact-tutor-box">
                        <h3 style="font-size: 1.15rem; color: var(--brand-teal-darker); font-weight: 800; margin-bottom: 4px;">
                            Interessado(a) em adotar <?= htmlspecialchars($pet['nome']) ?>?
                        </h3>
                        <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 16px;">
                            Tutor responsável: <strong><?= htmlspecialchars($pet['contato_nome'] ?: ($pet['nome_tutor_usuario'] ?? 'Protetor Independente')) ?></strong>
                        </p>

                        <div style="display:flex; gap:12px; flex-wrap:wrap;">
                            <!-- Botão WhatsApp Direto -->
                            <?php if (!empty($telefoneWhatsapp)): ?>
                                <a href="https://api.whatsapp.com/send?phone=<?= $telefoneWhatsapp ?>&text=<?= $mensagemWhatsapp ?>" 
                                   target="_blank" 
                                   rel="noopener" 
                                   class="btn-whatsapp-direct" 
                                   style="background: #25d366; color: white; padding: 13px 24px; border-radius: var(--radius-full); font-weight: 800; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; box-shadow: 0 4px 14px rgba(37, 211, 102, 0.3);">
                                    <i class="fab fa-whatsapp" style="font-size: 1.3rem;"></i> Conversar no WhatsApp
                                </a>
                            <?php endif; ?>

                            <!-- Botão Solicitar Adoção (Modal) -->
                            <button type="button" 
                                    class="btn-petfy-primary" 
                                    onclick="abrirModalAdocao()" 
                                    style="padding: 13px 26px; border-radius: var(--radius-full); font-weight: 800; display: inline-flex; align-items: center; gap: 8px;">
                                <i class="fas fa-heart"></i> Quero Adotar
                            </button>

                            <?php if ($isTutor): ?>
                                <a href="meus-pets.php" class="btn-petfy-outline" style="padding: 13px 20px; border-radius: var(--radius-full); display: inline-flex; align-items: center; gap: 8px;">
                                    <i class="fas fa-edit"></i> Gerenciar no Painel
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

        </div>

    </div>

    <!-- Seção de Pets Similares -->
    <?php if (!empty($petsSimilares)): ?>
        <div style="margin-top: 50px;">
            <h3 style="font-family: var(--font-heading); color: var(--brand-teal-darker); font-size: 1.6rem; font-weight: 800; margin-bottom: 20px;">
                Outros companheiros disponíveis para você conhecer
            </h3>
            <div class="pets-cards-grid" style="grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));">
                <?php foreach ($petsSimilares as $sim): ?>
                    <div class="pet-card">
                        <div class="pet-card-image-wrap">
                            <img src="<?= htmlspecialchars(getPetPhotoUrl($sim['foto_url'], $rootPath, $sim['nome_especie'] ?? 'Cachorro')) ?>" 
                                 alt="<?= htmlspecialchars($sim['nome']) ?>" 
                                 loading="lazy" 
                                 onerror="this.onerror=null; this.src='<?= $rootPath ?>img/default-pet.svg';">
                            <span class="pet-card-badge-status"><?= htmlspecialchars($sim['status']) ?></span>
                        </div>
                        <div class="pet-card-body">
                            <h4 class="pet-card-name"><?= htmlspecialchars($sim['nome']) ?></h4>
                            <p class="pet-card-breed"><?= htmlspecialchars($sim['nome_raca'] ?? 'SRD') ?></p>
                            <a href="pet-detalhes.php?id=<?= $sim['id_pet'] ?>" class="btn-card-adopt" style="margin-top:10px;">
                                Conhecer <?= htmlspecialchars($sim['nome']) ?> →
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

</div>

<!-- Modal de Solicitação de Adoção -->
<div class="modal-overlay" id="modalAdocao" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; align-items:center; justify-content:center;">
    <div class="modal-content" style="background:white; border-radius:20px; max-width:500px; width:90%; padding:30px; position:relative; box-shadow:0 20px 40px rgba(0,0,0,0.2);">
        <button type="button" onclick="fecharModalAdocao()" style="position:absolute; top:15px; right:20px; background:none; border:none; font-size:1.6rem; cursor:pointer; color:#64748b;">&times;</button>
        
        <div style="text-align:center; margin-bottom:20px;">
            <div style="width:60px; height:60px; border-radius:50%; background:#ecfdf5; color:#059669; font-size:1.8rem; display:flex; align-items:center; justify-content:center; margin:0 auto 10px auto;">
                <i class="fas fa-paw"></i>
            </div>
            <h3 style="font-family:var(--font-heading); color:var(--brand-teal-darker); margin:0;">Adotar <?= htmlspecialchars($pet['nome']) ?></h3>
            <p style="color:var(--text-muted); font-size:0.9rem; margin-top:4px;">Envie sua mensagem para o tutor responsável iniciar a análise de adoção.</p>
        </div>

        <?php if ($usuarioLogado): ?>
            <form action="../../back-end/adocao/processa_solicitacao.php" method="POST">
                <input type="hidden" name="id_pet" value="<?= $pet['id_pet'] ?>">
                
                <div style="margin-bottom:14px;">
                    <label style="display:block; font-size:0.85rem; font-weight:700; color:#374151; margin-bottom:4px;">Seu Telefone / WhatsApp para retorno: <span style="color:#ef4444;">*</span></label>
                    <input type="tel" name="telefone_contato" value="<?= htmlspecialchars($usuarioLogado['telefone'] ?? '') ?>" required style="width:100%; padding:10px 14px; border:1.5px solid #d1d5db; border-radius:10px;" placeholder="(00) 90000-0000" oninput="formatarTelefoneBR(this)">
                </div>

                <div style="margin-bottom:18px;">
                    <label style="display:block; font-size:0.85rem; font-weight:700; color:#374151; margin-bottom:4px;">Por que você quer adotar o <?= htmlspecialchars($pet['nome']) ?>? <span style="color:#ef4444;">*</span></label>
                    <textarea name="mensagem_motivo" rows="4" required style="width:100%; padding:10px 14px; border:1.5px solid #d1d5db; border-radius:10px; resize:none;" placeholder="Conte um pouco sobre você, seu espaço em casa e o carinho que poderá oferecer..."></textarea>
                </div>

                <div style="display:flex; gap:10px; justify-content:flex-end;">
                    <button type="button" onclick="fecharModalAdocao()" style="padding:10px 20px; border:1px solid #d1d5db; background:white; border-radius:10px; font-weight:600; cursor:pointer;">Cancelar</button>
                    <button type="submit" class="btn-petfy-primary" style="padding:10px 24px; border-radius:10px; font-weight:700; cursor:pointer;">Enviar Candidatura</button>
                </div>
            </form>
        <?php else: ?>
            <div style="text-align:center; padding:10px 0;">
                <p style="color:#4b5563; margin-bottom:16px;">Para enviar sua candidatura de adoção com segurança, faça login ou crie sua conta gratuita no Petfy.</p>
                <div style="display:flex; gap:10px; justify-content:center;">
                    <a href="../login.php" class="btn-petfy-outline">Fazer Login</a>
                    <a href="../cadastro.php" class="btn-petfy-primary">Criar Conta Nova</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function abrirModalAdocao() {
    const modal = document.getElementById('modalAdocao');
    if (modal) modal.style.display = 'flex';
}
function fecharModalAdocao() {
    const modal = document.getElementById('modalAdocao');
    if (modal) modal.style.display = 'none';
}
window.addEventListener('click', (e) => {
    const modal = document.getElementById('modalAdocao');
    if (e.target === modal) fecharModalAdocao();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
