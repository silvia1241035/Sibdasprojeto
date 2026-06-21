<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
require_perfil(['Administrador', 'Técnico', 'Profissional de saúde']);

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $resultados = $ligacao->query("
        SELECT e.*,
               l.servico,
               (SELECT f.nome FROM fornecedores f
                JOIN equipamento_fornecedor ef ON ef.id_fornecedor = f.id_fornecedor
                WHERE ef.id_equipamento = e.id_equipamento
                LIMIT 1) AS fornecedor_nome
        FROM equipamentos e
        LEFT JOIN localizacoes l ON l.id_localizacao = e.id_localizacao
        ORDER BY e.codigo_interno
    ")->fetchAll(PDO::FETCH_OBJ);
    $erro = '';
} catch (PDOException $err) {
    $erro = "Aconteceu um erro na ligação.";
    $resultados = [];
}
$ligacao = null;
$crit_map = ['Baixa' => 1, 'Média' => 2, 'Alta' => 3, 'Suporte de vida' => 4];
?>

<?php include '../includes/header.php'; ?>

<?php include '../includes/nav.php'; ?>

<?php include '../includes/sidebar.php'; ?>

    <main class="col-md-9 col-lg-10 p-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0">
                <i class="fa-solid fa-list fa-3"></i>
                <strong>Listagem de Equipamentos</strong>
            </h2>
            <a href="inserir.php" class="btn" style="background-color: #0077a8; color:white;">
                <i class="fa-solid fa-plus me-1"></i> Novo equipamento
            </a>
        </div>

        <div class="alert alert-success alert-dismissible fade show d-none" id="alertaSucesso" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>
            <span id="alertaSucessoMsg">Operação realizada com sucesso.</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <div class="alert alert-danger alert-dismissible fade show d-none" id="alertaErro" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i>
            <span id="alertaErroMsg">Ocorreu um erro. Por favor, tente novamente.</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <div class="card p-3 mb-4 shadow-sm">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Filtrar</label>
                    <input type="text" id="filtroTexto" class="form-control" placeholder="Código, designação, marca, modelo...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Estado</label>
                    <select id="filtroEstado" class="form-select">
                        <option value="">Todos</option>
                        <option value="Ativo">Ativo</option>
                        <option value="Em manutenção">Em manutenção</option>
                        <option value="Inativo">Inativo</option>
                        <option value="Em calibração">Em calibração</option>
                        <option value="Em quarentena">Em quarentena</option>
                        <option value="Abatido">Abatido</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Criticidade</label>
                    <select id="filtroCriticidade" class="form-select">
                        <option value="">Todas</option>
                        <option value="Baixa">Baixa</option>
                        <option value="Média">Média</option>
                        <option value="Alta">Alta</option>
                        <option value="Suporte de vida">Suporte de vida</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Categoria</label>
                    <select id="filtroCategoria" class="form-select">
                        <option value="">Todas</option>
                        <option value="Monitorização">Monitorização</option>
                        <option value="Suporte de vida">Suporte de vida</option>
                        <option value="Terapia">Terapia</option>
                        <option value="Diagnóstico">Diagnóstico</option>
                        <option value="Laboratório">Laboratório</option>
                        <option value="Esterilização">Esterilização</option>
                        <option value="Reabilitação">Reabilitação</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Localização</label>
                    <select id="filtroLocalizacao" class="form-select">
                        <option value="">Todas</option>
                        <?php foreach (array_unique(array_filter(array_column($resultados, 'servico'))) as $sv) : ?>
                            <option value="<?= htmlspecialchars($sv) ?>"><?= htmlspecialchars($sv) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fornecedor</label>
                    <select id="filtroFornecedor" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach (array_unique(array_filter(array_column($resultados, 'fornecedor_nome'))) as $fn) : ?>
                            <option value="<?= htmlspecialchars($fn) ?>"><?= htmlspecialchars($fn) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <table id="tblEquipamentos" class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Código</th>
                        <th>Designação</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Categoria</th>
                        <th>Localização</th>
                        <th>Nº de Série</th>
                        <th>Estado</th>
                        <th>Criticidade</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody id="equipamentosTable">
                    <?php if (!empty($erro)) : ?>
                        <tr><td colspan="10" class="text-center text-danger"><?= $erro ?></td></tr>
                    <?php elseif (count($resultados) == 0) : ?>
                        <tr><td colspan="10" class="text-center text-muted">Não existem equipamentos registados.</td></tr>
                    <?php else : ?>
                        <?php foreach ($resultados as $eq) :
                            $crit_ordem = $crit_map[$eq->criticidade] ?? 0;
                        ?>
                        <tr
                            data-codigo="<?= htmlspecialchars($eq->codigo_interno) ?>"
                            data-designacao="<?= htmlspecialchars($eq->designacao) ?>"
                            data-marca="<?= htmlspecialchars($eq->marca ?? '') ?>"
                            data-modelo="<?= htmlspecialchars($eq->modelo ?? '') ?>"
                            data-categoria="<?= htmlspecialchars($eq->categoria ?? '') ?>"
                            data-servico="<?= htmlspecialchars($eq->servico ?? '') ?>"
                            data-nserie="<?= htmlspecialchars($eq->numero_serie ?? '') ?>"
                            data-estado="<?= htmlspecialchars($eq->estado) ?>"
                            data-criticidade="<?= htmlspecialchars($eq->criticidade ?? '') ?>"
                            data-criticidade-ordem="<?= $crit_ordem ?>"
                            data-fornecedor="<?= htmlspecialchars($eq->fornecedor_nome ?? '') ?>">
                            <td><?= htmlspecialchars($eq->codigo_interno) ?></td>
                            <td><?= htmlspecialchars($eq->designacao) ?></td>
                            <td><?= htmlspecialchars($eq->marca ?? '—') ?></td>
                            <td><?= htmlspecialchars($eq->modelo ?? '—') ?></td>
                            <td><?= htmlspecialchars($eq->categoria ?? '—') ?></td>
                            <td><?= htmlspecialchars($eq->servico ?? '—') ?></td>
                            <td style="white-space: nowrap"><?= htmlspecialchars($eq->numero_serie ?? '—') ?></td>
                            <td><?= htmlspecialchars($eq->estado) ?></td>
                            <td><?= htmlspecialchars($eq->criticidade ?? '—') ?></td>
                            <td class="text-center align-middle">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="detalhes.php?id=<?= aes_encrypt($eq->id_equipamento) ?>" class="acao-tabela acao-consultar" title="Ver detalhes">
                                        <i class="fa-solid fa-eye me-1"></i>Consultar
                                    </a>
                                    <a href="editar.php?id=<?= aes_encrypt($eq->id_equipamento) ?>" class="acao-tabela acao-editar" title="Editar">
                                        <i class="fa-regular fa-pen-to-square me-1"></i>Editar
                                    </a>
                                    <a href="apagar.php?id=<?= aes_encrypt($eq->id_equipamento) ?>" class="acao-tabela acao-eliminar" title="Eliminar">
                                        <i class="fa-solid fa-trash-can me-1"></i>Eliminar
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php if (empty($erro) && count($resultados) > 0) : ?>
            <p class="mb-2">Total: <strong><?= count($resultados) ?></strong></p>
            <?php endif; ?>
    </main>

    <?php include '../includes/sidebarmobile.php'; ?>

<script>
$(document).ready(function () {
    var dt = $('#tblEquipamentos').DataTable({
        pageLength: 5,
        pagingType: "full_numbers",
        scrollX: true,
        autoWidth: false,
        dom: "<'row mb-2'<'col-sm-12 col-md-6'l>><'row'<'col-sm-12'tr>><'row mt-2'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        language: {
            decimal:        "",
            emptyTable:     "Sem dados disponíveis na tabela.",
            info:           "Mostrando _START_ até _END_ de _TOTAL_ registos",
            infoEmpty:      "Mostrando 0 até 0 de 0 registos",
            infoFiltered:   "(Filtrando _MAX_ total de registos)",
            infoPostFix:    "",
            thousands:      ",",
            lengthMenu:     "Mostrando _MENU_ registos por página.",
            loadingRecords: "A carregar...",
            processing:     "A processar...",
            search:         "Filtrar:",
            zeroRecords:    "Nenhum registo encontrado.",
            paginate: {
                first:    "Primeira",
                last:     "Última",
                next:     "Seguinte",
                previous: "Anterior"
            },
            aria: {
                sortAscending:  ": ativar para ordenar coluna de forma ascendente.",
                sortDescending: ": ativar para ordenar coluna de forma decrescente."
            }
        }
    });
    $('#filtroTexto').on('input', function () { dt.search(this.value).draw(); });
    $('#filtroEstado, #filtroCriticidade, #filtroCategoria, #filtroLocalizacao, #filtroFornecedor').on('change', function () { dt.draw(); });
});
</script>

<?php include '../includes/footer.php'; ?>