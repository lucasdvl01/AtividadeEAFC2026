<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
<h2>Cadastrar Usuário</h2>

<form action="cadastrar.php" method="POST">

    <label>Nome:</label><br>
    <input type="text" name="nome"><br><br>

    <label>Email:</label><br>
    <input type="email" name="email"><br><br>

    <label>Senha:</label><br>
    <input type="password" name="senha"><br><br>

    <label>Tipo:</label><br>
    <input type="text" name="tipo_usuario"><br><br>

    <button type="submit">Cadastrar</button>

</form>

</body>
</html>
<?php

$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "fifadados";

$conn = new mysqli($host, $usuario, $senha, $banco);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

include "conexao.php";

$nome = $_POST["nome"];
$email = $_POST["email"];
$senha = $_POST["senha"];
$tipo = $_POST["tipo_usuario"];

$sql = "INSERT INTO USUARIO (tipo_usuario, senha, email, nome)
        VALUES ('$tipo', '$senha', '$email', '$nome')";

if ($conn->query($sql)) {
    echo "Usuário cadastrado com sucesso!";
} else {
    echo "Erro: " . $conn->error;
}

$conn->close();





?>



