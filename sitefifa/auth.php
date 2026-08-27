<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function verificarAutenticacao() {
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: login.php?erro=autenticacao");
        exit();
    }
}

function verificarAdmin() {
    verificarAutenticacao();
    if ($_SESSION['usuario_perfil'] !== 'admin') {
        header("Location: dashboard.php?erro=permissao");
        exit();
    }
}
?>