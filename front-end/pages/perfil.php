<?php
/**
 * ============================================================
 * Petfy – FRONT-END: Meu Perfil & Endereço com CEP
 * Atualização dos dados cadastrais e endereço
 * ============================================================
 */

$pageTitle = 'Meu Perfil';
require_once __DIR__ . '/../../back-end/config/database.php';
require_once __DIR__ . '/../../back-end/auth/verifica_auth.php';

requireAuth();

$pdo = db();
$usuarioLogado = getLoggedUser();
$idUsuario = (int)$usuarioLogado['id'];

$stmtUser = $pdo->prepare("SELECT * FROM usuario WHERE id_usuario = :id LIMIT 1");
$stmtUser->execute([':id' => $idUsuario]);
$user = $stmtUser->fetch();

$rootPath = '../';
$extraCss = ['css/pet-form.css'];
include __DIR__ . '/../includes/header.php';
?>

<div class="pet-form-wrapper" style="max-width: 650px; margin: 40px auto 60px auto;">
    
    <div class="pet-form-header">
        <div class="header-paw"><i class="fas fa-user-circle"></i></div>
        <h1>Meu Perfil</h1>
        <p>Mantenha seus dados de contato e endereço sempre atualizados para facilitar as adoções.</p>
    </div>

    <form action="../../back-end/perfil/processa_perfil.php" method="POST">
        
        <div class="form-group-pet" style="margin-bottom: 16px;">
            <label for="perfil-nome">Nome Completo: <span class="required">*</span></label>
            <input type="text" id="perfil-nome" name="nome" class="form-control-pet" value="<?= htmlspecialchars($user['nome']) ?>" required>
        </div>

        <div class="form-row-2" style="margin-bottom: 16px;">
            <div class="form-group-pet">
                <label for="perfil-email">E-mail (Login):</label>
                <input type="email" id="perfil-email" class="form-control-pet" value="<?= htmlspecialchars($user['email']) ?>" disabled style="background:#f1f5f9; color:#64748b; cursor:not-allowed;">
                <small style="font-size:0.75rem; color:#64748b;">O e-mail não pode ser alterado por segurança.</small>
            </div>

            <div class="form-group-pet">
                <label for="perfil-telefone">Telefone / WhatsApp:</label>
                <input type="tel" id="perfil-telefone" name="telefone" class="form-control-pet" value="<?= htmlspecialchars($user['telefone'] ?? '') ?>" placeholder="(00) 90000-0000" oninput="formatarTelefoneBR(this)">
            </div>
        </div>

        <!-- Bloco de Endereço com CEP -->
        <div style="background:#f0fdf9; border:1.5px solid #a7f3d0; border-radius:14px; padding:20px; margin-bottom: 20px;">
            <h3 style="margin:0 0 12px 0; font-size:1.05rem; color:var(--brand-teal-darker); font-weight:800;">
                <i class="fas fa-map-marker-alt"></i> Meu Endereço Residencial (ViaCEP)
            </h3>

            <div class="form-group-pet" style="margin-bottom:12px;">
                <label for="perfil-cep">CEP:</label>
                <div class="cep-input-wrapper" style="display:flex; gap:8px;">
                    <input type="text" id="perfil-cep" name="cep" class="form-control-pet" value="<?= htmlspecialchars($user['cep'] ?? '') ?>" placeholder="00000-000" maxlength="9" oninput="aplicarMascaraCEP(this); if(this.value.replace(/\D/g,'').length===8) buscarEnderecoPorCEP('perfil-cep', 'perfil-rua', 'perfil-bairro', 'perfil-cidade', 'perfil-estado', 'perfil-cep-helper');" onblur="buscarEnderecoPorCEP('perfil-cep', 'perfil-rua', 'perfil-bairro', 'perfil-cidade', 'perfil-estado', 'perfil-cep-helper');">
                    <button type="button" class="btn-petfy-dark" style="padding:0 18px; border-radius:12px; white-space:nowrap; font-size:0.85rem;" onclick="buscarEnderecoPorCEP('perfil-cep', 'perfil-rua', 'perfil-bairro', 'perfil-cidade', 'perfil-estado', 'perfil-cep-helper');">
                        Consultar CEP
                    </button>
                </div>
                <div id="perfil-cep-helper" class="cep-helper-msg" style="font-size:0.8rem; font-weight:bold; margin-top:4px;"></div>
            </div>

            <div class="form-row-2" style="margin-bottom:12px;">
                <div class="form-group-pet" style="flex:3;">
                    <label for="perfil-rua">Rua / Logradouro:</label>
                    <input type="text" id="perfil-rua" name="rua" class="form-control-pet" value="<?= htmlspecialchars($user['rua'] ?? '') ?>" placeholder="Ex: Rua das Flores">
                </div>
                <div class="form-group-pet" style="flex:1;">
                    <label for="perfil-numero">Número:</label>
                    <input type="text" id="perfil-numero" name="numero" class="form-control-pet" value="<?= htmlspecialchars($user['numero'] ?? '') ?>" placeholder="123">
                </div>
            </div>

            <div class="form-row-2">
                <div class="form-group-pet">
                    <label for="perfil-bairro">Bairro:</label>
                    <input type="text" id="perfil-bairro" name="bairro" class="form-control-pet" value="<?= htmlspecialchars($user['bairro'] ?? '') ?>" placeholder="Bairro">
                </div>
                <div class="form-group-pet">
                    <label for="perfil-cidade">Cidade:</label>
                    <input type="text" id="perfil-cidade" name="cidade" class="form-control-pet" value="<?= htmlspecialchars($user['cidade'] ?? '') ?>" placeholder="Cidade">
                </div>
                <div class="form-group-pet" style="max-width:90px;">
                    <label for="perfil-estado">UF:</label>
                    <input type="text" id="perfil-estado" name="estado" class="form-control-pet" value="<?= htmlspecialchars($user['estado'] ?? '') ?>" placeholder="SP" maxlength="2" style="text-transform:uppercase;">
                </div>
            </div>
        </div>

        <!-- Trocar Senha -->
        <div class="form-group-pet" style="margin-bottom: 24px;">
            <label for="perfil-senha">Alterar Senha (opcional):</label>
            <input type="password" id="perfil-senha" name="nova_senha" class="form-control-pet" placeholder="Deixe em branco para manter a senha atual" minlength="6">
            <small style="color:var(--text-muted); font-size:0.8rem;">Preencha somente se desejar alterar sua senha de acesso.</small>
        </div>

        <button type="submit" class="btn-submit-pet">
            <i class="fas fa-save"></i> Salvar Alterações do Perfil
        </button>
    </form>

</div>

<script src="../js/cep.js"></script>
<script src="../js/auth-validate.js"></script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
