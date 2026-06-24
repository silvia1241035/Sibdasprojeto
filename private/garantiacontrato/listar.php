<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
require_perfil(['Administrador', 'Técnico']);

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $resultados = $ligacao->query("
        SELECT g.*, e.designacao AS equipamento_nome,
            EXISTS (
                SELECT 1 FROM documentacao d
                WHERE d.id_equipamento = g.id_equipamento
                AND d.tipo = 'Contrato de manutenção'
            ) AS tem_documento_contrato
        FROM garantias_contratos g
        JOIN equipamentos e ON g.id_equipamento = e.id_equipamento
        ORDER BY g.data_fim_garantia
    ")->fetchAll(PDO::FETCH_OBJ);
    $erro = '';
} catch (PDOException $err) {
    $erro = "Aconteceu um erro na ligação.";
    $resultados = [];
}
$ligacao = null;

$ativos = array_values(array_filter($resultados, fn($g) => (int)$g->ativo === 1));
$inativos = array_values(array_filter($resultados, fn($g) => (int)$g->ativo === 0));

$hoje    = new DateTime();
$em90dias = (new DateTime())->modify('+90 days');

function estadoGarantiaLinha($g, $hoje, $em90dias)
{
    $fimGarantia = new DateTime($g->data_fim_garantia);
    if ($fimGarantia < $hoje) {
        return 'expirada';
    }
    if ($fimGarantia <= $em90dias) {
        return 'expirar';
    }
    return 'valida';
}
?>

<?php include '../includes/header.php'; ?>

<?php include '../includes/nav.php'; ?>

    <?php include '../includes/sidebar.php'; ?>

            <main class="col-md-12 col-lg-10 col-sm-6">

                <!-- Título + botão novo -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="mb-0">
                        <i class="fa-solid fa-list fa-1x mb-3"></i>
                        <strong>Garantias e Contratos</strong>
                    </h2>
                    <a href="inserir.php" class="btn" style="background-color: #0077a8; color:white;">
                        <i class="fa-solid fa-plus me-1"></i> Novo registo
                    </a>
                </div>

                <!-- Mensagem de sucesso/erro — PHP remove d-none e preenche conforme necessário -->
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

                <!-- Painel de filtros -->
                <div class="card p-3 mb-4 shadow-sm ">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Filtrar</label>
                            <input type="text" id="filtroTexto" class="form-control" placeholder="Equipamento, entidade...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Estado da garantia</label>
                            <select id="filtroEstado" class="form-select">
                                <option value="">Todas</option>
                                <option value="valida">Válida</option>
                                <option value="expirar">A expirar</option>
                                <option value="expirada">Expirada</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Contrato de manutenção</label>
                            <select id="filtroContrato" class="form-select">
                                <option value="">Todos</option>
                                <option value="Sim">Com contrato</option>
                                <option value="Não">Sem contrato</option>
                            </select>
                        </div>
                    </div>
                </div>

                <?php if (!empty($erro)) : ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
                <?php else : ?>

                <ul class="nav nav-tabs" id="garantiasTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-ativas-btn" data-bs-toggle="tab" data-bs-target="#tab-ativas" type="button" role="tab" aria-controls="tab-ativas" aria-selected="true">
                            <i class="fa-solid fa-check me-1"></i> Ativas <span class="badge ms-1" style="background-color: #0077a8;"><?= count($ativos) ?></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-inativas-btn" data-bs-toggle="tab" data-bs-target="#tab-inativas" type="button" role="tab" aria-controls="tab-inativas" aria-selected="false">
                            <i class="fa-solid fa-clock-rotate-left me-1"></i> Histórico <span class="badge bg-secondary ms-1"><?= count($inativos) ?></span>
                        </button>
                    </li>
                </ul>

                <div class="tab-content border border-top-0 rounded-bottom p-3 bg-white mb-3" id="garantiasTabsContent">

                    <!-- ABA: ATIVAS -->
                    <div class="tab-pane fade show active" id="tab-ativas" role="tabpanel" aria-labelledby="tab-ativas-btn">
                        <table id="tblGarantiasAtivas" class="table table-bordered table-striped align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Equipamento</th>
                                    <th>Início Garantia</th>
                                    <th>Fim Garantia</th>
                                    <th>Estado</th>
                                    <th>Contrato</th>
                                    <th>Entidade</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ativos as $g) :
                                    $estadoGarantia = estadoGarantiaLinha($g, $hoje, $em90dias);
                                ?>
                                <tr
                                    data-equipamento="<?= htmlspecialchars($g->equipamento_nome) ?>"
                                    data-inicio="<?= htmlspecialchars($g->data_inicio_garantia ?? '') ?>"
                                    data-fim="<?= htmlspecialchars($g->data_fim_garantia) ?>"
                                    data-entidade="<?= htmlspecialchars($g->entidade_responsavel ?? '') ?>"
                                    data-contrato="<?= htmlspecialchars($g->tem_contrato ?? '') ?>"
                                    data-estadogarantia="<?= $estadoGarantia ?>">
                                    <td><?= htmlspecialchars($g->equipamento_nome) ?></td>
                                    <td><?= htmlspecialchars($g->data_inicio_garantia ?? '—') ?></td>
                                    <td>
                                        <?php if ($estadoGarantia === 'expirada') : ?>
                                            <span class="text-danger"><?= htmlspecialchars($g->data_fim_garantia) ?></span>
                                        <?php elseif ($estadoGarantia === 'expirar') : ?>
                                            <span class="text-warning fw-semibold"><?= htmlspecialchars($g->data_fim_garantia) ?></span>
                                        <?php else : ?>
                                            <?= htmlspecialchars($g->data_fim_garantia) ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($estadoGarantia === 'valida') : ?>
                                            <span class="badge bg-success">Válida</span>
                                        <?php elseif ($estadoGarantia === 'expirar') : ?>
                                            <span class="badge bg-warning text-dark">A expirar</span>
                                        <?php else : ?>
                                            <span class="badge bg-danger">Expirada</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($g->tem_contrato ?? '—') ?>
                                        <?php if ($g->tem_contrato === 'Sim' && !$g->tem_documento_contrato) : ?>
                                            <i class="fa-solid fa-triangle-exclamation text-warning ms-1" title="Sem documento de contrato associado nesta ficha"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($g->entidade_responsavel ?? '—') ?></td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-3">
                                            <a href="detalhes.php?id=<?= aes_encrypt($g->id_garantia) ?>" class="acao-tabela acao-consultar text-decoration-none" title="Ver detalhes">
                                                <i class="fa-solid fa-eye me-1"></i>Consultar
                                            </a>
                                            <a href="editar.php?id=<?= aes_encrypt($g->id_garantia) ?>" class="acao-tabela acao-editar text-decoration-none" title="Editar">
                                                <i class="fa-regular fa-pen-to-square me-1"></i>Editar
                                            </a>
                                            <a href="apagar.php?id=<?= aes_encrypt($g->id_garantia) ?>" class="acao-tabela acao-eliminar text-decoration-none" title="Eliminar">
                                                <i class="fa-solid fa-trash-can me-1"></i>Eliminar
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- ABA: INATIVAS -->
                    <div class="tab-pane fade" id="tab-inativas" role="tabpanel" aria-labelledby="tab-inativas-btn">
                        <table id="tblGarantiasInativas" class="table table-bordered table-striped align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Equipamento</th>
                                    <th>Início Garantia</th>
                                    <th>Fim Garantia</th>
                                    <th>Estado</th>
                                    <th>Contrato</th>
                                    <th>Entidade</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($inativos as $g) :
                                    $estadoGarantia = estadoGarantiaLinha($g, $hoje, $em90dias);
                                ?>
                                <tr
                                    data-equipamento="<?= htmlspecialchars($g->equipamento_nome) ?>"
                                    data-inicio="<?= htmlspecialchars($g->data_inicio_garantia ?? '') ?>"
                                    data-fim="<?= htmlspecialchars($g->data_fim_garantia) ?>"
                                    data-entidade="<?= htmlspecialchars($g->entidade_responsavel ?? '') ?>"
                                    data-contrato="<?= htmlspecialchars($g->tem_contrato ?? '') ?>"
                                    data-estadogarantia="<?= $estadoGarantia ?>">
                                    <td><?= htmlspecialchars($g->equipamento_nome) ?></td>
                                    <td><?= htmlspecialchars($g->data_inicio_garantia ?? '—') ?></td>
                                    <td><?= htmlspecialchars($g->data_fim_garantia) ?></td>
                                    <td>
                                        <?php if ($estadoGarantia === 'valida') : ?>
                                            <span class="badge bg-success">Válida</span>
                                        <?php elseif ($estadoGarantia === 'expirar') : ?>
                                            <span class="badge bg-warning text-dark">A expirar</span>
                                        <?php else : ?>
                                            <span class="badge bg-danger">Expirada</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($g->tem_contrato ?? '—') ?></td>
                                    <td><?= htmlspecialchars($g->entidade_responsavel ?? '—') ?></td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center">
                                            <a href="detalhes.php?id=<?= aes_encrypt($g->id_garantia) ?>" class="acao-tabela acao-consultar text-decoration-none" title="Ver detalhes">
                                                <i class="fa-solid fa-eye me-1"></i>Consultar
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                </div>

                <?php endif; ?>

            </main>

    <!-- Menu Mobile -->
    <?php include '../includes/sidebarmobile.php'; ?>

<script>
$(document).ready(function () {
    function criarOpcoes(mensagemVazio, nomeFicheiro, tituloPdf) {
        return {
            pageLength: 5,
            pagingType: "full_numbers",
            scrollX: true,
            autoWidth: false,
            dom: "<'row mb-2'<'col-sm-12 col-md-6'l>><'row'<'col-sm-12'tr>><'row mt-2'<'col-sm-12'B>><'row mt-2'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            buttons: criarBotoesExportacao(nomeFicheiro, tituloPdf),
            columnDefs: [{ targets: -1, className: 'noExport' }],
            initComplete: adicionarRotuloExportacao,
            language: {
                decimal:        "",
                emptyTable:     mensagemVazio,
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
        };
    }
    var dtAtivas = $('#tblGarantiasAtivas').DataTable(criarOpcoes("Não existem garantias/contratos ativos.", "garantias_contratos_ativos", "Listagem de Garantias e Contratos Ativos"));
    var dtInativas = $('#tblGarantiasInativas').DataTable(criarOpcoes("Não existem garantias/contratos inativos.", "garantias_contratos_inativos", "Listagem de Garantias e Contratos Inativos"));
    $('#filtroTexto').on('input', function () {
        dtAtivas.search(this.value).draw();
        dtInativas.search(this.value).draw();
    });
    $('#filtroEstado, #filtroContrato').on('change', function () {
        dtAtivas.draw();
        dtInativas.draw();
    });
    $('#tab-inativas-btn').on('shown.bs.tab', function () {
        dtInativas.columns.adjust();
    });
});
</script>

<?php include '../includes/footer.php'; ?>
