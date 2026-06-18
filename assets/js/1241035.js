
(function () {
    const tbody = document.getElementById('linhasDocumentos');
    const btnAdicionar = document.getElementById('btnAdicionarLinha');

    if (!tbody || !btnAdicionar) return;

    function novaLinha() {
        const modelo = tbody.querySelector('tr.linha-documento');
        const clone = modelo.cloneNode(true);

        // Limpa valores da linha clonada
        clone.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
        clone.querySelectorAll('input').forEach(i => { i.value = ''; i.classList.remove('is-invalid'); });
        clone.querySelectorAll('.invalid-feedback').forEach(d => d.style.display = 'none');

        // Activa botão remover
        clone.querySelector('.btn-remover-linha').disabled = false;

        return clone;
    }

    function actualizarBotoesRemover() {
        const linhas = tbody.querySelectorAll('tr.linha-documento');
        linhas.forEach(l => {
            l.querySelector('.btn-remover-linha').disabled = linhas.length === 1;
        });
    }

    btnAdicionar.addEventListener('click', () => {
        tbody.appendChild(novaLinha());
        actualizarBotoesRemover();
    });

    tbody.addEventListener('click', e => {
        const btn = e.target.closest('.btn-remover-linha');
        if (!btn || btn.disabled) return;
        btn.closest('tr.linha-documento').remove();
        actualizarBotoesRemover();
    });

    // Validação Bootstrap
    document.getElementById('formDocumento').addEventListener('submit', function (e) {
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        this.classList.add('was-validated');
    });
})();

/* Linhas dinâmicas de fornecedores associados — formulário de equipamentos */
(function () {
    const tbody = document.getElementById('linhasFornecedoresEquip');
    const btnAdicionar = document.getElementById('btnAdicionarFornecedor');

    if (!tbody || !btnAdicionar) return;

    function novaLinha() {
        const modelo = tbody.querySelector('tr.linha-fornecedor-equip');
        const clone = modelo.cloneNode(true);

        clone.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
        clone.querySelector('.btn-remover-fornecedor-equip').disabled = false;

        return clone;
    }

    function actualizarBotoesRemover() {
        const linhas = tbody.querySelectorAll('tr.linha-fornecedor-equip');
        linhas.forEach(l => {
            l.querySelector('.btn-remover-fornecedor-equip').disabled = linhas.length === 1;
        });
    }

    btnAdicionar.addEventListener('click', () => {
        tbody.appendChild(novaLinha());
        actualizarBotoesRemover();
    });

    tbody.addEventListener('click', e => {
        const btn = e.target.closest('.btn-remover-fornecedor-equip');
        if (!btn || btn.disabled) return;
        btn.closest('tr.linha-fornecedor-equip').remove();
        actualizarBotoesRemover();
    });
})();

/* Linhas dinâmicas de documentos — tab Documentação do formulário de equipamentos */
(function () {
    const tbody = document.getElementById('linhasDocumentosEquip');
    const btnAdicionar = document.getElementById('btnAdicionarDocumentoEquip');

    if (!tbody || !btnAdicionar) return;

    function novaLinha() {
        const modelo = tbody.querySelector('tr.linha-documento-equip');
        const clone = modelo.cloneNode(true);

        clone.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
        clone.querySelectorAll('input').forEach(i => { i.value = ''; });
        clone.querySelector('.btn-remover-documento-equip').disabled = false;

        return clone;
    }

    function actualizarBotoesRemover() {
        const linhas = tbody.querySelectorAll('tr.linha-documento-equip');
        linhas.forEach(l => {
            l.querySelector('.btn-remover-documento-equip').disabled = linhas.length === 1;
        });
    }

    btnAdicionar.addEventListener('click', () => {
        tbody.appendChild(novaLinha());
        actualizarBotoesRemover();
    });

    tbody.addEventListener('click', e => {
        const btn = e.target.closest('.btn-remover-documento-equip');
        if (!btn || btn.disabled) return;
        btn.closest('tr.linha-documento-equip').remove();
        actualizarBotoesRemover();
    });
})();

/* Ativa a garantia/contrato apenas quando o utilizador preenche a data de fim */
(function () {
    const fim = document.getElementById('fim_garantia_equip');
    if (!fim) return;
    const campos = document.querySelectorAll('.campo-garantia-equip');
    function atualizar() {
        const ativo = fim.value.trim() !== '';
        campos.forEach(c => c.disabled = !ativo);
    }
    fim.addEventListener('input', atualizar);
    atualizar();
})();

/* Validação genérica dos formulários (inserir/editar) */
(function () {
    const form = document.querySelector('#formFornecedor, #formEquipamento, #formLocalizacao, #formDocumento, #formGarantia');
    if (!form) return;

    const btnGuardar   = document.getElementById('btnGuardar');
    const errorBanner  = document.getElementById('errorBanner');
    const obrigatorios = form.querySelectorAll('[required]');

    const controlaBotao = btnGuardar && btnGuardar.disabled;

    function todosPreenchidos() {
        for (const campo of obrigatorios) {
            if (!campo.value.trim()) return false;
        }
        return true;
    }

    function atualizar() {
        if (controlaBotao) {
            btnGuardar.disabled = !todosPreenchidos();
        }
    }

    obrigatorios.forEach(function (campo) {
        campo.addEventListener('input', atualizar);
        campo.addEventListener('change', atualizar);
    });

    form.addEventListener('submit', function (e) {
        let valido = true;
        obrigatorios.forEach(function (campo) {
            if (!campo.value.trim()) {
                campo.classList.add('is-invalid');
                valido = false;
            } else {
                campo.classList.remove('is-invalid');
            }
        });
        if (!valido) {
            e.preventDefault();
            if (errorBanner) errorBanner.classList.remove('d-none');
        }
    });

    atualizar();
})();

/* Filtros DataTables — Localizações (Edifício e Serviço) */
$.fn.dataTable.ext.search.push(function (settings, data) {
    if (settings.nTable.id !== 'tblLocalizacoes') return true;
    var ed = document.getElementById('filtroEdificio');
    var sv = document.getElementById('filtroServico');
    return (!ed || !ed.value || data[0] === ed.value)
        && (!sv || !sv.value || data[2] === sv.value);
});

/* Filtros DataTables — Equipamentos (Estado, Criticidade, Categoria, Localização, Fornecedor) */
$.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
    if (settings.nTable.id !== 'tblEquipamentos') return true;
    var estad = document.getElementById('filtroEstado');
    var crit  = document.getElementById('filtroCriticidade');
    var cat   = document.getElementById('filtroCategoria');
    var loc   = document.getElementById('filtroLocalizacao');
    var forn  = document.getElementById('filtroFornecedor');
    if (estad && estad.value && data[7] !== estad.value) return false;
    if (crit  && crit.value  && data[8] !== crit.value)  return false;
    if (cat   && cat.value   && data[4] !== cat.value)   return false;
    if (loc   && loc.value   && data[5] !== loc.value)   return false;
    if (forn  && forn.value) {
        var nTr = settings.aoData[dataIndex].nTr;
        if (!nTr || nTr.getAttribute('data-fornecedor') !== forn.value) return false;
    }
    return true;
});

/* Filtros DataTables — Documentação (Tipo e Estado de Validade) */
$.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
    if (settings.nTable.id !== 'tblDocumentacao') return true;
    var tipo = document.getElementById('filtroTipo');
    var val  = document.getElementById('filtroValidade');
    if (tipo && tipo.value && data[0] !== tipo.value) return false;
    if (val && val.value) {
        var nTr = settings.aoData[dataIndex].nTr;
        if (!nTr || nTr.getAttribute('data-estadovalidade') !== val.value) return false;
    }
    return true;
});

/* Filtros DataTables — Garantias e Contratos (Estado da garantia e Contrato) */
$.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
    if (settings.nTable.id !== 'tblGarantias') return true;
    var estado   = document.getElementById('filtroEstado');
    var contrato = document.getElementById('filtroContrato');
    if (contrato && contrato.value && data[4] !== contrato.value) return false;
    if (estado && estado.value) {
        var nTr = settings.aoData[dataIndex].nTr;
        if (!nTr || nTr.getAttribute('data-estadogarantia') !== estado.value) return false;
    }
    return true;
});

if (typeof Chart !== 'undefined') {
    const g1 = document.getElementById('equipamentosPorServico');
    if (g1) {
        new Chart(g1, {
            type: 'pie',
            data: {
                labels: ['Urgência', 'Bloco Operatório', 'UCI', 'Imagiologia', 'Laboratório'],
                datasets: [{
                    data: [45, 32, 28, 19, 14],
                    backgroundColor: ['#0077a8', '#28a745', '#ffc107', '#dc3545', '#6f42c1']
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    const g2 = document.getElementById('suporteVidaServico');
    if (g2) {
        new Chart(g2, {
            type: 'pie',
            data: {
                labels: ['UCI', 'Urgência', 'Bloco', 'Pediatria'],
                datasets: [{
                    data: [14, 9, 6, 4],
                    backgroundColor: ['#ffc107', '#dc3545', '#0077a8', '#6f42c1']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    const g3 = document.getElementById('distribuicaoLocalizacao');
    if (g3) {
        new Chart(g3, {
            type: 'bar',
            data: {
                labels: ['Edifício A', 'Edifício B', 'Edifício C', 'Armazém', 'Outros'],
                datasets: [{
                    label: 'Equipamentos',
                    data: [35, 22, 18, 10, 15],
                    borderRadius: 6,
                    backgroundColor: ['#0077a8cc', '#0077a8dd', '#0077a8ee', '#0077a8ff', '#0077a8aa']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 5 } } }
            }
        });
    }
}

document.addEventListener("DOMContentLoaded", () => {
    const page = document.querySelector(".fade-page");
    if (!page) return;
    page.classList.add("show");

    document.querySelectorAll("a").forEach(link => {
        const href = link.getAttribute("href");

        if (href && !href.startsWith("#") && !href.startsWith("javascript")) {
            link.addEventListener("click", e => {
                e.preventDefault();
                page.classList.remove("show");

                setTimeout(() => {
                    window.location = href;
                }, 300);
            });
        }
    });
});
