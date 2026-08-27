<?php
$host = "localhost";
$usuario = "root";
$senha = ""; // Coloque sua senha do MySQL aqui se tiver uma
$banco = "fifadados"; // Corrigido: sem o underline!

$conn = new mysqli($host, $usuario, $senha, $banco);

if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}
?>