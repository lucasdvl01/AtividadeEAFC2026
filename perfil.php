<?php
require_once "auth.php";
verificarAutenticacao();
require_once "conexao.php";

// Pega o ID da URL, se não tiver, pega o primeiro jogador do banco
$id_jogador = isset($_GET['id']) ? (int)$_GET['id'] : 1;

$sql = "SELECT j.nome, j.overall, j.posicao, t.nome as clube, 
               e.pac, e.sho, e.pas, e.dri, e.def, e.phy, e.idade, e.potencial
        FROM JOGADOR j 
        LEFT JOIN TIME t ON j.id_time = t.id_time 
        LEFT JOIN ESTATISTICAS e ON j.id_jogador = e.id_jogador 
        WHERE j.id_jogador = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_jogador);
$stmt->execute();
$jogador = $stmt->get_result()->fetch_assoc();

if (!$jogador) {
    die("Jogador não encontrado!");
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil: <?= htmlspecialchars($jogador['nome']) ?> - EA FC Analytics</title>
    <link rel="stylesheet" href="style.css">
    <!-- RNF08: Biblioteca Chart.js via CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .perfil-container {
            display: flex;
            gap: 2rem;
            background: var(--bg-card);
            padding: 2rem;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-color);
            margin-top: 2rem;
        }
        .info-basica { flex: 1; }
        .grafico-container { flex: 1; max-width: 500px; }
        .overall-badge { font-size: 3rem; font-weight: 800; color: var(--primary-green); }
    </style>
</head>
<body>

    <?php require_once "navbar.php"; ?>

    <div class="container">
        <div class="header">
            <h2>🔎 Perfil do Atleta</h2>
            <p>Análise detalhada e gráfico de atributos[cite: 1]</p>
        </div>

        <div class="perfil-container">
            <!-- Coluna de Informações -->
            <div class="info-basica">
                <div class="overall-badge"><?= $jogador['overall'] ?></div>
                <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem;"><?= htmlspecialchars($jogador['nome']) ?></h1>
                <h3 style="color: var(--text-muted); margin-bottom: 2rem;">
                    <?= $jogador['posicao'] ?> | <?= htmlspecialchars($jogador['clube'] ?? 'Sem Clube') ?>
                </h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="alert alert-success"><strong>Idade:</strong> <?= $jogador['idade'] ?> anos</div>
                    <div class="alert alert-success"><strong>Potencial:</strong> <?= $jogador['potencial'] ?></div>
                </div>
            </div>

            <!-- Coluna do Gráfico de Radar -->
            <div class="grafico-container">
                <canvas id="radarChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('radarChart').getContext('2d');
        
        // Dados puxados diretamente do PHP para o JavaScript
        const atributos = [
            <?= $jogador['pac'] ?>, 
            <?= $jogador['sho'] ?>, 
            <?= $jogador['pas'] ?>, 
            <?= $jogador['dri'] ?>, 
            <?= $jogador['def'] ?>, 
            <?= $jogador['phy'] ?>
        ];

        new Chart(ctx, {
            type: 'radar',
            data: {
                labels: ['PAC (Ritmo)', 'SHO (Chute)', 'PAS (Passe)', 'DRI (Drible)', 'DEF (Defesa)', 'PHY (Físico)'], //
                datasets: [{
                    label: 'Atributos do Jogador',
                    data: atributos,
                    backgroundColor: 'rgba(0, 255, 135, 0.2)',
                    borderColor: '#00ff87',
                    pointBackgroundColor: '#00ff87',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#00ff87',
                    borderWidth: 2
                }]
            },
            options: {
                scales: {
                    r: {
                        angleLines: { color: 'rgba(255, 255, 255, 0.1)' },
                        grid: { color: 'rgba(255, 255, 255, 0.1)' },
                        pointLabels: { color: '#94a3b8', font: { size: 12, family: "'Plus Jakarta Sans', sans-serif" } },
                        ticks: { display: false, min: 0, max: 100 }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    </script>
</body>
</html>