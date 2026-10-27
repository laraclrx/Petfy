<?php
/**
 * ============================================================
 * Petfy – FRONT-END: Cabeçalho e Barra de Navegação Global
 * Toda Patinha merece um lar!
 * ============================================================
 */

if (!isset($rootPath)) {
    $rootPath = file_exists('css/style.css') ? '' : '../';
}

require_once __DIR__ . '/../../back-end/config/database.php';
require_once __DIR__ . '/../../back-end/auth/verifica_auth.php';

$usuarioLogado = getLoggedUser();
$pageTitle = isset($pageTitle) ? $pageTitle . ' | Petfy' : 'Petfy – Toda Patinha merece um lar!';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Petfy - Plataforma de Adoção Responsável de Cães e Gatos. Toda Patinha merece um lar!">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    
    <!-- Google Fonts & FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Folhas de Estilo Globais -->
    <link rel="stylesheet" href="<?= $rootPath ?>css/style.css">
    <?php if (isset($extraCss)): ?>
        <?php foreach ($extraCss as $cssFile): ?>
            <link rel="stylesheet" href="<?= $rootPath . $cssFile ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>

<!-- Barra de Navegação Principal -->
<header class="header-main">
    <nav class="navbar-petfy">
        <!-- Logotipo Oficial Petfy -->
        <a href="<?= $rootPath ?>index.php" class="nav-logo">
            <img src="<?= $rootPath ?>img/logo.png" alt="Petfy" class="nav-logo-img" onerror="this.style.display='none'; document.getElementById('navLogoFallback').style.display='flex';">
            <div class="nav-logo-fallback" id="navLogoFallback" style="display:none;">
                <span class="logo-icon"><i class="fas fa-paw"></i></span>
                <span class="logo-text">petfy</span>
            </div>
        </a>

        <!-- Barra de Busca Rápida no Topo -->
        <form action="<?= $rootPath ?>pages/pets.php" method="GET" class="nav-search">
            <input type="text" name="q" placeholder="Buscar por nome, raça ou cidade..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            <button type="submit" aria-label="Pesquisar"><i class="fas fa-search"></i></button>
        </form>

        <!-- Links Principais de Navegação -->
        <div class="nav-links">
            <a href="<?= $rootPath ?>index.php" class="nav-link"><i class="fas fa-home"></i> Início</a>
            <a href="<?= $rootPath ?>pages/pets.php" class="nav-link"><i class="fas fa-paw"></i> Conhecer os Pets</a>
            <a href="<?= $rootPath ?>pages/cadastrar-pet.php" class="nav-link nav-link-highlight">
                <i class="fas fa-plus-circle"></i> Cadastrar Pet
            </a>
            <a href="<?= $rootPath ?>index.php#como-funciona" class="nav-link">Como Funciona</a>
        </div>

        <!-- Área de Autenticação / Perfil -->
        <div class="nav-auth">
            <?php if ($usuarioLogado): ?>
                <div class="user-menu-dropdown">
                    <button type="button" class="user-menu-btn" onclick="toggleUserDropdown(event)">
                        <div class="user-avatar"><i class="fas fa-user"></i></div>
                        <span class="user-name"><?= htmlspecialchars(explode(' ', $usuarioLogado['nome'])[0]) ?></span>
                        <i class="fas fa-chevron-down caret-icon"></i>
                    </button>
                    <div class="user-dropdown-content" id="userDropdownContent">
                        <div class="dropdown-header">
                            <strong><?= htmlspecialchars($usuarioLogado['nome']) ?></strong>
                            <small><?= htmlspecialchars($usuarioLogado['email']) ?></small>
                        </div>
                        <hr>
                        <a href="<?= $rootPath ?>pages/cadastrar-pet.php"><i class="fas fa-plus-circle"></i> Divulgar Novo Pet</a>
                        <a href="<?= $rootPath ?>pages/meus-pets.php"><i class="fas fa-paw"></i> Meus Pets Cadastrados</a>
                        <a href="<?= $rootPath ?>pages/minhas-adocoes.php"><i class="fas fa-heart"></i> Minhas Adoções</a>
                        <a href="<?= $rootPath ?>pages/perfil.php"><i class="fas fa-user-cog"></i> Meu Perfil & CEP</a>
                        <hr>
                        <a href="<?= $rootPath ?>logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Sair da Conta</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="auth-buttons">
                    <a href="<?= $rootPath ?>login.php" class="btn-nav-login">Entrar</a>
                    <a href="<?= $rootPath ?>cadastro.php" class="btn-nav-register">Criar conta</a>
                </div>
            <?php endif; ?>

            <button class="mobile-toggle" onclick="toggleMobileMenu()" aria-label="Abrir Menu">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>
</header>

<!-- Menu Lateral Mobile (Drawer) -->
<div class="mobile-menu-overlay" id="mobileMenuOverlay" onclick="toggleMobileMenu()"></div>
<div class="mobile-drawer" id="mobileDrawer">
    <div class="drawer-header">
        <a href="<?= $rootPath ?>index.php" class="drawer-logo">
            <span class="logo-icon"><i class="fas fa-paw"></i></span>
            <span class="logo-text">petfy</span>
        </a>
        <button class="drawer-close" onclick="toggleMobileMenu()" aria-label="Fechar">&times;</button>
    </div>

    <div class="drawer-content">
        <a href="<?= $rootPath ?>index.php" class="drawer-link"><i class="fas fa-home"></i> Início</a>
        <a href="<?= $rootPath ?>pages/pets.php" class="drawer-link"><i class="fas fa-paw"></i> Conhecer os Pets</a>
        <a href="<?= $rootPath ?>pages/cadastrar-pet.php" class="drawer-link" style="color: var(--brand-teal); font-weight: bold;">
            <i class="fas fa-plus-circle"></i> Cadastrar Pet
        </a>
        <a href="<?= $rootPath ?>index.php#como-funciona" class="drawer-link"><i class="fas fa-info-circle"></i> Como Funciona</a>
        
        <hr style="margin: 15px 0; border: none; border-top: 1px solid #e5e7eb;">
        
        <?php if ($usuarioLogado): ?>
            <div style="padding: 10px 0; font-size: 0.9rem; color: #4b5563;">
                Conectado como: <strong><?= htmlspecialchars($usuarioLogado['nome']) ?></strong>
            </div>
            <a href="<?= $rootPath ?>pages/meus-pets.php" class="drawer-link"><i class="fas fa-dog"></i> Meus Pets</a>
            <a href="<?= $rootPath ?>pages/minhas-adocoes.php" class="drawer-link"><i class="fas fa-heart"></i> Minhas Adoções</a>
            <a href="<?= $rootPath ?>pages/perfil.php" class="drawer-link"><i class="fas fa-user-cog"></i> Meu Perfil</a>
            <a href="<?= $rootPath ?>logout.php" class="drawer-link" style="color: #dc2626;"><i class="fas fa-sign-out-alt"></i> Sair</a>
        <?php else: ?>
            <div class="drawer-auth-btns" style="display:flex; flex-direction:column; gap:10px; margin-top:10px;">
                <a href="<?= $rootPath ?>login.php" class="btn-petfy-outline" style="text-align:center;">Entrar</a>
                <a href="<?= $rootPath ?>cadastro.php" class="btn-petfy-primary" style="text-align:center;">Criar Conta</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<main class="main-content-wrapper">
<?php include __DIR__ . '/flash_messages.php'; ?>
