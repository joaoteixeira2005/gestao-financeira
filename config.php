<?php
// Removemos o session_start() daqui para ficar apenas no index.php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'gestao_financas');

// Credenciais Casal
define('USER_ADMIN', 'root');
define('PASS_ADMIN', '1234');
define('USER_GUEST', '');
define('PASS_GUEST', '');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$conn->set_charset("utf8mb4");

// Função auxiliar de permissão
function isAdmin() {
    return (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
}