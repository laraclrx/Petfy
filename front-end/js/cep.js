/**
 * ============================================================
 * Petfy – FRONT-END: Integração Avançada com ViaCEP
 * Auto-preenchimento de endereço e localização de pets
 * ============================================================
 */

function aplicarMascaraCEP(input) {
    let valor = input.value.replace(/\D/g, '');
    if (valor.length > 8) valor = valor.slice(0, 8);
    if (valor.length > 5) {
        valor = valor.replace(/^(\d{5})(\d)/, '$1-$2');
    }
    input.value = valor;
}

function buscarEnderecoPorCEP(cepInputId, ruaInputId, bairroInputId, cidadeInputId, estadoInputId, statusHelperId = 'cep-status-helper') {
    const cepElement = document.getElementById(cepInputId);
    if (!cepElement) return;

    const cepLimpo = cepElement.value.replace(/\D/g, '');
    if (cepLimpo.length !== 8) return;

    const statusHelper = document.getElementById(statusHelperId);
    if (statusHelper) {
        statusHelper.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Buscando endereço...';
        statusHelper.style.color = '#3b6f7a';
        statusHelper.style.display = 'block';
    }

    fetch(`https://viacep.com.br/ws/${cepLimpo}/json/`)
        .then(response => {
            if (!response.ok) throw new Error('Falha na requisição');
            return response.json();
        })
        .then(data => {
            if (data.erro) {
                if (statusHelper) {
                    statusHelper.innerHTML = '<i class="fas fa-exclamation-triangle"></i> CEP não encontrado. Preencha os campos manualmente.';
                    statusHelper.style.color = '#dc2626';
                }
                return;
            }

            const rua = ruaInputId ? document.getElementById(ruaInputId) : null;
            const bairro = bairroInputId ? document.getElementById(bairroInputId) : null;
            const cidade = cidadeInputId ? document.getElementById(cidadeInputId) : null;
            const estado = estadoInputId ? document.getElementById(estadoInputId) : null;

            if (rua && data.logradouro) {
                rua.value = data.logradouro;
                rua.classList.add('auto-filled');
            }
            if (bairro && data.bairro) {
                bairro.value = data.bairro;
                bairro.classList.add('auto-filled');
            }
            if (cidade && data.localidade) {
                cidade.value = data.localidade;
                cidade.classList.add('auto-filled');
            }
            if (estado && data.uf) {
                estado.value = data.uf;
                estado.classList.add('auto-filled');
            }

            if (statusHelper) {
                statusHelper.innerHTML = '<i class="fas fa-check-circle"></i> Endereço localizado: ' + data.localidade + ' - ' + data.uf;
                statusHelper.style.color = '#059669';
            }

            // Foca automaticamente no campo número se existir
            const numeroInput = document.getElementById('reg-numero') || document.getElementById('numero');
            if (numeroInput) {
                numeroInput.focus();
            }
        })
        .catch(err => {
            if (statusHelper) {
                statusHelper.innerHTML = '<i class="fas fa-info-circle"></i> Não foi possível consultar o CEP automaticamente. Digite o endereço manualmente.';
                statusHelper.style.color = '#6b7280';
            }
        });
}
