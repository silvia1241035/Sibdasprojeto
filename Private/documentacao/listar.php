<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $resultados = $ligacao->query("
        SELECT d.*, e.designacao AS equipamento_nome, f.nome AS fornecedor_nome
        FROM documentacao d
        JOIN equipamentos e ON d.id_equipamento = e.id_equipamento
        LEFT JOIN fornecedores f ON d.id_fornecedor = f.id_fornecedor
        ORDER BY d.tipo, d.nome
    ")->fetchAll(PDO::FETCH_OBJ);
    $erro = '';
} catch (PDOException $err) {
    $erro = "Aconteceu um erro na ligação.";
    $resultados = [];
}
$ligacao = null;

$hoje = new DateTime();
?>

<?php include '../includes/header.php'; ?>

<?php include '../includes/nav.php'; ?>

    <?php include '../includes/sidebar.php'; ?>

    <main class="col-md-9 col-lg-10 p-4">

        <!-- Título + botão novo -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0">
                <i class="fa-solid fa-list fa-1x mb-3"></i>
                <strong>Listagem de Documentação</strong>
            </h2>
            <a href="inserir.php" class="btn" style="background-color: #0077a8; color:white;">
                <i class="fa-solid fa-plus me-1"></i> Novo documento
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
        <div class="card p-3 mb-4 shadow-sm">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Filtrar</label>
                    <input type="text" id="filtroTexto" class="form-control" placeholder="Nome, equipamento, fornecedor...">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tipo de documento</label>
                    <select id="filtroTipo" class="form-select">
                        <option value="">Todos</option>
                        <option value="Manual de utilizador">Manual de utilizador</option>
                        <option value="Manual de serviço">Manual de serviço</option>
                        <option value="Certificado de calibração">Certificado de calibração</option>
                        <option value="Contrato de manutenção">Contrato de manutenção</option>
                        <option value="Fatura/Guia de aquisição">Fatura/Guia de aquisição</option>
                        <option value="Declaração de conformidade">Declaração de conformidade</option>
                        <option value="Relatório técnico">Relatório técnico</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Validade</label>
                    <select id="filtroValidade" class="form-select">
                        <option value="">Todas</option>
                        <option value="valido">Válidos</option>
                        <option value="expirado">Expirados</option>
                        <option value="sem">Sem validade</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Tabela -->
        <table id="tblDocumentacao" class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Tipo</th>
                        <th>Nome do Documento</th>
                        <th>Data</th>
                        <th>Validade</th>
                        <th>Equipamento Associado</th>
                        <th>Fornecedor Associado</th>
                        <th class="text-center">Ficheiro</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody id="documentosTable">
                    <?php if (!empty($erro)) : ?>
                        <tr><td colspan="8" class="text-center text-danger"><?= $erro ?></td></tr>
                    <?php elseif (count($resultados) == 0) : ?>
                        <tr><td colspan="8" class="text-center text-muted">Não existem documentos registados.</td></tr>
                    <?php else : ?>
                        <?php foreach ($resultados as $doc) :
                            if ($doc->validade === null) {
                                $estadoValidade = 'sem';
                            } elseif (new DateTime($doc->validade) < $hoje) {
                                $estadoValidade = 'expirado';
                            } else {
                                $estadoValidade = 'valido';
                            }
                        ?>
                        <tr
                            data-tipo="<?= htmlspecialchars($doc->tipo) ?>"
                            data-nome="<?= htmlspecialchars($doc->nome) ?>"
                            data-data="<?= htmlspecialchars($doc->data ?? '') ?>"
                            data-validade="<?= htmlspecialchars($doc->validade ?? '') ?>"
                            data-equipamento="<?= htmlspecialchars($doc->equipamento_nome ?? '') ?>"
                            data-fornecedor="<?= htmlspecialchars($doc->fornecedor_nome ?? '') ?>"
                            data-estadovalidade="<?= $estadoValidade ?>">
                            <td><?= htmlspecialchars($doc->tipo) ?></td>
                            <td><?= htmlspecialchars($doc->nome) ?></td>
                            <td><?= htmlspecialchars($doc->data ?? '—') ?></td>
                            <td>
                                <?php if ($doc->validade === null) : ?>
                                    <span class="text-muted">—</span>
                                <?php elseif ($estadoValidade === 'expirado') : ?>
                                    <span class="text-danger"><?= htmlspecialchars($doc->validade) ?></span>
                                <?php else : ?>
                                    <?= htmlspecialchars($doc->validade) ?>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($doc->equipamento_nome ?? '—') ?></td>
                            <td><?= htmlspecialchars($doc->fornecedor_nome ?? '—') ?></td>
                            <td class="text-center">
                                <?php if (!empty($doc->caminho_ficheiro)) : ?>
                                    <a href="<?= htmlspecialchars($doc->caminho_ficheiro) ?>" target="_blank" style="color:#0077a8;" title="Abrir ficheiro">
                                        <i class="fa-solid fa-file-arrow-down"></i>
                                    </a>
                                <?php else : ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-3">
                                    <a href="detalhes.php?id=<?= $doc->id_documento ?>" class="acao-tabela acao-consultar text-decoration-none" title="Ver detalhes">
                                        <i class="fa-solid fa-eye me-1"></i>Consultar
                                    </a>
                                    <a href="editar.php?id=<?= $doc->id_documento ?>" class="acao-tabela acao-editar text-decoration-none" title="Editar">
                                        <i class="fa-regular fa-pen-to-square me-1"></i>Editar
                                    </a>
                                    <a href="apagar.php?id=<?= $doc->id_documento ?>" class="acao-tabela acao-eliminar text-decoration-none" title="Eliminar">
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
    var dt = $('#tblDocumentacao').DataTable({
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
    $('#filtroTipo, #filtroValidade').on('change', function () { dt.draw(); });
});
</script>

<?php include '../includes/footer.php'; ?>