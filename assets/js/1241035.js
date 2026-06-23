
/* Filtra o fornecedor associado pelos fornecedores realmente associados ao equipamento escolhido */
(function () {
    const selectEquip = document.getElementById('equipamento');
    const selectForn = document.getElementById('fornecedor');

    if (!selectEquip || !selectForn || typeof window.relacoesPorEquipamento === 'undefined') return;

    function atualizarFornecedores() {
        const valorAtual = selectForn.value;
        const relacoes = window.relacoesPorEquipamento[selectEquip.value] || [];
        selectForn.innerHTML = '';
        const opcaoVazia = document.createElement('option');
        opcaoVazia.value = '';
        opcaoVazia.textContent = 'Nenhum / Selecione...';
        selectForn.appendChild(opcaoVazia);
        relacoes.forEach(function (rel) {
            const opcao = document.createElement('option');
            opcao.value = rel.id_fornecedor;
            opcao.textContent = rel.nome;
            if (String(rel.id_fornecedor) === String(valorAtual)) {
                opcao.selected = true;
            }
            selectForn.appendChild(opcao);
        });
    }

    selectEquip.addEventListener('change', atualizarFornecedores);
    if (selectEquip.value) {
        atualizarFornecedores();
    }
})();

(function () {
    const tbody = document.getElementById('linhasDocumentos');
    const btnAdicionar = document.getElementById('btnAdicionarLinha');

    if (!tbody || !btnAdicionar) return;

    function atualizarValidadeObrigatoria(linha) {
        const selectTipo = linha.querySelector('select[name="tipo_documento[]"]');
        const inputValidade = linha.querySelector('input[name="validade_documento[]"]');
        const infoValidade = linha.querySelector('.label-validade-info');
        if (!selectTipo || !inputValidade) return;
        const opcaoSelecionada = selectTipo.options[selectTipo.selectedIndex];
        const obrigatoria = !!opcaoSelecionada && opcaoSelecionada.dataset.requerValidade === '1';
        inputValidade.required = obrigatoria;
        if (infoValidade) infoValidade.classList.toggle('text-danger', obrigatoria);
    }

    function novaLinha() {
        const modelo = tbody.querySelector('tr.linha-documento');
        const clone = modelo.cloneNode(true);

        // Limpa valores da linha clonada
        clone.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
        clone.querySelectorAll('input').forEach(i => { i.value = ''; i.classList.remove('is-invalid'); });
        clone.querySelectorAll('.invalid-feedback').forEach(d => d.style.display = 'none');
        atualizarValidadeObrigatoria(clone);

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

    tbody.addEventListener('change', e => {
        if (e.target.matches('select[name="tipo_documento[]"]')) {
            atualizarValidadeObrigatoria(e.target.closest('tr.linha-documento'));
        }
    });

    // Estado inicial (ex: após reposição de valores devido a erro de validação)
    tbody.querySelectorAll('tr.linha-documento').forEach(atualizarValidadeObrigatoria);

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

/* Linhas dinâmicas de acessórios — formulário de equipamentos */
(function () {
    const tbody = document.getElementById('linhasAcessorios');
    const btnAdicionar = document.getElementById('btnAdicionarAcessorio');

    if (!tbody || !btnAdicionar) return;

    function novaLinha() {
        const modelo = tbody.querySelector('tr.linha-acessorio');
        const clone = modelo.cloneNode(true);

        clone.querySelectorAll('input').forEach(i => { i.value = ''; });
        clone.querySelector('.btn-remover-acessorio').disabled = false;

        return clone;
    }

    function actualizarBotoesRemover() {
        const linhas = tbody.querySelectorAll('tr.linha-acessorio');
        linhas.forEach(l => {
            l.querySelector('.btn-remover-acessorio').disabled = linhas.length === 1;
        });
    }

    btnAdicionar.addEventListener('click', () => {
        tbody.appendChild(novaLinha());
        actualizarBotoesRemover();
    });

    tbody.addEventListener('click', e => {
        const btn = e.target.closest('.btn-remover-acessorio');
        if (!btn || btn.disabled) return;
        btn.closest('tr.linha-acessorio').remove();
        actualizarBotoesRemover();
    });
})();

/* Linhas dinâmicas de documentos — tab Documentação do formulário de equipamentos */
(function () {
    const tbody = document.getElementById('linhasDocumentosEquip');
    const btnAdicionar = document.getElementById('btnAdicionarDocumentoEquip');

    if (!tbody || !btnAdicionar) return;

    function atualizarValidadeObrigatoria(linha) {
        const selectTipo = linha.querySelector('select[name="tipo_documento_equip[]"]');
        const inputValidade = linha.querySelector('input[name="validade_documento_equip[]"]');
        const infoValidade = linha.querySelector('.label-validade-info');
        if (!selectTipo || !inputValidade) return;
        const opcaoSelecionada = selectTipo.options[selectTipo.selectedIndex];
        const obrigatoria = !!opcaoSelecionada && opcaoSelecionada.dataset.requerValidade === '1';
        inputValidade.required = obrigatoria;
        if (infoValidade) infoValidade.classList.toggle('text-danger', obrigatoria);
    }

    function novaLinha() {
        const modelo = tbody.querySelector('tr.linha-documento-equip');
        const clone = modelo.cloneNode(true);

        clone.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
        clone.querySelectorAll('input').forEach(i => { i.value = ''; });
        clone.querySelector('.btn-remover-documento-equip').disabled = false;
        atualizarValidadeObrigatoria(clone);

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

    tbody.addEventListener('change', e => {
        if (e.target.matches('select[name="tipo_documento_equip[]"]')) {
            atualizarValidadeObrigatoria(e.target.closest('tr.linha-documento-equip'));
        }
    });

    tbody.querySelectorAll('tr.linha-documento-equip').forEach(atualizarValidadeObrigatoria);
})();

/* Validade obrigatória por tipo — formulário de editar documento (linha única) */
(function () {
    const form = document.getElementById('formDocumento');
    const selectTipo = document.getElementById('tipo');
    const inputValidade = document.getElementById('validade');
    if (!form || !selectTipo || !inputValidade) return;

    function atualizar() {
        const opcaoSelecionada = selectTipo.options[selectTipo.selectedIndex];
        const obrigatoria = !!opcaoSelecionada && opcaoSelecionada.dataset.requerValidade === '1';
        inputValidade.required = obrigatoria;
        const info = form.querySelector('.label-validade-info');
        if (info) info.classList.toggle('text-danger', obrigatoria);
    }

    selectTipo.addEventListener('change', atualizar);
    atualizar();
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

/* Botões de exportação (CSV, JSON, PDF) reutilizados em todas as listagens com DataTables.
   Respeitam os filtros/pesquisa ativos (search: 'applied') e excluem a coluna de Ações. */
function criarBotoesExportacao(nomeFicheiro, tituloPdf) {
    var opcoesExportacao = { columns: ':not(.noExport)', modifier: { search: 'applied' } };
    return [
        {
            extend: 'csvHtml5',
            text: '<i class="fa-solid fa-file-csv me-1"></i>CSV',
            className: 'btn btn-secondary btn-sm',
            titleAttr: 'Exportar os registos filtrados para um ficheiro CSV (ex: para abrir no Excel).',
            title: nomeFicheiro,
            bom: true,
            exportOptions: opcoesExportacao
        },
        {
            extend: 'pdfHtml5',
            text: '<i class="fa-solid fa-file-pdf me-1"></i>PDF',
            className: 'btn btn-secondary btn-sm',
            titleAttr: 'Exportar os registos filtrados para um documento PDF.',
            title: tituloPdf,
            orientation: 'landscape',
            exportOptions: opcoesExportacao
        },
        {
            text: '<i class="fa-solid fa-file-code me-1"></i>JSON',
            className: 'btn btn-secondary btn-sm',
            titleAttr: 'Exportar os registos filtrados para um ficheiro JSON (ex: para integração com outro sistema).',
            action: function (e, dt) {
                var colunas = dt.columns(':not(.noExport)').indexes().toArray();
                var dados = dt.rows({ search: 'applied' }).indexes().toArray().map(function (indiceLinha) {
                    var obj = {};
                    colunas.forEach(function (indiceColuna) {
                        var cabecalho = dt.column(indiceColuna).header().textContent.trim();
                        var celula = document.createElement('div');
                        celula.innerHTML = dt.cell(indiceLinha, indiceColuna).data();
                        obj[cabecalho] = celula.textContent.trim();
                    });
                    return obj;
                });
                var blob = new Blob([JSON.stringify(dados, null, 2)], { type: 'application/json' });
                var link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = nomeFicheiro + '.json';
                document.body.appendChild(link);
                link.click();
                link.remove();
            }
        }
    ];
}

/* Acrescenta um rótulo visível antes do grupo de botões de exportação de uma DataTable.
   Usar como "initComplete" na configuração da tabela. */
function adicionarRotuloExportacao() {
    $(this.api().table().container()).find('.dt-buttons')
        .before('<small class="text-muted me-2 align-self-center d-inline-block mb-1">Descarregar dados:</small>');
}

/* Filtros DataTables — Localizações (Edifício, Piso e Serviço) */
$.fn.dataTable.ext.search.push(function (settings, data) {
    if (!['tblLocalizacoesAtivas', 'tblLocalizacoesInativas'].includes(settings.nTable.id)) return true;
    var ed = document.getElementById('filtroEdificio');
    var ps = document.getElementById('filtroPiso');
    var sv = document.getElementById('filtroServico');
    return (!ed || !ed.value || data[0] === ed.value)
        && (!ps || !ps.value || data[1] === ps.value)
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
    var nTr = settings.aoData[dataIndex].nTr;
    if (!nTr) return true;
    if (estad && estad.value && nTr.getAttribute('data-estado') !== estad.value) return false;
    if (crit  && crit.value  && nTr.getAttribute('data-criticidade') !== crit.value) return false;
    if (cat   && cat.value   && nTr.getAttribute('data-categoria') !== cat.value) return false;
    if (loc   && loc.value   && nTr.getAttribute('data-servico') !== loc.value) return false;
    if (forn  && forn.value  && nTr.getAttribute('data-fornecedor') !== forn.value) return false;
    return true;
});

/* Filtros DataTables — Documentação (Tipo e Estado de Validade) */
$.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
    if (!['tblDocumentacaoAtivos', 'tblDocumentacaoInativos'].includes(settings.nTable.id)) return true;
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
    if (!['tblGarantiasAtivas', 'tblGarantiasInativas'].includes(settings.nTable.id)) return true;
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
    const paletaCores = ['#0077a8', '#28a745', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14', '#20c997', '#6c757d'];

    function lerDadosGrafico(canvas) {
        return {
            labels: JSON.parse(canvas.dataset.labels || '[]'),
            valores: JSON.parse(canvas.dataset.valores || '[]')
        };
    }

    const g1 = document.getElementById('equipamentosPorServico');
    if (g1) {
        const { labels, valores } = lerDadosGrafico(g1);
        const coresBarras = labels.map((_, i) => paletaCores[i % paletaCores.length]);
        new Chart(g1, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Equipamentos',
                    data: valores,
                    borderRadius: 6,
                    backgroundColor: coresBarras
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { x: { beginAtZero: true, ticks: { stepSize: 5 } } }
            }
        });
    }

    const g2 = document.getElementById('suporteVidaServico');
    if (g2) {
        const { labels, valores } = lerDadosGrafico(g2);
        new Chart(g2, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: valores,
                    backgroundColor: paletaCores
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
        const { labels, valores } = lerDadosGrafico(g3);
        new Chart(g3, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: valores,
                    backgroundColor: paletaCores
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }
}

/* Notificações em tempo real (polling) de eventos de outros utilizadores — só para Administrador */
(function () {
    if (!window.INVEMED_NOTIFICACOES_LOGS) return;
    const container = document.getElementById('toastContainerLogs');
    if (!container) return;

    const baseUrl = window.INVEMED_BASE_URL || '';
    let ultimoId = null;

    const iconesPorTipo = {
        login_sucesso: 'fa-solid fa-right-to-bracket text-success',
        login_falhado: 'fa-solid fa-triangle-exclamation text-danger',
        logout: 'fa-solid fa-right-from-bracket text-secondary',
        inserir: 'fa-solid fa-square-plus text-primary',
        editar: 'fa-regular fa-pen-to-square text-warning',
        eliminar: 'fa-solid fa-trash-can text-danger',
        erro: 'fa-solid fa-circle-exclamation text-danger',
        mensagem: 'fa-solid fa-envelope text-info',
    };

    function mostrarToast(evento) {
        const icone = iconesPorTipo[evento.tipo] || 'fa-solid fa-bell';
        const hora = new Date(evento.criado_em.replace(' ', 'T')).toLocaleTimeString('pt-PT', { hour: '2-digit', minute: '2-digit' });
        const toastEl = document.createElement('div');
        toastEl.className = 'toast';
        toastEl.setAttribute('role', 'alert');
        toastEl.innerHTML = `
            <div class="toast-header">
                <i class="${icone} me-2"></i>
                <strong class="me-auto">${evento.nome_utilizador || 'Sistema'}</strong>
                <small>${hora}</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Fechar"></button>
            </div>
            <div class="toast-body">${evento.descricao || ''}</div>
        `;
        container.appendChild(toastEl);
        const toast = new bootstrap.Toast(toastEl, { delay: 8000 });
        toast.show();
        toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
    }

    function verificarNovosEventos() {
        const url = baseUrl + '/private/logs_novos.php' + (ultimoId !== null ? ('?desde=' + ultimoId) : '');
        fetch(url)
            .then(r => r.json())
            .then(dados => {
                if (ultimoId !== null) {
                    dados.eventos.forEach(mostrarToast);
                }
                ultimoId = dados.max_id;
            })
            .catch(() => {});
    }

    verificarNovosEventos();
    setInterval(verificarNovosEventos, 15000);
})();

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
