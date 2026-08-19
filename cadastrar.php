<?php

$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "fifadados";

$conn = new mysqli($host, $usuario, $senha, $banco);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

$nome = $_POST["nome"];
$email = $_POST["email"];
$senhaUsuario = $_POST["senha"];
$tipo = $_POST["tipo_usuario"];

$sql = "INSERT INTO USUARIO (tipo_usuario, senha, email, nome)
        VALUES ('$tipo', '$senhaUsuario', '$email', '$nome')";

if ($conn->query($sql)) {
    echo "Usuário cadastrado com sucesso!";
} else {
    echo "Erro: " . $conn->error;
}

$conn->close();

?>