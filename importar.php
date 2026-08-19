<?php

include "conexao.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $tabela = $_POST["tabela"];
    $arquivoTmp = $_FILES["arquivo"]["tmp_name"];

    // Colunas esperadas de cada tabela, NA MESMA ORDEM das colunas do CSV
    $colunasPorTabela = array(
        "USUARIO"      => array("id_usuario","tipo_usuario","senha","email","nome"),
        "IMPORTACAO"   => array("id_importacao","arquivo","data_importacao","quantidade_importada","quantidade_erros","id_usuario"),
        "TIME"         => array("id_time","nome","liga","pais","id_importacao"),
        "JOGADOR"      => array("id_jogador","nome","posicao","overall","id_importacao","id_time"),
        "ESTATISTICAS" => array("id_estatistica","gol","faltas","pac","sho","dri","pas","phy","def","stamina","assistencias","id_jogador"),
        "PARTIDA"      => array("id_partida","data","adversario","campeonato"),
        "DESEMPENHO"   => array("id_desempenho","nota","gols","assistencias","pontuacao","id_jogador","id_partida")
    );

    if (!array_key_exists($tabela, $colunasPorTabela)) {
        die("Tabela inválida.");
    }

    $colunas = $colunasPorTabela[$tabela];

    $handle = fopen($arquivoTmp, "r");

    if ($handle === false) {
        die("Não foi possível abrir o arquivo enviado.");
    }

    // pula a linha de cabeçalho (nomes das colunas)
    $linhaCabecalho = fgetcsv($handle);

    $totalInseridos = 0;
    $totalErros = 0;

    while (($linha = fgetcsv($handle)) !== false) {

        // ignora linhas em branco no final do arquivo
        if (count($linha) == 1 && $linha[0] === null) {
            continue;
        }

        $valores = array();
        foreach ($linha as $valor) {
            $valores[] = "'" . $conn->real_escape_string($valor) . "'";
        }

        $sql = "INSERT INTO " . $tabela . " (" . implode(", ", $colunas) . ")
                VALUES (" . implode(", ", $valores) . ")";

        if ($conn->query($sql)) {
            $totalInseridos++;
        } else {
            $totalErros++;
            echo "Erro na linha: " . $conn->error . "<br>";
        }
    }

    fclose($handle);

    echo "<br>Importação concluída para a tabela <b>" . $tabela . "</b>.<br>";
    echo "Linhas inseridas: " . $totalInseridos . "<br>";
    echo "Linhas com erro: " . $totalErros . "<br>";
}

$conn->close();

?>
