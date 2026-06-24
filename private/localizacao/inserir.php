<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
require_perfil(['Administrador', 'Gestor de Logística']);

$erros = [];
$erro_sistema = '';

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $err) {
    $erro_sistema = "Aconteceu um erro na ligação.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Recolher dados
    $edificio = trim($_POST['edificio_localizacao'] ?? '');
    $piso     = trim($_POST['piso_localizacao'] ?? '');
    $servico  = trim($_POST['servico_localizacao'] ?? '');
    $sala     = trim($_POST['sala_localizacao'] ?? '');

    // 2. Validar dados
    if (empty($edificio)) {
        $erros[] = "O campo Edifício é obrigatório.";
    } elseif (preg_match('/^\d+$/', $edificio)) {
        $erros[] = "O campo Edifício não pode conter apenas números.";
    }

    if (empty($servico)) {
        $erros[] = "O campo Serviço/Departamento é obrigatório.";
    } elseif (preg_match('/^\d+$/', $servico)) {
        $erros[] = "O campo Serviço/Departamento não pode conter apenas números.";
    }

    // 3. Normalizar dados
    $edificio = ucwords(strtolower($edificio));
    // Nota: o serviço não é normalizado para maiúsculas/minúsculas porque pode conter
    // siglas (ex: "UCI") que não devem ser alteradas para "Uci".
    $piso     = $piso !== '' ? $piso : null;
    $sala     = $sala !== '' ? $sala : null;

    // 4. Guardar na base de dados
    if (empty($erros) && empty($erro_sistema)) {
        try {
            $sql = "INSERT INTO localizacoes (edificio, piso, servico, sala) VALUES (:edificio, :piso, :servico, :sala)";
            $stmt = $ligacao->prepare($sql);
            $stmt->execute([
                ':edificio' => $edificio,
                ':piso'     => $piso,
                ':servico'  => $servico,
                ':sala'     => $sala,
            ]);
            registar_log('inserir', "Localização criada: {$edificio} - {$servico}.", $_SESSION['id_utilizador'] ?? null);
            header('Location: listar.php');
            exit;
        } catch (PDOException $err) {
            if ($err->getCode() == 23000) {
                $erro_sistema = "Já existe uma localização registada com este edifício e serviço/departamento.";
            } else {
                $erro_sistema = "Erro ao gravar os dados: " . $err->getMessage();
            }
            $descricaoErro = $err->getCode() == 23000
                ? "Tentativa de inserir localização já existente (mesmo edifício e serviço)."
                : "Erro ao gravar a localização na base de dados.";
            registar_log('erro', $descricaoErro, $_SESSION['id_utilizador'] ?? null);
        }
    }
}
$ligacao = null;
?>

<?php include '../includes/header.php'; ?>

<?php include '../includes/nav.php'; ?>

    <?php include '../includes/sidebar.php'; ?>

    <main class="col-md-12 col-lg-10 col-sm-6">
        <div class="d-flex justify-content-center mt-4">
            <div class="card w-100 shadow rounded" style="max-width: 1000px;">
                <div class="card-body">
                    <h2 class="mb-4"><strong><i class="fa-solid fa-square-plus fa-1x mb-3"></i> Adicionar nova localização</strong></h2>
                    <hr>

                    <!-- Área de erros de validação / sistema (PHP) -->
                    <?php if (!empty($erros)) : ?>
                    <div class="alert alert-danger mb-4">
                        <strong>Foram encontrados os seguintes erros:</strong>
                        <ul class="mb-0">
                            <?php foreach ($erros as $erro) : ?>
                                <li><?= htmlspecialchars($erro) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($erro_sistema)) : ?>
                    <div class="alert alert-danger mb-4">
                        <strong>Erro:</strong> <?= htmlspecialchars($erro_sistema) ?>
                    </div>
                    <?php endif; ?>

                    <form action="#" method="post" novalidate id="formLocalizacao">

                        <!-- Área de erros — validação no browser -->
                        <div class="alert alert-danger d-none mb-4" id="errorBanner" role="alert">
                            <i class="fa-solid fa-circle-exclamation me-2"></i>
                            Erro ao inserir a localização. Por favor, tente novamente.
                        </div>

                        <!-- Linha 1: Edifício + Piso -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edificio" class="form-label">Edifício<span class="text-danger" title="Campo obrigatório">*</span></label>
                                <input type="text" class="form-control" id="edificio" name="edificio_localizacao" required placeholder="Ex: Edifício A" value="<?= htmlspecialchars($_POST['edificio_localizacao'] ?? '') ?>">
                                <div class="invalid-feedback">Por favor, insira o edifício.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="piso" class="form-label">Piso</label>
                                <input type="text" class="form-control" id="piso" name="piso_localizacao" placeholder="Ex: Piso 1 / R/C" value="<?= htmlspecialchars($_POST['piso_localizacao'] ?? '') ?>">
                            </div>
                        </div>

                        <!-- Linha 2: Serviço/Departamento + Sala/Gabinete -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="servico" class="form-label">Serviço/Departamento<span class="text-danger" title="Campo obrigatório">*</span></label>
                                <input type="text" class="form-control" id="servico" name="servico_localizacao" required placeholder="Ex: UCI, Medicina, Urgência..." value="<?= htmlspecialchars($_POST['servico_localizacao'] ?? '') ?>">
                                <div class="invalid-feedback">Por favor, insira o serviço/departamento.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="sala" class="form-label">Sala/Gabinete</label>
                                <input type="text" class="form-control" id="sala" name="sala_localizacao" placeholder="Ex: Sala 101" value="<?= htmlspecialchars($_POST['sala_localizacao'] ?? '') ?>">
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="d-flex justify-content-between align-items-center gap-2 pt-3 border-top">
                            <small class="text-muted">
                                <span class="text-danger">*</span> campos obrigatórios
                            </small>
                            <div class="d-flex gap-2">
                                <a href="listar.php" class="btn btn-outline-secondary">
                                    <i class="fa-solid fa-xmark me-1"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary" id="btnGuardar" disabled>
                                    <i class="fa-regular fa-floppy-disk me-1"></i> Guardar
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php include '../includes/sidebarmobile.php'; ?>

<?php include '../includes/footer.php'; ?>