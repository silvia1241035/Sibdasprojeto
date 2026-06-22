<?php
require_once __DIR__ . '/includes/funcoes.php';
redirect_if_not_logged();   // sem sessão → manda para o login
start_session();

$nome = $_SESSION['utilizador'];
$perfil = $_SESSION['perfil'] ?? '';

$success_message = $_SESSION['success_message'] ?? '';
unset($_SESSION['success_message']);

// Indicadores do dashboard — calculados a partir da base de dados
$totalEquipamentos = 0;
$ativos = 0;
$emManutencao = 0;
$inativos = 0;
$garantiaExpirada = 0;
$semDocumentacao = 0;
$criticidadeElevada = 0;
$garantiaAExpirar = 0;
$dadosServico = [];
$dadosSuporteVida = [];
$dadosEdificio = [];

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $totalEquipamentos = (int)$ligacao->query("SELECT COUNT(*) FROM equipamentos")->fetchColumn();
    $ativos = (int)$ligacao->query("SELECT COUNT(*) FROM equipamentos WHERE estado = 'Ativo'")->fetchColumn();
    $emManutencao = (int)$ligacao->query("SELECT COUNT(*) FROM equipamentos WHERE estado = 'Em manutenção'")->fetchColumn();
    $inativos = (int)$ligacao->query("SELECT COUNT(*) FROM equipamentos WHERE estado = 'Inativo'")->fetchColumn();
    $criticidadeElevada = (int)$ligacao->query("SELECT COUNT(*) FROM equipamentos WHERE criticidade IN ('Alta', 'Suporte de vida')")->fetchColumn();

    $garantiaExpirada = (int)$ligacao->query("
        SELECT COUNT(DISTINCT id_equipamento) FROM garantias_contratos
        WHERE ativo = 1 AND data_fim_garantia < CURDATE()
    ")->fetchColumn();

    $garantiaAExpirar = (int)$ligacao->query("
        SELECT COUNT(DISTINCT id_equipamento) FROM garantias_contratos
        WHERE ativo = 1 AND data_fim_garantia BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
    ")->fetchColumn();

    $semDocumentacao = (int)$ligacao->query("
        SELECT COUNT(*) FROM equipamentos e
        WHERE NOT EXISTS (SELECT 1 FROM documentacao d WHERE d.id_equipamento = e.id_equipamento AND d.ativo = 1)
    ")->fetchColumn();

    $dadosServico = $ligacao->query("
        SELECT l.servico, COUNT(*) AS total
        FROM equipamentos e
        JOIN localizacoes l ON l.id_localizacao = e.id_localizacao
        GROUP BY l.servico
        ORDER BY total DESC
    ")->fetchAll(PDO::FETCH_OBJ);

    $dadosSuporteVida = $ligacao->query("
        SELECT l.servico, COUNT(*) AS total
        FROM equipamentos e
        JOIN localizacoes l ON l.id_localizacao = e.id_localizacao
        WHERE e.criticidade = 'Suporte de vida'
        GROUP BY l.servico
        ORDER BY total DESC
    ")->fetchAll(PDO::FETCH_OBJ);

    $dadosEdificio = $ligacao->query("
        SELECT l.edificio, COUNT(*) AS total
        FROM equipamentos e
        JOIN localizacoes l ON l.id_localizacao = e.id_localizacao
        WHERE e.estado != 'Abatido'
        GROUP BY l.edificio
        ORDER BY total DESC
    ")->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException $err) {
    // Em caso de erro de ligação, os indicadores mantêm-se a 0 / vazios
}
$ligacao = null;
?>

<?php include 'includes/header.php'; ?>



<!-- Toast de sucesso -->
<?php if (!empty($success_message)) : ?>
<div class="position-fixed top-0 end-0 p-3" style="z-index: 11">
    <div id="toastSuccess" class="toast align-items-center text-bg-success border-0 show" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                <?= htmlspecialchars($success_message) ?>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
<?php endif; ?>

<?php $esconder_sidebar = true; ?>
<?php include 'includes/nav.php'; ?>

            <main class="col-12 col-lg-10 p-5 mt-5" style="margin-left: 0; width: 100%;">
                <h1><strong>Bem-vindo à Área Privada da InveMed</strong></h1>
                <h6 class="mb-10">Aqui em baixo podes observar os dados do nosso sistema e aceder rapidamente às principais secções.</h6>
                <br>
                <h3 class="mb-5"> <i class="fa-solid fa-chart-line"></i> &ensp; <strong>Dashboard</strong></h3>
                <h6 class="text-muted mb-3"><i class="fa-solid fa-toggle-on me-1"></i> Estado dos Equipamentos</h6>
                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <div class="p-4 bg-light rounded shadow text-center card-hover d-flex flex-column justify-content-center" style="min-height: 140px;">
                                <h6 class="text-muted d-flex align-items-center justify-content-center" style="min-height: 40px;">Total de Equipamentos</h6>
                                <h2 style="color:#0077a8;font-weight: 700; letter-spacing: -1px;"><?= $totalEquipamentos ?></h2>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-4 bg-light rounded shadow text-center card-hover d-flex flex-column justify-content-center" style="min-height: 140px;">
                                <h6 class="text-muted d-flex align-items-center justify-content-center" style="min-height: 40px;">Equipamentos Ativos</h6>
                                <h2 class="text-success" style="font-weight: 700; letter-spacing: -1px;"><?= $ativos ?></h2>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-4 rounded shadow text-center card-hover d-flex flex-column justify-content-center" style="background-color: #fff8e6; min-height: 140px;">
                            <h6 class="text-muted d-flex align-items-center justify-content-center" style="min-height: 40px;">Equipamentos em manutenção</h6>
                            <h2 class="text-warning" style="font-weight: 700; letter-spacing: -1px;"><?= $emManutencao ?></h2>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-4 rounded shadow text-center card-hover d-flex flex-column justify-content-center" style="background-color: #fff5f5; min-height: 140px;">
                                <h6 class="text-muted d-flex align-items-center justify-content-center" style="min-height: 40px;">Equipamentos inativos</h6>
                                <h2 class="text-danger" style="font-weight: 700; letter-spacing: -1px;"><?= $inativos ?></h2>
                        </div>
                    </div>
                </div>

                <h6 class="text-muted mb-3"><i class="fa-solid fa-triangle-exclamation me-1"></i> Dados a ter Atenção</h6>
                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <div class="p-4 rounded shadow text-center card-hover d-flex flex-column justify-content-center" style="background-color: #fff5f5; min-height: 140px;">
                                <h6 class="text-muted d-flex align-items-center justify-content-center" style="min-height: 40px;">Garantia expirada</h6>
                                <h2 class="text-danger" style="font-weight: 700; letter-spacing: -1px;"><?= $garantiaExpirada ?></h2>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-4 rounded shadow text-center card-hover d-flex flex-column justify-content-center" style="background-color: #fff5f5; min-height: 140px;">
                                <h6 class="text-muted d-flex align-items-center justify-content-center" style="min-height: 40px;">Garantia a expirar em 30 dias</h6>
                                <h2 class="text-danger" style="font-weight: 700; letter-spacing: -1px;"><?= $garantiaAExpirar ?></h2>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-4 rounded shadow text-center card-hover d-flex flex-column justify-content-center" style="background-color: #fff8e6; min-height: 140px;">
                                <h6 class="text-muted d-flex align-items-center justify-content-center" style="min-height: 40px;">Sem documentação</h6>
                                <h2 class="text-warning" style="font-weight: 700; letter-spacing: -1px;"><?= $semDocumentacao ?></h2>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-4 rounded shadow text-center card-hover d-flex flex-column justify-content-center" style="background-color: #fff5f5; min-height: 140px;">
                            <h6 class="text-muted d-flex align-items-center justify-content-center" style="min-height: 40px;">Criticidade elevada</h6>
                            <h2 class="text-danger" style="font-weight: 700; letter-spacing: -1px;"><?= $criticidadeElevada ?></h2>
                        </div>
                    </div>
                </div>

                <h6 class="text-muted mt-4"><i class="fa-solid fa-chart-column me-1"></i> Distribuição dos Equipamentos</h6>
                <div class="row g-4 mt-1 justify-content-center">

                    <div class="col-md-6 col-lg-4 d-flex justify-content-center">
                        <div class="p-4 bg-light rounded shadow text-center card-hover" style="width: 280px">
                            <h6 class="mb-3 text-center">Equipamentos por Serviço</h6>
                            <div class="grafico">
                                <canvas id="equipamentosPorServico"
                                    data-labels='<?= htmlspecialchars(json_encode(array_column($dadosServico, "servico")), ENT_QUOTES) ?>'
                                    data-valores='<?= htmlspecialchars(json_encode(array_map("intval", array_column($dadosServico, "total"))), ENT_QUOTES) ?>'></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4 d-flex justify-content-center">
                        <div class="p-4 bg-light rounded shadow text-center card-hover" style="width: 280px;">
                            <h6 class="mb-3 text-center">Equipamentos de Suporte de Vida por Serviço</h6>
                            <div class="grafico">
                                <canvas id="suporteVidaServico"
                                    data-labels='<?= htmlspecialchars(json_encode(array_column($dadosSuporteVida, "servico")), ENT_QUOTES) ?>'
                                    data-valores='<?= htmlspecialchars(json_encode(array_map("intval", array_column($dadosSuporteVida, "total"))), ENT_QUOTES) ?>'></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4 d-flex justify-content-center">
                        <div class="p-4 bg-light rounded shadow text-center card-hover" style="width: 280px;">
                            <h6 class="mb-3 text-center">Localização dos Equipamentos</h6>
                            <div class="chart-wrapper">
                                <canvas id="distribuicaoLocalizacao"
                                    data-labels='<?= htmlspecialchars(json_encode(array_column($dadosEdificio, "edificio")), ENT_QUOTES) ?>'
                                    data-valores='<?= htmlspecialchars(json_encode(array_map("intval", array_column($dadosEdificio, "total"))), ENT_QUOTES) ?>'></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <h3 class="mt-5"> <i class="fa-solid fa-bolt"></i> &ensp; <strong>Acesso Rápido às Principais Secções</strong></h3>
                
                <div class="row g-4">
                    <?php if ($perfil === 'Administrador') : ?>
                    <div class="col-md-4 d-flex justify-content-center">
                        <div class="card-menu">
                            <a href="gestaoconteudos.php" class="text-decoration-none text-dark">
                                <div class="text-center p-4 bg-light rounded shadow h-100 card-hover" style="max-width: 350px;">
                                    <i class="fa-solid fa-sitemap fa-3x mb-3" style="color:#0077a8"></i>
                                    <h4 class="mb-2">Gestão de conteúdos</h4>
                                    <p class="text-muted mb-0">
                                        Atualização de páginas, textos e imagens do site
                                    </p>
                                </div>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if (in_array($perfil, ['Administrador', 'Técnico', 'Profissional de saúde'], true)) : ?>
                    <div class="col-md-4 d-flex justify-content-center">
                        <div class="card-menu">
                            <a href="equipamentos/listar.php" class="text-decoration-none text-dark">
                                <div class="text-center p-4 bg-light rounded shadow h-100 card-hover" style="max-width: 350px;">
                                    <i class="fa-solid fa-laptop-medical fa-3x mb-3" style="color:#0077a8"></i>
                                    <h4 class="mb-2">Equipamentos</h4>
                                    <p class="text-muted mb-0">
                                        Gestão de Equipamentos
                                    </p>
                                </div>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if (in_array($perfil, ['Administrador', 'Gestor de Logística', 'Profissional de saúde'], true)) : ?>
                    <div class="col-md-4 d-flex justify-content-center">
                        <div class="card-menu">
                            <a href="localizacao/listar.php" class="text-decoration-none text-dark">
                                <div class="text-center p-4 bg-light rounded shadow h-100 card-hover" style="max-width: 350px;">
                                    <i class="fa-solid fa-map-location-dot fa-3x mb-3" style="color:#0077a8"></i>
                                    <h4 class="mb-2">Localização</h4>
                                    <p class="text-muted mb-0">
                                        Localização dos equipamentos
                                    </p>
                                </div>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if (in_array($perfil, ['Administrador', 'Gestor de Logística'], true)) : ?>
                    <div class="col-md-4 d-flex justify-content-center">
                        <div class="card-menu">
                            <a href="fornecedores/listar.php" class="text-decoration-none text-dark">
                                <div class="text-center p-4 bg-light rounded shadow h-100 card-hover" style="max-width: 350px;">
                                    <i class="fa-solid fa-truck fa-3x mb-3" style="color:#0077a8"></i>
                                    <h4 class="mb-2">Fornecedores</h4>
                                    <p class="text-muted mb-0">
                                        Gestão de fornecedores
                                    </p>
                                </div>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if (in_array($perfil, ['Administrador', 'Técnico', 'Profissional de saúde'], true)) : ?>
                    <div class="col-md-4 d-flex justify-content-center">
                        <div class="card-menu">
                            <a href="documentacao/listar.php" class="text-decoration-none text-dark">
                                <div class="text-center p-4 bg-light rounded shadow h-100 card-hover" style="max-width: 350px;">
                                    <i class="fa-solid fa-file-medical fa-3x mb-3" style="color:#0077a8"></i>
                                    <h4 class="mb-2">Documentação</h4>
                                    <p class="text-muted mb-0">
                                        Documentação de equipamentos e fornecedores
                                    </p>
                                </div>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if (in_array($perfil, ['Administrador', 'Técnico'], true)) : ?>
                    <div class="col-md-4 d-flex justify-content-center">
                        <div class="card-menu">
                            <a href="garantiacontrato/listar.php" class="text-decoration-none text-dark">
                                <div class="text-center p-4 bg-light rounded shadow h-100 card-hover" style="max-width: 350px;">
                                    <i class="fa-solid fa-file-contract fa-3x mb-3" style="color:#0077a8"></i>
                                    <h4 class="mb-2">Garantias e Contratos</h4>
                                    <p class="text-muted mb-0">
                                        Garantias e Contratos dos equipamentos
                                    </p>
                                </div>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </main>
        
    <script src="<?php echo BASE_URL; ?>/assets/js/chart.umd.min.js"></script>
    <?php include 'includes/footer.php'; ?>