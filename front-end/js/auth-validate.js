/**
 * ============================================================
 * Petfy – FRONT-END: Validações e Utilitários de Autenticação
 * ============================================================
 */

function formatarTelefoneBR(input) {
    let valor = input.value.replace(/\D/g, '');
    if (valor.length > 11) valor = valor.slice(0, 11);

    if (valor.length > 10) {
        valor = valor.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3');
    } else if (valor.length > 5) {
        valor = valor.replace(/^(\d{2})(\d{4})(\d{0,4})$/, '($1) $2-$3');
    } else if (valor.length > 2) {
        valor = valor.replace(/^(\d{2})(\d{0,5})$/, '($1) $2');
    }
    input.value = valor;
}

function simularGoogleOAuth() {
    alert(
        "🐾 Petfy - Login Social:\n\n" +
        "Esta opção conecta diretamente com o Google OAuth quando configurado o Client ID. " +
        "Para a apresentação e testes no XAMPP, utilize o cadastro e login rápido com seu e-mail e senha!"
    );
}

function alternarVisibilidadeSenha(inputId, iconBtn) {
    const input = document.getElementById(inputId);
    if (!input) return;
    if (input.type === 'password') {
        input.type = 'text';
        iconBtn.innerHTML = '<i class="fas fa-eye-slash"></i>';
    } else {
        input.type = 'password';
        iconBtn.innerHTML = '<i class="fas fa-eye"></i>';
    }
}
