<?php
/**
 * ============================================================
 * Petfy – FRONT-END: Área Cadastrar os Pets (Multistep)
 * Formulário completo de cadastro de animal para adoção
 * ============================================================
 */

$pageTitle = 'Cadastrar Pet para Adoção';
require_once __DIR__ . '/../../back-end/config/database.php';
require_once __DIR__ . '/../../back-end/auth/verifica_auth.php';

requireAuth();

$pdo = db();
$usuario = getLoggedUser();

$especies = $pdo->query("SELECT * FROM especie ORDER BY id_especie ASC")->fetchAll();
$racas = $pdo->query("SELECT * FROM raca ORDER BY nome ASC")->fetchAll();

$rootPath = '../';
$extraCss = ['css/pet-form.css', 'css/pets-grid.css'];
include __DIR__ . '/../includes/header.php';
?>

<div class="pet-form-page-layout">
    
    <!-- COLUNA PRINCIPAL: FORMULÁRIO MULTISTEP -->
    <div class="pet-form-col-main">
        <div class="pet-form-wrapper">
            
            <div class="pet-form-header">
                <div class="header-paw"><i class="fas fa-paw"></i></div>
                <h1>Cadastrar Pet para Adoção</h1>
                <p>Preencha as informações do animal resgatado para que ele encontre uma família amorosa e responsável.</p>
            </div>

            <!-- Indicador de Progresso das Etapas -->
            <div class="steps-indicator">
                <div class="step-badge active" id="stepBadge1">
                    <span class="step-number">1</span>
                    <span>Sobre o Pet</span>
                </div>
                <div class="step-badge" id="stepBadge2">
                    <span class="step-number">2</span>
                    <span>Saúde & Hábitos</span>
                </div>
                <div class="step-badge" id="stepBadge3">
                    <span class="step-number">3</span>
                    <span>Contato & Local</span>
                </div>
            </div>

            <form action="../../back-end/pets/processa_cadastro_pet.php" method="POST" enctype="multipart/form-data" id="formCadastroPet">
                
                <!-- ETAPA 1: SOBRE O PET -->
                <div class="form-step-section active" id="stepSection1">
                    <h3 class="step-subheading"><i class="fas fa-id-card"></i> 1. Informações Básicas do Animal</h3>

                    <!-- Upload da Foto com Preview -->
                    <div class="form-group-pet">
                        <label>Foto do pet: <span class="required">*</span></label>
                        <div class="upload-dropzone" onclick="document.getElementById('fileFotoPet').click()">
                            <div id="uploadDropzoneContent">
                                <i class="fas fa-camera upload-icon"></i>
                                <p>Clique aqui para selecionar a foto do pet</p>
                                <small>Formatos: JPG, PNG ou WEBP (Máx. 6MB) • Uma foto nítida e bem iluminada atrai muito mais adotantes!</small>
                            </div>
                            <div class="image-preview-box" id="imagePreviewContainer" style="display:none;">
                                <img id="imagePreview" src="" alt="Prévia da Foto">
                                <p style="margin-top: 8px; font-size: 0.82rem; color: var(--brand-teal-dark);">Clique para trocar a foto</p>
                            </div>
                            <input type="file" id="fileFotoPet" name="foto" accept="image/jpeg,image/png,image/webp" style="display:none;" onchange="previewPetImage(this)">
                        </div>
                    </div>

                    <!-- Nome do Pet -->
                    <div class="form-group-pet">
                        <label for="pet-nome">Nome do pet: <span class="required">*</span></label>
                        <input type="text" id="pet-nome" name="nome" class="form-control-pet" placeholder="Ex: Thor, Luna, Pipoca, Mel, Bob..." required>
                    </div>

                    <div class="form-row-2">
                        <!-- Espécie -->
                        <div class="form-group-pet">
                            <label>Espécie: <span class="required">*</span></label>
                            <input type="hidden" name="id_especie" id="id_especieInput" value="1">
                            <div class="options-selector-group">
                                <div class="option-chip-btn selected" onclick="setOption('id_especie', 1, this)">
                                    <i class="fas fa-dog"></i> Cão
                                </div>
                                <div class="option-chip-btn" onclick="setOption('id_especie', 2, this)">
                                    <i class="fas fa-cat"></i> Gato
                                </div>
                                <div class="option-chip-btn" onclick="setOption('id_especie', 3, this)">
                                    Outro
                                </div>
                            </div>
                        </div>

                        <!-- Sexo -->
                        <div class="form-group-pet">
                            <label>Sexo: <span class="required">*</span></label>
                            <input type="hidden" name="sexo" id="sexoInput" value="Macho">
                            <div class="options-selector-group">
                                <div class="option-chip-btn selected" onclick="setOption('sexo', 'Macho', this)">
                                    <i class="fas fa-mars"></i> Macho
                                </div>
                                <div class="option-chip-btn" onclick="setOption('sexo', 'Fêmea', this)">
                                    <i class="fas fa-venus"></i> Fêmea
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-row-2">
                        <!-- Raça -->
                        <div class="form-group-pet">
                            <label for="racaSelect">Raça:</label>
                            <select name="id_raca" id="racaSelect" class="form-control-pet">
                                <option value="">Vira-lata / Sem raça definida (SRD)</option>
                                <?php foreach ($racas as $rc): ?>
                                    <option value="<?= $rc['id_raca'] ?>" data-especie="<?= $rc['id_especie'] ?>">
                                        <?= htmlspecialchars($rc['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Outra Raça (se não estiver na lista) -->
                        <div class="form-group-pet">
                            <label for="raca_outra">Ou digite a raça:</label>
                            <input type="text" name="raca_outra" id="raca_outra" class="form-control-pet" placeholder="Ex: Maltês, Border Collie...">
                        </div>
                    </div>

                    <div class="form-row-2">
                        <!-- Porte -->
                        <div class="form-group-pet">
                            <label>Porte do animal: <span class="required">*</span></label>
                            <input type="hidden" name="porte" id="porteInput" value="M">
                            <div class="options-selector-group">
                                <div class="option-chip-btn" onclick="setOption('porte', 'P', this)">
                                    P (Pequeno)
                                </div>
                                <div class="option-chip-btn selected" onclick="setOption('porte', 'M', this)">
                                    M (Médio)
                                </div>
                                <div class="option-chip-btn" onclick="setOption('porte', 'G', this)">
                                    G (Grande)
                                </div>
                            </div>
                        </div>

                        <!-- Idade -->
                        <div class="form-group-pet">
                            <label for="pet-idade">Idade aproximada: <span class="required">*</span></label>
                            <input type="text" id="pet-idade" name="idade" class="form-control-pet" placeholder="Ex: 2 meses, 1 ano, 4 anos..." required>
                        </div>
                    </div>

                    <div class="step-nav-buttons" style="justify-content: flex-end;">
                        <button type="button" class="btn-step-next" onclick="goToStep(2)">
                            Avançar para Saúde e Hábitos <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- ETAPA 2: SAÚDE & PERSONALIDADE -->
                <div class="form-step-section" id="stepSection2">
                    <h3 class="step-subheading"><i class="fas fa-heartbeat"></i> 2. Saúde, Cuidados e Temperamento</h3>

                    <div class="form-group-pet">
                        <label>Condições de Saúde:</label>
                        <div class="checkbox-health-group">
                            <label class="custom-checkbox-card">
                                <input type="checkbox" name="vacinado" value="1" checked>
                                <span class="checkmark-icon"><i class="fas fa-syringe"></i></span>
                                <div>
                                    <strong>Vacinado</strong>
                                    <small>Com vacinas essenciais em dia</small>
                                </div>
                            </label>

                            <label class="custom-checkbox-card">
                                <input type="checkbox" name="castrado" value="1" checked>
                                <span class="checkmark-icon"><i class="fas fa-cut"></i></span>
                                <div>
                                    <strong>Castrado</strong>
                                    <small>Procedimento cirúrgico realizado</small>
                                </div>
                            </label>

                            <label class="custom-checkbox-card">
                                <input type="checkbox" name="vermifugado" value="1" checked>
                                <span class="checkmark-icon"><i class="fas fa-shield-alt"></i></span>
                                <div>
                                    <strong>Vermifugado</strong>
                                    <small>Protegido contra parasitas</small>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="form-group-pet">
                        <label for="pet-necessidades">Necessidades ou cuidados especiais:</label>
                        <input type="text" id="pet-necessidades" name="necessidades_especiais" class="form-control-pet" placeholder="Ex: Nenhuma, ou uso contínuo de colírio, deficiência física, etc.">
                    </div>

                    <div class="form-group-pet">
                        <label>Temperamento / Personalidade (Selecione todas as que se aplicam):</label>
                        <input type="hidden" name="personalidades" id="personalidadesInput" value="Brincalhão, Dócil, Carinhoso">
                        <div class="personality-tags-picker">
                            <span class="personality-tag selected" onclick="togglePersonalityTag(this, 'Brincalhão')">Brincalhão</span>
                            <span class="personality-tag selected" onclick="togglePersonalityTag(this, 'Dócil')">Dócil</span>
                            <span class="personality-tag selected" onclick="togglePersonalityTag(this, 'Carinhoso')">Carinhoso</span>
                            <span class="personality-tag" onclick="togglePersonalityTag(this, 'Calmo')">Calmo</span>
                            <span class="personality-tag" onclick="togglePersonalityTag(this, 'Protetor')">Protetor</span>
                            <span class="personality-tag" onclick="togglePersonalityTag(this, 'Sociável com cães')">Sociável com cães</span>
                            <span class="personality-tag" onclick="togglePersonalityTag(this, 'Sociável com gatos')">Sociável com gatos</span>
                            <span class="personality-tag" onclick="togglePersonalityTag(this, 'Bom com crianças')">Bom com crianças</span>
                            <span class="personality-tag" onclick="togglePersonalityTag(this, 'Tímido')">Tímido</span>
                            <span class="personality-tag" onclick="togglePersonalityTag(this, 'Independente')">Independente</span>
                        </div>
                    </div>

                    <div class="form-group-pet">
                        <label for="pet-descricao">História e descrição do pet:</label>
                        <textarea id="pet-descricao" name="descricao" class="form-control-pet" rows="4" placeholder="Conte a história do resgate, hábitos diários, convivência e por que ele é um companheiro maravilhoso..."></textarea>
                    </div>

                    <div class="step-nav-buttons">
                        <button type="button" class="btn-step-prev" onclick="goToStep(1)">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </button>
                        <button type="button" class="btn-step-next" onclick="goToStep(3)">
                            Avançar para Contato e Local <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- ETAPA 3: CONTATO & LOCALIZAÇÃO (COM CEP) -->
                <div class="form-step-section" id="stepSection3">
                    <h3 class="step-subheading"><i class="fas fa-map-marker-alt"></i> 3. Localização e Contato do Tutor</h3>

                    <!-- Busca por CEP para Localização Automática -->
                    <div class="form-group-pet" style="background:#f0fdf9; padding:16px; border-radius:14px; border:1.5px solid #a7f3d0;">
                        <label for="pet-cep"><i class="fas fa-search-location"></i> CEP do local onde o pet se encontra:</label>
                        <div class="cep-input-wrapper">
                            <input type="text" id="pet-cep" name="pet_cep" class="form-control-pet" placeholder="00000-000" maxlength="9" oninput="aplicarMascaraCEP(this); if(this.value.replace(/\D/g,'').length===8) buscarEnderecoPorCEP('pet-cep', null, null, 'pet-cidade', 'pet-estado', 'pet-cep-helper');" onblur="buscarEnderecoPorCEP('pet-cep', null, null, 'pet-cidade', 'pet-estado', 'pet-cep-helper');">
                            <button type="button" class="btn-cep-search" onclick="buscarEnderecoPorCEP('pet-cep', null, null, 'pet-cidade', 'pet-estado', 'pet-cep-helper');">
                                Buscar CEP
                            </button>
                        </div>
                        <div id="pet-cep-helper" class="cep-helper-msg"></div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group-pet" style="flex: 2;">
                            <label for="pet-cidade">Cidade: <span class="required">*</span></label>
                            <input type="text" id="pet-cidade" name="cidade" class="form-control-pet" placeholder="Ex: São Paulo" required>
                        </div>

                        <div class="form-group-pet" style="max-width: 110px;">
                            <label for="pet-estado">Estado (UF): <span class="required">*</span></label>
                            <input type="text" id="pet-estado" name="estado" class="form-control-pet" placeholder="SP" maxlength="2" style="text-transform: uppercase;" required>
                        </div>
                    </div>

                    <div class="form-group-pet">
                        <label for="pet-contato-nome">Nome do Tutor / Responsável:</label>
                        <input type="text" id="pet-contato-nome" name="contato_nome" class="form-control-pet" value="<?= htmlspecialchars($usuario['nome'] ?? '') ?>" required>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group-pet">
                            <label for="pet-contato-tel">WhatsApp / Telefone para contato: <span class="required">*</span></label>
                            <input type="tel" id="pet-contato-tel" name="contato_telefone" class="form-control-pet" placeholder="(00) 90000-0000" value="<?= htmlspecialchars($usuario['telefone'] ?? '') ?>" oninput="formatarTelefoneBR(this)" required>
                        </div>

                        <div class="form-group-pet">
                            <label for="pet-contato-email">E-mail para contato:</label>
                            <input type="email" id="pet-contato-email" name="contato_email" class="form-control-pet" placeholder="seu@email.com" value="<?= htmlspecialchars($usuario['email'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="step-nav-buttons">
                        <button type="button" class="btn-step-prev" onclick="goToStep(2)">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </button>
                        <button type="submit" class="btn-submit-pet">
                            <i class="fas fa-check-circle"></i> Concluir Cadastro do Pet
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <!-- COLUNA LATERAL: PRÉVIA AO VIVO (LIVE PREVIEW) -->
    <div class="pet-form-col-side">
        <div class="live-preview-box">
            <span class="preview-tag"><i class="fas fa-eye"></i> Pré-visualização em tempo real</span>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 15px;">É assim que o card do seu pet será visto no catálogo:</p>

            <div class="pet-card" style="box-shadow: var(--shadow-sm); border: 1.5px solid #e2e8f0;">
                <div class="pet-card-image-wrap">
                    <img id="replicaPetImage" src="" alt="Prévia" style="display:none;">
                    <div class="replica-placeholder-img" id="replicaPlaceholder" style="width:100%; height:200px; background:#e0f7f3; display:flex; flex-direction:column; align-items:center; justify-content:center; color:#3b6f7a;">
                        <i class="fas fa-camera" style="font-size:2.2rem; margin-bottom:6px;"></i>
                        <small>A foto aparecerá aqui</small>
                    </div>
                    <span class="pet-card-badge-status">Disponível</span>
                </div>

                <div class="pet-card-body">
                    <div class="pet-card-name-row">
                        <h3 class="pet-card-name" id="replicaPetName">Nome do Pet</h3>
                        <span class="pet-card-sex macho" id="replicaPetSex"><i class="fas fa-mars"></i></span>
                    </div>

                    <p class="pet-card-breed" id="replicaPetBreed">Cachorro • Sem raça definida (SRD)</p>

                    <div class="pet-card-attributes">
                        <span class="pet-attr-pill" id="replicaPetPorte">Porte M</span>
                        <span class="pet-attr-pill" id="replicaPetIdade">Idade</span>
                    </div>

                    <div class="pet-card-location">
                        <i class="fas fa-map-marker-alt"></i>
                        <span id="replicaPetLocation">Sua Cidade - UF</span>
                    </div>

                    <button type="button" class="btn-card-adopt" style="pointer-events:none; opacity:0.85;">
                        Conhecer Pet <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Scripts de Formulário e CEP -->
<script src="../js/pet-form.js"></script>
<script src="../js/cep.js"></script>
<script src="../js/auth-validate.js"></script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
