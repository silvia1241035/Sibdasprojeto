<?php
require_once __DIR__ . '/includes/funcoes.php';
redirect_if_not_logged();
require_perfil(['Administrador']);

header('Content-Type: application/json');

$desde = isset($_GET['desde']) ? (int)$_GET['desde'] : null;
$resposta = ['eventos' => [], 'max_id' => $desde ?? 0];

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($desde === null) {
        // Primeira chamada: só inicializa o último id conhecido, sem mostrar eventos antigos.
        $resposta['max_id'] = (int)$ligacao->query("SELECT COALESCE(MAX(id_log), 0) FROM logs")->fetchColumn();
    } else {
        // Eventos de outros utilizadores desde a última verificação — as próprias
        // ações do admin não geram pop-up, já que ele sabe o que acabou de fazer.
        $stmt = $ligacao->prepare("
            SELECT l.id_log, l.tipo, l.descricao, l.criado_em, u.nome AS nome_utilizador
            FROM logs l
            LEFT JOIN utilizadores u ON u.id_utilizador = l.id_utilizador
            WHERE l.id_log > :desde
              AND (l.id_utilizador IS NULL OR l.id_utilizador != :id_atual)
            ORDER BY l.id_log ASC
            LIMIT 20
        ");
        $stmt->execute([
            ':desde'    => $desde,
            ':id_atual' => $_SESSION['id_utilizador'] ?? 0,
        ]);
        $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resposta['eventos'] = $eventos;
        $resposta['max_id'] = !empty($eventos) ? (int)$eventos[count($eventos) - 1]['id_log'] : $desde;
    }
} catch (PDOException $err) {
    // Devolve resposta vazia em caso de erro — o polling tenta novamente no próximo ciclo.
}
$ligacao = null;

echo json_encode($resposta);
