<?php
require_once "conexao.php";
require_once "auth.php";
verificarAutenticacao();

// Remove tempo limite do PHP e expande memória para datasets grandes (+18k linhas)
set_time_limit(0);
ini_set('memory_limit', '512M');

$mensagem = "";
$tipoAlert = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["arquivo"])) {
    $arquivoTmp = $_FILES["arquivo"]["tmp_name"];
    $nomeArquivo = $_FILES["arquivo"]["name"];

    if (!empty($arquivoTmp) && is_uploaded_file($arquivoTmp)) {
        $handle = fopen($arquivoTmp, "r");

        if ($handle !== false) {
            $headers = fgetcsv($handle, 0, ",");
            $headerMap = array();
            
            if ($headers !== false) {
                $headers[0] = preg_replace('/\x{EF}\x{BB}\x{BF}/', '', $headers[0]);
                foreach ($headers as $index => $col) {
                    $headerMap[trim(strtolower($col))] = $index;
                }
            }

            $getVal = function($linha, $chaves, $default = '') use ($headerMap) {
                foreach ((array)$chaves as $chave) {
                    if (isset($headerMap[$chave]) && isset($linha[$headerMap[$chave]])) {
                        return trim($linha[$headerMap[$chave]]);
                    }
                }
                return $default;
            };

            // Inicia Transação SQL para processamento ultra rápido em lote
            $conn->begin_transaction();

            $sqlImport = "INSERT INTO IMPORTACAO (arquivo, data_importacao, quantidade_importada, quantidade_erros) 
                          VALUES ('" . $conn->real_escape_string($nomeArquivo) . "', NOW(), 0, 0)";
            $conn->query($sqlImport);
            $idImportacao = $conn->insert_id;

            $totalInseridos = 0;
            $totalErros = 0;
            $cacheTimes = array();

            // Prepared Statements (compila a SQL apenas 1 vez para reuso rápido)
            $stmtTimeSelect = $conn->prepare("SELECT id_time FROM TIME WHERE nome = ? LIMIT 1");
            $stmtTimeInsert = $conn->prepare("INSERT INTO TIME (nome, liga, pais, id_importacao) VALUES (?, ?, ?, ?)");
            $stmtJogador    = $conn->prepare("INSERT INTO JOGADOR (nome, posicao, overall, id_importacao, id_time) VALUES (?, ?, ?, ?, ?)");
            $stmtStats      = $conn->prepare("INSERT INTO ESTATISTICAS (pac, sho, pas, dri, def, phy, stamina, potencial, idade, valor_eur, salario_eur, perna_ruim, fintas, finalizacao, forca_chute, visao, forca, id_jogador) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            while (($linha = fgetcsv($handle, 0, ",")) !== false) {
                if (count($linha) <= 1 && empty($linha[0])) continue;

                $nomeJogador = $getVal($linha, array('short_name', 'name', 'player_name', 'nome'), 'Desconhecido');
                $posicao     = $getVal($linha, array('player_positions', 'club_position', 'position', 'posicao'), 'N/A');
                $overall     = (int)$getVal($linha, array('overall', 'ovr', 'rating'), 0);
                
                $nomeTime    = $getVal($linha, array('club_name', 'team', 'club', 'time'), 'Sem Clube');
                $liga        = $getVal($linha, array('league_name', 'league', 'liga'), 'N/A');
                $pais        = $getVal($linha, array('nationality_name', 'nation', 'pais'), 'N/A');

                $pac     = (int)$getVal($linha, array('pace', 'pac'), 0);
                $sho     = (int)$getVal($linha, array('shooting', 'sho'), 0);
                $pas     = (int)$getVal($linha, array('passing', 'pas'), 0);
                $dri     = (int)$getVal($linha, array('dribbling', 'dri'), 0);
                $def     = (int)$getVal($linha, array('defending', 'def'), 0);
                $phy     = (int)$getVal($linha, array('physic', 'phy'), 0);
                $stamina = (int)$getVal($linha, array('power_stamina', 'stamina'), 0);

                $potencial   = (int)$getVal($linha, array('potential', 'pot'), 0);
                $idade       = (int)$getVal($linha, array('age', 'idade'), 0);
                $valorEur    = (float)$getVal($linha, array('value_eur', 'value'), 0);
                $salarioEur  = (float)$getVal($linha, array('wage_eur', 'wage'), 0);
                $pernaRuim   = (int)$getVal($linha, array('weak_foot'), 0);
                $fintas      = (int)$getVal($linha, array('skill_moves'), 0);
                $finalizacao = (int)$getVal($linha, array('attacking_finishing', 'finishing'), 0);
                $forcaChute  = (int)$getVal($linha, array('power_shot_power', 'shot_power'), 0);
                $visao       = (int)$getVal($linha, array('mentality_vision', 'vision'), 0);
                $forca       = (int)$getVal($linha, array('power_strength', 'strength'), 0);

                // Mapeamento e Cache de Times
                $idTime = null;
                if (!empty($nomeTime) && $nomeTime !== 'Sem Clube') {
                    if (isset($cacheTimes[$nomeTime])) {
                        $idTime = $cacheTimes[$nomeTime];
                    } else {
                        $stmtTimeSelect->bind_param("s", $nomeTime);
                        $stmtTimeSelect->execute();
                        $res = $stmtTimeSelect->get_result();
                        
                        if ($row = $res->fetch_assoc()) {
                            $idTime = $row['id_time'];
                        } else {
                            $stmtTimeInsert->bind_param("sssi", $nomeTime, $liga, $pais, $idImportacao);
                            $stmtTimeInsert->execute();
                            $idTime = $stmtTimeInsert->insert_id;
                        }
                        $cacheTimes[$nomeTime] = $idTime;
                    }
                }

                // Inserção do Jogador
                $stmtJogador->bind_param("ssiii", $nomeJogador, $posicao, $overall, $idImportacao, $idTime);
                if ($stmtJogador->execute()) {
                    $idJogador = $stmtJogador->insert_id;

                    // Inserção das Estatísticas
                    $stmtStats->bind_param("iiiiiiiiidiiiiiiii", 
                        $pac, $sho, $pas, $dri, $def, $phy, $stamina, 
                        $potencial, $idade, $valorEur, $salarioEur, 
                        $pernaRuim, $fintas, $finalizacao, $forcaChute, $visao, $forca, $idJogador
                    );
                    $stmtStats->execute();
                    $totalInseridos++;
                } else {
                    $totalErros++;
                }
            }
            fclose($handle);

            // Confirma todas as gravações no MySQL de uma única vez
            $conn->commit();

            $conn->query("UPDATE IMPORTACAO SET quantidade_importada = $totalInseridos, quantidade_erros = $totalErros WHERE id_importacao = $idImportacao");

            $mensagem = "Importação concluída com sucesso!<br>
                         <b>" . number_format($totalInseridos, 0, ',', '.') . "</b> jogadores inseridos instantaneamente | 
                         Erros: <b>$totalErros</b> | Clubes mapeados: <b>" . count($cacheTimes) . "</b>";
            $tipoAlert = "alert-success";
        } else {
            $mensagem = "Erro ao abrir o arquivo CSV enviado.";
            $tipoAlert = "alert-error";
        }
    } else {
        $mensagem = "Por favor, selecione um arquivo CSV válido.";
        $tipoAlert = "alert-error";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importar Dataset EA FC 26 - FIFA Dados</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php require_once "navbar.php"; ?>
    
    <div class="container">
        <!-- Seu conteúdo normal da página aqui -->
    </div>


    <div class="container">
        <a href="index.php" class="nav-link">← Voltar ao Menu</a>

        <div class="header">
            <h2>Importar Planilha Completa (EA FC 26)</h2>
            <p>Envie a planilha inteira <code>FC26_20250921.csv</code> para popular o banco de dados</p>
        </div>

        <?php if (!empty($mensagem)): ?>
            <div class="alert <?= $tipoAlert ?>">
                <?= $mensagem ?>
            </div>
        <?php endif; ?>

        <form action="importar.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Arquivo CSV do EA FC 26</label>
                <input type="file" name="arquivo" accept=".csv" required>
            </div>

            <button type="submit">Processar e Enviar para o Banco</button>
        </form>

        <div style="margin-top: 1.5rem; text-align: center;">
            <a href="dashboard.php" class="nav-link" style="color: var(--primary-green);">📊 Ir para o Dashboard e Analytics →</a>
        </div>
    </div>

</body>
</html>