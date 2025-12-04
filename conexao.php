<?php


// Configurações de sessão segura
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', 1);

// Carrega configurações
$conf = parse_ini_file("config.ini");

if (!$conf) {
    die("Erro: arquivo config.ini não encontrado ou inválido.");
}

$string_connection = $conf["driver"] .
    ":dbname=" . $conf["database"] .
    ";host=" . $conf["server"] .
    ";port=" . $conf["port"] .
    ";charset=utf8mb4";

try {
    $conn = new PDO(
        $string_connection,
        $conf["user"],
        $conf["password"],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]
    );
    
    if ($conf["debug"] === "true") {
        echo "<div style='background:#d4edda;padding:10px;border:1px solid #c3e6cb;border-radius:5px;margin:10px;'>";
        echo "<strong>✓ Sucesso!</strong> Conectado ao banco <b>" . htmlspecialchars($conf["database"]) . "</b>";
        echo "</div>";
    }
    
} catch (PDOException $e) {
    // Em produção, não exiba detalhes do erro
    if ($conf["debug"] === "true") {
        die("<div style='background:#f8d7da;padding:10px;border:1px solid #f5c6cb;border-radius:5px;margin:10px;'>"
            . "<strong>✗ Erro de conexão:</strong> " . htmlspecialchars($e->getMessage())
            . "</div>");
    } else {
        die("Erro ao conectar ao banco de dados. Contate o suporte.");
    }
}

/**
 * Função para registrar logs de auditoria
 */
function registrarLog($conn, $id_funcionario, $acao, $descricao = '') {
    try {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $stmt = $conn->prepare(
            "INSERT INTO logs_auditoria (id_funcionario, acao, descricao, ip_origem) 
             VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$id_funcionario, $acao, $descricao, $ip]);
    } catch (Exception $e) {
        error_log("Erro ao registrar log: " . $e->getMessage());
    }
}

/**
 * Função para verificar rate limit de login
 */
function verificarRateLimit($conn, $email, $limite = 5, $janela_minutos = 15) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    
    $stmt = $conn->prepare(
        "SELECT COUNT(*) as tentativas 
         FROM tentativas_login 
         WHERE email = ? 
         AND ip_origem = ? 
         AND sucesso = 0 
         AND data_hora > DATE_SUB(NOW(), INTERVAL ? MINUTE)"
    );
    $stmt->execute([$email, $ip, $janela_minutos]);
    $resultado = $stmt->fetch();
    
    return $resultado['tentativas'] >= $limite;
}

/**
 * Função para registrar tentativa de login
 */
function registrarTentativaLogin($conn, $email, $sucesso) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    
    $stmt = $conn->prepare(
        "INSERT INTO tentativas_login (email, ip_origem, sucesso) 
         VALUES (?, ?, ?)"
    );
    $stmt->execute([$email, $ip, $sucesso ? 1 : 0]);
}