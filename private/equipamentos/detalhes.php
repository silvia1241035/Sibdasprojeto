<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
require_perfil(['Administrador', 'Técnico', 'Profissional de saúde']);

if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}

// 1. Recolher e validar o ID encriptado
$idEncrypted = $_GET['id'] ?? null;
$idEquipamento = aes_decrypt($idEncrypted);

if (!$idEquipamento || !is_numeric($idEquipamento)) {
    header('Location: listar.php');
    exit;
}

$erro_sistema = '';
$equipamento = null;
$fornecedoresAssociados = [];
$documentos = [];
$documentosInativos = [];
$garantias = [];
$acessorios = [];
$perfil = $_SESSION['perfil'] ?? '';

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $ligacao->prepare("
        SELECT e.*, l.edificio, l.piso, l.servico, l.sala
        FROM equipamentos e
        LEFT JOIN localizacoes l ON l.id_localizacao = e.id_localizacao
        WHERE e.id_equipamento = :id
    ");
    $stmt->execute([':id' => $idEquipamento]);
    $equipamento = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$equipamento) {
        header('Location: listar.php');
        exit;
    }

    $stmt = $ligacao->prepare("
        SELECT a.id_acessorio, a.codigo, a.nome, a.observacoes, f.nome AS fornecedor_nome
        FROM acessorios a
        LEFT JOIN fornecedores f ON f.id_fornecedor = a.id_fornecedor
        WHERE a.id_equipamento = :id
        ORDER BY a.id_acessorio
    ");
    $stmt->execute([':id' => $idEquipamento]);
    $acessorios = $stmt->fetchAll(PDO::FETCH_OBJ);

    $stmt = $ligacao->prepare("
        SELECT f.*, ef.tipo AS tipo_relacao
        FROM equipamento_fornecedor ef
        JOIN fornecedores f ON f.id_fornecedor = ef.id_fornecedor
        WHERE ef.id_equipamento = :id
        ORDER BY f.nome
    ");
    $stmt->execute([':id' => $idEquipamento]);
    $fornecedoresAssociados = $stmt->fetchAll(PDO::FETCH_OBJ);

    $stmt = $ligacao->prepare("
        SELECT * FROM documentacao
        WHERE id_equipamento = :id AND ativo = 1
        ORDER BY tipo, nome
    ");
    $stmt->execute([':id' => $idEquipamento]);
    $documentos = $stmt->fetchAll(PDO::FETCH_OBJ);

    $stmt = $ligacao->prepare("
        SELECT * FROM documentacao
        WHERE id_equipamento = :id AND ativo = 0
        ORDER BY tipo, nome
    ");
    $stmt->execute([':id' => $idEquipamento]);
    $documentosInativos = $stmt->fetchAll(PDO::FETCH_OBJ);

    $stmt = $ligacao->prepare("
        SELECT * FROM garantias_contratos
        WHERE id_equipamento = :id AND ativo = 1
        ORDER BY data_fim_garantia
    ");
    $stmt->execute([':id' => $idEquipamento]);
    $garantias = $stmt->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException $err) {
    $erro_sistema = "Aconteceu um erro na ligação.";
}
$ligacao = null;

$badgeEstado = [
    'Ativo'           => 'badge-estado-ativo',
    'Em manutenção'   => 'badge-estado-manutencao',
    'Inativo'         => 'badge-estado-inativo',
    'Em calibração'   => 'badge-estado-calibracao',
    'Em quarentena'   => 'badge-estado-quarentena',
    'Abatido'         => 'badge-estado-abatido',
];
$badgeCriticidade = [
    'Baixa'            => 'badge-critico-baixa',
    'Média'            => 'badge-critico-media',
    'Alta'             => 'badge-critico-alta',
    'Suporte de vida'  => 'badge-critico-suporte',
];

$hoje = new DateTime();
$em90dias = (new DateTime())->modify('+90 days');
?>

<?php include '../includes/header.php'; ?>

<?php include '../includes/nav.php'; ?>

    <?php include '../includes/sidebar.php'; ?>

    <main class="col-md-9 col-lg-10 p-4">

        <?php if (!empty($erro_sistema)) : ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erro_sistema) ?></div>
        <?php else : ?>

        <!-- Título + ações -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0">
                <i class="fa-solid fa-laptop-medical me-2"></i>
                <strong>Detalhes do Equipamento</strong>
            </h2>
            <div class="d-flex gap-2">
                <a href="listar.php" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Voltar
                </a>
                <a href="editar.php?id=<?= htmlspecialchars($idEncrypted) ?>" class="btn btn-primary" style="background-color: #0077a8; border-color: #0077a8;">
                    <i class="fa-regular fa-pen-to-square me-1"></i> Editar
                </a>
            </div>
        </div>

        <!-- Cartão de identificação (visível em todas as abas) -->
        <div class="card shadow-sm mb-4">
            <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <div class="text-muted small">Código interno</div>
                    <h3 class="mb-1"><?= htmlspecialchars($equipamento->codigo_interno) ?></h3>
                    <div class="fs-5"><?= htmlspecialchars($equipamento->designacao) ?></div>
                </div>
                <div class="text-end">
                    <span class="badge <?= $badgeEstado[$equipamento->estado] ?? 'bg-secondary' ?> mb-2 fs-6"><?= htmlspecialchars($equipamento->estado) ?></span><br>
                    <span class="badge <?= $badgeCriticidade[$equipamento->criticidade] ?? 'bg-secondary' ?> fs-6">Criticidade: <?= htmlspecialchars($equipamento->criticidade ?? '—') ?></span>
                </div>
            </div>
        </div>

        <!-- ABAS -->
        <ul class="nav nav-tabs" id="detalhesTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-detalhes-btn" data-bs-toggle="tab" data-bs-target="#tab-detalhes" type="button" role="tab" aria-controls="tab-detalhes" aria-selected="true">
                    <i class="fa-solid fa-circle-info me-1"></i> Detalhes
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-fornecedor-btn" data-bs-toggle="tab" data-bs-target="#tab-fornecedor" type="button" role="tab" aria-controls="tab-fornecedor" aria-selected="false">
                    <i class="fa-solid fa-truck me-1"></i> Fornecedor
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-localizacao-btn" data-bs-toggle="tab" data-bs-target="#tab-localizacao" type="button" role="tab" aria-controls="tab-localizacao" aria-selected="false">
                    <i class="fa-solid fa-map-location-dot me-1"></i> Localização
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-documentacao-btn" data-bs-toggle="tab" data-bs-target="#tab-documentacao" type="button" role="tab" aria-controls="tab-documentacao" aria-selected="false">
                    <i class="fa-solid fa-file-medical me-1"></i> Documentação
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-garantia-btn" data-bs-toggle="tab" data-bs-target="#tab-garantia" type="button" role="tab" aria-controls="tab-garantia" aria-selected="false">
                    <i class="fa-solid fa-file-contract me-1"></i> Garantia e Contrato
                </button>
            </li>
        </ul>

        <div class="tab-content border border-top-0 rounded-bottom p-4 bg-white" id="detalhesTabsContent">

            <!-- ABA 1: DETALHES DO EQUIPAMENTO -->
            <div class="tab-pane fade show active" id="tab-detalhes" role="tabpanel" aria-labelledby="tab-detalhes-btn">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="detalhe-label">Código interno</div>
                        <div class="detalhe-valor"><?= htmlspecialchars($equipamento->codigo_interno) ?></div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="detalhe-label">Designação</div>
                        <div class="detalhe-valor"><?= htmlspecialchars($equipamento->designacao) ?></div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="detalhe-label">Categoria</div>
                        <div class="detalhe-valor"><?= htmlspecialchars($equipamento->categoria ?? '—') ?></div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="detalhe-label">Marca</div>
                        <div class="detalhe-valor"><?= htmlspecialchars($equipamento->marca ?? '—') ?></div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="detalhe-label">Modelo</div>
                        <div class="detalhe-valor"><?= htmlspecialchars($equipamento->modelo ?? '—') ?></div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="detalhe-label">Nº de série</div>
                        <div class="detalhe-valor"><?= htmlspecialchars($equipamento->numero_serie ?? '—') ?></div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="detalhe-label">Fabricante</div>
                        <div class="detalhe-valor"><?= htmlspecialchars($equipamento->fabricante ?? '—') ?></div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="detalhe-label">Data de aquisição</div>
                        <div class="detalhe-valor"><?= htmlspecialchars($equipamento->data_aquisicao ?? '—') ?></div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="detalhe-label">Ano de fabrico</div>
                        <div class="detalhe-valor"><?= htmlspecialchars($equipamento->ano_fabrico ?? '—') ?></div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="detalhe-label">Custo de aquisição</div>
                        <div class="detalhe-valor"><?= $equipamento->custo_aquisicao !== null ? htmlspecialchars(number_format((float)$equipamento->custo_aquisicao, 2, ',', '.')) . ' €' : '—' ?></div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="detalhe-label">Tipo de entrada</div>
                        <div class="detalhe-valor"><?= htmlspecialchars($equipamento->tipo_entrada ?? '—') ?></div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="detalhe-label">Estado</div>
                        <div><span class="badge <?= $badgeEstado[$equipamento->estado] ?? 'bg-secondary' ?>"><?= htmlspecialchars($equipamento->estado) ?></span></div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="detalhe-label">Criticidade</div>
                        <div><span class="badge <?= $badgeCriticidade[$equipamento->criticidade] ?? 'bg-secondary' ?>"><?= htmlspecialchars($equipamento->criticidade ?? '—') ?></span></div>
                    </div>
                    <div class="col-12 mb-2">
                        <div class="detalhe-label">Observações</div>
                        <div class="detalhe-valor fw-normal"><?= htmlspecialchars($equipamento->observacoes ?? '—') ?></div>
                    </div>
                </div>
            </div>

            <!-- ABA 2: FORNECEDOR -->
            <div class="tab-pane fade" id="tab-fornecedor" role="tabpanel" aria-labelledby="tab-fornecedor-btn">
                <?php if (empty($fornecedoresAssociados)) : ?>
                    <p class="text-center text-muted">
                        <i class="fa-solid fa-truck me-2"></i>Este equipamento ainda não tem fornecedores associados.
                    </p>
                <?php else : ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Nome</th>
                                    <th>Tipo</th>
                                    <th>NIF</th>
                                    <th>Contacto</th>
                                    <th>Email</th>
                                    <?php if (in_array($perfil, ['Administrador', 'Gestor de Logística'], true)) : ?>
                                    <th class="text-center">Ficha</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($fornecedoresAssociados as $forn) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($forn->nome) ?></td>
                                    <td><?= htmlspecialchars($forn->tipo_relacao ?? '—') ?></td>
                                    <td><?= htmlspecialchars($forn->nif) ?></td>
                                    <td><?= htmlspecialchars($forn->contacto ?? '—') ?></td>
                                    <td><?= htmlspecialchars($forn->email ?? '—') ?></td>
                                    <?php if (in_array($perfil, ['Administrador', 'Gestor de Logística'], true)) : ?>
                                    <td class="text-center">
                                        <a href="../fornecedores/detalhes.php?id=<?= aes_encrypt($forn->id_fornecedor) ?>" class="text-decoration-none" style="color:#0077a8;" title="Ver ficha completa">
                                            <i class="fa-solid fa-up-right-from-square"></i>
                                        </a>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ABA 3: LOCALIZAÇÃO -->
            <div class="tab-pane fade" id="tab-localizacao" role="tabpanel" aria-labelledby="tab-localizacao-btn">
                <?php if (empty($equipamento->id_localizacao)) : ?>
                    <p class="text-center text-muted">
                        <i class="fa-solid fa-map-location-dot me-2"></i>Este equipamento ainda não tem localização associada.
                    </p>
                <?php else : ?>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="detalhe-label">Edifício</div>
                            <div class="detalhe-valor"><?= htmlspecialchars($equipamento->edificio ?? '—') ?></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="detalhe-label">Piso</div>
                            <div class="detalhe-valor"><?= htmlspecialchars($equipamento->piso ?? '—') ?></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="detalhe-label">Serviço / Departamento</div>
                            <div class="detalhe-valor"><?= htmlspecialchars($equipamento->servico ?? '—') ?></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="detalhe-label">Sala / Gabinete</div>
                            <div class="detalhe-valor"><?= htmlspecialchars($equipamento->sala ?? '—') ?></div>
                        </div>
                    </div>
                <?php endif; ?>

                <hr class="my-4">

                <h6 class="mb-3">
                    <i class="fa-solid fa-diagram-project me-2" style="color:#0077a8;"></i>
                    Acessórios / componentes deste equipamento
                    <span class="badge bg-primary ms-1"><?= count($acessorios) ?></span>
                </h6>
                <?php if (empty($acessorios)) : ?>
                    <p class="text-center text-muted">
                        <i class="fa-solid fa-circle-info me-2"></i>Este equipamento ainda não tem acessórios associados.
                    </p>
                <?php else : ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Código</th>
                                    <th>Nome</th>
                                    <th>Fornecedor</th>
                                    <th>Observações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($acessorios as $acessorio) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($acessorio->codigo ?? '—') ?></td>
                                    <td><?= htmlspecialchars($acessorio->nome) ?></td>
                                    <td><?= htmlspecialchars($acessorio->fornecedor_nome ?? '—') ?></td>
                                    <td><?= htmlspecialchars($acessorio->observacoes ?? '—') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ABA 4: DOCUMENTAÇÃO DESTE EQUIPAMENTO -->
            <div class="tab-pane fade" id="tab-documentacao" role="tabpanel" aria-labelledby="tab-documentacao-btn">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Documentos associados a este equipamento</span>
                    <?php if (in_array($perfil, ['Administrador', 'Técnico'], true)) : ?>
                    <a href="../documentacao/inserir.php" class="btn btn-sm btn-primary" style="background-color: #0077a8; border-color: #0077a8;">
                        <i class="fa-solid fa-plus me-1"></i> Adicionar documento
                    </a>
                    <?php endif; ?>
                </div>
                <?php if (empty($documentos)) : ?>
                    <p class="text-center text-muted">
                        <i class="fa-solid fa-folder-open me-2"></i>Este equipamento ainda não tem documentos associados.
                    </p>
                <?php else : ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Tipo</th>
                                    <th>Nome</th>
                                    <th>Data</th>
                                    <th>Validade</th>
                                    <th class="text-center">Ficheiro</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($documentos as $doc) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($doc->tipo) ?></td>
                                    <td><?= htmlspecialchars($doc->nome) ?></td>
                                    <td><?= htmlspecialchars($doc->data) ?></td>
                                    <td>
                                        <?php if ($doc->validade === null) : ?>
                                            <span class="text-muted">—</span>
                                        <?php elseif (new DateTime($doc->validade) < $hoje) : ?>
                                            <span class="badge bg-danger"><?= htmlspecialchars($doc->validade) ?></span>
                                        <?php else : ?>
                                            <span class="badge bg-success"><?= htmlspecialchars($doc->validade) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if (!empty($doc->caminho_ficheiro)) : ?>
                                            <a href="<?= htmlspecialchars($doc->caminho_ficheiro) ?>" target="_blank" class="text-decoration-none" style="color:#0077a8;" title="Descarregar">
                                                <i class="fa-solid fa-download me-1"></i>Descarregar
                                            </a>
                                        <?php else : ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <?php if (!empty($documentosInativos)) : ?>
                <div class="accordion mt-3" id="accordionHistoricoDocumentos">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#colapsoHistoricoDocumentos" aria-expanded="false">
                                <i class="fa-solid fa-clock-rotate-left me-2"></i>
                                Histórico de documentos inativos (<?= count($documentosInativos) ?>)
                            </button>
                        </h2>
                        <div id="colapsoHistoricoDocumentos" class="accordion-collapse collapse">
                            <div class="accordion-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped align-middle mb-0">
                                        <thead class="table-secondary">
                                            <tr>
                                                <th>Tipo</th>
                                                <th>Nome</th>
                                                <th>Data</th>
                                                <th>Validade</th>
                                                <th class="text-center">Ficheiro</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($documentosInativos as $doc) : ?>
                                            <tr class="text-muted">
                                                <td><?= htmlspecialchars($doc->tipo) ?></td>
                                                <td><?= htmlspecialchars($doc->nome) ?></td>
                                                <td><?= htmlspecialchars($doc->data) ?></td>
                                                <td><?= htmlspecialchars($doc->validade ?? '—') ?></td>
                                                <td class="text-center">
                                                    <?php if (!empty($doc->caminho_ficheiro)) : ?>
                                                        <a href="<?= htmlspecialchars($doc->caminho_ficheiro) ?>" target="_blank" class="text-decoration-none" style="color:#0077a8;" title="Descarregar">
                                                            <i class="fa-solid fa-download me-1"></i>Descarregar
                                                        </a>
                                                    <?php else : ?>
                                                        <span class="text-muted">—</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- ABA 5: GARANTIA E CONTRATO -->
            <div class="tab-pane fade" id="tab-garantia" role="tabpanel" aria-labelledby="tab-garantia-btn">
                <?php if (empty($garantias)) : ?>
                    <p class="text-center text-muted">
                        <i class="fa-solid fa-file-contract me-2"></i>Este equipamento ainda não tem garantias ou contratos associados.
                    </p>
                <?php else : ?>
                    <?php foreach ($garantias as $i => $g) :
                        $fimGarantia = new DateTime($g->data_fim_garantia);
                        if ($fimGarantia < $hoje) {
                            $estadoGarantia = 'expirada';
                        } elseif ($fimGarantia <= $em90dias) {
                            $estadoGarantia = 'expirar';
                        } else {
                            $estadoGarantia = 'valida';
                        }
                    ?>
                    <div class="row<?= $i < count($garantias) - 1 ? ' pb-3 mb-3 border-bottom' : '' ?>">
                        <div class="col-md-4 mb-3">
                            <div class="detalhe-label">Entidade responsável</div>
                            <div class="detalhe-valor"><?= htmlspecialchars($g->entidade_responsavel ?? '—') ?></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="detalhe-label">Data de início da garantia</div>
                            <div class="detalhe-valor"><?= htmlspecialchars($g->data_inicio_garantia ?? '—') ?></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="detalhe-label">Data de fim da garantia</div>
                            <div class="detalhe-valor"><?= htmlspecialchars($g->data_fim_garantia) ?></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="detalhe-label">Estado da garantia</div>
                            <div>
                                <?php if ($estadoGarantia === 'valida') : ?>
                                    <span class="badge bg-success">Válida</span>
                                <?php elseif ($estadoGarantia === 'expirar') : ?>
                                    <span class="badge bg-warning text-dark">A expirar</span>
                                <?php else : ?>
                                    <span class="badge bg-danger">Expirada</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="detalhe-label">Contrato de manutenção</div>
                            <div class="detalhe-valor"><?= htmlspecialchars($g->tem_contrato ?? '—') ?></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="detalhe-label">Tipo de contrato</div>
                            <div class="detalhe-valor"><?= htmlspecialchars($g->tipo_contrato ?? '—') ?></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="detalhe-label">Periodicidade</div>
                            <div class="detalhe-valor"><?= htmlspecialchars($g->periodicidade ?? '—') ?></div>
                        </div>
                        <div class="col-12 mb-2">
                            <div class="detalhe-label">Observações</div>
                            <div class="detalhe-valor fw-normal"><?= htmlspecialchars($g->observacoes ?? '—') ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <a href="../garantiacontrato/listar.php" class="btn btn-sm btn-outline-primary">
                        <i class="fa-solid fa-up-right-from-square me-1"></i> Ver todas as garantias e contratos
                    </a>
                <?php endif; ?>
            </div>

        </div>

        <?php endif; ?>

    </main>

    <?php include '../includes/sidebarmobile.php'; ?>

<?php include '../includes/footer.php'; ?>
