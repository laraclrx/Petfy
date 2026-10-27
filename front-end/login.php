<?php
/**
 * ============================================================
 * Petfy – FRONT-END: Tela de Login (Entrar)
 * Fiel à tela de inspiração (print.cadastro.jpg)
 * ============================================================
 */

require_once __DIR__ . '/../back-end/config/database.php';
require_once __DIR__ . '/../back-end/auth/verifica_auth.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$pageTitle = 'Entrar no Petfy';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar | Petfy – Toda Patinha merece um lar!</title>
    
    <!-- Google Fonts & FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Estilos de Autenticação e Globais -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/auth.css">
</head>
<body class="auth-page-body">

    <?php include __DIR__ . '/includes/flash_messages.php'; ?>

    <div class="auth-split-container">
        
        <!-- Esquerda: Verde Menta Pastel com Nuvens e Logo -->
        <div class="auth-split-left">
            <div class="auth-cloud c1"></div>
            <div class="auth-cloud c2"></div>
            <div class="auth-cloud c3"></div>
            <div class="auth-cloud c4"></div>

            <div class="auth-brand-box">
                <a href="index.php" title="Voltar para a Página Inicial">
                    <img src="img/logo.png" alt="Petfy" class="auth-logo-img" onerror="this.style.display='none'; document.getElementById('authLeftLogoFallback').style.display='block';">
                    <div id="authLeftLogoFallback" style="display:none;" class="auth-logo-paw">
                        <i class="fas fa-paw"></i>
                    </div>
                </a>
                <h1 class="auth-brand-name">petfy</h1>
                
                <p class="auth-subtext">Ainda não tem uma conta?</p>
                <h2 class="auth-heading-left">Cadastre-se</h2>

                <a href="cadastro.php" class="btn-auth-pill">Criar conta</a>
            </div>
        </div>

        <!-- Direita: Formulário de Login Branco Limpo -->
        <div class="auth-split-right">
            <div class="auth-form-wrapper">
                <a href="index.php" title="Ir para a Home">
                    <div class="auth-right-paw">
                        <i class="fas fa-paw"></i>
                    </div>
                </a>
                <h2 class="auth-heading-right">Entrar</h2>

                <form action="../back-end/auth/processa_login.php" method="POST" class="auth-form" id="loginForm">
                    <div class="auth-input-group">
                        <label for="login-email" class="sr-only">E-mail</label>
                        <input type="email" id="login-email" name="email" class="auth-input-pill" placeholder="E-mail" required autofocus>
                    </div>

                    <div class="auth-input-group">
                        <label for="login-senha" class="sr-only">Senha</label>
                        <input type="password" id="login-senha" name="senha" class="auth-input-pill" placeholder="Senha" required>
                    </div>

                    <button type="submit" class="btn-auth-submit">Entrar</button>

                    <button type="button" class="btn-google-pill" onclick="simularGoogleOAuth()">
                        <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google" width="18">
                        Entre com a conta do google
                    </button>
                </form>

                <div class="auth-helpers-links">
                    <p>Novo por aqui? <a href="cadastro.php" style="color: var(--brand-teal); font-weight: bold;">Criar minha conta</a></p>
                    <a href="index.php" class="btn-back-home">
                        <i class="fas fa-arrow-left"></i> Voltar para o início
                    </a>
                </div>
            </div>
        </div>

    </div>

    <!-- Scripts -->
    <script src="js/main.js"></script>
    <script src="js/auth-validate.js"></script>
</body>
</html>
