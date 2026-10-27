<?php
/**
 * ============================================================
 * Petfy – Toda Patinha merece um lar!
 * BACK-END: Configuração e Conexão com Banco de Dados (PDO)
 * Suporte a MySQL/MariaDB (portas 3306/3307) com Auto-Criação
 * e Fallback Automático para SQLite (zero configuração)
 * ============================================================
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('DB_HOST')) define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
if (!defined('DB_PORT')) define('DB_PORT', getenv('DB_PORT') ?: '3306');
if (!defined('DB_NAME')) define('DB_NAME', getenv('DB_NAME') ?: 'petfy');
if (!defined('DB_USER')) define('DB_USER', getenv('DB_USER') ?: 'root');
if (!defined('DB_PASS')) define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
if (!defined('DB_CHARSET')) define('DB_CHARSET', 'utf8mb4');

/**
 * Retorna a conexão PDO configurada com Prepared Statements
 */
function getDatabaseConnection(): PDO {
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    $mysqlOptions = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_TIMEOUT            => 1,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ];

    $host = (DB_HOST === 'localhost') ? '127.0.0.1' : DB_HOST;

    // 1. Tenta conexões MySQL apenas nas portas que realmente estão abertas
    $portasParaTestar = [DB_PORT, '3306', '3307'];
    $portasParaTestar = array_unique(array_filter($portasParaTestar));

    $senhasParaTestar = [DB_PASS, ''];
    $senhasParaTestar = array_unique($senhasParaTestar);

    foreach ($portasParaTestar as $porta) {
        // Teste rápido via socket com timeout de 0.2s para não atrasar o carregamento da página
        $sock = @fsockopen($host, (int)$porta, $errno, $errstr, 0.2);
        if (!$sock) {
            continue; // Porta fechada, passa direto sem bloquear o PHP
        }
        fclose($sock);

        foreach ($senhasParaTestar as $senha) {
            // Tenta banco 'petfy'
            try {
                $dsn = "mysql:host=" . $host . ";port=" . $porta . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
                $pdo = new PDO($dsn, DB_USER, $senha, $mysqlOptions);
                $GLOBALS['PETFY_DB_DRIVER'] = 'mysql';
                return $pdo;
            } catch (PDOException $e1) {
                // Se o erro foi que o banco não existe (código 1049), tenta auto-criar
                if ($e1->getCode() == 1049 || stripos($e1->getMessage(), 'Unknown database') !== false) {
                    try {
                        $dsnRoot = "mysql:host=" . $host . ";port=" . $porta . ";charset=" . DB_CHARSET;
                        $pdoRoot = new PDO($dsnRoot, DB_USER, $senha, $mysqlOptions);
                        $pdoRoot->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                        $pdoRoot->exec("USE `" . DB_NAME . "`");
                        
                        // Importa o script SQL se disponível
                        $sqlPath = __DIR__ . '/../../banco de dados/petfy_database.sql';
                        if (file_exists($sqlPath)) {
                            $sqlCommands = file_get_contents($sqlPath);
                            $pdoRoot->exec($sqlCommands);
                        }
                        $pdo = $pdoRoot;
                        $GLOBALS['PETFY_DB_DRIVER'] = 'mysql';
                        return $pdo;
                    } catch (Throwable $eCreate) {
                        // continua tentando
                    }
                }

                // Tenta nome alternativo 'vidapet'
                try {
                    $dsnAlt = "mysql:host=" . $host . ";port=" . $porta . ";dbname=vidapet;charset=" . DB_CHARSET;
                    $pdo = new PDO($dsnAlt, DB_USER, $senha, $mysqlOptions);
                    $GLOBALS['PETFY_DB_DRIVER'] = 'mysql';
                    return $pdo;
                } catch (PDOException $e2) {
                    // continua tentando outras opções
                }
            }
        }
    }

    // 2. Se o MySQL não estiver disponível, inicializa fallback transparente em SQLite
    try {
        $sqliteFile = __DIR__ . '/petfy.sqlite';
        $pdo = new PDO('sqlite:' . $sqliteFile);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // Suporte a funções compatíveis com MySQL em SQLite
        $pdo->sqliteCreateFunction('now', fn() => date('Y-m-d H:i:s'));

        // Verifica se tabelas existem, caso contrário inicializa
        $stmtCheck = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='pet'");
        if (!$stmtCheck->fetch()) {
            initSqliteDatabase($pdo);
        }

        $GLOBALS['PETFY_DB_DRIVER'] = 'sqlite';
        return $pdo;

    } catch (Throwable $eSqlite) {
        die("
            <!DOCTYPE html>
            <html lang='pt-BR'>
            <head>
                <meta charset='UTF-8'>
                <title>Petfy – Conexão com Banco de Dados</title>
                <style>
                    body { font-family: 'Segoe UI', Arial, sans-serif; background: #f0fdf9; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
                    .error-box { background: white; max-width: 600px; padding: 35px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); text-align: center; border: 1.5px solid #fecaca; }
                    .error-box h2 { color: #b91c1c; margin-top: 0; font-size: 24px; }
                    .error-box p { color: #4b5563; line-height: 1.6; }
                </style>
            </head>
            <body>
                <div class='error-box'>
                    <h2>🐾 Atenção: Conexão com o Banco de Dados</h2>
                    <p>O Petfy não conseguiu inicializar o banco de dados.</p>
                    <small style='color: #9ca3af;'>Detalhes: " . htmlspecialchars($eSqlite->getMessage()) . "</small>
                </div>
            </body>
            </html>
        ");
    }
}

/**
 * Cria tabelas e insere dados iniciais no banco SQLite
 */
function initSqliteDatabase(PDO $pdo): void {
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS usuario (
      id_usuario INTEGER PRIMARY KEY AUTOINCREMENT,
      nome TEXT NOT NULL,
      email TEXT NOT NULL UNIQUE,
      senha TEXT NOT NULL,
      telefone TEXT DEFAULT NULL,
      cep TEXT DEFAULT NULL,
      rua TEXT DEFAULT NULL,
      numero TEXT DEFAULT NULL,
      bairro TEXT DEFAULT NULL,
      cidade TEXT DEFAULT NULL,
      estado TEXT DEFAULT NULL,
      tipo_usuario TEXT NOT NULL DEFAULT 'usuario',
      data_cadastro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS especie (
      id_especie INTEGER PRIMARY KEY AUTOINCREMENT,
      nome TEXT NOT NULL UNIQUE
    );

    CREATE TABLE IF NOT EXISTS raca (
      id_raca INTEGER PRIMARY KEY AUTOINCREMENT,
      nome TEXT NOT NULL,
      id_especie INTEGER NOT NULL,
      FOREIGN KEY (id_especie) REFERENCES especie (id_especie) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS pet (
      id_pet INTEGER PRIMARY KEY AUTOINCREMENT,
      id_usuario_tutor INTEGER NOT NULL,
      id_especie INTEGER NOT NULL,
      id_raca INTEGER DEFAULT NULL,
      nome TEXT NOT NULL,
      sexo TEXT NOT NULL,
      porte TEXT NOT NULL DEFAULT 'M',
      idade TEXT NOT NULL,
      personalidades TEXT DEFAULT NULL,
      descricao TEXT DEFAULT NULL,
      vacinado INTEGER NOT NULL DEFAULT 0,
      castrado INTEGER NOT NULL DEFAULT 0,
      vermifugado INTEGER NOT NULL DEFAULT 0,
      necessidades_especiais TEXT DEFAULT NULL,
      foto_url TEXT DEFAULT NULL,
      contato_nome TEXT DEFAULT NULL,
      contato_telefone TEXT DEFAULT NULL,
      contato_email TEXT DEFAULT NULL,
      cidade TEXT NOT NULL,
      estado TEXT NOT NULL,
      status TEXT NOT NULL DEFAULT 'Disponível',
      data_cadastro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (id_usuario_tutor) REFERENCES usuario (id_usuario) ON DELETE CASCADE,
      FOREIGN KEY (id_especie) REFERENCES especie (id_especie),
      FOREIGN KEY (id_raca) REFERENCES raca (id_raca) ON DELETE SET NULL
    );

    CREATE TABLE IF NOT EXISTS solicitacao_adocao (
      id_solicitacao INTEGER PRIMARY KEY AUTOINCREMENT,
      id_pet INTEGER NOT NULL,
      id_usuario_adotante INTEGER NOT NULL,
      mensagem_motivo TEXT NOT NULL,
      telefone_contato TEXT NOT NULL,
      status TEXT NOT NULL DEFAULT 'Pendente',
      resposta_tutor TEXT DEFAULT NULL,
      data_solicitacao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
      data_atualizacao DATETIME DEFAULT NULL,
      FOREIGN KEY (id_pet) REFERENCES pet (id_pet) ON DELETE CASCADE,
      FOREIGN KEY (id_usuario_adotante) REFERENCES usuario (id_usuario) ON DELETE CASCADE
    );

    INSERT INTO especie (id_especie, nome) VALUES
    (1, 'Cachorro'),
    (2, 'Gato'),
    (3, 'Outro');

    INSERT INTO raca (id_raca, nome, id_especie) VALUES
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

    INSERT INTO usuario (id_usuario, nome, email, senha, telefone, cep, rua, numero, bairro, cidade, estado, tipo_usuario) VALUES
    (1, 'Maria Souza', 'maria@exemplo.com', '\$2y\$10\$MU4LaZ0JPkitaScwonlB4O0cxUuheMN2TtMoagf3XPSCHEIo9cmvC', '(11) 98765-4321', '01310-100', 'Avenida Paulista', '1000', 'Bela Vista', 'São Paulo', 'SP', 'usuario'),
    (2, 'Carlos Silva', 'carlos@exemplo.com', '\$2y\$10\$MU4LaZ0JPkitaScwonlB4O0cxUuheMN2TtMoagf3XPSCHEIo9cmvC', '(11) 91234-5678', '04571-010', 'Avenida Engenheiro Luís Carlos Berrini', '500', 'Itaim Bibi', 'São Paulo', 'SP', 'usuario'),
    (3, 'Administrador Petfy', 'admin@petfy.com.br', '\$2y\$10\$MU4LaZ0JPkitaScwonlB4O0cxUuheMN2TtMoagf3XPSCHEIo9cmvC', '(11) 99999-9999', '01001-000', 'Praça da Sé', '1', 'Centro', 'São Paulo', 'SP', 'admin');

    INSERT INTO pet (id_pet, id_usuario_tutor, id_especie, id_raca, nome, sexo, porte, idade, personalidades, descricao, vacinado, castrado, vermifugado, necessidades_especiais, foto_url, contato_nome, contato_telefone, contato_email, cidade, estado, status) VALUES
    (1, 1, 1, 4, 'Thor', 'Macho', 'G', '2 anos', 'Brincalhão, Dócil, Carinhoso', 'O Thor é um Golden super dócil e cheio de energia. Adora correr no parque e se dá muito bem com crianças e outros cães.', 1, 1, 1, NULL, 'https://images.unsplash.com/photo-1552053831-71594a27632d?auto=format&fit=crop&w=600&q=80', 'Maria Souza', '(11) 98765-4321', 'maria@exemplo.com', 'São Paulo', 'SP', 'Disponível'),
    (2, 1, 2, 9, 'Luna', 'Fêmea', 'P', '1 ano', 'Calma, Carinhosa, Tímida', 'A Luna é uma gatinha siamesa tranquila e dengosa. Prefere ambientes sossegados e adora um colo quentinho.', 1, 1, 1, NULL, 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?auto=format&fit=crop&w=600&q=80', 'Maria Souza', '(11) 98765-4321', 'maria@exemplo.com', 'São Paulo', 'SP', 'Disponível'),
    (3, 2, 1, 1, 'Pipoca', 'Fêmea', 'P', '8 meses', 'Brincalhona, Curiosa, Inteligente', 'Pipoca foi resgatada ainda filhote. É muito alegre, esperta e adora brinquedos que apitam.', 1, 1, 1, NULL, 'https://images.unsplash.com/photo-1543466835-00a7907e9de1?auto=format&fit=crop&w=600&q=80', 'Carlos Silva', '(11) 91234-5678', 'carlos@exemplo.com', 'São Paulo', 'SP', 'Disponível'),
    (4, 2, 1, 7, 'Bob', 'Macho', 'G', '3 anos', 'Protetor, Leal, Companheiro', 'Bob é um cão forte, obediente e excelente para quem procura um companheiro fiel para caminhadas e segurança.', 1, 1, 1, NULL, 'https://images.unsplash.com/photo-1589941013453-ec89f33b5455?auto=format&fit=crop&w=600&q=80', 'Carlos Silva', '(11) 91234-5678', 'carlos@exemplo.com', 'Campinas', 'SP', 'Disponível'),
    (5, 1, 2, 8, 'Mimi', 'Fêmea', 'P', '5 meses', 'Curiosa, Brincalhona, Afetuosa', 'Mimi é uma gatinha SRD com pelos tigrados lindíssimos. Muito brincalhona e adora caçar bolinhas de papel.', 1, 0, 1, NULL, 'https://images.unsplash.com/photo-1533738363-b7f9aef128ce?auto=format&fit=crop&w=600&q=80', 'Maria Souza', '(11) 98765-4321', 'maria@exemplo.com', 'São Paulo', 'SP', 'Disponível'),
    (6, 2, 1, 3, 'Mel', 'Fêmea', 'P', '4 anos', 'Dócil, Calma, Amorosa', 'Mel é uma Poodle muito carinhosa e tranquila. Ideal para quem mora em apartamento e procura uma amiga fiel.', 1, 1, 1, NULL, 'https://images.unsplash.com/photo-1583337130417-3346a1be7dee?auto=format&fit=crop&w=600&q=80', 'Carlos Silva', '(11) 91234-5678', 'carlos@exemplo.com', 'Santos', 'SP', 'Disponível');

    INSERT INTO solicitacao_adocao (id_solicitacao, id_pet, id_usuario_adotante, mensagem_motivo, telefone_contato, status, resposta_tutor) VALUES
    (1, 1, 2, 'Tenho uma casa com quintal amplo e já tive cachorros antes. Adoraria dar um lar com muito amor para o Thor!', '(11) 91234-5678', 'Em Análise', 'Olá Carlos! Vamos conversar pelo WhatsApp para combinarmos uma visita!');
    ");
}

function db(): PDO {
    return getDatabaseConnection();
}

function setFlashMessage(string $type, string $message): void {
    $_SESSION['flash'] = [
        'type' => $type, // success, error, warning, info
        'message' => $message
    ];
}

function getFlashMessage(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Retorna URL segura para foto do pet, garantindo prefixo do diretório e fallback offline
 */
function getPetPhotoUrl(?string $fotoUrl, string $rootPath = '', string $especie = 'Cachorro'): string {
    if (empty($fotoUrl)) {
        return strtolower($especie) === 'gato' 
            ? $rootPath . 'img/default-cat.svg' 
            : $rootPath . 'img/default-dog.svg';
    }

    // Se começar com uploads/
    if (str_starts_with($fotoUrl, 'uploads/')) {
        return $rootPath . $fotoUrl;
    }

    return $fotoUrl;
}
