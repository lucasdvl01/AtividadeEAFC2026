<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Principal - EA FC 26 Analytics</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Chama a Navbar com o CSS arrumado -->
    <?php require_once "navbar.php"; ?>
    
    <div class="container">
        <div class="header">
            <h1>🎮 EA FC 26 Analytics</h1>
            <p>Painel de Controle e Gestão</p>
        </div>

        <div class="menu-grid">
            <a href="dashboard.php" class="menu-card">
                📊 Visão Geral (Dashboard) <span>→</span>
            </a>
            
            <!-- AQUI ESTÁ A TELA NOVA DE PESQUISA E PERFIL -->
            <a href="pesquisa.php" class="menu-card" style="border-left: 4px solid var(--primary-cyan);">
                🔍 Pesquisar & Perfil de Jogadores <span>→</span>
            </a>
            
            <a href="comparar.php" class="menu-card">
                ⚖️ Comparador de Atletas <span>→</span>
            </a>
            
            <a href="rankings.php" class="menu-card">
                🏆 Rankings e Liderança <span>→</span>
            </a>
            
            <a href="importar.php" class="menu-card">
                📁 Importar Planilha (Excel/CSV) <span>→</span>
            </a>

            <a href="usuarios.php" class="menu-card">
                👥 Gerenciar Usuários <span>→</span>
            </a>
            
            <a href="cadastro.php" class="menu-card">
                👤 Cadastrar Novo Usuário <span>→</span>
            </a>
        </div>
    </div>

</body>
</html>