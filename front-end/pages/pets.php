<?php
/**
 * ============================================================
 * Petfy – FRONT-END: Área Conhecer os Pets (Catálogo Completo)
 * Toda Patinha merece um lar!
 * ============================================================
 */

$pageTitle = 'Conhecer os Pets para Adoção';
require_once __DIR__ . '/../../back-end/config/database.php';
require_once __DIR__ . '/../../back-end/auth/verifica_auth.php';

$pdo = db();

$busca    = trim($_GET['q'] ?? '');
$especie  = !empty($_GET['especie']) ? (int)$_GET['especie'] : null;
$raca     = !empty($_GET['raca']) ? (int)$_GET['raca'] : null;
$sexo     = in_array($_GET['sexo'] ?? '', ['Macho', 'Fêmea']) ? $_GET['sexo'] : null;
$porte    = in_array($_GET['porte'] ?? '', ['P', 'M', 'G']) ? $_GET['porte'] : null;
$cidade   = trim($_GET['cidade'] ?? '');

$whereClauses = ["1=1"];
$params = [];

if (!empty($busca)) {
    $whereClauses[] = "(p.nome LIKE :busca OR r.nome LIKE :busca OR p.cidade LIKE :busca OR p.descricao LIKE :busca)";
    $params[':busca'] = '%' . $busca . '%';
}

if ($especie) {
    $whereClauses[] = "p.id_especie = :especie";
    $params[':especie'] = $especie;
}

if ($raca) {
    $whereClauses[] = "p.id_raca = :raca";
    $params[':raca'] = $raca;
}

if ($sexo) {
    $whereClauses[] = "p.sexo = :sexo";
    $params[':sexo'] = $sexo;
}

if ($porte) {
    $whereClauses[] = "p.porte = :porte";
    $params[':porte'] = $porte;
}

if (!empty($cidade)) {
    $whereClauses[] = "p.cidade LIKE :cidade";
    $params[':cidade'] = '%' . $cidade . '%';
}

$whereSql = implode(' AND ', $whereClauses);

$sql = "SELECT p.*, e.nome AS nome_especie, r.nome AS nome_raca
        FROM pet p
        LEFT JOIN especie e ON p.id_especie = e.id_especie
        LEFT JOIN raca r ON p.id_raca = r.id_raca
        WHERE {$whereSql}
        ORDER BY (p.status = 'Disponível') DESC, p.id_pet DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$pets = $stmt->fetchAll();

$especies = $pdo->query("SELECT * FROM especie ORDER BY id_especie ASC")->fetchAll();
$racas = $pdo->query("SELECT * FROM raca ORDER BY nome ASC")->fetchAll();

$rootPath = '../';
$extraCss = ['css/pets-grid.css'];
include __DIR__ . '/../includes/header.php';
?>

<!-- Cabeçalho da Página de Conhecer os Pets -->
<div class="pets-catalog-header">
    <div class="container-petfy">
        <span class="catalog-badge"><i class="fas fa-paw"></i> Toda Patinha merece um lar</span>
        <h1>Conheça Nossos Amigos de Quatro Patas</h1>
        <p>Centenas de cães e gatos resgatados estão esperando por uma família amorosa e responsável. Use os filtros abaixo para encontrar seu novo companheiro!</p>
        
        <!-- Atalhos Rápidos de Categorias -->
        <div class="quick-category-pills" style="display:flex; justify-content:center; gap:10px; margin-top:18px; flex-wrap:wrap;">
            <a href="pets.php" class="chip-filter <?= empty($_GET) ? 'active' : '' ?>">
                <i class="fas fa-th-large"></i> Todos os Pets
            </a>
            <a href="pets.php?especie=1" class="chip-filter <?= ($especie === 1) ? 'active' : '' ?>">
                <i class="fas fa-dog"></i> Cães
            </a>
            <a href="pets.php?especie=2" class="chip-filter <?= ($especie === 2) ? 'active' : '' ?>">
                <i class="fas fa-cat"></i> Gatos
            </a>
            <a href="pets.php?porte=P" class="chip-filter <?= ($porte === 'P') ? 'active' : '' ?>">
                <i class="fas fa-building"></i> P/ Apartamento (P)
            </a>
            <a href="cadastrar-pet.php" class="chip-filter" style="background:#059669; color:white; border-color:#059669;">
                <i class="fas fa-plus-circle"></i> Cadastrar Pet para Adoção
            </a>
        </div>
    </div>
</div>

<!-- Layout com Filtros e Catálogo -->
<div class="pets-catalog-layout">
    
    <!-- Barra Lateral de Filtros -->
    <aside class="filters-sidebar">
        <div class="filters-header">
            <h3><i class="fas fa-sliders-h"></i> Filtrar Busca</h3>
            <?php if (!empty($_GET)): ?>
                <a href="pets.php" class="btn-clear-filters" title="Limpar todos os filtros">Limpar Filtros</a>
            <?php endif; ?>
        </div>

        <form action="pets.php" method="GET" id="filterForm">
            <div class="filter-group">
                <label for="f-busca">Palavra-chave</label>
                <input type="text" id="f-busca" name="q" class="filter-input" placeholder="Nome, raça ou cidade..." value="<?= htmlspecialchars($busca) ?>">
            </div>

            <div class="filter-group">
                <label for="f-especie">Espécie</label>
                <select id="f-especie" name="especie" class="filter-select">
                    <option value="">Todas as espécies</option>
                    <?php foreach ($especies as $esp): ?>
                        <option value="<?= $esp['id_especie'] ?>" <?= $especie == $esp['id_especie'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($esp['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <label for="f-porte">Porte do Animal</label>
                <select id="f-porte" name="porte" class="filter-select">
                    <option value="">Todos os portes</option>
                    <option value="P" <?= $porte === 'P' ? 'selected' : '' ?>>Pequeno (Até 10kg)</option>
                    <option value="M" <?= $porte === 'M' ? 'selected' : '' ?>>Médio (11kg a 25kg)</option>
                    <option value="G" <?= $porte === 'G' ? 'selected' : '' ?>>Grande (Mais de 25kg)</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="f-sexo">Sexo</label>
                <select id="f-sexo" name="sexo" class="filter-select">
                    <option value="">Ambos</option>
                    <option value="Macho" <?= $sexo === 'Macho' ? 'selected' : '' ?>>Macho</option>
                    <option value="Fêmea" <?= $sexo === 'Fêmea' ? 'selected' : '' ?>>Fêmea</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="f-cidade">Cidade</label>
                <input type="text" id="f-cidade" name="cidade" class="filter-input" placeholder="Ex: São Paulo" value="<?= htmlspecialchars($cidade) ?>">
            </div>

            <button type="submit" class="btn-filter-submit">
                <i class="fas fa-search"></i> Aplicar Filtros
            </button>
            
            <a href="cadastrar-pet.php" class="btn-petfy-outline" style="width:100%; text-align:center; margin-top:10px; display:block; padding:10px;">
                <i class="fas fa-plus"></i> Divulgar um Resgatado
            </a>
        </form>
    </aside>

    <!-- Grid de Resultados dos Animais -->
    <section class="pets-results-area">
        
        <div class="results-header-info">
            <span class="results-count">
                Exibindo <strong><?= count($pets) ?></strong> pet(s) para adoção
            </span>
        </div>

        <?php if (!empty($pets)): ?>
            <div class="pets-cards-grid">
                <?php foreach ($pets as $pet): ?>
                    <div class="pet-card">
                        <div class="pet-card-image-wrap">
                            <img src="<?= htmlspecialchars(getPetPhotoUrl($pet['foto_url'], $rootPath, $pet['nome_especie'] ?? 'Cachorro')) ?>" 
                                 alt="<?= htmlspecialchars($pet['nome']) ?>" 
                                 loading="lazy" 
                                 onerror="this.onerror=null; this.src='<?= $rootPath ?>img/default-pet.svg';">
                            
                            <!-- Botão de Favoritar -->
                            <button type="button" class="pet-card-fav-btn" data-id="<?= $pet['id_pet'] ?>" onclick="toggleFavorito(this, <?= $pet['id_pet'] ?>)" title="Salvar nos favoritos">
                                <i class="fas fa-heart"></i>
                            </button>

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
                                <span class="pet-card-sex <?= strtolower($pet['sexo']) === 'macho' ? 'macho' : 'femea' ?>" title="<?= htmlspecialchars($pet['sexo']) ?>">
                                    <i class="fas <?= strtolower($pet['sexo']) === 'macho' ? 'fa-mars' : 'fa-venus' ?>"></i>
                                </span>
                            </div>

                            <p class="pet-card-breed">
                                <?= htmlspecialchars($pet['nome_raca'] ?? 'Sem raça definida (SRD)') ?>
                            </p>

                            <div class="pet-card-attributes">
                                <span class="pet-attr-pill">Porte <?= htmlspecialchars($pet['porte']) ?></span>
                                <span class="pet-attr-pill"><?= htmlspecialchars($pet['idade']) ?></span>
                                <?php if ($pet['castrado']): ?>
                                    <span class="pet-attr-pill" style="background:#ecfdf5; color:#065f46;" title="Castrado"><i class="fas fa-check"></i> Castrado</span>
                                <?php endif; ?>
                                <?php if ($pet['vacinado']): ?>
                                    <span class="pet-attr-pill" style="background:#eff6ff; color:#1e40af;" title="Vacinado"><i class="fas fa-syringe"></i> Vacinado</span>
                                <?php endif; ?>
                            </div>

                            <div class="pet-card-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?= htmlspecialchars($pet['cidade']) ?> - <?= htmlspecialchars($pet['estado']) ?></span>
                            </div>

                            <!-- Botão Destacado: Conhecer o Pet -->
                            <a href="pet-detalhes.php?id=<?= $pet['id_pet'] ?>" class="btn-card-adopt">
                                Conhecer <?= htmlspecialchars($pet['nome']) ?> <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-results-box">
                <i class="fas fa-search" style="font-size: 3rem; color: var(--brand-teal); margin-bottom: 12px;"></i>
                <h3>Nenhum animal encontrado com esses filtros</h3>
                <p>Tente ajustar ou limpar seus filtros de pesquisa para visualizar outros amigos disponíveis.</p>
                <div style="display:flex; gap:12px; justify-content:center; margin-top:15px; flex-wrap:wrap;">
                    <a href="pets.php" class="btn-petfy-dark">Ver Todos os Pets</a>
                    <a href="cadastrar-pet.php" class="btn-petfy-outline">Cadastrar um Pet para Adoção</a>
                </div>
            </div>
        <?php endif; ?>

    </section>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
