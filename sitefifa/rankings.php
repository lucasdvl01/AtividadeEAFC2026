<?php
require_once "auth.php";
verificarAutenticacao();
require_once "conexao.php";

// Define a categoria atual (padrão é 'overall')
$categoria = $_GET['cat'] ?? 'overall';
$limite = 50; // Vamos mostrar o Top 50 para cada categoria

// Configura a busca no banco de dados dependendo da categoria escolhida
switch ($categoria) {
    case 'promessas':
        $tituloRanking = "💎 Maiores Promessas (Sub-21)";
        $nomeColunaDestaque = "Potencial";
        $sql = "SELECT j.nome, j.posicao, j.overall, t.nome as clube, e.potencial as destaque, e.idade 
                FROM JOGADOR j 
                LEFT JOIN TIME t ON j.id_time = t.id_time 
                LEFT JOIN ESTATISTICAS e ON j.id_jogador = e.id_jogador 
                WHERE e.idade <= 21 
                ORDER BY e.potencial DESC, j.overall DESC LIMIT $limite";
        break;

    case 'velozes':
        $tituloRanking = "⚡ Mais Velozes do Jogo";
        $nomeColunaDestaque = "Pace (Ritmo)";
        $sql = "SELECT j.nome, j.posicao, j.overall, t.nome as clube, e.pac as destaque, e.idade 
                FROM JOGADOR j 
                LEFT JOIN TIME t ON j.id_time = t.id_time 
                LEFT JOIN ESTATISTICAS e ON j.id_jogador = e.id_jogador 
                ORDER BY e.pac DESC, j.overall DESC LIMIT $limite";
        break;

    case 'finalizadores':
        $tituloRanking = "🎯 Melhores Finalizadores";
        $nomeColunaDestaque = "Shooting (Chute)";
        $sql = "SELECT j.nome, j.posicao, j.overall, t.nome as clube, e.sho as destaque, e.idade 
                FROM JOGADOR j 
                LEFT JOIN TIME t ON j.id_time = t.id_time 
                LEFT JOIN ESTATISTICAS e ON j.id_jogador = e.id_jogador 
                ORDER BY e.sho DESC, j.overall DESC LIMIT $limite";
        break;

    case 'valiosos':
        $tituloRanking = "💰 Jogadores Mais Valiosos";
        $nomeColunaDestaque = "Valor de Mercado";
        $sql = "SELECT j.nome, j.posicao, j.overall, t.nome as clube, e.valor_eur as destaque, e.idade 
                FROM JOGADOR j 
                LEFT JOIN TIME t ON j.id_time = t.id_time 
                LEFT JOIN ESTATISTICAS e ON j.id_jogador = e.id_jogador 
                ORDER BY e.valor_eur DESC LIMIT $limite";
        break;

    default: // 'overall'
        $tituloRanking = "🏆 Top 50 Melhores Jogadores";
        $nomeColunaDestaque = "Overall";
        $sql = "SELECT j.nome, j.posicao, j.overall, t.nome as clube, j.overall as destaque, e.idade 
                FROM JOGADOR j 
                LEFT JOIN TIME t ON j.id_time = t.id_time 
                LEFT JOIN ESTATISTICAS e ON j.id_jogador = e.id_jogador
                ORDER BY j.overall DESC LIMIT $limite";
        break;
}

$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rankings - EA FC 26 Analytics</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .filtros-ranking {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            justify-content: center;
        }
        .btn-filtro {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            padding: 0.8rem 1.5rem;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        .btn-filtro:hover, .btn-filtro.active {
            background: rgba(0, 255, 135, 0.1);
            border-color: var(--primary-green);
            color: var(--primary-green);
            box-shadow: var(--shadow-glow);
        }
        .tabela-ranking {
            width: 100%;
            border-collapse: collapse;
            background: var(--bg-card);
            border-radius: var(--radius-md);
            overflow: hidden;
        }
        .tabela-ranking th {
            background: rgba(0, 0, 0, 0.2);
            color: var(--text-muted);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.85rem;
            padding: 1rem;
            text-align: left;
        }
        .tabela-ranking td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-main);
            font-size: 0.95rem;
        }
        .tabela-ranking tr:last-child td {
            border-bottom: none;
        }
        .tabela-ranking tr:hover {
            background: rgba(255, 255, 255, 0.02);
        }
        .posicao-num {
            font-weight: 800;
            color: var(--text-muted);
            width: 40px;
        }
        .destaque-valor {
            font-weight: 800;
            color: var(--primary-green);
            font-size: 1.1rem;
        }
    </style>
</head>
<body>

    <?php require_once "navbar.php"; ?>

    <div class="container container-wide">
        <div class="header">
            <h2>📊 Tabelas de Liderança (Leaderboards)</h2>
            <p>Filtre e descubra os destaques do banco de dados</p>
        </div>

        <!-- Botões de Filtro -->
        <div class="filtros-ranking">
            <a href="?cat=overall" class="btn-filtro <?= $categoria == 'overall' ? 'active' : '' ?>">🏆 Top Geral</a>
            <a href="?cat=promessas" class="btn-filtro <?= $categoria == 'promessas' ? 'active' : '' ?>">💎 Promessas Sub-21</a>
            <a href="?cat=velozes" class="btn-filtro <?= $categoria == 'velozes' ? 'active' : '' ?>">⚡ Mais Velozes</a>
            <a href="?cat=finalizadores" class="btn-filtro <?= $categoria == 'finalizadores' ? 'active' : '' ?>">🎯 Finalizadores</a>
            <a href="?cat=valiosos" class="btn-filtro <?= $categoria == 'valiosos' ? 'active' : '' ?>">💰 Mais Valiosos</a>
        </div>

        <h3 style="margin-bottom: 1rem; text-align: center; color: var(--primary-cyan);"><?= $tituloRanking ?></h3>

        <div style="overflow-x: auto;">
            <table class="tabela-ranking">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome do Jogador</th>
                        <th>Posição</th>
                        <th>Clube</th>
                        <th>Idade</th>
                        <th><?= $nomeColunaDestaque ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $posicao = 1;
                    if ($resultado && $resultado->num_rows > 0):
                        while ($row = $resultado->fetch_assoc()): 
                            
                            // Formata o valor se a categoria for "valiosos" (ex: de 100000000 para € 100.000.000)
                            $valorExibicao = $row['destaque'];
                            if ($categoria == 'valiosos') {
                                $valorExibicao = '€ ' . number_format($row['destaque'], 0, ',', '.');
                            }
                    ?>
                        <tr>
                            <td class="posicao-num"><?= $posicao++ ?>º</td>
                            <td style="font-weight: 600;"><?= htmlspecialchars($row['nome']) ?></td>
                            <td><?= htmlspecialchars($row['posicao']) ?></td>
                            <td style="color: var(--text-muted);"><?= htmlspecialchars($row['clube'] ?? 'Sem Clube') ?></td>
                            <td><?= $row['idade'] ?? '--' ?></td>
                            <td class="destaque-valor"><?= $valorExibicao ?></td>
                        </tr>
                    <?php 
                        endwhile; 
                    else:
                    ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-muted);">Nenhum dado encontrado para este ranking. (Importe o CSV primeiro)</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>