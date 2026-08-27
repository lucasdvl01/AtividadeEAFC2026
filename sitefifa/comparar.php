<?php
require_once "auth.php";
verificarAutenticacao();
require_once "conexao.php";

// Busca os jogadores para preencher a lista. 
// DICA: Limitamos aos 1000 melhores em Overall para o navegador não travar tentando carregar 18 mil nomes de uma vez.
$queryJogadores = $conn->query("SELECT id_jogador, nome, overall FROM JOGADOR ORDER BY overall DESC LIMIT 1000");
$listaJogadores = [];
while ($row = $queryJogadores->fetch_assoc()) {
    $listaJogadores[] = $row;
}

$p1 = null;
$p2 = null;

if (isset($_GET['j1']) && isset($_GET['j2'])) {
    $id1 = (int)$_GET['j1'];
    $id2 = (int)$_GET['j2'];

    // Consulta que junta os dados do Jogador, Time e Estatísticas
    $sql = "SELECT j.nome, j.overall, j.posicao, t.nome as clube, 
                   e.pac, e.sho, e.pas, e.dri, e.def, e.phy, e.idade, e.potencial, e.perna_ruim, e.fintas 
            FROM JOGADOR j 
            LEFT JOIN TIME t ON j.id_time = t.id_time 
            LEFT JOIN ESTATISTICAS e ON j.id_jogador = e.id_jogador 
            WHERE j.id_jogador = ?";
    
    $stmt = $conn->prepare($sql);
    
    // Pega os dados do Jogador 1
    $stmt->bind_param("i", $id1);
    $stmt->execute();
    $p1 = $stmt->get_result()->fetch_assoc();

    // Pega os dados do Jogador 2
    $stmt->bind_param("i", $id2);
    $stmt->execute();
    $p2 = $stmt->get_result()->fetch_assoc();
}

// Função para definir quem ganha e pintar de verde ou vermelho
function compararAtributo($val1, $val2, $isP1) {
    if ($val1 > $val2) {
        return $isP1 ? 'color: #00ff87; font-weight: 800;' : 'color: #ef4444;';
    } elseif ($val1 < $val2) {
        return $isP1 ? 'color: #ef4444;' : 'color: #00ff87; font-weight: 800;';
    }
    return 'color: #94a3b8;'; // Empate fica cinza
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comparador de Jogadores - EA FC 26 Analytics</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .comparador-container {
            display: flex;
            gap: 2rem;
            margin-top: 2rem;
        }
        .jogador-card {
            flex: 1;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 2rem;
            text-align: center;
        }
        .overall-badge {
            font-size: 3rem;
            font-weight: 800;
            color: var(--primary-green);
            margin: 1rem 0;
        }
        .stat-row {
            display: flex;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid var(--border-color);
            font-size: 1.1rem;
        }
        .stat-label {
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            width: 33%;
            text-align: center;
        }
        .stat-value {
            width: 33%;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <?php require_once "navbar.php"; ?>

    <div class="container container-wide">
        <div class="header">
            <h2>⚖️ Comparador de Atletas</h2>
            <p>Selecione dois jogadores para comparar seus atributos lado a lado</p>
        </div>

        <form method="GET" action="comparar.php" style="flex-direction: row; align-items: flex-end; background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
            <div class="form-group" style="flex: 1;">
                <label>Jogador 1 (Azul)</label>
                <select name="j1" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($listaJogadores as $j): ?>
                        <option value="<?= $j['id_jogador'] ?>" <?= (isset($_GET['j1']) && $_GET['j1'] == $j['id_jogador']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($j['nome']) ?> (GER: <?= $j['overall'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="flex: 1;">
                <label>Jogador 2 (Vermelho)</label>
                <select name="j2" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($listaJogadores as $j): ?>
                        <option value="<?= $j['id_jogador'] ?>" <?= (isset($_GET['j2']) && $_GET['j2'] == $j['id_jogador']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($j['nome']) ?> (GER: <?= $j['overall'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" style="width: auto; padding: 1rem 2rem; margin-top: 0;">Comparar</button>
        </form>

        <?php if ($p1 && $p2): ?>
            <div class="comparador-container">
                <!-- Coluna Jogador 1 -->
                <div class="jogador-card" style="border-top: 4px solid #3b82f6;">
                    <h3><?= htmlspecialchars($p1['nome']) ?></h3>
                    <p style="color: var(--text-muted);"><?= htmlspecialchars($p1['clube'] ?? 'Sem Clube') ?> | <?= $p1['posicao'] ?></p>
                    <div class="overall-badge" style="color: #3b82f6;"><?= $p1['overall'] ?></div>
                </div>

                <!-- Coluna Tabela de Comparação -->
                <div class="jogador-card" style="flex: 2; padding: 1rem 2rem;">
                    <h3 style="margin-bottom: 1rem;">Comparativo de Atributos</h3>
                    
                    <?php
                    $stats = [
                        'PAC' => 'pac', 'SHO' => 'sho', 'PAS' => 'pas', 
                        'DRI' => 'dri', 'DEF' => 'def', 'PHY' => 'phy',
                        'Potencial' => 'potencial', 'Idade' => 'idade',
                        'Perna Ruim' => 'perna_ruim', 'Fintas' => 'fintas'
                    ];

                    foreach ($stats as $label => $key): 
                        $v1 = (int)$p1[$key];
                        $v2 = (int)$p2[$key];
                    ?>
                    <div class="stat-row">
                        <div class="stat-value" style="text-align: right; <?= compararAtributo($v1, $v2, true) ?>"><?= $v1 ?></div>
                        <div class="stat-label"><?= $label ?></div>
                        <div class="stat-value" style="text-align: left; <?= compararAtributo($v2, $v1, false) ?>"><?= $v2 ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Coluna Jogador 2 -->
                <div class="jogador-card" style="border-top: 4px solid #ef4444;">
                    <h3><?= htmlspecialchars($p2['nome']) ?></h3>
                    <p style="color: var(--text-muted);"><?= htmlspecialchars($p2['clube'] ?? 'Sem Clube') ?> | <?= $p2['posicao'] ?></p>
                    <div class="overall-badge" style="color: #ef4444;"><?= $p2['overall'] ?></div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</body>
</html>