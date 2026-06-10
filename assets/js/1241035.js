
        /* Filtros e ordenação da tabela de garantias/contratos */
        (function () {
            const searchAll      = document.getElementById('searchAll');
            const filtroEstado   = document.getElementById('filtroEstado');
            const filtroContrato = document.getElementById('filtroContrato');
            const sortBy         = document.getElementById('sortBy');
            const tbody          = document.getElementById('garantiasTable');
            const noResults      = document.getElementById('noResults');

            if (!tbody) return;

            function aplicar() {
                const pesquisa = searchAll.value.toLowerCase().trim();
                const estado   = filtroEstado.value;
                const contrato = filtroContrato.value;
                const ordenar  = sortBy.value;

                /* 1. Ordenar */
                const rows = Array.from(tbody.querySelectorAll('tr'));
                rows.sort(function (a, b) {
                    return (a.dataset[ordenar] || '').toLowerCase().localeCompare((b.dataset[ordenar] || '').toLowerCase(), 'pt');
                });
                rows.forEach(function (r) { tbody.appendChild(r); });

                /* 2. Filtrar */
                var visiveis = 0;
                rows.forEach(function (row) {
                    var texto = [
                        row.dataset.equipamento || '',
                        row.dataset.entidade    || ''
                    ].join(' ').toLowerCase();

                    var ok = (!pesquisa || texto.includes(pesquisa))
                          && (!estado   || row.dataset.estadogarantia === estado)
                          && (!contrato || row.dataset.contrato === contrato);

                    row.style.display = ok ? '' : 'none';
                    if (ok) visiveis++;
                });

                noResults.style.display = visiveis === 0 ? 'block' : 'none';
            }

            [searchAll, filtroEstado, filtroContrato, sortBy].forEach(function (el) {
                el.addEventListener(el.tagName === 'INPUT' ? 'input' : 'change', aplicar);
            });

            aplicar();
        })();

/* Validação genérica dos formulários (inserir/editar) */
(function () {
    // Apanha qualquer um dos formulários do projeto
    const form = document.querySelector('#formFornecedor, #formEquipamento, #formLocalizacao, #formDocumento, #formGarantia');
    if (!form) return;

    const btnGuardar   = document.getElementById('btnGuardar');
    const errorBanner  = document.getElementById('errorBanner');
    const obrigatorios = form.querySelectorAll('[required]');

    // Só controla o botão nas páginas de inserir (onde ele começa disabled)
    const controlaBotao = btnGuardar && btnGuardar.disabled;

    // Verifica se todos os campos obrigatórios estão preenchidos
    function todosPreenchidos() {
        for (const campo of obrigatorios) {
            if (!campo.value.trim()) return false;
        }
        return true;
    }

    // Ativa/desativa o botão Guardar
    function atualizar() {
        if (controlaBotao) {
            btnGuardar.disabled = !todosPreenchidos();
        }
    }

    // Reavalia a cada alteração
    obrigatorios.forEach(function (campo) {
        campo.addEventListener('input', atualizar);
        campo.addEventListener('change', atualizar);
    });

    // Ao submeter: marca os campos vazios e mostra o banner de erro
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

/* Filtros e ordenação da tabela de documentos */
        (function () {
            const searchAll      = document.getElementById('searchAll');
            const filtroTipo     = document.getElementById('filtroTipo');
            const filtroValidade = document.getElementById('filtroValidade');
            const sortBy         = document.getElementById('sortBy');
            const tbody          = document.getElementById('documentosTable');
            const noResults      = document.getElementById('noResults');
 
            if (!tbody) return;
 
            function aplicar() {
                const pesquisa = searchAll.value.toLowerCase().trim();
                const tipo     = filtroTipo.value;
                const validade = filtroValidade.value;
                const ordenar  = sortBy.value;
 
                /* 1. Ordenar */
                const rows = Array.from(tbody.querySelectorAll('tr'));
                rows.sort(function (a, b) {
                    return (a.dataset[ordenar] || '').toLowerCase().localeCompare((b.dataset[ordenar] || '').toLowerCase(), 'pt');
                });
                rows.forEach(function (r) { tbody.appendChild(r); });
 
                /* 2. Filtrar */
                var visiveis = 0;
                rows.forEach(function (row) {
                    var texto = [
                        row.dataset.nome        || '',
                        row.dataset.tipo        || '',
                        row.dataset.equipamento || '',
                        row.dataset.fornecedor  || ''
                    ].join(' ').toLowerCase();
 
                    var ok = (!pesquisa || texto.includes(pesquisa))
                          && (!tipo     || row.dataset.tipo === tipo)
                          && (!validade || row.dataset.estadovalidade === validade);
 
                    row.style.display = ok ? '' : 'none';
                    if (ok) visiveis++;
                });
 
                noResults.style.display = visiveis === 0 ? 'block' : 'none';
            }
 
            [searchAll, filtroTipo, filtroValidade, sortBy].forEach(function (el) {
                el.addEventListener(el.tagName === 'INPUT' ? 'input' : 'change', aplicar);
            });
 
            aplicar();
        })();

/* Filtros e ordenação da tabela de localizações */
        (function () {
            const searchAll      = document.getElementById('searchAll');
            const filtroEdificio = document.getElementById('filtroEdificio');
            const filtroServico  = document.getElementById('filtroServico');
            const sortBy         = document.getElementById('sortBy');
            const tbody          = document.getElementById('localizacoesTable');
            const noResults      = document.getElementById('noResults');
 
            if (!tbody) return;
 
            function aplicar() {
                const pesquisa = searchAll.value.toLowerCase().trim();
                const edificio = filtroEdificio.value;
                const servico  = filtroServico.value;
                const ordenar  = sortBy.value;
 
                /* 1. Ordenar */
                const rows = Array.from(tbody.querySelectorAll('tr'));
                rows.sort(function (a, b) {
                    if (ordenar === 'nequipamentos') {
                        return parseInt(a.dataset.nequipamentos || 0) - parseInt(b.dataset.nequipamentos || 0);
                    }
                    return (a.dataset[ordenar] || '').toLowerCase().localeCompare((b.dataset[ordenar] || '').toLowerCase(), 'pt');
                });
                rows.forEach(function (r) { tbody.appendChild(r); });
 
                /* 2. Filtrar */
                var visiveis = 0;
                rows.forEach(function (row) {
                    var texto = [
                        row.dataset.edificio || '',
                        row.dataset.piso     || '',
                        row.dataset.servico  || '',
                        row.dataset.sala     || ''
                    ].join(' ').toLowerCase();
 
                    var ok = (!pesquisa || texto.includes(pesquisa))
                          && (!edificio || row.dataset.edificio === edificio)
                          && (!servico  || row.dataset.servico  === servico);
 
                    row.style.display = ok ? '' : 'none';
                    if (ok) visiveis++;
                });
 
                noResults.style.display = visiveis === 0 ? 'block' : 'none';
            }
 
            [searchAll, filtroEdificio, filtroServico, sortBy].forEach(function (el) {
                el.addEventListener(el.tagName === 'INPUT' ? 'input' : 'change', aplicar);
            });
 
            aplicar();
        })();


(function () {
    const searchAll         = document.getElementById('searchAll');
    const filtroEstado      = document.getElementById('filtroEstado');
    const filtroCriticidade = document.getElementById('filtroCriticidade');
    const filtroCategoria   = document.getElementById('filtroCategoria');
    const filtroLocalizacao = document.getElementById('filtroLocalizacao');
    const filtroFornecedor  = document.getElementById('filtroFornecedor');
    const sortBy            = document.getElementById('sortBy');
    const tbody             = document.getElementById('equipamentosTable');
    const noResults         = document.getElementById('noResults');

    if (!tbody) return;

    function aplicar() {
        const pesquisa    = searchAll.value.toLowerCase().trim();
        const estado      = filtroEstado.value;
        const criticidade = filtroCriticidade.value;
        const categoria   = filtroCategoria.value;
        const localizacao = filtroLocalizacao.value;
        const fornecedor  = filtroFornecedor.value;
        const ordenar     = sortBy.value;

        const rows = Array.from(tbody.querySelectorAll('tr'));
        rows.sort(function (a, b) {
            if (ordenar === 'criticidade-ordem') {
                return parseInt(a.dataset.criticidadeOrdem || 0) - parseInt(b.dataset.criticidadeOrdem || 0);
            }
            return (a.dataset[ordenar] || '').toLowerCase().localeCompare((b.dataset[ordenar] || '').toLowerCase(), 'pt');
        });
        rows.forEach(function (r) { tbody.appendChild(r); });

        var visiveis = 0;
        rows.forEach(function (row) {
            var texto = [
                row.dataset.codigo, row.dataset.designacao, row.dataset.marca,
                row.dataset.modelo, row.dataset.nserie, row.dataset.servico
            ].join(' ').toLowerCase();

            var ok = (!pesquisa    || texto.includes(pesquisa))
                  && (!estado      || row.dataset.estado      === estado)
                  && (!criticidade || row.dataset.criticidade === criticidade)
                  && (!categoria   || row.dataset.categoria   === categoria)
                  && (!localizacao || row.dataset.servico     === localizacao)
                  && (!fornecedor  || row.dataset.fornecedor  === fornecedor);

            row.style.display = ok ? '' : 'none';
            if (ok) visiveis++;
        });

        noResults.style.display = visiveis === 0 ? 'block' : 'none';
    }

    [searchAll, filtroEstado, filtroCriticidade, filtroCategoria, filtroLocalizacao, filtroFornecedor, sortBy]
        .forEach(function (el) {
            el.addEventListener(el.tagName === 'INPUT' ? 'input' : 'change', aplicar);
        });

    aplicar();
})();

const nome        = document.getElementById('texto_nome');
const nif         = document.getElementById('texto_nif');
const btnGuardar  = document.getElementById('btnGuardar');
const form        = document.getElementById('formFornecedor');
const errorBanner = document.getElementById('errorBanner');

if (form) {

    function verificarObrigatorios() {
        btnGuardar.disabled = !(nome.value.trim() && nif.value.trim());
    }

    nome.addEventListener('input', verificarObrigatorios);
    nif.addEventListener('input', verificarObrigatorios);

    [nome, nif].forEach(function(campo) {
        campo.addEventListener('input', function() {
            if (campo.value.trim()) {
                campo.classList.remove('is-invalid');
            }
        });
    });

    form.addEventListener('submit', function(e) {
        var valido = true;

        [nome, nif].forEach(function(campo) {
            if (!campo.value.trim()) {
                campo.classList.add('is-invalid');
                valido = false;
            } else {
                campo.classList.remove('is-invalid');
            }
        });

        if (!valido) {
            e.preventDefault();
            errorBanner.classList.remove('d-none');
            errorBanner.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    });

}

(function () {
    const searchAll = document.getElementById('searchAll');
    const sortBy    = document.getElementById('sortBy');
    const table     = document.querySelector('#fornecedoresTable');
    const noResults = document.getElementById('noResults');

    if (!table) return;   // só corre na página dos fornecedores

    function filtrar() {
        const termo = searchAll.value.toLowerCase();
        let resultados = 0;

        const linhas = table.querySelectorAll("tr");

        linhas.forEach(row => {

            if (!row.dataset.nome) {
                row.style.display = "none";
                return;
            }

            const nome = row.dataset.nome.toLowerCase();
            const nif = row.dataset.nif.toLowerCase();
            const email = row.dataset.email.toLowerCase();
            const telefone = row.dataset.telefone.toLowerCase();
            const website = row.dataset.website.toLowerCase();
            const pessoa = row.dataset.pessoa.toLowerCase();

            const match =
                nome.includes(termo) ||
                nif.includes(termo) ||
                email.includes(termo) ||
                telefone.includes(termo) ||
                website.includes(termo) ||
                pessoa.includes(termo);

            row.style.display = match ? "" : "none";

            if (match) resultados++;
        });

        noResults.style.display = resultados === 0 ? "block" : "none";
    }

    function ordenar() {
        const criterio = sortBy.value;
        const linhas = [...table.querySelectorAll("tr")].filter(r => r.dataset.nome);

        linhas.sort((a, b) => {
            const valA = a.dataset[criterio].toLowerCase();
            const valB = b.dataset[criterio].toLowerCase();
            return valA.localeCompare(valB);
        });

        linhas.forEach(l => table.appendChild(l));
    }

    searchAll.addEventListener("input", filtrar);
    sortBy.addEventListener("change", ordenar);
})();   


const btn = document.getElementById("btnEntrar");
if (btn) {
    btn.addEventListener("click", function () {
        const email = document.getElementById("email").value.trim();
        const password = document.getElementById("password").value.trim();
        const erro = document.getElementById("mensagemErro");

        const emailCorreto = "admin@invemed.com";
        const passwordCorreta = "1234";

        if (email === "" || password === "") {
            erro.textContent = "Preencha todos os campos.";
            erro.classList.remove("d-none");
            return;
        }

        if (email !== emailCorreto || password !== passwordCorreta) {
            erro.classList.remove("d-none");
            return;
        }

        erro.classList.add("d-none");
        window.location.href = "../../Private/Index.html";
    });

    document.querySelectorAll("#email, #password").forEach(input => {
        input.addEventListener("input", () => {
            document.getElementById("mensagemErro").classList.add("d-none");
        });
    });
}

if (typeof Chart !== 'undefined') {
    const g1 = document.getElementById('equipamentosPorServico');
    if (g1) {
        new Chart(g1, {
            type: 'pie',
            data: {
                labels: ['Urgência', 'Bloco Operatório', 'UCI', 'Imagiologia', 'Laboratório'],
                datasets: [{
                data: [45, 32, 28, 19, 14],
                backgroundColor: [
                    '#0077a8',
                    '#28a745',
                    '#ffc107',
                    '#dc3545',
                    '#6f42c1'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

};
    const g2 = document.getElementById('suporteVidaServico');
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
    
    const g3 = document.getElementById('distribuicaoLocalizacao');
    new Chart(g3, {
        type: 'bar',
        data: {
            labels: ['Edifício A', 'Edifício B', 'Edifício C', 'Armazém', 'Outros'],
            datasets: [{
                label: 'Equipamentos',
                data: [35, 22, 18, 10, 15],
                borderRadius: 6,
                backgroundColor: ['#0077a8cc',
                                '#0077a8dd',
                                '#0077a8ee',
                                '#0077a8ff',
                                '#0077a8aa',]
                
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 5 }
                }
            }
        }
    });};

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






