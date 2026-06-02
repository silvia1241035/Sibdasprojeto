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












const searchAll = document.getElementById("searchAll");
const sortBy = document.getElementById("sortBy");
const table = document.querySelector("#fornecedoresTable");
const noResults = document.getElementById("noResults");

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

document.addEventListener("DOMContentLoaded", function () {

    const ctx = document.getElementById('equipamentosPorServico');

    new Chart(ctx, {
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

});

new Chart(document.getElementById("suporteVidaServico"), {
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

new Chart(document.getElementById("distribuicaoLocalizacao"), {
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
});

document.addEventListener("DOMContentLoaded", () => {
    const page = document.querySelector(".fade-page");
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






