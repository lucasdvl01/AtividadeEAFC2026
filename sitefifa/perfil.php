<?php
require_once "auth.php";
verificarAutenticacao();
require_once "conexao.php";

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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <?php require_once "navbar.php"; ?>

    <div class="container container-wide">
        <div class="bento-container">
            
            <!-- Cartão do Jogador (Bento 1) -->
            <div class="bento-card player-hero">
                <div class="fut-shield">
                    <div class="fut-shield-inner">
                        <div class="fut-ovr"><?= $jogador['overall'] ?></div>
                        <div class="fut-pos"><?= htmlspecialchars($jogador['posicao']) ?></div>
                    </div>
                </div>
                
                <h1 style="font-size: 2.2rem; margin-bottom: 0.5rem;"><?= htmlspecialchars($jogador['nome']) ?></h1>
                <h3 style="color: var(--text-muted); font-weight: 500;">
                    <?= htmlspecialchars($jogador['clube'] ?? 'Agente Livre') ?>
                </h3>

                <div style="display: flex; gap: 1rem; margin-top: 2rem; width: 100%;">
                    <div style="flex: 1; background: rgba(255,255,255,0.03); padding: 1rem; border-radius: 12px;">
                        <span style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase;">Idade</span>
                        <div style="font-size: 1.5rem; font-weight: 800;"><?= $jogador['idade'] ?></div>
                    </div>
                    <div style="flex: 1; background: rgba(0,255,135,0.05); padding: 1rem; border-radius: 12px; border: 1px solid rgba(0,255,135,0.2);">
                        <span style="font-size: 0.8rem; color: var(--primary-green); text-transform: uppercase;">Potencial</span>
                        <div style="font-size: 1.5rem; font-weight: 800; color: var(--primary-green);"><?= $jogador['potencial'] ?></div>
                    </div>
                </div>
            </div>

            <!-- Análise e Radar (Bento 2) -->
            <div class="bento-card" style="display: flex; flex-direction: column; justify-content: center;">
                <h3 style="margin-bottom: 1.5rem; font-size: 1.2rem; display: flex; align-items: center; gap: 0.5rem;">
                    <span style="color: var(--primary-green);">⚡</span> Análise de Atributos
                </h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; align-items: center;">
                    
                    <div class="stats-list">
                        <?php 
                        $stats = [
                            'PAC' => $jogador['pac'], 'SHO' => $jogador['sho'], 
                            'PAS' => $jogador['pas'], 'DRI' => $jogador['dri'], 
                            'DEF' => $jogador['def'], 'PHY' => $jogador['phy']
                        ];
                        foreach ($stats as $label => $val): ?>
                            <div class="stat-row">
                                <div class="stat-label"><?= $label ?></div>
                                <div class="stat-bar-bg">
                                    <div class="stat-bar-fill" style="width: <?= $val ?>%;"></div>
                                </div>
                                <div class="stat-value"><?= $val ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div style="position: relative; height: 100%; min-height: 250px;">
                        <canvas id="radarChart"></canvas>
                    </div>
                    
                </div>
            </div>

        </div>
    </div>

    <script>
        const ctx = document.getElementById('radarChart').getContext('2d');
        const atributos = [<?= $jogador['pac'] ?>, <?= $jogador['sho'] ?>, <?= $jogador['pas'] ?>, <?= $jogador['dri'] ?>, <?= $jogador['def'] ?>, <?= $jogador['phy'] ?>];

        new Chart(ctx, {
            type: 'radar',
            data: {
                labels: ['PAC', 'SHO', 'PAS', 'DRI', 'DEF', 'PHY'],
                datasets: [{
                    label: 'Atributos',
                    data: atributos,
                    backgroundColor: 'rgba(0, 255, 135, 0.15)',
                    borderColor: '#00ff87',
                    pointBackgroundColor: '#0b0f19',
                    pointBorderColor: '#00ff87',
                    pointBorderWidth: 3,
                    pointHoverBackgroundColor: '#00ff87',
                    pointHoverBorderColor: '#fff',
                    pointHoverRadius: 6,
                    borderWidth: 2,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        angleLines: { color: 'rgba(255, 255, 255, 0.05)' },
                        grid: { color: 'rgba(255, 255, 255, 0.05)', circular: true },
                        pointLabels: { 
                            color: '#94a3b8', 
                            font: { size: 11, family: "'Plus Jakarta Sans', sans-serif", weight: 'bold' } 
                        },
                        ticks: { display: false, min: 0, max: 100 }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(11, 15, 25, 0.9)',
                        titleColor: '#00ff87',
                        bodyColor: '#fff',
                        borderColor: 'rgba(0, 255, 135, 0.3)',
                        borderWidth: 1,
                        padding: 10,
                        displayColors: false,
                        callbacks: {
                            label: function(context) { return context.raw + ' pts'; }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>