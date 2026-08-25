<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel - FIFA Dados</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php require_once "navbar.php"; ?>
    
    <div class="container">
        <!-- Seu conteúdo normal da página aqui -->
    </div>



<!-- Chamada do arquivo JavaScript no final do <body> ou dentro do <head> -->
<script src="theme.js"></script>
    <div class="container">
        <div class="header">
            <h1>🎮 FIFA DADOS</h1>
            <p>Painel de Controle e Gestão</p>
        </div>

        <div class="menu-grid">
            <a href="dashboard.php" class="menu-card">
                <span>🏆 Visualizar Dashboard & Ranking</span>
                <span>→</span>
            </a>

            <a href="cadastro.php" class="menu-card">
                <span>👤 Cadastrar Novo Usuário</span>
                <span>→</span>
            </a>

            <a href="importar.php" class="menu-card">
                <span>📂 Importar Planilhas CSV</span>
                <span>→</span>
            </a>
        </div>
    </div>

</body>
</html>