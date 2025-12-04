<?php


/**
 * Gera um token CSRF único para a sessão
 */
function gerarTokenCSRF() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Valida o token CSRF enviado
 */
function validarTokenCSRF($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Retorna HTML do input hidden com token CSRF
 */
function campoTokenCSRF() {
    $token = gerarTokenCSRF();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

/**
 * Middleware para validar CSRF em requisições POST
 */
function verificarCSRF() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        
        if (!validarTokenCSRF($token)) {
            http_response_code(403);
            die(json_encode([
                'status' => 'erro',
                'mensagem' => 'Token de segurança inválido. Recarregue a página e tente novamente.'
            ]));
        }
    }
}