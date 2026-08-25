<?php
require_once "auth.php";
verificarAdmin(); // Restringe o acesso apenas a perfis Admin
require_once "conexao.php";

$mensagem = "";
$tipoAlert = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['cadastrar'])) {
    $nome   = trim($_POST['nome']);
    $email  = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha  = $_POST['senha'];
    $perfil = $_POST['perfil'];

    if (!empty($nome) && !empty($email) && !empty($senha)) {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO USUARIO (nome, email, senha, perfil) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nome, $email, $senhaHash, $perfil);

        if ($stmt->execute()) {
            $mensagem = "Usuário <b>" . htmlspecialchars($nome) . "</b> cadastrado com sucesso!";
            $tipoAlert = "alert-success";
        } else {
            $mensagem = "Erro ao cadastrar. E-mail pode já estar em uso.";
            $tipoAlert = "alert-error";
        }
    }
}

// Listagem de usuários
$usuarios = $conn->query("SELECT id_usuario, nome, email, perfil, data_criacao FROM USUARIO ORDER BY id_usuario DESC");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Usuários - EA FC 26</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>


    <?php require_once "navbar.php"; ?>
    
    <div class="container">
        <!-- Seu conteúdo normal da página aqui -->
    </div>

    <div class="container">
        <a href="dashboard.php" class="nav-link">← Voltar ao Dashboard</a>

        <div class="header">
            <h2>👥 Gerenciamento de Usuários</h2>
            <p>Cadastre novos usuários e defina níveis de permissão (Administrador / Comum)</p>
        </div>

        <?php if (!empty($mensagem)): ?>
            <div class="alert <?= $tipoAlert ?>"><?= $mensagem ?></div>
        <?php endif; ?>

        <form method="POST" action="usuarios.php" style="margin-bottom: 2rem;">
            <h3>Cadastrar Novo Usuário</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label>Nome</label>
                    <input type="text" name="nome" required>
                </div>
                <div class="form-group">
                    <label>E-mail</label>
                    <input type="email" name="email" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label>Senha</label>
                    <input type="password" name="senha" required>
                </div>
                <div class="form-group">
                    <label>Perfil / Permissão</label>
                    <select name="perfil" required>
                        <option value="usuario">Usuário Comum</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
            </div>

            <button type="submit" name="cadastrar">Cadastrar Usuário</button>
        </form>

        <h3>Usuários Cadastrados</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Perfil</th>
                    <th>Data de Cadastro</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($u = $usuarios->fetch_assoc()): ?>
                    <tr>
                        <td><?= $u['id_usuario'] ?></td>
                        <td><?= htmlspecialchars($u['nome']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><strong><?= strtoupper($u['perfil']) ?></strong></td>
                        <td><?= date('d/m/Y H:i', strtotime($u['data_criacao'])) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>