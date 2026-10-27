/**
 * ============================================================
 * Petfy – FRONT-END: Script Principal Global (main.js)
 * ============================================================
 */

document.addEventListener('DOMContentLoaded', () => {
    // Esmaecer e remover alertas temporários
    const flashAlerts = document.querySelectorAll('.flash-alert-container');
    if (flashAlerts.length > 0) {
        setTimeout(() => {
            flashAlerts.forEach(el => {
                el.style.transition = 'opacity 0.5s ease';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            });
        }, 5000);
    }

    // Inicializa botões de favoritos salvos no LocalStorage
    sincronizarFavoritosUI();
});

// Dropdown de perfil do usuário na barra de navegação
function toggleUserDropdown(event) {
    if (event) event.stopPropagation();
    const dropdown = document.getElementById('userDropdownContent');
    if (dropdown) {
        dropdown.classList.toggle('show');
    }
}

// Fecha dropdowns ao clicar fora
window.addEventListener('click', (event) => {
    const dropdown = document.getElementById('userDropdownContent');
    if (dropdown && dropdown.classList.contains('show')) {
        if (!event.target.closest('.user-menu-dropdown')) {
            dropdown.classList.remove('show');
        }
    }
});

// Menu Mobile Drawer
function toggleMobileMenu() {
    const drawer = document.getElementById('mobileDrawer');
    const overlay = document.getElementById('mobileMenuOverlay');
    if (drawer && overlay) {
        drawer.classList.toggle('active');
        overlay.classList.toggle('active');
        document.body.style.overflow = drawer.classList.contains('active') ? 'hidden' : 'auto';
    }
}

// ============================================================
// SISTEMA DE FAVORITOS (LOCALSTORAGE)
// ============================================================
function getFavoritos() {
    try {
        return JSON.parse(localStorage.getItem('petfy_favs') || '[]');
    } catch(e) {
        return [];
    }
}

function toggleFavorito(btn, petId) {
    if (!petId) return;
    let favs = getFavoritos();
    const id = parseInt(petId);
    const index = favs.indexOf(id);

    if (index > -1) {
        favs.splice(index, 1);
        btn.classList.remove('favorited');
    } else {
        favs.push(id);
        btn.classList.add('favorited');
    }

    localStorage.setItem('petfy_favs', JSON.stringify(favs));
}

function sincronizarFavoritosUI() {
    const favs = getFavoritos();
    document.querySelectorAll('.pet-card-fav-btn').forEach(btn => {
        const petId = btn.getAttribute('data-id');
        if (petId && favs.includes(parseInt(petId))) {
            btn.classList.add('favorited');
        }
    });
}
