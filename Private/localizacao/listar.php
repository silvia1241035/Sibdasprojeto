<?php
require_once '../includes/funcoes.php';
redirect_if_not_logged();

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $resultados = $ligacao->query("
        SELECT l.*, COUNT(e.id_equipamento) AS n_equipamentos
        FROM localizacoes l
        LEFT JOIN equipamentos e ON e.id_localizacao = l.id_localizacao
        GROUP BY l.id_localizacao
        ORDER BY l.edificio, l.servico
    ")->fetchAll(PDO::FETCH_OBJ);
    $erro = '';
} catch (PDOException $err) {
    $erro = "Aconteceu um erro na ligação.";
    $resultados = [];
}
$ligacao = null;
?>

<?php include '../includes/header.php'; ?>

<?php include '../includes/nav.php'; ?>

    <?php include '../includes/sidebar.php'; ?>

    <main class="col-md-9 col-lg-10 p-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0">
                <i class="fa-solid fa-list fa-1x mb-3"></i>
                <strong>Listagem de Localizações</strong>
            </h2>
            <a href="inserir.php" class="btn" style="background-color: #0077a8; color:white;">
                <i class="fa-solid fa-plus me-1"></i> Nova localização
            </a>
        </div>

        <div class="card p-3 mb-4 shadow-sm">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Pesquisar</label>
                    <input type="text" id="searchAll" class="form-control" placeholder="Edifício, serviço, sala...">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Edifício</label>
                    <select id="filtroEdificio" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach (array_unique(array_column($resultados, 'edificio')) as $ed) : ?>
                            <option value="<?= htmlspecialchars($ed) ?>"><?= htmlspecialchars($ed) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Serviço/Departamento</label>
                    <select id="filtroServico" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach (array_unique(array_column($resultados, 'servico')) as $sv) : ?>
                            <option value="<?= htmlspecialchars($sv) ?>"><?= htmlspecialchars($sv) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Ordenar por</label>
                    <select id="sortBy" class="form-select">
                        <option value="edificio">Edifício</option>
                        <option value="piso">Piso</option>
                        <option value="servico">Serviço</option>
                        <option value="sala">Sala</option>
                        <option value="nequipamentos">Nº Equipamentos</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Edifício</th>
                        <th>Piso</th>
                        <th>Serviço/Departamento</th>
                        <th>Sala/Gabinete</th>
                        <th class="text-center">Nº de Equipamentos</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody id="localizacoesTable">
                    <?php if (!empty($erro)) : ?>
                        <tr><td colspan="6" class="text-center text-danger"><?= $erro ?></td></tr>
                    <?php elseif (count($resultados) == 0) : ?>
                        <tr><td colspan="6" class="text-center text-muted">Não existem localizações registadas.</td></tr>
                    <?php else : ?>
                        <?php foreach ($resultados as $loc) : ?>
                        <tr
                            data-edificio="<?= htmlspecialchars($loc->edificio) ?>"
                            data-piso="<?= htmlspecialchars($loc->piso ?? '') ?>"
                            data-servico="<?= htmlspecialchars($loc->servico) ?>"
                            data-sala="<?= htmlspecialchars($loc->sala ?? '') ?>"
                            data-nequipamentos="<?= (int)$loc->n_equipamentos ?>">
                            <td><?= htmlspecialchars($loc->edificio) ?></td>
                            <td><?= htmlspecialchars($loc->piso ?? '—') ?></td>
                            <td><?= htmlspecialchars($loc->servico) ?></td>
                            <td><?= htmlspecialchars($loc->sala ?? '—') ?></td>
                            <td class="text-center">
                                <a href="../equipamentos/listar.php" class="badge text-decoration-none text-dark" title="Ver equipamentos nesta localização">
                                    <?= (int)$loc->n_equipamentos ?>
                                </a>
                            </td>
                            <td class="text-center align-middle">
                                <div class="d-flex justify-content-center gap-3">
                                    <a href="detalhes.php?id=<?= $loc->id_localizacao ?>" class="acao-tabela acao-consultar" title="Ver detalhes">
                                        <i class="fa-solid fa-eye me-1"></i>Consultar
                                    </a>
                                    <a href="editar.php?id=<?= $loc->id_localizacao ?>" class="acao-tabela acao-editar" title="Editar">
                                        <i class="fa-regular fa-pen-to-square me-1"></i>Editar
                                    </a>
                                    <a href="apagar.php?id=<?= $loc->id_localizacao ?>" class="acao-tabela acao-eliminar" title="Eliminar">
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
            <p id="noResults" class="text-center text-muted mt-3" style="display: none;">
                <i class="fa-solid fa-magnifying-glass me-2"></i>Nenhuma localização encontrada com os critérios selecionados.
            </p>
        </div>

    </main>

    <?php include '../includes/sidebarmobile.php'; ?>

<?php include '../includes/footer.php'; ?>
