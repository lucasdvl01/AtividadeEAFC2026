<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "conexao.php";

$mensagem = "";
$tipoAlert = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = trim($_POST["nome"]);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senhaUsuario = $_POST["senha"];
    $perfil = $_POST["perfil"]; // Atualizado de tipo_usuario para perfil

    if (!empty($nome) && !empty($email) && !empty($senhaUsuario)) {
        // CRÍTICO: Criptografar a senha antes de salvar no banco
        $senhaHash = password_hash($senhaUsuario, PASSWORD_DEFAULT);

        // Usando Prepared Statements para evitar SQL Injection
        $stmt = $conn->prepare("INSERT INTO USUARIO (perfil, senha, email, nome) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $perfil, $senhaHash, $email, $nome);

        if ($stmt->execute()) {
            $mensagem = "Usuário cadastrado com sucesso! Já pode fazer login.";
            $tipoAlert = "alert-success";
        } else {
            $mensagem = "Erro ao cadastrar. O e-mail já pode estar em uso.";
            $tipoAlert = "alert-error";
        }
    } else {
        $mensagem = "Preencha todos os campos.";
        $tipoAlert = "alert-error";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Usuário - EA FC 26 Analytics</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Incluindo a Navbar no topo -->
    <?php require_once "navbar.php"; ?>

    <div class="container">
        <div class="header">
            <h2>Cadastrar Novo Usuário</h2>
            <p>Preencha os dados abaixo para criar um acesso ao sistema</p>
        </div>

        <?php if (!empty($mensagem)): ?>
            <div class="alert <?= $tipoAlert ?>">
                <?= $mensagem ?>
            </div>
        <?php endif; ?>

        <form action="cadastro.php" method="POST">
            <div class="form-group">
                <label>Nome Completo</label>
                <input type="text" name="nome" placeholder="Ex: Lionel Messi" required>
            </div>

            <div class="form-group">
                <label>E-mail</label>
                <input type="email" name="email" placeholder="usuario@exemplo.com" required>
            </div>

            <div class="form-group">
                <label>Senha</label>
                <input type="password" name="senha" placeholder="••••••••" required>
            </div>

            <div class="form-group">
                <label>Nível de Acesso (Perfil)</label>
                <select name="perfil" required>
                    <option value="usuario">Usuário Comum (Apenas leitura)</option>
                    <option value="admin">Administrador (Total Acesso)</option>
                </select>
            </div>

            <button type="submit">Cadastrar Usuário</button>
        </form>
    </div>

</body>
</html>