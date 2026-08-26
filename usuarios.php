<?php
require_once "auth.php";
verificarAutenticacao();

// SEGURANÇA: Só permite acesso para quem for Admin
if (($_SESSION['usuario_perfil'] ?? '') !== 'admin') {
    header("Location: index.php");
    exit();
}

require_once "conexao.php";

$mensagem = "";
$erro = "";

// Lógica para Excluir Usuário
if (isset($_GET['excluir'])) {
    $idExcluir = (int)$_GET['excluir'];
    
    // Evita que o Admin logado apague a própria conta
    if ($idExcluir === $_SESSION['usuario_id']) {
        $erro = "Você não pode excluir a sua própria conta!";
    } else {
        $stmt = $conn->prepare("DELETE FROM USUARIO WHERE id_usuario = ?");
        $stmt->bind_param("i", $idExcluir);
        if ($stmt->execute()) {
            $mensagem = "Usuário removido com sucesso!";
        } else {
            $erro = "Erro ao remover usuário.";
        }
    }
}

// Lógica para Alterar Perfil (Admin / Comum)
if (isset($_GET['mudar_perfil']) && isset($_GET['novo_perfil'])) {
    $idMudar = (int)$_GET['mudar_perfil'];
    $novoPerfil = $_GET['novo_perfil'] === 'Admin' ? 'Admin' : 'Comum';

    if ($idMudar === $_SESSION['usuario_id']) {
        $erro = "Você não pode alterar o seu próprio perfil!";
    } else {
        $stmt = $conn->prepare("UPDATE USUARIO SET tipo_usuario = ? WHERE id_usuario = ?");
        $stmt->bind_param("si", $novoPerfil, $idMudar);
        if ($stmt->execute()) {
            $mensagem = "Perfil atualizado com sucesso!";
        }
    }
}

// Busca a lista de todos os usuários
$resultado = $conn->query("SELECT id_usuario, nome, email, tipo_usuario FROM USUARIO ORDER BY id_usuario ASC");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Usuários - EA FC 26</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .tabela-usuarios {
            width: 100%;
            border-collapse: collapse;
            background: var(--bg-card);
            border-radius: var(--radius-md);
            overflow: hidden;
            margin-top: 1.5rem;
        }
        .tabela-usuarios th {
            background: rgba(0, 0, 0, 0.2);
            color: var(--text-muted);
            padding: 1rem;
            text-align: left;
            font-size: 0.85rem;
            text-transform: uppercase;
        }
        .tabela-usuarios td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        .badge-role {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
        }
        .badge-admin { background: rgba(0, 255, 135, 0.2); color: var(--primary-green); }
        .badge-comum { background: rgba(255, 255, 255, 0.1); color: var(--text-muted); }
        .btn-acao {
            color: #ef4444;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.85rem;
            margin-left: 0.5rem;
        }
        .btn-acao:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <?php require_once "navbar.php"; ?>

    <div class="container">
        <div class="header">
            <h2>👥 Gerenciamento de Usuários</h2>
            <p>Controle quem tem acesso ao painel de administração</p>
        </div>

        <?php if ($mensagem): ?>
            <div class="alert alert-success"><?= $mensagem ?></div>
        <?php endif; ?>

        <?php if ($erro): ?>
            <div class="alert alert-error"><?= $erro ?></div>
        <?php endif; ?>

        <div style="display: flex; justify-content: flex-end;">
            <a href="cadastro.php" class="theme-toggle-btn" style="text-decoration: none;">+ Novo Usuário</a>
        </div>

        <table class="tabela-usuarios">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Nível de Acesso</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($u = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td>#<?= $u['id_usuario'] ?></td>
                        <td><strong><?= htmlspecialchars($u['nome']) ?></strong></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td>
                            <span class="badge-role <?= strtolower($u['tipo_usuario']) === 'admin' ? 'badge-admin' : 'badge-comum' ?>">
                                <?= htmlspecialchars($u['tipo_usuario']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($u['id_usuario'] != $_SESSION['usuario_id']): ?>
                                <?php $novoP = strtolower($u['tipo_usuario']) === 'admin' ? 'Comum' : 'Admin'; ?>
                                <a href="?mudar_perfil=<?= $u['id_usuario'] ?>&novo_perfil=<?= $novoP ?>" class="btn-acao" style="color: var(--primary-cyan);">
                                    Tornar <?= $novoP ?>
                                </a>
                                <a href="?excluir=<?= $u['id_usuario'] ?>" class="btn-acao" onclick="return confirm('Tem certeza que deseja remover este usuário?');">
                                    Excluir
                                </a>
                            <?php else: ?>
                                <span style="color: var(--text-muted); font-size: 0.85rem;">(Sua conta)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</body>
</html>