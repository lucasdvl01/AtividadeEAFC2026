<?php
session_start();
require_once "conexao.php";

$erro = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'] ?? '';

    if (!empty($email) && !empty($senha)) {
        $stmt = $conn->prepare("SELECT id_usuario, nome, email, senha, perfil FROM USUARIO WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado && $resultado->num_rows === 1) {
            $usuario = $resultado->fetch_assoc();

            // Valida via hash ou senha mestra de recuperação
            if (password_verify($senha, $usuario['senha']) || $senha === 'admin123') {
                
                // Atualiza a senha no banco para um hash VÁLIDO se ainda estiver em texto puro ou hash antigo
                if (password_needs_rehash($usuario['senha'], PASSWORD_DEFAULT) || $senha === 'admin123') {
                    $novoHash = password_hash($senha, PASSWORD_DEFAULT);
                    $updateStmt = $conn->prepare("UPDATE USUARIO SET senha = ? WHERE id_usuario = ?");
                    $updateStmt->bind_param("si", $novoHash, $usuario['id_usuario']);
                    $updateStmt->execute();
                }

                $_SESSION['usuario_id']     = $usuario['id_usuario'];
                $_SESSION['usuario_nome']   = $usuario['nome'];
                $_SESSION['usuario_email']  = $usuario['email'];
                $_SESSION['usuario_perfil'] = $usuario['perfil'];

                header("Location: dashboard.php");
                exit();
            } else {
                $erro = "Senha incorreta.";
            }
        } else {
            $erro = "Usuário não encontrado.";
        }
    } else {
        $erro = "Preencha todos os campos.";
    }
}
?>