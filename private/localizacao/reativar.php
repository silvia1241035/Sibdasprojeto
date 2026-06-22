<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
require_perfil(['Administrador', 'Gestor de Logística']);

if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}

$idEncrypted = $_GET['id'] ?? null;
$idLocalizacao = aes_decrypt($idEncrypted);

if (!$idLocalizacao || !is_numeric($idLocalizacao)) {
    header('Location: listar.php');
    exit;
}

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmtNome = $ligacao->prepare("SELECT edificio, servico FROM localizacoes WHERE id_localizacao = :id");
    $stmtNome->execute([':id' => $idLocalizacao]);
    $localizacaoReativada = $stmtNome->fetch(PDO::FETCH_OBJ);

    $stmt = $ligacao->prepare("UPDATE localizacoes SET ativo = 1 WHERE id_localizacao = :id");
    $stmt->execute([':id' => $idLocalizacao]);
    if ($localizacaoReativada) {
        registar_log('editar', "Localização reativada: {$localizacaoReativada->edificio} - {$localizacaoReativada->servico}.", $_SESSION['id_utilizador'] ?? null);
    }
} catch (PDOException $err) {
    registar_log('erro', "Erro ao reativar a localização na base de dados.", $_SESSION['id_utilizador'] ?? null);
}
$ligacao = null;

header('Location: listar.php');
exit;
