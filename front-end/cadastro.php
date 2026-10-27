<?php
/**
 * ============================================================
 * Petfy – FRONT-END: Tela de Cadastro de Usuário (Criar Conta)
 * Fiel à inspiração (print.criarconta.jpg) com integração ViaCEP
 * ============================================================
 */

require_once __DIR__ . '/../back-end/config/database.php';
require_once __DIR__ . '/../back-end/auth/verifica_auth.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$pageTitle = 'Crie sua conta no Petfy';
$old = $_SESSION['old_cadastro'] ?? [];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crie sua conta | Petfy – Toda Patinha merece um lar!</title>
    
    <!-- Google Fonts & FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Estilos de Autenticação e Globais -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/auth.css">
</head>
<body class="auth-page-body register-body-theme">

    <?php include __DIR__ . '/includes/flash_messages.php'; ?>

    <div class="register-page-container">
        
        <!-- Cabeçalho Menta com Logo e Título -->
        <div class="register-card-header">
            <a href="index.php" title="Voltar para a Página Inicial">
                <img src="img/logo.png" alt="Petfy" class="register-logo-img" onerror="this.style.display='none'; document.getElementById('regHeaderPaw').style.display='block';">
                <div id="regHeaderPaw" style="display:none;" class="paw-icon">
                    <i class="fas fa-paw"></i>
                </div>
            </a>
            <h1>Crie sua conta</h1>
        </div>

        <!-- Card Central Flutuante -->
        <div class="register-card">
            
            <!-- Botão Google OAuth (Simulado amigável) -->
            <button type="button" class="btn-google-register" onclick="simularGoogleOAuth()">
                <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google" width="20">
                Criar conta com o google
            </button>

            <div class="auth-divider-text">
                <span>ou digite seus dados</span>
            </div>

            <form action="../back-end/auth/processa_cadastro.php" method="POST" class="register-form" id="registerForm">
                
                <!-- E-mail -->
                <div class="register-input-group">
                    <label for="reg-email">E-mail <span class="required">*</span></label>
                    <input type="email" id="reg-email" name="email" class="register-input-pill" placeholder="seu@email.com" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
                </div>

                <!-- Telefone com Bandeira do Brasil -->
                <div class="register-input-group">
                    <label for="reg-telefone">Telefone / WhatsApp</label>
                    <div class="phone-input-wrapper">
                        <span class="phone-flag">
                            <img src="https://flagcdn.com/w40/br.png" alt="Brasil" width="18">
                            +55
                        </span>
                        <input type="tel" id="reg-telefone" name="telefone" class="register-input-pill" placeholder="(11) 98888-7777" value="<?= htmlspecialchars($old['telefone'] ?? '') ?>" oninput="formatarTelefoneBR(this)">
                    </div>
                </div>

                <!-- Nome Completo -->
                <div class="register-input-group">
                    <label for="reg-nome">Nome completo <span class="required">*</span></label>
                    <input type="text" id="reg-nome" name="nome" class="register-input-pill" placeholder="Seu nome completo" value="<?= htmlspecialchars($old['nome'] ?? '') ?>" required>
                </div>

                <!-- Senha -->
                <div class="register-input-group">
                    <label for="reg-senha">Senha <span class="required">*</span></label>
                    <div style="position: relative;">
                        <input type="password" id="reg-senha" name="senha" class="register-input-pill" placeholder="Crie uma senha segura (mín. 6 caracteres)" minlength="6" required>
                        <button type="button" class="btn-toggle-pwd" onclick="alternarVisibilidadeSenha('reg-senha', this)" aria-label="Mostrar senha">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Bloco de Endereço com CEP Automático -->
                <div class="register-address-box">
                    <div class="address-box-title">
                        <i class="fas fa-map-marker-alt"></i> Localização & Endereço
                        <small style="display:block; font-weight:normal; color:#4b6369; margin-top:2px;">Digite seu CEP para preencher o endereço automaticamente.</small>
                    </div>

                    <div class="register-input-group" style="margin-top: 10px;">
                        <label for="reg-cep">CEP</label>
                        <div class="cep-input-wrapper">
                            <input type="text" id="reg-cep" name="cep" class="register-input-pill" placeholder="00000-000" maxlength="9" value="<?= htmlspecialchars($old['cep'] ?? '') ?>" oninput="aplicarMascaraCEP(this); if(this.value.replace(/\D/g,'').length === 8) buscarEnderecoPorCEP('reg-cep', 'reg-rua', 'reg-bairro', 'reg-cidade', 'reg-estado');" onblur="buscarEnderecoPorCEP('reg-cep', 'reg-rua', 'reg-bairro', 'reg-cidade', 'reg-estado');">
                            <button type="button" class="btn-cep-search" onclick="buscarEnderecoPorCEP('reg-cep', 'reg-rua', 'reg-bairro', 'reg-cidade', 'reg-estado');" title="Consultar CEP">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                        </div>
                        <div id="cep-status-helper" class="cep-helper-msg"></div>
                    </div>

                    <div class="form-row-2">
                        <div class="register-input-group" style="flex: 3;">
                            <label for="reg-rua">Rua / Logradouro</label>
                            <input type="text" id="reg-rua" name="rua" class="register-input-pill" placeholder="Ex: Av. Paulista" value="<?= htmlspecialchars($old['rua'] ?? '') ?>">
                        </div>
                        <div class="register-input-group" style="flex: 1;">
                            <label for="reg-numero">Nº</label>
                            <input type="text" id="reg-numero" name="numero" class="register-input-pill" placeholder="123" value="<?= htmlspecialchars($old['numero'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="register-input-group">
                            <label for="reg-bairro">Bairro</label>
                            <input type="text" id="reg-bairro" name="bairro" class="register-input-pill" placeholder="Bairro" value="<?= htmlspecialchars($old['bairro'] ?? '') ?>">
                        </div>
                        <div class="register-input-group">
                            <label for="reg-cidade">Cidade</label>
                            <input type="text" id="reg-cidade" name="cidade" class="register-input-pill" placeholder="Cidade" value="<?= htmlspecialchars($old['cidade'] ?? '') ?>">
                        </div>
                        <div class="register-input-group" style="max-width: 90px;">
                            <label for="reg-estado">UF</label>
                            <input type="text" id="reg-estado" name="estado" class="register-input-pill" placeholder="SP" maxlength="2" style="text-transform: uppercase;" value="<?= htmlspecialchars($old['estado'] ?? '') ?>">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-register-submit">Criar</button>
            </form>
        </div>

        <!-- Links Inferiores -->
        <div class="register-footer-links">
            <p>Já tem uma conta no Petfy? <a href="login.php">Entrar</a></p>
            <a href="index.php" class="btn-back-home">
                <i class="fas fa-arrow-left"></i> Voltar para o início
            </a>
        </div>

    </div>

    <!-- Scripts -->
    <script src="js/main.js"></script>
    <script src="js/auth-validate.js"></script>
    <script src="js/cep.js"></script>
</body>
</html>
