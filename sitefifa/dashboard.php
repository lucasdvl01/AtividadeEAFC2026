<?php
require_once "conexao.php";
require_once "auth.php";
verificarAutenticacao();

$busca   = isset($_GET['busca']) ? $conn->real_escape_string($_GET['busca']) : '';
$posicao = isset($_GET['posicao']) ? $conn->real_escape_string($_GET['posicao']) : '';
$ordem   = isset($_GET['ordem']) ? $_GET['ordem'] : 'overall';

$colunasPermitidas = [
    'overall'     => 'j.overall',
    'potencial'   => 'e.potencial',
    'valor_eur'   => 'e.valor_eur',
    'idade'       => 'e.idade',
    'pac'         => 'e.pac',
    'sho'         => 'e.sho',
    'pas'         => 'e.pas',
    'dri'         => 'e.dri',
    'def'         => 'e.def',
    'phy'         => 'e.phy',
    'finalizacao' => 'e.finalizacao',
    'forca_chute' => 'e.forca_chute',
    'visao'       => 'e.visao',
    'forca'       => 'e.forca'
];

$campoOrdenacao = isset($colunasPermitidas[$ordem]) ? $colunasPermitidas[$ordem] : 'j.overall';

// KPI Cards Gerais
$sqlKPI = "SELECT 
            COUNT(j.id_jogador) as total_jogadores,
            ROUND(AVG(j.overall), 1) as media_ovr,
            MAX(e.potencial) as maior_potencial,
            (SELECT j2.nome FROM JOGADOR j2 LEFT JOIN ESTATISTICAS e2 ON j2.id_jogador = e2.id_jogador ORDER BY e2.valor_eur DESC LIMIT 1) as mais_valioso,
            (SELECT MAX(e3.valor_eur) FROM ESTATISTICAS e3) as maior_valor
           FROM JOGADOR j
           LEFT JOIN ESTATISTICAS e ON j.id_jogador = e.id_jogador";
$resKPI = $conn->query($sqlKPI);
$kpi = $resKPI ? $resKPI->fetch_assoc() : ['total_jogadores' => 0, 'media_ovr' => 0, 'maior_potencial' => 0, 'mais_valioso' => 'N/A', 'maior_valor' => 0];

// Filtros da Tabela
$whereClause = "WHERE j.nome LIKE '%$busca%'";
if (!empty($posicao)) {
    $whereClause .= " AND j.posicao LIKE '%$posicao%'";
}

$sql = "SELECT j.nome, j.posicao, j.overall, t.nome AS time_nome,
               e.pac, e.sho, e.pas, e.dri, e.def, e.phy,
               e.potencial, e.idade, e.valor_eur, e.salario_eur,
               e.perna_ruim, e.fintas, e.finalizacao, e.forca_chute, e.visao, e.forca
        FROM JOGADOR j
        LEFT JOIN ESTATISTICAS e ON j.id_jogador = e.id_jogador
        LEFT JOIN TIME t ON j.id_time = t.id_time
        $whereClause
        ORDER BY CAST($campoOrdenacao AS UNSIGNED) DESC
        LIMIT 50";

$resultado = $conn->query($sql);

function formatarMoeda($valor) {
    if ($valor >= 1000000) {
        return '€ ' . number_format($valor / 1000000, 1, ',', '.') . 'M';
    } elseif ($valor >= 1000) {
        return '€ ' . number_format($valor / 1000, 0, ',', '.') . 'K';
    }
    return '€ ' . number_format($valor, 0, ',', '.');
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard & Analytics - EA FC 26</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
        .kpi-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.25rem; text-align: center; }
        .kpi-card h3 { font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.5rem; }
        .kpi-card p { font-size: 1.5rem; font-weight: 800; color: var(--primary-green); margin: 0; }
        
        .filter-grid { display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 0.75rem; margin-bottom: 1.5rem; }
        .table-responsive { overflow-x: auto; margin-top: 1rem; }
        table { width: 100%; border-collapse: collapse; background: var(--bg-card); border-radius: var(--radius-sm); overflow: hidden; white-space: nowrap; }
        th, td { padding: 0.75rem 0.85rem; text-align: center; border-bottom: 1px solid var(--border-color); font-size: 0.85rem; }
        th { background: rgba(255, 255, 255, 0.05); color: var(--primary-green); font-weight: 700; text-transform: uppercase; }
        td.text-left { text-align: left; }
        tr:hover { background: rgba(255, 255, 255, 0.03); }
        .stat-highlight { color: var(--primary-green); font-weight: 700; }
        .stat-badge { background: rgba(0, 255, 135, 0.1); padding: 2px 6px; border-radius: 4px; }
    </style>
</head>
<body>

    <?php require_once "navbar.php"; ?>

    <div class="container container-wide">
        <div class="header">
            <h2>📊 Dashboard & Analytics EA FC 26</h2>
            <p>Análise detalhada de 16 atributos técnicos, físicos e econômicos</p>
        </div>

        <div class="kpi-grid">
            <div class="kpi-card">
                <h3>Total Jogadores</h3>
                <p><?= number_format($kpi['total_jogadores']) ?></p>
            </div>
            <div class="kpi-card">
                <h3>Média de Overall</h3>
                <p><?= $kpi['media_ovr'] ?></p>
            </div>
            <div class="kpi-card">
                <h3>Maior Potencial</h3>
                <p><?= $kpi['maior_potencial'] ?></p>
            </div>
            <div class="kpi-card">
                <h3>Mais Valioso</h3>
                <p style="font-size: 1.05rem; color: var(--text-main);"><?= htmlspecialchars($kpi['mais_valioso'] ?? 'N/A') ?></p>
                <span style="font-size: 0.8rem; color: var(--primary-green);"><?= formatarMoeda($kpi['maior_valor']) ?></span>
            </div>
        </div>

        <form method="GET" action="dashboard.php" class="filter-grid">
            <input type="text" name="busca" placeholder="Buscar por nome..." value="<?= htmlspecialchars($busca) ?>">
            <input type="text" name="posicao" placeholder="Posição (Ex: ST, CAM, CB)" value="<?= htmlspecialchars($posicao) ?>">

            <select name="ordem">
                <option value="overall" <?= $ordem === 'overall' ? 'selected' : '' ?>>Overall (OVR)</option>
                <option value="potencial" <?= $ordem === 'potencial' ? 'selected' : '' ?>>Potencial (POT)</option>
                <option value="valor_eur" <?= $ordem === 'valor_eur' ? 'selected' : '' ?>>Valor de Mercado (€)</option>
                <option value="idade" <?= $ordem === 'idade' ? 'selected' : '' ?>>Idade</option>
                <option value="pac" <?= $ordem === 'pac' ? 'selected' : '' ?>>Ritmo (PAC)</option>
                <option value="sho" <?= $ordem === 'sho' ? 'selected' : '' ?>>Chute (SHO)</option>
                <option value="pas" <?= $ordem === 'pas' ? 'selected' : '' ?>>Passe (PAS)</option>
                <option value="dri" <?= $ordem === 'dri' ? 'selected' : '' ?>>Drible (DRI)</option>
                <option value="def" <?= $ordem === 'def' ? 'selected' : '' ?>>Defesa (DEF)</option>
                <option value="phy" <?= $ordem === 'phy' ? 'selected' : '' ?>>Físico (PHY)</option>
                <option value="finalizacao" <?= $ordem === 'finalizacao' ? 'selected' : '' ?>>Finalização</option>
                <option value="forca_chute" <?= $ordem === 'forca_chute' ? 'selected' : '' ?>>Força Chute</option>
                <option value="visao" <?= $ordem === 'visao' ? 'selected' : '' ?>>Visão de Jogo</option>
                <option value="forca" <?= $ordem === 'forca' ? 'selected' : '' ?>>Força Física</option>
            </select>

            <button type="submit" style="margin-top:0; padding: 0.85rem 1.25rem;">Filtrar</button>
        </form>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th class="text-left">Nome</th>
                        <th>Pos.</th>
                        <th>Idade</th>
                        <th class="text-left">Time</th>
                        <th>OVR</th>
                        <th>POT</th>
                        <th>Valor</th>
                        <th>PAC</th>
                        <th>SHO</th>
                        <th>PAS</th>
                        <th>DRI</th>
                        <th>DEF</th>
                        <th>PHY</th>
                        <th>FIN</th>
                        <th>VIS</th>
                        <th>PWR</th>
                        <th>FOR</th>
                        <th>P.Ruim</th>
                        <th>Fintas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($resultado && $resultado->num_rows > 0): ?>
                        <?php $posicaoNum = 1; ?>
                        <?php while ($linha = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td><?= $posicaoNum++ ?></td>
                                <td class="text-left"><strong><?= htmlspecialchars($linha['nome']) ?></strong></td>
                                <td><span class="stat-badge"><?= htmlspecialchars($linha['posicao'] ?? 'N/A') ?></span></td>
                                <td><?= htmlspecialchars($linha['idade'] ?? '-') ?></td>
                                <td class="text-left"><?= htmlspecialchars($linha['time_nome'] ?? 'Sem Clube') ?></td>
                                <td><span class="stat-highlight"><?= htmlspecialchars($linha['overall'] ?? '-') ?></span></td>
                                <td><?= htmlspecialchars($linha['potencial'] ?? '-') ?></td>
                                <td><?= formatarMoeda($linha['valor_eur']) ?></td>
                                <td><?= htmlspecialchars($linha['pac'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($linha['sho'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($linha['pas'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($linha['dri'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($linha['def'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($linha['phy'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($linha['finalizacao'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($linha['visao'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($linha['forca_chute'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($linha['forca'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($linha['perna_ruim'] ?? '-') ?>★</td>
                                <td><?= htmlspecialchars($linha['fintas'] ?? '-') ?>★</td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="20" style="text-align: center; color: var(--text-muted);">
                                Nenhum jogador encontrado.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>