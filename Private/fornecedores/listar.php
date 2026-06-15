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
        SELECT * FROM fornecedores ORDER BY nome
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
                <strong>Listagem de Fornecedores</strong>
            </h2>
            <a href="inserir.php" class="btn" style="background-color: #0077a8; color:white;">
                <i class="fa-solid fa-plus me-1"></i> Novo fornecedor
            </a>
        </div>

        <div class="card p-3 mb-4 shadow-sm">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Pesquisar</label>
                    <input type="text" id="searchAll" class="form-control" placeholder="Nome, email, NIF...">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Ordenar por</label>
                    <select id="sortBy" class="form-select">
                        <option value="nome">Nome</option>
                        <option value="nif">NIF</option>
                        <option value="email">Email</option>
                        <option value="telefone">Contacto telefónico</option>
                        <option value="website">Website</option>
                        <option value="pessoa">Pessoa de contacto</option>
                        <option value="telefonePessoa">Telefone da pessoa de contacto</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Nome da Empresa</th>
                        <th>NIF</th>
                        <th>Contacto telefónico</th>
                        <th>Email</th>
                        <th>Website</th>
                        <th>Pessoa de Contacto</th>
                        <th>Telefone da pessoa de contacto</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody id="fornecedoresTable">
                    <?php if (!empty($erro)) : ?>
                        <tr><td colspan="8" class="text-center text-danger"><?= $erro ?></td></tr>
                    <?php elseif (count($resultados) == 0) : ?>
                        <tr><td colspan="8" class="text-center text-muted">Não existem fornecedores registados.</td></tr>
                    <?php else : ?>
                        <?php foreach ($resultados as $forn) : ?>
                        <tr
                            data-nome="<?= htmlspecialchars($forn->nome) ?>"
                            data-nif="<?= htmlspecialchars($forn->nif ?? '') ?>"
                            data-email="<?= htmlspecialchars($forn->email ?? '') ?>"
                            data-telefone="<?= htmlspecialchars($forn->contacto ?? '') ?>"
                            data-website="<?= htmlspecialchars($forn->website ?? '') ?>"
                            data-pessoa="<?= htmlspecialchars($forn->pessoa_contacto ?? '') ?>"
                            data-telefone-pessoa="<?= htmlspecialchars($forn->telefone_pessoa ?? '') ?>">
                            <td><?= htmlspecialchars($forn->nome) ?></td>
                            <td><?= htmlspecialchars($forn->nif ?? '—') ?></td>
                            <td style="white-space: nowrap"><?= htmlspecialchars($forn->contacto ?? '—') ?></td>
                            <td><?= htmlspecialchars($forn->email ?? '—') ?></td>
                            <td><?= htmlspecialchars($forn->website ?? '—') ?></td>
                            <td><?= htmlspecialchars($forn->pessoa_contacto ?? '—') ?></td>
                            <td style="white-space: nowrap"><?= htmlspecialchars($forn->telefone_pessoa ?? '—') ?></td>
                            <td class="text-center align-middle">
                                <div class="d-flex justify-content-center gap-3">
                                    <a href="detalhes.php?id=<?= $forn->id_fornecedor ?>" class="acao-tabela acao-consultar" title="Ver detalhes">
                                        <i class="fa-solid fa-eye me-1"></i>Consultar
                                    </a>
                                    <a href="editar.php?id=<?= $forn->id_fornecedor ?>" class="acao-tabela acao-editar" title="Editar">
                                        <i class="fa-regular fa-pen-to-square me-1"></i>Editar
                                    </a>
                                    <a href="apagar.php?id=<?= $forn->id_fornecedor ?>" class="acao-tabela acao-eliminar" title="Eliminar">
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
                Nenhum fornecedor encontrado com os critérios selecionados.
            </p>
        </div>

    </main>

    <?php include '../includes/sidebarmobile.php'; ?>

<?php include '../includes/footer.php'; ?>
