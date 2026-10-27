-- ============================================================
-- PROJETO: Petfy – Toda Patinha merece um lar!
-- SISTEMA DE ADOÇÃO RESPONSÁVEL DE ANIMAIS
-- Banco de Dados: MariaDB / MySQL (Compatível com XAMPP)
-- Versão do PHP: 8.2+
-- ============================================================

CREATE DATABASE IF NOT EXISTS `petfy` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `petfy`;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `solicitacao_adocao`;
DROP TABLE IF EXISTS `pet`;
DROP TABLE IF EXISTS `raca`;
DROP TABLE IF EXISTS `especie`;
DROP TABLE IF EXISTS `usuario`;

SET FOREIGN_KEY_CHECKS = 1;

-- --------------------------------------------------------
-- Tabela `usuario`
-- --------------------------------------------------------
CREATE TABLE `usuario` (
  `id_usuario` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `senha` VARCHAR(255) NOT NULL,
  `telefone` VARCHAR(20) DEFAULT NULL,
  `cep` VARCHAR(9) DEFAULT NULL,
  `rua` VARCHAR(100) DEFAULT NULL,
  `numero` VARCHAR(10) DEFAULT NULL,
  `bairro` VARCHAR(50) DEFAULT NULL,
  `cidade` VARCHAR(50) DEFAULT NULL,
  `estado` CHAR(2) DEFAULT NULL,
  `tipo_usuario` ENUM('usuario', 'admin') NOT NULL DEFAULT 'usuario',
  `data_cadastro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `uk_usuario_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabela `especie`
-- --------------------------------------------------------
CREATE TABLE `especie` (
  `id_especie` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(30) NOT NULL,
  PRIMARY KEY (`id_especie`),
  UNIQUE KEY `uk_especie_nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabela `raca`
-- --------------------------------------------------------
CREATE TABLE `raca` (
  `id_raca` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(50) NOT NULL,
  `id_especie` INT(11) NOT NULL,
  PRIMARY KEY (`id_raca`),
  KEY `fk_raca_especie` (`id_especie`),
  CONSTRAINT `fk_raca_especie` FOREIGN KEY (`id_especie`) REFERENCES `especie` (`id_especie`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabela `pet`
-- --------------------------------------------------------
CREATE TABLE `pet` (
  `id_pet` INT(11) NOT NULL AUTO_INCREMENT,
  `id_usuario_tutor` INT(11) NOT NULL,
  `id_especie` INT(11) NOT NULL,
  `id_raca` INT(11) DEFAULT NULL,
  `nome` VARCHAR(60) NOT NULL,
  `sexo` ENUM('Macho', 'Fêmea') NOT NULL,
  `porte` ENUM('P', 'M', 'G') NOT NULL DEFAULT 'M',
  `idade` VARCHAR(30) NOT NULL,
  `personalidades` VARCHAR(255) DEFAULT NULL,
  `descricao` TEXT DEFAULT NULL,
  `vacinado` TINYINT(1) NOT NULL DEFAULT 0,
  `castrado` TINYINT(1) NOT NULL DEFAULT 0,
  `vermifugado` TINYINT(1) NOT NULL DEFAULT 0,
  `necessidades_especiais` VARCHAR(255) DEFAULT NULL,
  `foto_url` VARCHAR(255) DEFAULT NULL,
  `contato_nome` VARCHAR(100) DEFAULT NULL,
  `contato_telefone` VARCHAR(20) DEFAULT NULL,
  `contato_email` VARCHAR(100) DEFAULT NULL,
  `cidade` VARCHAR(50) NOT NULL,
  `estado` CHAR(2) NOT NULL,
  `status` ENUM('Disponível', 'Em Processo', 'Adotado') NOT NULL DEFAULT 'Disponível',
  `data_cadastro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pet`),
  KEY `fk_pet_tutor` (`id_usuario_tutor`),
  KEY `fk_pet_especie` (`id_especie`),
  KEY `fk_pet_raca` (`id_raca`),
  CONSTRAINT `fk_pet_tutor` FOREIGN KEY (`id_usuario_tutor`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_pet_especie` FOREIGN KEY (`id_especie`) REFERENCES `especie` (`id_especie`) ON UPDATE CASCADE,
  CONSTRAINT `fk_pet_raca` FOREIGN KEY (`id_raca`) REFERENCES `raca` (`id_raca`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabela `solicitacao_adocao`
-- --------------------------------------------------------
CREATE TABLE `solicitacao_adocao` (
  `id_solicitacao` INT(11) NOT NULL AUTO_INCREMENT,
  `id_pet` INT(11) NOT NULL,
  `id_usuario_adotante` INT(11) NOT NULL,
  `mensagem_motivo` TEXT NOT NULL,
  `telefone_contato` VARCHAR(20) NOT NULL,
  `status` ENUM('Pendente', 'Em Análise', 'Aprovada', 'Recusada', 'Finalizada') NOT NULL DEFAULT 'Pendente',
  `resposta_tutor` TEXT DEFAULT NULL,
  `data_solicitacao` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data_atualizacao` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id_solicitacao`),
  KEY `fk_adocao_pet` (`id_pet`),
  KEY `fk_adocao_usuario` (`id_usuario_adotante`),
  CONSTRAINT `fk_adocao_pet` FOREIGN KEY (`id_pet`) REFERENCES `pet` (`id_pet`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_adocao_usuario` FOREIGN KEY (`id_usuario_adotante`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Dados Iniciais
-- --------------------------------------------------------
INSERT INTO `especie` (`id_especie`, `nome`) VALUES
(1, 'Cachorro'),
(2, 'Gato'),
(3, 'Outro');

INSERT INTO `raca` (`id_raca`, `nome`, `id_especie`) VALUES
(1, 'Vira-lata (SRD)', 1),
(2, 'Labrador Retriever', 1),
(3, 'Poodle', 1),
(4, 'Golden Retriever', 1),
(5, 'Bulldog Francês', 1),
(6, 'Shih Tzu', 1),
(7, 'Pastor Alemão', 1),
(8, 'Vira-lata (SRD)', 2),
(9, 'Siamês', 2),
(10, 'Persa', 2),
(11, 'Maine Coon', 2),
(12, 'Angorá', 2),
(13, 'Outra Raça', 3);

-- Senha padrão para todos os usuários de teste: 123456
INSERT INTO `usuario` (`id_usuario`, `nome`, `email`, `senha`, `telefone`, `cep`, `rua`, `numero`, `bairro`, `cidade`, `estado`, `tipo_usuario`) VALUES
(1, 'Maria Souza', 'maria@exemplo.com', '$2y$10$MU4LaZ0JPkitaScwonlB4O0cxUuheMN2TtMoagf3XPSCHEIo9cmvC', '(11) 98765-4321', '01310-100', 'Avenida Paulista', '1000', 'Bela Vista', 'São Paulo', 'SP', 'usuario'),
(2, 'Carlos Silva', 'carlos@exemplo.com', '$2y$10$MU4LaZ0JPkitaScwonlB4O0cxUuheMN2TtMoagf3XPSCHEIo9cmvC', '(11) 91234-5678', '04571-010', 'Avenida Engenheiro Luís Carlos Berrini', '500', 'Itaim Bibi', 'São Paulo', 'SP', 'usuario'),
(3, 'Administrador Petfy', 'admin@petfy.com.br', '$2y$10$MU4LaZ0JPkitaScwonlB4O0cxUuheMN2TtMoagf3XPSCHEIo9cmvC', '(11) 99999-9999', '01001-000', 'Praça da Sé', '1', 'Centro', 'São Paulo', 'SP', 'admin');

INSERT INTO `pet` (`id_pet`, `id_usuario_tutor`, `id_especie`, `id_raca`, `nome`, `sexo`, `porte`, `idade`, `personalidades`, `descricao`, `vacinado`, `castrado`, `vermifugado`, `necessidades_especiais`, `foto_url`, `contato_nome`, `contato_telefone`, `contato_email`, `cidade`, `estado`, `status`) VALUES
(1, 1, 1, 4, 'Thor', 'Macho', 'G', '2 anos', 'Brincalhão, Dócil, Carinhoso', 'O Thor é um Golden super dócil e cheio de energia. Adora correr no parque e se dá muito bem com crianças e outros cães.', 1, 1, 1, NULL, 'https://images.unsplash.com/photo-1552053831-71594a27632d?auto=format&fit=crop&w=600&q=80', 'Maria Souza', '(11) 98765-4321', 'maria@exemplo.com', 'São Paulo', 'SP', 'Disponível'),
(2, 1, 2, 9, 'Luna', 'Fêmea', 'P', '1 ano', 'Calma, Carinhosa, Tímida', 'A Luna é uma gatinha siamesa tranquila e dengosa. Prefere ambientes sossegados e adora um colo quentinho.', 1, 1, 1, NULL, 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?auto=format&fit=crop&w=600&q=80', 'Maria Souza', '(11) 98765-4321', 'maria@exemplo.com', 'São Paulo', 'SP', 'Disponível'),
(3, 2, 1, 1, 'Bolinha', 'Macho', 'M', '8 meses', 'Brincalhão, Agitado, Protetor', 'Bolinha foi resgatado de rua muito pequeno e agora está forte, saudável e pronto para um lar amoroso e responsável.', 1, 0, 1, 'Nenhuma', 'https://images.unsplash.com/photo-1543466835-00a7907e9de1?auto=format&fit=crop&w=600&q=80', 'Carlos Silva', '(11) 91234-5678', 'carlos@exemplo.com', 'São Paulo', 'SP', 'Disponível'),
(4, 2, 2, 8, 'Mia', 'Fêmea', 'P', '6 meses', 'Dócil, Curiosa, Independente', 'Mia é uma filhotinha brincalhona que ama bolinhas de papel e explorar cantinhos da casa.', 1, 1, 1, NULL, 'https://images.unsplash.com/photo-1533738363-b7f9aef128ce?auto=format&fit=crop&w=600&q=80', 'Carlos Silva', '(11) 91234-5678', 'carlos@exemplo.com', 'São Paulo', 'SP', 'Disponível');

INSERT INTO `solicitacao_adocao` (`id_solicitacao`, `id_pet`, `id_usuario_adotante`, `mensagem_motivo`, `telefone_contato`, `status`, `data_solicitacao`) VALUES
(1, 1, 2, 'Olá Maria! Tenho uma casa com quintal amplo e já tive cachorros antes. Adoraria dar um lar com muito carinho para o Thor.', '(11) 91234-5678', 'Pendente', NOW());
