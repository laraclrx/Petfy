/**
 * ============================================================
 * Petfy – FRONT-END: Script de Controle do Formulário Multistep de Pet
 * ============================================================
 */

let currentStep = 1;

function goToStep(step) {
    if (step > currentStep) {
        if (currentStep === 1) {
            const nome = document.querySelector('input[name="nome"]')?.value.trim();
            const idade = document.querySelector('input[name="idade"]')?.value.trim();
            
            if (!nome) {
                alert('Por favor, informe o nome do pet antes de avançar.');
                document.querySelector('input[name="nome"]')?.focus();
                return;
            }
            if (!idade) {
                alert('Por favor, informe a idade aproximada do pet.');
                document.querySelector('input[name="idade"]')?.focus();
                return;
            }
        }
    }

    currentStep = step;

    document.querySelectorAll('.form-step-section').forEach((section, index) => {
        section.classList.toggle('active', index + 1 === step);
    });

    document.querySelectorAll('.step-badge').forEach((badge, index) => {
        const badgeStep = index + 1;
        badge.classList.remove('active', 'completed');
        if (badgeStep === step) {
            badge.classList.add('active');
        } else if (badgeStep < step) {
            badge.classList.add('completed');
        }
    });

    document.querySelector('.pet-form-wrapper')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function setOption(groupName, value, element) {
    const hiddenInput = document.getElementById(groupName + 'Input');
    if (hiddenInput) {
        hiddenInput.value = value;
    }

    const parent = element.closest('.options-selector-group');
    if (parent) {
        parent.querySelectorAll('.option-chip-btn').forEach(btn => btn.classList.remove('selected'));
    }
    element.classList.add('selected');

    if (groupName === 'id_especie') {
        const racaSelect = document.getElementById('racaSelect');
        if (racaSelect) {
            const options = racaSelect.querySelectorAll('option');
            options.forEach(opt => {
                const optEspecie = opt.getAttribute('data-especie');
                if (!optEspecie || optEspecie == value || opt.value === '') {
                    opt.style.display = 'block';
                } else {
                    opt.style.display = 'none';
                }
            });
            racaSelect.value = '';
        }
    }

    if (typeof updateLivePreview === 'function') {
        updateLivePreview();
    }
}

function togglePersonalityTag(element, tagValue) {
    element.classList.toggle('selected');
    
    const selectedTags = [];
    document.querySelectorAll('.personality-tag.selected').forEach(tag => {
        selectedTags.push(tag.innerText.trim());
    });

    const hiddenInput = document.getElementById('personalidadesInput');
    if (hiddenInput) {
        hiddenInput.value = selectedTags.join(', ');
    }
}

function previewPetImage(input) {
    const previewContainer = document.getElementById('imagePreviewContainer');
    const previewImg = document.getElementById('imagePreview');
    const dropzoneContent = document.getElementById('uploadDropzoneContent');
    const replicaImg = document.getElementById('replicaPetImage');
    const replicaPlaceholder = document.getElementById('replicaPlaceholder');

    if (input.files && input.files[0]) {
        const file = input.files[0];

        if (file.size > 6 * 1024 * 1024) {
            alert('A imagem selecionada ultrapassa o limite de 6MB.');
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            if (previewImg) {
                previewImg.src = e.target.result;
            }
            if (previewContainer) {
                previewContainer.style.display = 'block';
            }
            if (dropzoneContent) {
                dropzoneContent.style.display = 'none';
            }
            if (replicaImg) {
                replicaImg.src = e.target.result;
                replicaImg.style.display = 'block';
            }
            if (replicaPlaceholder) {
                replicaPlaceholder.style.display = 'none';
            }
        };
        reader.readAsDataURL(file);
    }
}

// ============================================================
// ATUALIZAÇÃO DO LIVE PREVIEW CARD EM TEMPO REAL
// ============================================================
function updateLivePreview() {
    const nomeInput = document.querySelector('input[name="nome"]')?.value.trim();
    const idadeInput = document.querySelector('input[name="idade"]')?.value.trim();
    const cidadeInput = document.querySelector('input[name="cidade"]')?.value.trim();
    const estadoInput = document.querySelector('input[name="estado"]')?.value.trim();
    const sexo = document.getElementById('sexoInput')?.value || 'Macho';
    const porte = document.getElementById('porteInput')?.value || 'M';
    const especieId = document.getElementById('id_especieInput')?.value || '1';
    
    // Nome do Pet
    const replicaName = document.getElementById('replicaPetName');
    if (replicaName) {
        replicaName.innerText = nomeInput || 'Nome do Pet';
    }

    // Sexo Ícone
    const replicaSex = document.getElementById('replicaPetSex');
    if (replicaSex) {
        replicaSex.className = 'pet-card-sex ' + (sexo.toLowerCase() === 'macho' ? 'macho' : 'femea');
        replicaSex.innerHTML = '<i class="fas ' + (sexo.toLowerCase() === 'macho' ? 'fa-mars' : 'fa-venus') + '"></i>';
    }

    // Raça / Espécie
    const replicaBreed = document.getElementById('replicaPetBreed');
    const racaSelect = document.getElementById('racaSelect');
    const racaOutra = document.querySelector('input[name="raca_outra"]')?.value.trim();
    let especieNome = especieId === '1' ? 'Cachorro' : (especieId === '2' ? 'Gato' : 'Pet');
    let racaNome = racaOutra || (racaSelect && racaSelect.options[racaSelect.selectedIndex]?.text) || 'Sem raça definida (SRD)';
    if (racaNome.includes('Selecione')) racaNome = 'Sem raça definida (SRD)';

    if (replicaBreed) {
        replicaBreed.innerText = especieNome + ' • ' + racaNome;
    }

    // Pills de Atributos
    const replicaPorte = document.getElementById('replicaPetPorte');
    if (replicaPorte) replicaPorte.innerText = 'Porte ' + porte;

    const replicaIdade = document.getElementById('replicaPetIdade');
    if (replicaIdade) replicaIdade.innerText = idadeInput || 'Idade a definir';

    // Localização
    const replicaLoc = document.getElementById('replicaPetLocation');
    if (replicaLoc) {
        replicaLoc.innerText = (cidadeInput || 'Sua Cidade') + ' - ' + (estadoInput || 'UF');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const inputsToWatch = ['nome', 'idade', 'cidade', 'estado', 'raca_outra'];
    inputsToWatch.forEach(name => {
        document.querySelector(`input[name="${name}"]`)?.addEventListener('input', updateLivePreview);
    });
    document.getElementById('racaSelect')?.addEventListener('change', updateLivePreview);
});
