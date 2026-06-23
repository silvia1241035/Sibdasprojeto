<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
require_perfil(['Administrador']);

if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}

$idEncrypted = $_GET['id'] ?? null;
$idUtilizador = aes_decrypt($idEncrypted);

if (!$idUtilizador || !is_numeric($idUtilizador)) {
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
    $stmtNome = $ligacao->prepare("SELECT nome FROM utilizadores WHERE id_utilizador = :id");
    $stmtNome->execute([':id' => $idUtilizador]);
    $nomeUtilizador = $stmtNome->fetchColumn();

    $stmt = $ligacao->prepare("UPDATE utilizadores SET ativo = 1 WHERE id_utilizador = :id");
    $stmt->execute([':id' => $idUtilizador]);
    if ($nomeUtilizador) {
        registar_log('editar', "Utilizador reativado: {$nomeUtilizador}.", $_SESSION['id_utilizador'] ?? null);
    }
} catch (PDOException $err) {
    registar_log('erro', "Erro ao reativar o utilizador na base de dados.", $_SESSION['id_utilizador'] ?? null);
}
$ligacao = null;

header('Location: listar.php');
exit;
