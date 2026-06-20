<?php
// Configurações globais da aplicação InveMed

define('APP_NAME', 'InveMed');
define('APP_VERSION', '1.0.0');
define('APP_COPYRIGHT', '© 2025 InveMed - Silvia (1241035)');

// Caminho base do projeto a partir da raiz do servidor.

define('BASE_URL', '/sibdas/1241035/invemed');

// Configurações da base de dados
define('MYSQL_HOST',     'vsgate-s1.dei.isep.ipp.pt');
define('MYSQL_PORT',     10464);
define('MYSQL_DATABASE', 'db1241035');
define('MYSQL_USERNAME', '1241035');
define('MYSQL_PASSWORD', 'magalhães_035');

// Encriptação dos IDs nos URLs (ex: editar.php?id=...), para evitar manipulação direta
define('OPENSSL_METHOD', 'AES-256-CBC');
define('OPENSSL_KEY', '4b56cd8ceb2c6d7b1d308d552ce6c6df'); // 32 caracteres
define('OPENSSL_IV', 'ce8f7b4e77f974dd'); // 16 caracteres
?>