-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 19/08/2026 às 22:39
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `fifadados`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `desempenho`
--

CREATE TABLE `desempenho` (
  `id_desempenho` int(11) NOT NULL,
  `nota` int(11) NOT NULL,
  `gols` int(11) NOT NULL,
  `assistencias` int(11) NOT NULL,
  `pontuacao` int(11) NOT NULL,
  `id_jogador` int(11) DEFAULT NULL,
  `id_partida` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `desempenho`
--

INSERT INTO `desempenho` (`id_desempenho`, `nota`, `gols`, `assistencias`, `pontuacao`, `id_jogador`, `id_partida`) VALUES
(1, 9, 2, 1, 12, 1, 1),
(2, 8, 1, 0, 7, 2, 1),
(3, 7, 0, 2, 6, 3, 1),
(4, 8, 1, 1, 9, 1, 2),
(5, 9, 2, 0, 11, 2, 2),
(6, 6, 0, 1, 4, 4, 2),
(7, 7, 1, 0, 6, 5, 3),
(8, 8, 1, 2, 10, 3, 3),
(9, 9, 2, 1, 12, 4, 3);

-- --------------------------------------------------------

--
-- Estrutura para tabela `estatisticas`
--

CREATE TABLE `estatisticas` (
  `id_estatistica` int(11) NOT NULL,
  `gol` int(11) NOT NULL,
  `faltas` int(11) NOT NULL,
  `pac` int(11) NOT NULL,
  `sho` int(11) NOT NULL,
  `dri` int(11) NOT NULL,
  `pas` int(11) NOT NULL,
  `phy` int(11) NOT NULL,
  `def` int(11) NOT NULL,
  `stamina` int(11) NOT NULL,
  `assistencias` int(11) NOT NULL,
  `id_jogador` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `estatisticas`
--

INSERT INTO `estatisticas` (`id_estatistica`, `gol`, `faltas`, `pac`, `sho`, `dri`, `pas`, `phy`, `def`, `stamina`, `assistencias`, `id_jogador`) VALUES
(1, 44, 12, 97, 90, 92, 81, 78, 36, 88, 15, 1),
(2, 52, 18, 87, 91, 80, 69, 88, 45, 85, 8, 2),
(3, 12, 22, 81, 81, 91, 91, 65, 90, 90, 22, 3),
(4, 39, 10, 65, 92, 84, 92, 82, 49, 80, 18, 4),
(5, 28, 20, 93, 88, 93, 85, 70, 42, 87, 20, 5);

-- --------------------------------------------------------

--
-- Estrutura para tabela `importacao`
--

CREATE TABLE `importacao` (
  `id_importacao` int(11) NOT NULL,
  `arquivo` varchar(255) NOT NULL,
  `data_importacao` datetime NOT NULL,
  `quantidade_importada` int(11) NOT NULL,
  `quantidade_erros` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `importacao`
--

INSERT INTO `importacao` (`id_importacao`, `arquivo`, `data_importacao`, `quantidade_importada`, `quantidade_erros`, `id_usuario`) VALUES
(1, 'jogadores_fifa_2024.xlsx', '2024-08-15 09:30:00', 5, 0, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `jogador`
--

CREATE TABLE `jogador` (
  `id_jogador` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `posicao` varchar(30) NOT NULL,
  `overall` int(11) NOT NULL,
  `id_importacao` int(11) DEFAULT NULL,
  `id_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `jogador`
--

INSERT INTO `jogador` (`id_jogador`, `nome`, `posicao`, `overall`, `id_importacao`, `id_time`) VALUES
(1, 'Kylian Mbappé', 'ATA', 91, 1, 1),
(2, 'Erling Haaland', 'ATA', 91, 1, 2),
(3, 'Aitana Bonmatí', 'MC', 91, 1, 3),
(4, 'Harry Kane', 'ATA', 90, 1, 4),
(5, 'Ousmane Dembélé', 'PD', 90, 1, 5);

-- --------------------------------------------------------

--
-- Estrutura para tabela `partida`
--

CREATE TABLE `partida` (
  `id_partida` int(11) NOT NULL,
  `data` date NOT NULL,
  `adversario` varchar(100) NOT NULL,
  `campeonato` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `partida`
--

INSERT INTO `partida` (`id_partida`, `data`, `adversario`, `campeonato`) VALUES
(1, '2024-09-14', 'Juventus', 'UEFA Champions League'),
(2, '2024-09-28', 'Liverpool', 'UEFA Champions League'),
(3, '2024-10-05', 'Milan', 'UEFA Champions League');

-- --------------------------------------------------------

--
-- Estrutura para tabela `time`
--

CREATE TABLE `time` (
  `id_time` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `liga` varchar(255) NOT NULL,
  `pais` varchar(255) NOT NULL,
  `id_importacao` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `time`
--

INSERT INTO `time` (`id_time`, `nome`, `liga`, `pais`, `id_importacao`) VALUES
(1, 'Real Madrid', 'La Liga', 'Espanha', 1),
(2, 'Manchester City', 'Premier League', 'Inglaterra', 1),
(3, 'Barcelona', 'La Liga', 'Espanha', 1),
(4, 'Bayern München', 'Bundesliga', 'Alemanha', 1),
(5, 'Paris Saint-Germain', 'Ligue 1', 'França', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `tipo_usuario` varchar(20) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `email` varchar(150) NOT NULL,
  `nome` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `tipo_usuario`, `senha`, `email`, `nome`) VALUES
(1, 'Admin', 'admin@123hash', 'admin@fifadata.com', 'Carlos Mendes'),
(2, 'Comum', 'user@456hash', 'juliana.souza@fifadata.com', 'Juliana Souza');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `desempenho`
--
ALTER TABLE `desempenho`
  ADD PRIMARY KEY (`id_desempenho`),
  ADD KEY `fk_desempenho_jogador` (`id_jogador`),
  ADD KEY `fk_desempenho_partida` (`id_partida`);

--
-- Índices de tabela `estatisticas`
--
ALTER TABLE `estatisticas`
  ADD PRIMARY KEY (`id_estatistica`),
  ADD KEY `fk_estatisticas_jogador` (`id_jogador`);

--
-- Índices de tabela `importacao`
--
ALTER TABLE `importacao`
  ADD PRIMARY KEY (`id_importacao`),
  ADD KEY `fk_importacao_usuario` (`id_usuario`);

--
-- Índices de tabela `jogador`
--
ALTER TABLE `jogador`
  ADD PRIMARY KEY (`id_jogador`),
  ADD KEY `fk_jogador_time` (`id_time`),
  ADD KEY `fk_jogador_importacao` (`id_importacao`);

--
-- Índices de tabela `partida`
--
ALTER TABLE `partida`
  ADD PRIMARY KEY (`id_partida`);

--
-- Índices de tabela `time`
--
ALTER TABLE `time`
  ADD PRIMARY KEY (`id_time`),
  ADD KEY `fk_time_importacao` (`id_importacao`);

--
-- Índices de tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `desempenho`
--
ALTER TABLE `desempenho`
  MODIFY `id_desempenho` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `estatisticas`
--
ALTER TABLE `estatisticas`
  MODIFY `id_estatistica` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `importacao`
--
ALTER TABLE `importacao`
  MODIFY `id_importacao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `jogador`
--
ALTER TABLE `jogador`
  MODIFY `id_jogador` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `partida`
--
ALTER TABLE `partida`
  MODIFY `id_partida` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `time`
--
ALTER TABLE `time`
  MODIFY `id_time` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `desempenho`
--
ALTER TABLE `desempenho`
  ADD CONSTRAINT `fk_desempenho_jogador` FOREIGN KEY (`id_jogador`) REFERENCES `jogador` (`id_jogador`),
  ADD CONSTRAINT `fk_desempenho_partida` FOREIGN KEY (`id_partida`) REFERENCES `partida` (`id_partida`);

--
-- Restrições para tabelas `estatisticas`
--
ALTER TABLE `estatisticas`
  ADD CONSTRAINT `fk_estatisticas_jogador` FOREIGN KEY (`id_jogador`) REFERENCES `jogador` (`id_jogador`);

--
-- Restrições para tabelas `importacao`
--
ALTER TABLE `importacao`
  ADD CONSTRAINT `fk_importacao_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Restrições para tabelas `jogador`
--
ALTER TABLE `jogador`
  ADD CONSTRAINT `fk_jogador_importacao` FOREIGN KEY (`id_importacao`) REFERENCES `importacao` (`id_importacao`),
  ADD CONSTRAINT `fk_jogador_time` FOREIGN KEY (`id_time`) REFERENCES `time` (`id_time`);

--
-- Restrições para tabelas `time`
--
ALTER TABLE `time`
  ADD CONSTRAINT `fk_time_importacao` FOREIGN KEY (`id_importacao`) REFERENCES `importacao` (`id_importacao`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
