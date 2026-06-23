<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
require_perfil(['Administrador', 'Técnico']);

if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}

// 1. Recolher e validar o ID encriptado
$idEncrypted = $_GET['id'] ?? $_POST['id'] ?? null;
$idEquipamento = aes_decrypt($idEncrypted);

if (!$idEquipamento || !is_numeric($idEquipamento)) {
    header('Location: listar.php');
    exit;
}

$erros = [];
$erro_sistema = '';
$localizacoes = [];
$fornecedores = [];
$acessoriosExistentes = [];
$relacoesFornecedorExistentes = [];

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Inclui também a localização atual do equipamento mesmo que tenha sido entretanto desativada,
    // para não a "perder" silenciosamente do formulário ao editar um registo já existente.
    $stmtLoc = $ligacao->prepare("
        SELECT id_localizacao, edificio, servico, sala FROM localizacoes
        WHERE ativo = 1 OR id_localizacao = (SELECT id_localizacao FROM equipamentos WHERE id_equipamento = :id)
        ORDER BY edificio, servico
    ");
    $stmtLoc->execute([':id' => $idEquipamento]);
    $localizacoes = $stmtLoc->fetchAll(PDO::FETCH_OBJ);

    // O mesmo princípio para os fornecedores já associados aos acessórios ou diretamente ao equipamento.
    $stmtForn = $ligacao->prepare("
        SELECT id_fornecedor, nome FROM fornecedores
        WHERE ativo = 1
           OR id_fornecedor IN (SELECT id_fornecedor FROM acessorios WHERE id_equipamento = :id1 AND id_fornecedor IS NOT NULL)
           OR id_fornecedor IN (SELECT id_fornecedor FROM equipamento_fornecedor WHERE id_equipamento = :id2)
        ORDER BY nome
    ");
    $stmtForn->execute([':id1' => $idEquipamento, ':id2' => $idEquipamento]);
    $fornecedores = $stmtForn->fetchAll(PDO::FETCH_OBJ);

    $stmtAcessorios = $ligacao->prepare("SELECT codigo, nome, id_fornecedor FROM acessorios WHERE id_equipamento = :id ORDER BY id_acessorio");
    $stmtAcessorios->execute([':id' => $idEquipamento]);
    $acessoriosExistentes = $stmtAcessorios->fetchAll(PDO::FETCH_ASSOC);

    $stmtRelForn = $ligacao->prepare("SELECT id_fornecedor, tipo FROM equipamento_fornecedor WHERE id_equipamento = :id ORDER BY id_relacao");
    $stmtRelForn->execute([':id' => $idEquipamento]);
    $relacoesFornecedorExistentes = $stmtRelForn->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $err) {
    $erro_sistema = "Aconteceu um erro na ligação.";
}

$categoriasValidas   = ['Monitorização', 'Suporte de vida', 'Terapia', 'Diagnóstico', 'Laboratório', 'Esterilização', 'Reabilitação'];
$tiposEntradaValidos = ['Compra', 'Doação', 'Aluguer', 'Empréstimo'];
$estadosValidos      = ['Ativo', 'Em manutenção', 'Inativo', 'Em calibração', 'Em quarentena', 'Abatido'];
$criticidadesValidas = ['Baixa', 'Média', 'Alta', 'Suporte de vida'];
$tiposFornecedorValidos = ['Fabricante', 'Distribuidor', 'Assistência técnica', 'Outro'];

// 2. Obter o equipamento atual — feito antes do POST porque o código interno e o
// número de série são imutáveis (identificador de rastreabilidade e facto de
// fabrico do equipamento, respetivamente — não devem mudar depois de criados).
$equipamento = null;
if (empty($erro_sistema)) {
    try {
        $stmt = $ligacao->prepare("SELECT * FROM equipamentos WHERE id_equipamento = :id");
        $stmt->execute([':id' => $idEquipamento]);
        $equipamento = $stmt->fetch(PDO::FETCH_OBJ);
        if (!$equipamento) {
            header('Location: listar.php');
            exit;
        }
        // Um equipamento abatido representa um facto encerrado — não pode voltar a ser editado.
        if ($equipamento->estado === 'Abatido') {
            header('Location: detalhes.php?id=' . $idEncrypted);
            exit;
        }
    } catch (PDOException $err) {
        $erro_sistema = "Aconteceu um erro na ligação.";
    }
}

// Linhas de acessórios a apresentar: as submetidas (em caso de reapresentação por erro) ou as já guardadas
$acessoriosSubmetidos = [];
if (!empty($_POST['nome_acessorio']) && is_array($_POST['nome_acessorio'])) {
    foreach ($_POST['nome_acessorio'] as $i => $nome) {
        $acessoriosSubmetidos[] = [
            'codigo'        => $_POST['codigo_acessorio'][$i] ?? '',
            'nome'          => $nome,
            'id_fornecedor' => $_POST['fornecedor_acessorio'][$i] ?? '',
        ];
    }
} else {
    $acessoriosSubmetidos = $acessoriosExistentes;
}
if (empty($acessoriosSubmetidos)) {
    $acessoriosSubmetidos = [['codigo' => '', 'nome' => '', 'id_fornecedor' => '']];
}

// Linhas de fornecedores associados a apresentar: as submetidas (em caso de reapresentação por erro) ou as já guardadas
$fornecedoresSubmetidos = [];
if (!empty($_POST['fornecedor_equipamento']) && is_array($_POST['fornecedor_equipamento'])) {
    foreach ($_POST['fornecedor_equipamento'] as $i => $idF) {
        $fornecedoresSubmetidos[] = [
            'id_fornecedor' => $idF,
            'tipo'          => $_POST['tipo_relacao_fornecedor'][$i] ?? '',
        ];
    }
} else {
    foreach ($relacoesFornecedorExistentes as $rel) {
        $fornecedoresSubmetidos[] = ['id_fornecedor' => $rel['id_fornecedor'], 'tipo' => $rel['tipo']];
    }
}
if (empty($fornecedoresSubmetidos)) {
    $fornecedoresSubmetidos = [['id_fornecedor' => '', 'tipo' => '']];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($erro_sistema)) {
    // 3. Recolher dados do formulário (código interno e número de série não são
    // recolhidos do POST — são imutáveis)
    $designacao  = trim($_POST['designacao_equipamento'] ?? '');
    $categoria   = trim($_POST['categoria_equipamento'] ?? '');
    $marca       = trim($_POST['marca_equipamento'] ?? '');
    $modelo      = trim($_POST['modelo_equipamento'] ?? '');
    $fabricante  = trim($_POST['fabricante_equipamento'] ?? '');
    $dataAquis   = trim($_POST['dataaquisicao_equipamento'] ?? '');
    $anoFabrico  = trim($_POST['anofabrico_equipamento'] ?? '');
    $custo       = trim($_POST['custo_equipamento'] ?? '');
    $tipoEntrada = trim($_POST['tipoentrada_equipamento'] ?? '');
    $estado      = trim($_POST['estado_equipamento'] ?? '');
    $criticidade = trim($_POST['criticidade_equipamento'] ?? '');
    $idLoc       = trim($_POST['localizacao_equipamento'] ?? '');
    $obs         = trim($_POST['observacoes_equipamento'] ?? '');

    // 4. Validar dados (mesmas regras do inserir.php, exceto código interno/número de série)
    if (empty($designacao)) {
        $erros[] = "O campo Designação é obrigatório.";
    } elseif (preg_match('/^\d+$/', $designacao)) {
        $erros[] = "O campo Designação não pode conter apenas números.";
    }

    if (!empty($categoria) && !in_array($categoria, $categoriasValidas, true)) {
        $erros[] = "Categoria inválida.";
    }

    if (!empty($dataAquis)) {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dataAquis)) {
            $erros[] = "Formato de Data de Aquisição inválido. Use AAAA-MM-DD.";
        } else {
            $partes = explode('-', $dataAquis);
            if (!checkdate((int)$partes[1], (int)$partes[2], (int)$partes[0])) {
                $erros[] = "Data de Aquisição inválida.";
            } elseif ($dataAquis > date('Y-m-d')) {
                $erros[] = "A Data de Aquisição não pode ser no futuro.";
            }
        }
    }

    if (!empty($anoFabrico)) {
        if (!preg_match('/^\d{4}$/', $anoFabrico) || (int)$anoFabrico < 1980 || (int)$anoFabrico > (int)date('Y')) {
            $erros[] = "Ano de Fabrico inválido (deve estar entre 1980 e " . date('Y') . ").";
        }
    }

    if (!empty($custo)) {
        if (!preg_match('/^\d+(\.\d{1,2})?$/', $custo) || (float)$custo < 0) {
            $erros[] = "Custo de Aquisição inválido. Use um valor numérico positivo (ex: 1000.00).";
        }
    }

    if (!empty($tipoEntrada) && !in_array($tipoEntrada, $tiposEntradaValidos, true)) {
        $erros[] = "Tipo de Entrada inválido.";
    }

    if (!empty($estado) && !in_array($estado, $estadosValidos, true)) {
        $erros[] = "Estado inválido.";
    }

    if (!empty($criticidade) && !in_array($criticidade, $criticidadesValidas, true)) {
        $erros[] = "Criticidade inválida.";
    }

    if (empty($idLoc)) {
        $erros[] = "A Localização é obrigatória: cada equipamento deve estar associado a uma localização atual.";
    } elseif (!in_array((int)$idLoc, array_column($localizacoes, 'id_localizacao'), true)) {
        $erros[] = "Localização selecionada não é válida.";
    }

    // Fornecedores associados: filtrar linhas em branco e remover duplicados exatos
    // (o mesmo fornecedor pode aparecer mais do que uma vez, desde que com tipos de relação diferentes)
    $relacoesFornecedor = [];
    $paresFornecedorTipoVistos = [];
    foreach ($fornecedoresSubmetidos as $linha) {
        $idF = trim($linha['id_fornecedor']);
        $tipoRel = trim($linha['tipo']);
        if ($idF === '') {
            continue;
        }
        if (!in_array((int)$idF, array_column($fornecedores, 'id_fornecedor'), true)) {
            $erros[] = "O fornecedor selecionado não é válido.";
            continue;
        }
        $tipoNormalizado = $tipoRel !== '' ? $tipoRel : null;
        $par = $idF . '|' . ($tipoNormalizado ?? '');
        if (in_array($par, $paresFornecedorTipoVistos, true)) {
            $erros[] = "O mesmo fornecedor não pode ser associado mais do que uma vez ao equipamento com o mesmo tipo de relação.";
            continue;
        }
        $paresFornecedorTipoVistos[] = $par;
        $relacoesFornecedor[] = ['id_fornecedor' => (int)$idF, 'tipo' => $tipoNormalizado];
    }

    // Acessórios: filtrar linhas em branco
    $acessoriosValidos = [];
    foreach ($acessoriosSubmetidos as $idx => $acessorio) {
        $codigoAcessorio = trim($acessorio['codigo']);
        $nomeAcessorio = trim($acessorio['nome']);
        $idFornecedorAcessorio = trim($acessorio['id_fornecedor'] ?? '');
        if ($codigoAcessorio === '' && $nomeAcessorio === '' && $idFornecedorAcessorio === '') {
            continue;
        }
        if (empty($nomeAcessorio)) {
            $erros[] = "Acessório " . ($idx + 1) . ": o nome é obrigatório.";
        }
        if ($idFornecedorAcessorio !== '' && !in_array((int)$idFornecedorAcessorio, array_column($fornecedores, 'id_fornecedor'), true)) {
            $erros[] = "Acessório " . ($idx + 1) . ": o fornecedor selecionado não é válido.";
        }
        $acessoriosValidos[] = [
            'codigo'        => $codigoAcessorio !== '' ? $codigoAcessorio : null,
            'nome'          => $nomeAcessorio,
            'id_fornecedor' => $idFornecedorAcessorio !== '' ? (int)$idFornecedorAcessorio : null,
        ];
    }

    // 5. Normalizar dados
    $designacao = ucwords(strtolower($designacao));
    $marca      = $marca !== '' ? ucwords(strtolower($marca)) : null;
    $modelo     = $modelo !== '' ? $modelo : null;
    $fabricante = $fabricante !== '' ? ucwords(strtolower($fabricante)) : null;
    $dataAquis  = $dataAquis !== '' ? $dataAquis : null;
    $anoFabrico = $anoFabrico !== '' ? (int)$anoFabrico : null;
    $custo      = $custo !== '' ? (float)$custo : null;
    $tipoEntrada = $tipoEntrada !== '' ? $tipoEntrada : null;
    $estado     = $estado !== '' ? $estado : 'Ativo';
    $criticidade = $criticidade !== '' ? $criticidade : null;
    $categoria  = $categoria !== '' ? $categoria : null;
    $idLoc      = (int)$idLoc;
    $obs        = $obs !== '' ? $obs : null;

    // 6. Atualizar na base de dados
    if (empty($erros)) {
        try {
            $ligacao->beginTransaction();

            $sql = "UPDATE equipamentos SET
                        designacao = :designacao, categoria = :categoria, marca = :marca, modelo = :modelo,
                        fabricante = :fabricante, data_aquisicao = :dataaquis, ano_fabrico = :anofabrico,
                        custo_aquisicao = :custo, tipo_entrada = :tipoentrada, estado = :estado,
                        criticidade = :criticidade, id_localizacao = :idloc, observacoes = :obs
                    WHERE id_equipamento = :id";
            $stmt = $ligacao->prepare($sql);
            $stmt->execute([
                ':designacao'  => $designacao,
                ':categoria'   => $categoria,
                ':marca'       => $marca,
                ':modelo'      => $modelo,
                ':fabricante'  => $fabricante,
                ':dataaquis'   => $dataAquis,
                ':anofabrico'  => $anoFabrico,
                ':custo'       => $custo,
                ':tipoentrada' => $tipoEntrada,
                ':estado'      => $estado,
                ':criticidade' => $criticidade,
                ':idloc'       => $idLoc,
                ':obs'         => $obs,
                ':id'          => $idEquipamento,
            ]);

            // Fornecedores associados: substitui sempre a lista completa pela submetida
            $ligacao->prepare("DELETE FROM equipamento_fornecedor WHERE id_equipamento = :id")->execute([':id' => $idEquipamento]);
            if (!empty($relacoesFornecedor)) {
                $stmtRel = $ligacao->prepare("INSERT INTO equipamento_fornecedor (id_equipamento, id_fornecedor, tipo) VALUES (:idequip, :idforn, :tipo)");
                foreach ($relacoesFornecedor as $rel) {
                    $stmtRel->execute([
                        ':idequip' => $idEquipamento,
                        ':idforn'  => $rel['id_fornecedor'],
                        ':tipo'    => $rel['tipo'],
                    ]);
                }
            }

            // Acessórios: substitui sempre a lista completa pela submetida
            $ligacao->prepare("DELETE FROM acessorios WHERE id_equipamento = :id")->execute([':id' => $idEquipamento]);
            if (!empty($acessoriosValidos)) {
                $stmtAcessorio = $ligacao->prepare("INSERT INTO acessorios (codigo, nome, id_equipamento, id_fornecedor) VALUES (:codigo, :nome, :idequip, :idforn)");
                foreach ($acessoriosValidos as $acessorio) {
                    $stmtAcessorio->execute([
                        ':codigo'  => $acessorio['codigo'],
                        ':nome'    => $acessorio['nome'],
                        ':idequip' => $idEquipamento,
                        ':idforn'  => $acessorio['id_fornecedor'],
                    ]);
                }
            }

            $ligacao->commit();
            registar_log('editar', "Equipamento atualizado: {$designacao} (código {$equipamento->codigo_interno}).", $_SESSION['id_utilizador'] ?? null);
            header('Location: listar.php');
            exit;
        } catch (PDOException $err) {
            $ligacao->rollBack();
            $erro_sistema = "Erro ao atualizar os dados: " . $err->getMessage();
            registar_log('erro', "Erro ao atualizar o equipamento na base de dados.", $_SESSION['id_utilizador'] ?? null);
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
            <div class="card w-100 shadow rounded" style="max-width: 1200px;">
                <div class="card-body">
                    <h2 class="mb-4"><strong><i class="fa-solid fa-pen fa-1x mb-3"></i> Atualizar dados de equipamento</strong></h2>
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

                    <form action="editar.php?id=<?= htmlspecialchars($idEncrypted) ?>" method="post" novalidate id="formEquipamento">

                        <!-- Área de erros -->
                        <div class="alert alert-danger d-none mb-4" id="errorBanner" role="alert">
                            <i class="fa-solid fa-circle-exclamation me-2"></i>
                            Erro ao atualizar o equipamento. Por favor, tente novamente.
                        </div>

                        <!-- Linha 1: Código + Designação -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="codigo" class="form-label">Código interno</label>
                                <input type="text" class="form-control" id="codigo" readonly value="<?= htmlspecialchars($equipamento->codigo_interno ?? '') ?>">
                                <div class="form-text">Não pode ser alterado — é o identificador de rastreabilidade do equipamento.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="designacao" class="form-label">Designação<span class="text-danger" title="Campo obrigatório">*</span></label>
                                <input type="text" class="form-control" id="designacao" name="designacao_equipamento" required placeholder="Ex: Monitor Multiparamétrico" value="<?= htmlspecialchars(valorCampo('designacao_equipamento', $equipamento, 'designacao')) ?>">
                                <div class="invalid-feedback">Por favor, insira a designação.</div>
                            </div>
                        </div>

                        <!-- Linha 2: Categoria + Marca + Modelo + Nº Série -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="categoria" class="form-label">Categoria</label>
                                <?php $categoriaAtual = valorCampo('categoria_equipamento', $equipamento, 'categoria'); ?>
                                <select class="form-select" id="categoria" name="categoria_equipamento">
                                    <option value="">Selecione...</option>
                                    <?php foreach ($categoriasValidas as $cat) : ?>
                                        <option value="<?= htmlspecialchars($cat) ?>" <?= ($categoriaAtual === $cat) ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="marca" class="form-label">Marca</label>
                                <input type="text" class="form-control" id="marca" name="marca_equipamento" placeholder="Ex: Philips" value="<?= htmlspecialchars(valorCampo('marca_equipamento', $equipamento, 'marca')) ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="modelo" class="form-label">Modelo</label>
                                <input type="text" class="form-control" id="modelo" name="modelo_equipamento" placeholder="Ex: IntelliVue MP5" value="<?= htmlspecialchars(valorCampo('modelo_equipamento', $equipamento, 'modelo')) ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="nserie" class="form-label">Número de Série</label>
                                <input type="text" class="form-control" id="nserie" readonly value="<?= htmlspecialchars($equipamento->numero_serie ?? '') ?>">
                                <div class="form-text">Não pode ser alterado — é um facto de fabrico do equipamento.</div>
                            </div>
                        </div>

                        <!-- Linha 3: Fabricante + Data Aquisição + Ano Fabrico + Custo -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="fabricante" class="form-label">Fabricante</label>
                                <input type="text" class="form-control" id="fabricante" name="fabricante_equipamento" placeholder="Ex: Philips" value="<?= htmlspecialchars(valorCampo('fabricante_equipamento', $equipamento, 'fabricante')) ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="dataaquisicao" class="form-label">Data de Aquisição</label>
                                <input type="date" class="form-control" id="dataaquisicao" name="dataaquisicao_equipamento" value="<?= htmlspecialchars(valorCampo('dataaquisicao_equipamento', $equipamento, 'data_aquisicao')) ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="anofabrico" class="form-label">Ano de Fabrico</label>
                                <input type="number" class="form-control" id="anofabrico" name="anofabrico_equipamento" placeholder="Ex: 2022" value="<?= htmlspecialchars(valorCampo('anofabrico_equipamento', $equipamento, 'ano_fabrico')) ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="custo" class="form-label">Custo de Aquisição (€)</label>
                                <input type="number" step="0.01" class="form-control" id="custo" name="custo_equipamento" placeholder="Ex: 1000.00" value="<?= htmlspecialchars(valorCampo('custo_equipamento', $equipamento, 'custo_aquisicao')) ?>">
                            </div>
                        </div>

                        <!-- Linha 4: Tipo Entrada + Estado + Criticidade + Localização -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="tipoentrada" class="form-label">Tipo de Entrada</label>
                                <?php $tipoEntradaAtual = valorCampo('tipoentrada_equipamento', $equipamento, 'tipo_entrada'); ?>
                                <select class="form-select" id="tipoentrada" name="tipoentrada_equipamento">
                                    <option value="">Selecione...</option>
                                    <?php foreach ($tiposEntradaValidos as $te) : ?>
                                        <option value="<?= htmlspecialchars($te) ?>" <?= ($tipoEntradaAtual === $te) ? 'selected' : '' ?>><?= htmlspecialchars($te) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="estado" class="form-label">Estado atual</label>
                                <?php $estadoAtual = valorCampo('estado_equipamento', $equipamento, 'estado'); ?>
                                <select class="form-select" id="estado" name="estado_equipamento">
                                    <?php foreach ($estadosValidos as $est) : ?>
                                        <option value="<?= htmlspecialchars($est) ?>" <?= ($estadoAtual === $est) ? 'selected' : '' ?>><?= htmlspecialchars($est) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="criticidade" class="form-label">Criticidade</label>
                                <?php $criticidadeAtual = valorCampo('criticidade_equipamento', $equipamento, 'criticidade'); ?>
                                <select class="form-select" id="criticidade" name="criticidade_equipamento">
                                    <option value="">Selecione...</option>
                                    <?php foreach ($criticidadesValidas as $crit) : ?>
                                        <option value="<?= htmlspecialchars($crit) ?>" <?= ($criticidadeAtual === $crit) ? 'selected' : '' ?>><?= htmlspecialchars($crit) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="localizacao" class="form-label">Localização<span class="text-danger" title="Campo obrigatório">*</span></label>
                                <?php $idLocAtual = valorCampo('localizacao_equipamento', $equipamento, 'id_localizacao'); ?>
                                <select class="form-select" id="localizacao" name="localizacao_equipamento" required>
                                    <option value="">Selecione...</option>
                                    <?php foreach ($localizacoes as $loc) : ?>
                                        <option value="<?= $loc->id_localizacao ?>" <?= ((string)$idLocAtual === (string)$loc->id_localizacao) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($loc->edificio) ?> - <?= htmlspecialchars($loc->servico) ?><?= $loc->sala ? ' (' . htmlspecialchars($loc->sala) . ')' : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Por favor, selecione a localização.</div>
                            </div>
                        </div>

                        <!-- Linha 5: Fornecedores associados -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label d-block">Fornecedores associados (opcional)</label>
                                <p class="text-muted small">Um equipamento pode estar associado a vários fornecedores (fabricante, distribuidor, assistência técnica, consumíveis, etc.).</p>
                                <div class="d-flex justify-content-end mb-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnAdicionarFornecedor">
                                        <i class="fa-solid fa-plus me-1"></i> Adicionar fornecedor
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle" id="tabelaFornecedoresEquip">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Fornecedor</th>
                                                <th>Tipo de fornecedor</th>
                                                <th class="text-center" style="width:50px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="linhasFornecedoresEquip">
                                            <?php foreach ($fornecedoresSubmetidos as $linha) : ?>
                                            <tr class="linha-fornecedor-equip">
                                                <td>
                                                    <select class="form-select form-select-sm" name="fornecedor_equipamento[]">
                                                        <option value="">Selecione...</option>
                                                        <?php foreach ($fornecedores as $forn) : ?>
                                                            <option value="<?= $forn->id_fornecedor ?>" <?= ((int)($linha['id_fornecedor'] ?? 0) === (int)$forn->id_fornecedor) ? 'selected' : '' ?>><?= htmlspecialchars($forn->nome) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-select form-select-sm" name="tipo_relacao_fornecedor[]">
                                                        <option value="">Selecione...</option>
                                                        <?php foreach ($tiposFornecedorValidos as $tr) : ?>
                                                            <option value="<?= htmlspecialchars($tr) ?>" <?= (($linha['tipo'] ?? '') === $tr) ? 'selected' : '' ?>><?= htmlspecialchars($tr) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-outline-danger btn-remover-fornecedor-equip" title="Remover linha" <?= count($fornecedoresSubmetidos) === 1 ? 'disabled' : '' ?>>
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Linha 6: Acessórios -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label d-block">Acessórios / componentes (opcional)</label>
                                <div class="d-flex justify-content-end mb-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnAdicionarAcessorio">
                                        <i class="fa-solid fa-plus me-1"></i> Adicionar acessório
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle" id="tabelaAcessorios">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="min-width:140px;">Código</th>
                                                <th style="min-width:180px;">Nome</th>
                                                <th style="min-width:180px;">Fornecedor</th>
                                                <th class="text-center" style="width:50px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="linhasAcessorios">
                                            <?php foreach ($acessoriosSubmetidos as $linha) : ?>
                                            <tr class="linha-acessorio">
                                                <td>
                                                    <input type="text" class="form-control form-control-sm" name="codigo_acessorio[]" placeholder="Ex: 04.002.01" value="<?= htmlspecialchars($linha['codigo'] ?? '') ?>">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm" name="nome_acessorio[]" placeholder="Ex: Sensor de oximetria" value="<?= htmlspecialchars($linha['nome'] ?? '') ?>">
                                                </td>
                                                <td>
                                                    <select class="form-select form-select-sm" name="fornecedor_acessorio[]">
                                                        <option value="">Nenhum / Selecione...</option>
                                                        <?php foreach ($fornecedores as $forn) : ?>
                                                            <option value="<?= $forn->id_fornecedor ?>" <?= ((int)($linha['id_fornecedor'] ?? 0) === (int)$forn->id_fornecedor) ? 'selected' : '' ?>><?= htmlspecialchars($forn->nome) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-outline-danger btn-remover-acessorio" title="Remover linha" <?= count($acessoriosSubmetidos) === 1 ? 'disabled' : '' ?>>
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Linha 6: Observações -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="observacoes" class="form-label">Observações</label>
                                <textarea class="form-control" id="observacoes" name="observacoes_equipamento" rows="3" placeholder="Notas adicionais sobre o equipamento..."><?= htmlspecialchars(valorCampo('observacoes_equipamento', $equipamento, 'observacoes')) ?></textarea>
                            </div>
                        </div>

                        <p class="text-muted small">
                            <i class="fa-solid fa-circle-info me-1"></i>
                            Documentos e garantia/contrato deste equipamento gerem-se nos respetivos módulos
                            (<a href="../documentacao/listar.php" style="color:#0077a8;">Documentação</a>,
                            <a href="../garantiacontrato/listar.php" style="color:#0077a8;">Garantias e Contratos</a>).
                        </p>

                        <!-- Botões -->
                        <div class="d-flex justify-content-between align-items-center gap-2 pt-3 border-top">
                            <small class="text-muted">
                                <span class="text-danger">*</span> campos obrigatórios
                            </small>
                            <div class="d-flex gap-2">
                                <a href="listar.php" class="btn btn-outline-secondary">
                                    <i class="fa-solid fa-xmark me-1"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary" id="btnGuardar">
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
