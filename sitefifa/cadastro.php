<?php
require_once "conexao.php";

$mensagem = "";
$tipoAlert = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $senhaUsuario = $_POST["senha"];
    $tipo = $_POST["tipo_usuario"];

    $sql = "INSERT INTO USUARIO (tipo_usuario, senha, email, nome)
            VALUES ('$tipo', '$senhaUsuario', '$email', '$nome')";

    if ($conn->query($sql)) {
        $mensagem = "Usuário cadastrado com sucesso!";
        $tipoAlert = "alert-success";
    } else {
        $mensagem = "Erro ao cadastrar: " . $conn->error;
        $tipoAlert = "alert-error";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Usuário - FIFA Dados</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        <a href="index.php" class="nav-link">← Voltar ao Menu</a>

        <div class="header">
            <h2>Cadastrar Usuário</h2>
            <p>Preencha os dados do novo usuário</p>
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
                <label>Tipo de Usuário</label>
                <input type="text" name="tipo_usuario" placeholder="Ex: ADMIN ou JOGADOR" required>
            </div>

            <button type="submit">Cadastrar Usuário</button>
        </form>
    </div>

</body>
</html>