<?php
require_once "auth.php";
verificarAutenticacao();
require_once "conexao.php";

// Captura os filtros de busca
$buscaNome = $_GET['nome'] ?? '';
$buscaPosicao = $_GET['posicao'] ?? '';

// Monta a consulta SQL dinamicamente baseada nos filtros
$sql = "SELECT j.id_jogador, j.nome, j.posicao, j.overall, t.nome as clube 
        FROM JOGADOR j 
        LEFT JOIN TIME t ON j.id_time = t.id_time 
        WHERE 1=1";

$params = [];
$types = "";

if (!empty($buscaNome)) {
    $sql .= " AND j.nome LIKE ?";
    $params[] = "%" . $buscaNome . "%";
    $types .= "s";
}

if (!empty($buscaPosicao)) {
    $sql .= " AND j.posicao = ?";
    $params[] = $buscaPosicao;
    $types .= "s";
}

$sql .= " ORDER BY j.overall DESC LIMIT 100"; // Limite para não sobrecarregar

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesquisa de Jogadores - EA FC Analytics</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .barra-pesquisa {
            display: flex;
            gap: 1rem;
            background: var(--bg-card);
            padding: 1.5rem;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
            margin-bottom: 2rem;
            align-items: flex-end;
        }
        .tabela-ranking { width: 100%; border-collapse: collapse; background: var(--bg-card); border-radius: var(--radius-md); overflow: hidden; }
        .tabela-ranking th { background: rgba(0, 0, 0, 0.2); color: var(--text-muted); padding: 1rem; text-align: left; }
        .tabela-ranking td { padding: 1rem; border-bottom: 1px solid var(--border-color); color: var(--text-main); }
        .tabela-ranking tr:hover { background: rgba(255, 255, 255, 0.02); }
        .btn-perfil {
            background: rgba(0, 255, 135, 0.15);
            color: var(--primary-green);
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.2s;
        }
        .btn-perfil:hover { background: var(--primary-green); color: #0b0f19; }
    </style>
</head>
<body>

    <?php require_once "navbar.php"; ?>

    <div class="container container-wide">
        <div class="header">
            <h2>🔍 Explorar Base de Dados</h2>
            <p>Pesquise jogadores por nome ou filtre por posição[cite: 1]</p>
        </div>

        <!-- Formulário de Filtros -->
        <form method="GET" action="pesquisa.php" class="barra-pesquisa">
            <div class="form-group" style="flex: 2;">
                <label>Nome do Jogador</label>
                <input type="text" name="nome" value="<?= htmlspecialchars($buscaNome) ?>" placeholder="Ex: Messi, Vini Jr...">
            </div>
            <div class="form-group" style="flex: 1;">
                <label>Posição</label>
                <select name="posicao">
                    <option value="">Todas as Posições</option>
                    <option value="ATA" <?= $buscaPosicao == 'ATA' ? 'selected' : '' ?>>Atacante (ATA)</option>
                    <option value="MEI" <?= $buscaPosicao == 'MEI' ? 'selected' : '' ?>>Meio-Campo (MEI)</option>
                    <option value="ZAG" <?= $buscaPosicao == 'ZAG' ? 'selected' : '' ?>>Zagueiro (ZAG)</option>
                    <option value="GOL" <?= $buscaPosicao == 'GOL' ? 'selected' : '' ?>>Goleiro (GOL)</option>
                </select>
            </div>
            <button type="submit" style="width: auto; padding: 0.95rem 2rem; margin-top: 0;">Buscar</button>
        </form>

        <!-- Tabela de Resultados -->
        <div style="overflow-x: auto;">
            <table class="tabela-ranking">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Overall</th>
                        <th>Posição</th>
                        <th>Clube</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($resultado && $resultado->num_rows > 0): ?>
                        <?php while ($row = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td style="font-weight: 700;"><?= htmlspecialchars($row['nome']) ?></td>
                                <td style="color: var(--primary-green); font-weight: 800;"><?= $row['overall'] ?></td>
                                <td><?= htmlspecialchars($row['posicao']) ?></td>
                                <td style="color: var(--text-muted);"><?= htmlspecialchars($row['clube'] ?? '--') ?></td>
                                <td>
                                    <a href="perfil.php?id=<?= $row['id_jogador'] ?>" class="btn-perfil">Ver Perfil</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align: center; padding: 2rem;">Nenhum jogador encontrado com esses filtros.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>