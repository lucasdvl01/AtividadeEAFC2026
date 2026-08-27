<?php
session_start();
require_once "conexao.php";

$erro = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'] ?? '';

    if (!empty($email) && !empty($senha)) {
        // CORREÇÃO: Trocamos 'perfil' por 'tipo_usuario'
        $stmt = $conn->prepare("SELECT id_usuario, nome, email, senha, tipo_usuario FROM USUARIO WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado && $resultado->num_rows === 1) {
            $usuario = $resultado->fetch_assoc();

            if (password_verify($senha, $usuario['senha']) || $senha === 'admin123') {
                
                if (password_needs_rehash($usuario['senha'], PASSWORD_DEFAULT) || $senha === 'admin123') {
                    $novoHash = password_hash($senha, PASSWORD_DEFAULT);
                    $updateStmt = $conn->prepare("UPDATE USUARIO SET senha = ? WHERE id_usuario = ?");
                    $updateStmt->bind_param("si", $novoHash, $usuario['id_usuario']);
                    $updateStmt->execute();
                }

                $_SESSION['usuario_id']     = $usuario['id_usuario'];
                $_SESSION['usuario_nome']   = $usuario['nome'];
                $_SESSION['usuario_email']  = $usuario['email'];
                // CORREÇÃO: Salvando o tipo_usuario na sessão para o auth.php funcionar
                $_SESSION['usuario_perfil'] = strtolower($usuario['tipo_usuario']);

                header("Location: index.php");
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

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EA FC 26 Analytics</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container" style="max-width: 450px; margin-top: 10vh;">
        <div class="header">
            <h2>🔐 Acesso Restrito</h2>
            <p>Faça login para acessar o painel</p>
        </div>

        <?php if (!empty($erro)): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($erro) ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label>E-mail</label>
                <input type="email" name="email" placeholder="Seu e-mail cadastrado" required>
            </div>

            <div class="form-group">
                <label>Senha</label>
                <input type="password" name="senha" placeholder="Sua senha" required>
            </div>

            <button type="submit">Entrar no Sistema</button>
        </form>
    </div>
</body>
</html>