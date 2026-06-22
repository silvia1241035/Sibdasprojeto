<?php
require_once __DIR__ . '/includes/funcoes.php';
redirect_if_not_logged();
require_perfil(['Administrador']);

$erro_sistema = '';
$mensagens = [];

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Alternar o estado de leitura de uma mensagem (sem confirmação prévia — ação não destrutiva)
    if (isset($_GET['marcar']) && is_numeric($_GET['marcar'])) {
        $stmt = $ligacao->prepare("UPDATE mensagens_contacto SET lida = 1 - lida WHERE id_mensagem = :id");
        $stmt->execute([':id' => (int)$_GET['marcar']]);
        header('Location: mensagens.php');
        exit;
    }

    $mensagens = $ligacao->query("SELECT * FROM mensagens_contacto ORDER BY enviado_em DESC")->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException $err) {
    $erro_sistema = "Aconteceu um erro na ligação.";
}
$ligacao = null;

$naoLidas = count(array_filter($mensagens, fn($m) => (int)$m->lida === 0));

// Corta a mensagem para não desformatar a tabela — a versão completa é mostrada no modal.
function truncarMensagem(string $texto, int $tamanho = 60): string
{
    return mb_strlen($texto) > $tamanho ? mb_substr($texto, 0, $tamanho) . '…' : $texto;
}
?>

<?php include 'includes/header.php'; ?>

<?php include 'includes/nav.php'; ?>

    <?php include 'includes/sidebar.php'; ?>

    <main class="col-md-9 col-lg-10 p-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0">
                <i class="fa-solid fa-envelope fa-1x mb-3"></i>
                <strong>Mensagens de Contacto</strong>
                <?php if ($naoLidas > 0) : ?>
                    <span class="badge bg-danger ms-2"><?= $naoLidas ?> não lida(s)</span>
                <?php endif; ?>
            </h2>
        </div>

        <?php if (!empty($erro_sistema)) : ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erro_sistema) ?></div>
        <?php elseif (empty($mensagens)) : ?>
            <p class="text-center text-muted mt-4">
                <i class="fa-solid fa-circle-info me-2"></i>Ainda não existem mensagens de contacto.
            </p>
        <?php else : ?>

        <table id="tblMensagens" class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Estado</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Mensagem</th>
                    <th>Data</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mensagens as $msg) : ?>
                    <tr<?= (int)$msg->lida === 0 ? ' class="fw-bold"' : '' ?>>
                        <td>
                            <?php if ((int)$msg->lida === 0) : ?>
                                <span class="badge bg-danger">Não lida</span>
                            <?php else : ?>
                                <span class="badge bg-secondary">Lida</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($msg->nome) ?></td>
                        <td><a href="mailto:<?= htmlspecialchars($msg->email) ?>"><?= htmlspecialchars($msg->email) ?></a></td>
                        <td>
                            <a href="#" class="text-decoration-none ver-mensagem-completa" style="color:#0077a8;"
                               data-nome="<?= htmlspecialchars($msg->nome) ?>"
                               data-email="<?= htmlspecialchars($msg->email) ?>"
                               data-data="<?= htmlspecialchars(date('d-m-Y H:i', strtotime($msg->enviado_em))) ?>"
                               data-mensagem="<?= htmlspecialchars($msg->mensagem) ?>">
                                <?= htmlspecialchars(truncarMensagem($msg->mensagem)) ?>
                            </a>
                        </td>
                        <td style="white-space: nowrap"><?= htmlspecialchars(date('d-m-Y H:i', strtotime($msg->enviado_em))) ?></td>
                        <td class="text-center" style="white-space: nowrap">
                            <a href="mensagens.php?marcar=<?= $msg->id_mensagem ?>" class="acao-tabela acao-editar" title="<?= (int)$msg->lida === 0 ? 'Marcar como lida' : 'Marcar como não lida' ?>">
                                <i class="fa-solid fa-<?= (int)$msg->lida === 0 ? 'envelope-open' : 'envelope' ?> me-1"></i>
                                <?= (int)$msg->lida === 0 ? 'Marcar como lida' : 'Marcar como não lida' ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php endif; ?>

        <!-- Modal com o texto completo da mensagem -->
        <div class="modal fade" id="modalMensagemContacto" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fa-solid fa-envelope-open-text me-2" style="color:#0077a8;"></i>Mensagem de Contacto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-1"><strong>Nome:</strong> <span id="modalMsgNome"></span></p>
                        <p class="mb-1"><strong>Email:</strong> <span id="modalMsgEmail"></span></p>
                        <p class="mb-3"><strong>Data:</strong> <span id="modalMsgData"></span></p>
                        <hr>
                        <p id="modalMsgTexto" style="white-space: pre-wrap;"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <?php include 'includes/sidebarmobile.php'; ?>

<script>
$(document).ready(function () {
    if (!$.fn.DataTable.isDataTable('#tblMensagens')) {
        $('#tblMensagens').DataTable({
            order: [[4, 'desc']],
            pageLength: 10,
            pagingType: "full_numbers",
            scrollX: true,
            autoWidth: false,
            dom: "<'row mb-2'<'col-sm-12 col-md-6'l>><'row'<'col-sm-12'tr>><'row mt-2'<'col-sm-12'B>><'row mt-2'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            buttons: criarBotoesExportacao('mensagens_contacto', 'Mensagens de Contacto'),
            columnDefs: [{ targets: -1, className: 'noExport' }],
            initComplete: adicionarRotuloExportacao,
            language: {
                decimal:        "",
                emptyTable:     "Não existem mensagens.",
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
    }

    $(document).on('click', '.ver-mensagem-completa', function (e) {
        e.preventDefault();
        var $link = $(this);
        $('#modalMsgNome').text($link.data('nome'));
        $('#modalMsgEmail').text($link.data('email'));
        $('#modalMsgData').text($link.data('data'));
        $('#modalMsgTexto').text($link.data('mensagem'));
        new bootstrap.Modal(document.getElementById('modalMensagemContacto')).show();
    });
});
</script>

<?php include 'includes/footer.php'; ?>
