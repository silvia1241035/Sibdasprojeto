<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
require_perfil(['Administrador', 'Gestor de Logística']);

if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}

// 1. Recolher e validar o ID encriptado
$idEncrypted = $_GET['id'] ?? $_POST['id'] ?? null;
$idLocalizacao = aes_decrypt($idEncrypted);

if (!$idLocalizacao || !is_numeric($idLocalizacao)) {
    header('Location: listar.php');
    exit;
}

$erros = [];
$erro_sistema = '';
$localizacao = null;

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtido antes do POST para bloquear edições a localizações já inativas
    // (primeiro têm de ser reativadas).
    $stmt = $ligacao->prepare("SELECT * FROM localizacoes WHERE id_localizacao = :id");
    $stmt->execute([':id' => $idLocalizacao]);
    $localizacao = $stmt->fetch(PDO::FETCH_OBJ);
    if (!$localizacao) {
        header('Location: listar.php');
        exit;
    }
    if ((int)$localizacao->ativo === 0) {
        header('Location: detalhes.php?id=' . $idEncrypted);
        exit;
    }
} catch (PDOException $err) {
    $erro_sistema = "Aconteceu um erro na ligação.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($erro_sistema)) {
    // 2. Recolher dados
    $edificio = trim($_POST['edificio_localizacao'] ?? '');
    $piso     = trim($_POST['piso_localizacao'] ?? '');
    $servico  = trim($_POST['servico_localizacao'] ?? '');
    $sala     = trim($_POST['sala_localizacao'] ?? '');

    // 3. Validar dados
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

    // 4. Normalizar dados — o serviço não é normalizado para Maiúsculas/minúsculas
    // para não corromper siglas (ex: "UCI").
    $edificio = ucwords(strtolower($edificio));
    $piso     = $piso !== '' ? $piso : null;
    $sala     = $sala !== '' ? $sala : null;

    // 5. Atualizar na base de dados
    if (empty($erros)) {
        try {
            $sql = "UPDATE localizacoes SET edificio = :edificio, piso = :piso, servico = :servico, sala = :sala WHERE id_localizacao = :id";
            $stmt = $ligacao->prepare($sql);
            $stmt->execute([
                ':edificio' => $edificio,
                ':piso'     => $piso,
                ':servico'  => $servico,
                ':sala'     => $sala,
                ':id'       => $idLocalizacao,
            ]);
            registar_log('editar', "Localização atualizada: {$edificio} - {$servico}.", $_SESSION['id_utilizador'] ?? null);
            header('Location: listar.php');
            exit;
        } catch (PDOException $err) {
            if ($err->getCode() == 23000) {
                $erro_sistema = "Já existe uma localização registada com este edifício e serviço/departamento.";
            } else {
                $erro_sistema = "Erro ao atualizar os dados: " . $err->getMessage();
            }
            $descricaoErro = $err->getCode() == 23000
                ? "Tentativa de atualizar localização para um edifício/serviço já existente."
                : "Erro ao atualizar a localização na base de dados.";
            registar_log('erro', $descricaoErro, $_SESSION['id_utilizador'] ?? null);
        }
    }
}

$ligacao = null;

// Valor a apresentar em cada campo: o que foi submetido (em caso de erro) ou o valor atual na BD
function valorCampo($postKey, $registo, $campoBd)
{
    return $_POST[$postKey] ?? ($registo->$campoBd ?? '');
}
?>

<?php include '../includes/header.php'; ?>

<?php include '../includes/nav.php'; ?>

    <?php include '../includes/sidebar.php'; ?>

    <main class="col-md-9 col-lg-10 p-4">
        <div class="d-flex justify-content-center mt-4">
            <div class="card w-100 shadow rounded" style="max-width: 1000px;">
                <div class="card-body">
                    <h2 class="mb-4"><strong><i class="fa-solid fa-pen fa-1x mb-3"></i> Atualizar localização</strong></h2>
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

                    <form action="editar.php?id=<?= htmlspecialchars($idEncrypted) ?>" method="post" novalidate id="formLocalizacao">

                        <!-- Área de erros — validação no browser -->
                        <div class="alert alert-danger d-none mb-4" id="errorBanner" role="alert">
                            <i class="fa-solid fa-circle-exclamation me-2"></i>
                            Erro ao atualizar a localização. Por favor, tente novamente.
                        </div>

                        <!-- Linha 1: Edifício + Piso -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edificio" class="form-label">Edifício<span class="text-danger" title="Campo obrigatório">*</span></label>
                                <input type="text" class="form-control" id="edificio" name="edificio_localizacao" required placeholder="Ex: Edifício A" value="<?= htmlspecialchars(valorCampo('edificio_localizacao', $localizacao, 'edificio')) ?>">
                                <div class="invalid-feedback">Por favor, insira o edifício.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="piso" class="form-label">Piso</label>
                                <input type="text" class="form-control" id="piso" name="piso_localizacao" placeholder="Ex: Piso 1 / R/C" value="<?= htmlspecialchars(valorCampo('piso_localizacao', $localizacao, 'piso')) ?>">
                            </div>
                        </div>

                        <!-- Linha 2: Serviço/Departamento + Sala/Gabinete -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="servico" class="form-label">Serviço/Departamento<span class="text-danger" title="Campo obrigatório">*</span></label>
                                <input type="text" class="form-control" id="servico" name="servico_localizacao" required placeholder="Ex: UCI, Medicina, Urgência..." value="<?= htmlspecialchars(valorCampo('servico_localizacao', $localizacao, 'servico')) ?>">
                                <div class="invalid-feedback">Por favor, insira o serviço/departamento.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="sala" class="form-label">Sala/Gabinete</label>
                                <input type="text" class="form-control" id="sala" name="sala_localizacao" placeholder="Ex: Sala 101" value="<?= htmlspecialchars(valorCampo('sala_localizacao', $localizacao, 'sala')) ?>">
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="d-flex justify-content-between align-items-center gap-2 pt-3 border-top">
                            <small class="text-muted"><span class="text-danger">*</span> campos obrigatórios</small>
                            <div class="d-flex gap-2">
                                <a href="listar.php" class="btn btn-outline-secondary"><i class="fa-solid fa-xmark me-1"></i> Cancelar</a>
                                <button type="submit" class="btn btn-primary" id="btnGuardar"><i class="fa-regular fa-floppy-disk me-1"></i> Guardar</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php include '../includes/sidebarmobile.php'; ?>

<?php include '../includes/footer.php'; ?>
