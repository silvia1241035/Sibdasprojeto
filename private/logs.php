<?php
require_once __DIR__ . '/includes/funcoes.php';
redirect_if_not_logged();
require_perfil(['Administrador']);

$erro = '';
$logs = [];

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $logs = $ligacao->query("
        SELECT l.id_log, l.tipo, l.descricao, l.ip_address, l.criado_em, u.nome AS nome_utilizador
        FROM logs l
        LEFT JOIN utilizadores u ON u.id_utilizador = l.id_utilizador
        ORDER BY l.id_log DESC
    ")->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException $err) {
    $erro = "Aconteceu um erro na ligação.";
}
$ligacao = null;

$rotulosTipo = [
    'login_sucesso' => 'Login bem-sucedido',
    'login_falhado' => 'Login falhado',
    'logout'        => 'Logout',
    'inserir'       => 'Inserção',
    'editar'        => 'Edição',
    'eliminar'      => 'Eliminação',
    'erro'          => 'Erro',
];

$iconesTipo = [
    'login_sucesso' => 'fa-solid fa-right-to-bracket text-success',
    'login_falhado' => 'fa-solid fa-triangle-exclamation text-danger',
    'logout'        => 'fa-solid fa-right-from-bracket text-secondary',
    'inserir'       => 'fa-solid fa-square-plus text-primary',
    'editar'        => 'fa-regular fa-pen-to-square text-warning',
    'eliminar'      => 'fa-solid fa-trash-can text-danger',
    'erro'          => 'fa-solid fa-circle-exclamation text-danger',
];
?>

<?php include 'includes/header.php'; ?>

<?php include 'includes/nav.php'; ?>

    <?php include 'includes/sidebar.php'; ?>

    <main class="col-md-9 col-lg-10 p-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0">
                <i class="fa-solid fa-clipboard-list fa-1x mb-3"></i>
                <strong>Registo de Eventos</strong>
            </h2>
        </div>

        <div class="card p-3 mb-4 shadow-sm">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="filtroTipo" class="form-label">Tipo de evento</label>
                    <select id="filtroTipo" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach ($rotulosTipo as $valor => $rotulo) : ?>
                            <option value="<?= htmlspecialchars($rotulo) ?>"><?= htmlspecialchars($rotulo) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-8">
                    <label for="filtroTexto" class="form-label">Filtrar</label>
                    <input type="text" id="filtroTexto" class="form-control" placeholder="Descrição, utilizador, IP...">
                </div>
            </div>
        </div>

        <?php if (!empty($erro)) : ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
        <?php else : ?>

        <table id="tblLogs" class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Tipo</th>
                    <th>Descrição</th>
                    <th>Utilizador</th>
                    <th>IP</th>
                    <th>Data/Hora</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log) : ?>
                    <tr>
                        <td>
                            <i class="<?= $iconesTipo[$log->tipo] ?? 'fa-solid fa-bell' ?> me-1"></i>
                            <?= htmlspecialchars($rotulosTipo[$log->tipo] ?? $log->tipo) ?>
                        </td>
                        <td><?= htmlspecialchars($log->descricao ?? '—') ?></td>
                        <td><?= htmlspecialchars($log->nome_utilizador ?? '—') ?></td>
                        <td><?= htmlspecialchars($log->ip_address ?? '—') ?></td>
                        <td style="white-space: nowrap"><?= htmlspecialchars(date('d-m-Y H:i:s', strtotime($log->criado_em))) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php endif; ?>

    </main>

    <?php include 'includes/sidebarmobile.php'; ?>

<script>
$(document).ready(function () {
    var dtLogs = $('#tblLogs').DataTable({
        order: [[4, 'desc']],
        pageLength: 10,
        pagingType: "full_numbers",
        scrollX: true,
        autoWidth: false,
        dom: "<'row mb-2'<'col-sm-12 col-md-6'l>><'row'<'col-sm-12'tr>><'row mt-2'<'col-sm-12'B>><'row mt-2'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        buttons: criarBotoesExportacao('registo_eventos', 'Registo de Eventos'),
        initComplete: adicionarRotuloExportacao,
        language: {
            decimal:        "",
            emptyTable:     "Não existem eventos registados.",
            info:           "Mostrando _START_ até _END_ de _TOTAL_ registos",
            infoEmpty:      "Mostrando 0 até 0 de 0 registos",
            infoFiltered:   "(Filtrando _MAX_ total de registos)",
            infoPostFix:    "",
            thousands:      ",",
            lengthMenu:     "Mostrando _MENU_ registos por página.",
            loadingRecords: "A carregar...",
            processing:     "A processar...",
            search:         "Filtrar:",
            zeroRecords:    "Nenhum evento encontrado.",
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
    $('#filtroTexto').on('input', function () {
        dtLogs.search(this.value).draw();
    });
    $('#filtroTipo').on('change', function () {
        dtLogs.column(0).search(this.value, true, false).draw();
    });
});
</script>

<?php include 'includes/footer.php'; ?>
