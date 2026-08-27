<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$perfilUser = $_SESSION['usuario_perfil'] ?? 'usuario';
$nomeUser   = $_SESSION['usuario_nome'] ?? 'Visitante';
?>

<header class="main-navbar">
    <div class="nav-brand">
        <!-- Alterado para voltar ao menu principal (index.php) -->
        <a href="index.php">⚽ EA FC 26 Analytics</a>
    </div>
    
    <nav class="nav-links">
        <a href="dashboard.php" class="nav-item">📊 Dashboard</a>
        <!-- Adicionado o link para a página de Pesquisa e Gráficos de Radar -->
        <a href="pesquisa.php" class="nav-item">🔍 Pesquisar & Gráficos</a>
        <a href="comparar.php" class="nav-item">⚖️ Comparador</a>
        <a href="rankings.php" class="nav-item">🏆 Rankings</a>
        
        <?php if ($perfilUser === 'admin'): ?>
            <a href="importar.php" class="nav-item">📥 Importar CSV</a>
            <a href="usuarios.php" class="nav-item">👥 Usuários</a>
        <?php endif; ?>

        <button id="theme-toggle" class="theme-toggle-btn" type="button">
            <span>🌙</span> <span>Modo Escuro</span>
        </button>

        <a href="logout.php" class="nav-item nav-logout">Sair (<?= htmlspecialchars($nomeUser) ?>)</a>
    </nav>
</header>

<script src="theme.js"></script>