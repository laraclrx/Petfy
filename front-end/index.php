<?php
/**
 * ============================================================
 * Petfy – FRONT-END: Página Inicial (Home)
 * Toda Patinha merece um lar!
 * ============================================================
 */

$pageTitle = 'Petfy – Toda Patinha merece um lar!';
require_once __DIR__ . '/../back-end/config/database.php';
require_once __DIR__ . '/../back-end/auth/verifica_auth.php';

$pdo = db();

// Total de pets disponíveis
$stmtCount = $pdo->query("SELECT COUNT(*) AS total FROM pet WHERE status = 'Disponível'");
$totalDisponiveis = (int)$stmtCount->fetchColumn();

// Pets em destaque para a vitrine
$sqlDestaques = "SELECT p.*, e.nome AS nome_especie, r.nome AS nome_raca
                 FROM pet p
                 LEFT JOIN especie e ON p.id_especie = e.id_especie
                 LEFT JOIN raca r ON p.id_raca = r.id_raca
                 WHERE p.status = 'Disponível'
                 ORDER BY p.id_pet DESC
                 LIMIT 6";
$stmtDestaques = $pdo->query($sqlDestaques);
$petsDestaque = $stmtDestaques->fetchAll();

$especiesHero = $pdo->query("SELECT * FROM especie ORDER BY id_especie ASC")->fetchAll();

$rootPath = '';
$extraCss = ['css/pets-grid.css'];
include __DIR__ . '/includes/header.php';
?>

<!-- 1. HERO SECTION PRINCIPAL -->
<section class="hero-modern">
    <div class="hero-bg-shapes">
        <div class="hero-blob hero-blob-1"></div>
        <div class="hero-blob hero-blob-2"></div>
    </div>

    <div class="hero-inner">
        <div class="hero-text-col">
            <span class="hero-tag-badge">
                <i class="fas fa-heart" style="color: #ef4444;"></i> Adoção 100% Responsável & Amor Animal
            </span>
            
            <h1 class="hero-title">
                Toda Patinha merece<br>
                <span class="highlight-text">um lar com amor!</span>
            </h1>
            
            <p class="hero-subtitle">
                Conectamos animais resgatados a tutores conscientes de forma simples, segura e acolhedora. Dê uma segunda chance a um amigo de quatro patas!
            </p>

            <!-- Botões Principais em Destaque -->
            <div class="hero-cta-group">
                <a href="pages/pets.php" class="btn-petfy-dark" style="font-size:1.1rem; padding: 14px 28px;">
                    <i class="fas fa-paw"></i> Conhecer os Pets
                </a>
                <a href="pages/cadastrar-pet.php" class="btn-petfy-outline" style="background: rgba(255,255,255,0.9); font-size:1.1rem; padding: 14px 28px;">
                    <i class="fas fa-plus-circle"></i> Cadastrar Pet
                </a>
            </div>

            <!-- Busca Rápida -->
            <div class="hero-quick-search-card">
                <form action="pages/pets.php" method="GET">
                    <div class="search-field-group">
                        <label for="hero-especie"><i class="fas fa-paw"></i> Espécie</label>
                        <select name="especie" id="hero-especie">
                            <option value="">Todas</option>
                            <?php foreach ($especiesHero as $esp): ?>
                                <option value="<?= $esp['id_especie'] ?>"><?= htmlspecialchars($esp['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="search-field-group">
                        <label for="hero-porte"><i class="fas fa-weight-hanging"></i> Porte</label>
                        <select name="porte" id="hero-porte">
                            <option value="">Todos</option>
                            <option value="P">Pequeno (P)</option>
                            <option value="M">Médio (M)</option>
                            <option value="G">Grande (G)</option>
                        </select>
                    </div>

                    <div class="search-field-group">
                        <label for="hero-cidade"><i class="fas fa-map-marker-alt"></i> Cidade</label>
                        <input type="text" name="cidade" id="hero-cidade" placeholder="Ex: São Paulo">
                    </div>

                    <button type="submit" class="btn-search-hero-submit">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </form>
            </div>
        </div>

        <div class="hero-image-col">
            <div class="hero-main-photo-frame">
                <img src="https://images.unsplash.com/photo-1537151608804-ea2f1416d7cd?auto=format&fit=crop&w=700&q=80" 
                     alt="Cão e Gato felizes" 
                     onerror="this.onerror=null; this.src='img/default-pet.svg';">
            </div>

            <!-- Badges Flutuantes -->
            <div class="floating-badge floating-badge-1">
                <div class="floating-badge-icon badge-icon-heart">
                    <i class="fas fa-heart"></i>
                </div>
                <div class="floating-badge-text">
                    <strong>+1.200 Adoções</strong>
                    <small>Finais felizes realizados</small>
                </div>
            </div>

            <div class="floating-badge floating-badge-2">
                <div class="floating-badge-icon badge-icon-check">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="floating-badge-text">
                    <strong>100% Gratuito</strong>
                    <small>Apoio à causa animal</small>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 2. NÚMEROS E ESTATÍSTICAS -->
<div class="impact-stats-section">
    <div class="impact-stats-grid">
        <div class="stat-card-modern">
            <div class="stat-icon-wrap"><i class="fas fa-paw"></i></div>
            <div class="stat-info-wrap">
                <strong><?= max($totalDisponiveis, count($petsDestaque)) ?></strong>
                <span>Pets Disponíveis Agora</span>
            </div>
        </div>

        <div class="stat-card-modern">
            <div class="stat-icon-wrap"><i class="fas fa-home"></i></div>
            <div class="stat-info-wrap">
                <strong>1.280+</strong>
                <span>Lares Amorosos Formados</span>
            </div>
        </div>

        <div class="stat-card-modern">
            <div class="stat-icon-wrap"><i class="fas fa-heartbeat"></i></div>
            <div class="stat-info-wrap">
                <strong>98%</strong>
                <span>Taxa de Adaptação Positiva</span>
            </div>
        </div>

        <div class="stat-card-modern">
            <div class="stat-icon-wrap"><i class="fas fa-map-marked-alt"></i></div>
            <div class="stat-info-wrap">
                <strong>45+</strong>
                <span>Cidades Conectadas</span>
            </div>
        </div>
    </div>
</div>

<!-- 3. CATEGORIAS DE NAVEGAÇÃO RÁPIDA -->
<div class="container-petfy" style="padding-top: 30px;">
    <div style="text-align: center; margin-bottom: 28px;">
        <span style="color: var(--brand-teal); font-weight: 800; text-transform: uppercase; font-size: 0.82rem; letter-spacing: 1px;">Navegue por Categoria</span>
        <h2 style="font-family: var(--font-heading); color: var(--brand-teal-darker); font-size: 2.1rem; font-weight: 900; margin-top: 4px;">Encontre o Perfil que Combina com Você</h2>
    </div>

    <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 18px;">
        <a href="pages/pets.php?especie=1" style="background: white; padding: 22px 20px; border-radius: 22px; box-shadow: var(--shadow-xs); width: 160px; text-align: center; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); border: 1.5px solid var(--border-color); display: flex; flex-direction: column; align-items: center;" onmouseover="this.style.transform='translateY(-6px)'; this.style.borderColor='var(--brand-teal)';" onmouseout="this.style.transform='none'; this.style.borderColor='var(--border-color)';">
            <div style="width: 60px; height: 60px; border-radius: 50%; background: #ecfdf5; display: flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                <i class="fas fa-dog" style="font-size: 1.8rem; color: #059669;"></i>
            </div>
            <strong style="font-size: 1rem; color: var(--text-primary); font-family: var(--font-heading);">Cachorros</strong>
            <small style="font-size: 0.78rem; color: var(--text-muted); margin-top: 2px;">Cãopanheiros fiéis</small>
        </a>

        <a href="pages/pets.php?especie=2" style="background: white; padding: 22px 20px; border-radius: 22px; box-shadow: var(--shadow-xs); width: 160px; text-align: center; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); border: 1.5px solid var(--border-color); display: flex; flex-direction: column; align-items: center;" onmouseover="this.style.transform='translateY(-6px)'; this.style.borderColor='var(--brand-teal)';" onmouseout="this.style.transform='none'; this.style.borderColor='var(--border-color)';">
            <div style="width: 60px; height: 60px; border-radius: 50%; background: #eff6ff; display: flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                <i class="fas fa-cat" style="font-size: 1.8rem; color: #2563eb;"></i>
            </div>
            <strong style="font-size: 1rem; color: var(--text-primary); font-family: var(--font-heading);">Gatos</strong>
            <small style="font-size: 0.78rem; color: var(--text-muted); margin-top: 2px;">Carinhosos & calmos</small>
        </a>

        <a href="pages/pets.php?porte=P" style="background: white; padding: 22px 20px; border-radius: 22px; box-shadow: var(--shadow-xs); width: 160px; text-align: center; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); border: 1.5px solid var(--border-color); display: flex; flex-direction: column; align-items: center;" onmouseover="this.style.transform='translateY(-6px)'; this.style.borderColor='var(--brand-teal)';" onmouseout="this.style.transform='none'; this.style.borderColor='var(--border-color)';">
            <div style="width: 60px; height: 60px; border-radius: 50%; background: #fffbeb; display: flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                <i class="fas fa-building" style="font-size: 1.8rem; color: #d97706;"></i>
            </div>
            <strong style="font-size: 1rem; color: var(--text-primary); font-family: var(--font-heading);">P/ Apartamento</strong>
            <small style="font-size: 0.78rem; color: var(--text-muted); margin-top: 2px;">Porte pequeno</small>
        </a>

        <a href="pages/cadastrar-pet.php" style="background: #f0fdf9; padding: 22px 20px; border-radius: 22px; box-shadow: var(--shadow-xs); width: 160px; text-align: center; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); border: 2px dashed var(--brand-teal); display: flex; flex-direction: column; align-items: center;" onmouseover="this.style.transform='translateY(-6px)'; this.style.background='var(--brand-mint-light)';" onmouseout="this.style.transform='none'; this.style.background='#f0fdf9';">
            <div style="width: 60px; height: 60px; border-radius: 50%; background: var(--brand-teal); color: white; display: flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                <i class="fas fa-plus" style="font-size: 1.6rem;"></i>
            </div>
            <strong style="font-size: 1rem; color: var(--brand-teal-darker); font-family: var(--font-heading);">Cadastrar Pet</strong>
            <small style="font-size: 0.78rem; color: var(--brand-teal-dark); margin-top: 2px;">Divulgue um pet</small>
        </a>
    </div>
</div>

<!-- 4. VITRINE DE PETS DISPONÍVEIS (ÁREA CONHECER OS PETS) -->
<div class="container-petfy" style="padding-top: 50px;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 28px; flex-wrap: wrap; gap: 15px;">
        <div>
            <span style="color: var(--brand-teal); font-weight: 800; text-transform: uppercase; font-size: 0.82rem; letter-spacing: 1px;">Adote Hoje</span>
            <h2 style="font-family: var(--font-heading); font-size: 2.3rem; color: var(--brand-teal-darker); font-weight: 900;">Amigos esperando por você</h2>
        </div>
        <a href="pages/pets.php" class="btn-petfy-outline">Ver catálogo completo (<?= max($totalDisponiveis, count($petsDestaque)) ?>) →</a>
    </div>

    <div class="pets-cards-grid">
        <?php if (!empty($petsDestaque)): ?>
            <?php foreach ($petsDestaque as $pet): ?>
                <div class="pet-card">
                    <div class="pet-card-image-wrap">
                        <img src="<?= htmlspecialchars(getPetPhotoUrl($pet['foto_url'], $rootPath, $pet['nome_especie'] ?? 'Cachorro')) ?>" 
                             alt="<?= htmlspecialchars($pet['nome']) ?>" 
                             loading="lazy" 
                             onerror="this.onerror=null; this.src='img/default-pet.svg';">
                        
                        <!-- Botão de Favoritar -->
                        <button type="button" class="pet-card-fav-btn" data-id="<?= $pet['id_pet'] ?>" onclick="toggleFavorito(this, <?= $pet['id_pet'] ?>)" title="Salvar nos favoritos">
                            <i class="fas fa-heart"></i>
                        </button>

                        <span class="pet-card-badge-status"><?= htmlspecialchars($pet['status']) ?></span>
                        <span class="pet-card-badge-especie"><?= htmlspecialchars($pet['nome_especie'] ?? 'Pet') ?></span>
                    </div>

                    <div class="pet-card-body">
                        <div class="pet-card-name-row">
                            <h3 class="pet-card-name"><?= htmlspecialchars($pet['nome']) ?></h3>
                            <span class="pet-card-sex <?= strtolower($pet['sexo']) === 'macho' ? 'macho' : 'femea' ?>" title="<?= htmlspecialchars($pet['sexo']) ?>">
                                <i class="fas <?= strtolower($pet['sexo']) === 'macho' ? 'fa-mars' : 'fa-venus' ?>"></i>
                            </span>
                        </div>

                        <p class="pet-card-breed"><?= htmlspecialchars($pet['nome_raca'] ?? 'Sem raça definida (SRD)') ?></p>

                        <div class="pet-card-attributes">
                            <span class="pet-attr-pill">Porte <?= htmlspecialchars($pet['porte']) ?></span>
                            <span class="pet-attr-pill"><?= htmlspecialchars($pet['idade']) ?></span>
                            <?php if ($pet['castrado']): ?><span class="pet-attr-pill" style="background:#ecfdf5; color:#065f46;"><i class="fas fa-check"></i> Castrado</span><?php endif; ?>
                            <?php if ($pet['vacinado']): ?><span class="pet-attr-pill" style="background:#eff6ff; color:#1e40af;"><i class="fas fa-syringe"></i> Vacinado</span><?php endif; ?>
                        </div>

                        <div class="pet-card-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?= htmlspecialchars($pet['cidade']) ?> - <?= htmlspecialchars($pet['estado']) ?></span>
                        </div>

                        <!-- Botão Conhecer o Pet -->
                        <a href="pages/pet-detalhes.php?id=<?= $pet['id_pet'] ?>" class="btn-card-adopt">
                            Conhecer <?= htmlspecialchars($pet['nome']) ?> <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-results-box" style="grid-column: 1 / -1;">
                <i class="fas fa-paw" style="font-size:3rem; color:var(--brand-teal);"></i>
                <h3>Nenhum animal cadastrado no momento</h3>
                <p>Seja o primeiro a divulgar um resgatado para encontrar uma nova família!</p>
                <a href="pages/cadastrar-pet.php" class="btn-petfy-primary" style="margin-top:10px; display:inline-block;">Cadastrar Pet Agora</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- 5. QUIZ INTERATIVO: QUAL PET COMBINA COM VOCÊ? -->
<div class="container-petfy" style="padding-top: 40px;">
    <div class="quiz-widget-container" id="quizPetMatcher">
        <div class="quiz-header">
            <span class="quiz-badge"><i class="fas fa-magic"></i> Quiz Inteligente</span>
            <h3>Descubra o Pet Ideal para Você</h3>
            <p>Responda 3 perguntas rápidas e veja qual perfil de animal se encaixa perfeitamente no seu espaço e dia a dia.</p>
        </div>

        <div class="quiz-options-row">
            <!-- Pergunta 1 -->
            <div class="quiz-question-box">
                <h4><i class="fas fa-home" style="color:var(--brand-teal);"></i> 1. Onde você mora?</h4>
                <div class="quiz-pills-selector" id="quizQ1">
                    <div class="quiz-pill-choice selected" onclick="selectQuizChoice('q1', 'apto', this)">
                        <i class="fas fa-building"></i> Apartamento
                    </div>
                    <div class="quiz-pill-choice" onclick="selectQuizChoice('q1', 'casa', this)">
                        <i class="fas fa-home"></i> Casa com quintal
                    </div>
                    <div class="quiz-pill-choice" onclick="selectQuizChoice('q1', 'sitio', this)">
                        <i class="fas fa-tree"></i> Sítio ou Chácara
                    </div>
                </div>
            </div>

            <!-- Pergunta 2 -->
            <div class="quiz-question-box">
                <h4><i class="fas fa-clock" style="color:var(--brand-teal);"></i> 2. Qual é sua rotina?</h4>
                <div class="quiz-pills-selector" id="quizQ2">
                    <div class="quiz-pill-choice selected" onclick="selectQuizChoice('q2', 'trabalho_casa', this)">
                        <i class="fas fa-laptop-house"></i> Fico muito em casa
                    </div>
                    <div class="quiz-pill-choice" onclick="selectQuizChoice('q2', 'fora', this)">
                        <i class="fas fa-briefcase"></i> Trabalho fora o dia todo
                    </div>
                    <div class="quiz-pill-choice" onclick="selectQuizChoice('q2', 'ativo', this)">
                        <i class="fas fa-running"></i> Super ativa / Esportes
                    </div>
                </div>
            </div>

            <!-- Pergunta 3 -->
            <div class="quiz-question-box">
                <h4><i class="fas fa-heart" style="color:var(--brand-teal);"></i> 3. Preferência de energia:</h4>
                <div class="quiz-pills-selector" id="quizQ3">
                    <div class="quiz-pill-choice selected" onclick="selectQuizChoice('q3', 'calmo', this)">
                        <i class="fas fa-couch"></i> Sossegado e dengoso
                    </div>
                    <div class="quiz-pill-choice" onclick="selectQuizChoice('q3', 'brincalhao', this)">
                        <i class="fas fa-baseball-ball"></i> Brincalhão e alegre
                    </div>
                    <div class="quiz-pill-choice" onclick="selectQuizChoice('q3', 'independente', this)">
                        <i class="fas fa-sun"></i> Independente
                    </div>
                </div>
            </div>
        </div>

        <div class="quiz-result-card" id="quizResultBox">
            <div class="quiz-result-icon">
                <i class="fas fa-paw"></i>
            </div>
            <div class="quiz-result-text">
                <h4 id="quizResultTitle">✨ Recomendação: Gatinho Dócil ou Cão Porte Pequeno</h4>
                <p id="quizResultDesc">Para o ambiente de apartamento e rotina equilibrada, um gato meigo ou um cãozinho de porte P é a combinação perfeita para convívio harmonioso!</p>
            </div>
            <a href="pages/pets.php?porte=P" id="quizResultLink" class="btn-petfy-dark">
                <i class="fas fa-paw"></i> Ver Pets Indicados
            </a>
        </div>
    </div>
</div>

<!-- 6. COMO FUNCIONA A ADOÇÃO -->
<section class="section-how-it-works" id="como-funciona" style="padding: 70px 0; background: white; margin-top: 40px;">
    <div class="container-petfy">
        <div style="text-align: center; margin-bottom: 45px;">
            <span style="color: var(--brand-teal); font-weight: 800; text-transform: uppercase; font-size: 0.82rem; letter-spacing: 1px;">Simples, Transparente & Seguro</span>
            <h2 style="font-family: var(--font-heading); color: var(--brand-teal-darker); font-size: 2.2rem; font-weight: 900; margin-top: 4px;">Como Funciona o Petfy</h2>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px;">
            <div style="text-align: center; padding: 30px 20px; background: #f8fafc; border-radius: 20px; border: 1.5px solid #e2e8f0;">
                <div style="width: 70px; height: 70px; border-radius: 50%; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 16px auto;">
                    <i class="fas fa-search"></i>
                </div>
                <h3 style="font-family: var(--font-heading); color: var(--brand-teal-darker); font-size: 1.25rem;">1. Conheça e Escolha</h3>
                <p style="color: var(--text-muted); font-size: 0.92rem; line-height: 1.5;">Navegue pelos pets cadastrados, filtre por espécie, porte ou localização e leia suas histórias completas.</p>
            </div>

            <div style="text-align: center; padding: 30px 20px; background: #f8fafc; border-radius: 20px; border: 1.5px solid #e2e8f0;">
                <div style="width: 70px; height: 70px; border-radius: 50%; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 16px auto;">
                    <i class="fas fa-comments"></i>
                </div>
                <h3 style="font-family: var(--font-heading); color: var(--brand-teal-darker); font-size: 1.25rem;">2. Fale com o Tutor</h3>
                <p style="color: var(--text-muted); font-size: 0.92rem; line-height: 1.5;">Converse diretamente pelo WhatsApp ou envie uma solicitação de adoção para alinhar os cuidados necessários.</p>
            </div>

            <div style="text-align: center; padding: 30px 20px; background: #f8fafc; border-radius: 20px; border: 1.5px solid #e2e8f0;">
                <div style="width: 70px; height: 70px; border-radius: 50%; background: #fdf2f8; color: #db2777; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 16px auto;">
                    <i class="fas fa-home"></i>
                </div>
                <h3 style="font-family: var(--font-heading); color: var(--brand-teal-darker); font-size: 1.25rem;">3. Novo Lar & Amor</h3>
                <p style="color: var(--text-muted); font-size: 0.92rem; line-height: 1.5;">Finalize o processo de adoção responsável e ganhe um amigo leal com carinho incondicional por toda a vida!</p>
            </div>
        </div>
    </div>
</section>

<!-- 7. PERGUNTAS FREQUENTES (FAQ) -->
<section class="container-petfy" id="faq" style="padding-top: 50px; padding-bottom: 70px;">
    <div style="text-align: center; margin-bottom: 35px;">
        <span style="color: var(--brand-teal); font-weight: 800; text-transform: uppercase; font-size: 0.82rem; letter-spacing: 1px;">Tire Suas Dúvidas</span>
        <h2 style="font-family: var(--font-heading); color: var(--brand-teal-darker); font-size: 2.1rem; font-weight: 900; margin-top: 4px;">Perguntas Frequentes</h2>
    </div>

    <div style="max-width: 750px; margin: 0 auto; display: flex; flex-direction: column; gap: 12px;">
        <div class="faq-item" style="background: white; border-radius: 14px; border: 1px solid #e2e8f0; overflow: hidden;">
            <button type="button" onclick="toggleFaq(this)" style="width: 100%; text-align: left; padding: 18px 20px; background: none; border: none; font-size: 1.05rem; font-weight: 800; color: var(--brand-teal-darker); display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                <span>A adoção de animais pelo Petfy é gratuita?</span>
                <i class="fas fa-chevron-down" style="color: var(--brand-teal);"></i>
            </button>
            <div class="faq-answer" style="padding: 0 20px 18px 20px; color: var(--text-secondary); font-size: 0.93rem; line-height: 1.6; display: none;">
                Sim! O Petfy é uma plataforma sem fins lucrativos desenvolvida para conectar animais resgatados a lares amorosos. Não cobramos nenhuma taxa para adotar ou cadastrar animais.
            </div>
        </div>

        <div class="faq-item" style="background: white; border-radius: 14px; border: 1px solid #e2e8f0; overflow: hidden;">
            <button type="button" onclick="toggleFaq(this)" style="width: 100%; text-align: left; padding: 18px 20px; background: none; border: none; font-size: 1.05rem; font-weight: 800; color: var(--brand-teal-darker); display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                <span>Como faço para cadastrar um animal que resgatei?</span>
                <i class="fas fa-chevron-down" style="color: var(--brand-teal);"></i>
            </button>
            <div class="faq-answer" style="padding: 0 20px 18px 20px; color: var(--text-secondary); font-size: 0.93rem; line-height: 1.6; display: none;">
                Basta criar sua conta gratuitamente no Petfy, clicar no botão <strong>Cadastrar Pet</strong>, preencher as fotos, hábitos e seu contato. Seu animalzinho será divulgado imediatamente para adotantes da sua região!
            </div>
        </div>

        <div class="faq-item" style="background: white; border-radius: 14px; border: 1px solid #e2e8f0; overflow: hidden;">
            <button type="button" onclick="toggleFaq(this)" style="width: 100%; text-align: left; padding: 18px 20px; background: none; border: none; font-size: 1.05rem; font-weight: 800; color: var(--brand-teal-darker); display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                <span>Para que serve o preenchimento do CEP?</span>
                <i class="fas fa-chevron-down" style="color: var(--brand-teal);"></i>
            </button>
            <div class="faq-answer" style="padding: 0 20px 18px 20px; color: var(--text-secondary); font-size: 0.93rem; line-height: 1.6; display: none;">
                Ao digitar seu CEP no cadastro ou no perfil, o sistema consulta automaticamente a API do ViaCEP e preenche sua rua, bairro, cidade e estado, garantindo que o tutor e os adotantes saibam a proximidade exata para combinar a entrega ou visita do pet.
            </div>
        </div>
    </div>
</section>

<!-- Scripts Específicos da Home -->
<script>
const quizState = { q1: 'apto', q2: 'trabalho_casa', q3: 'calmo' };

function selectQuizChoice(question, value, el) {
    quizState[question] = value;
    const parent = el.parentElement;
    parent.querySelectorAll('.quiz-pill-choice').forEach(item => item.classList.remove('selected'));
    el.classList.add('selected');
    atualizarResultadoQuiz();
}

function atualizarResultadoQuiz() {
    const title = document.getElementById('quizResultTitle');
    const desc = document.getElementById('quizResultDesc');
    const link = document.getElementById('quizResultLink');

    if (quizState.q1 === 'apto' && (quizState.q3 === 'calmo' || quizState.q3 === 'independente')) {
        title.innerHTML = "✨ Recomendação: Gatinho Dócil ou Cão Porte Pequeno";
        desc.innerText = "Para o ambiente de apartamento e rotina equilibrada, um gato meigo ou um cãozinho de porte P é a combinação perfeita para convívio harmonioso!";
        link.href = "pages/pets.php?porte=P";
        link.innerHTML = '<i class="fas fa-paw"></i> Ver Pets Porte P e Gatos';
    } else if (quizState.q1 === 'casa' || quizState.q1 === 'sitio' || quizState.q2 === 'ativo') {
        title.innerHTML = "✨ Recomendação: Cão Médio ou Grande Ativo";
        desc.innerText = "Com espaço livre e energia disponível, um cão de porte M ou G brincalhão trará muita alegria, lealdade e proteção para sua casa!";
        link.href = "pages/pets.php?porte=M";
        link.innerHTML = '<i class="fas fa-dog"></i> Ver Cães Porte M e G';
    } else {
        title.innerHTML = "✨ Recomendação: Cão Adulto de Porte Médio Tranquilo";
        desc.innerText = "Cães adultos já têm a personalidade formada, não roem móveis e são extremamente gratos pelo acolhimento com lealdade infinita.";
        link.href = "pages/pets.php?especie=1";
        link.innerHTML = '<i class="fas fa-search"></i> Ver Cães Disponíveis';
    }
}

function toggleFaq(btn) {
    const item = btn.parentElement;
    const answer = item.querySelector('.faq-answer');
    const icon = btn.querySelector('.fa-chevron-down');
    
    if (answer.style.display === 'block') {
        answer.style.display = 'none';
        if (icon) icon.style.transform = 'rotate(0deg)';
    } else {
        document.querySelectorAll('.faq-answer').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.faq-item .fa-chevron-down').forEach(el => el.style.transform = 'rotate(0deg)');
        answer.style.display = 'block';
        if (icon) icon.style.transform = 'rotate(180deg)';
    }
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
