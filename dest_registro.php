<?php 
session_start();
require_once 'conexao.php';
require_once 'csrf_token.php';

header('Content-Type: application/json; charset=UTF-8');

// Verifica CSRF
verificarCSRF();

// Captura e sanitiza entradas
$nome = filter_input(INPUT_POST, "nome", FILTER_SANITIZE_SPECIAL_CHARS);
$telefone = filter_input(INPUT_POST, "telefone", FILTER_SANITIZE_SPECIAL_CHARS);
$cpf = preg_replace('/\D/', '', filter_input(INPUT_POST, "cpf", FILTER_SANITIZE_SPECIAL_CHARS));
$email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
$senha = $_POST["senha"] ?? '';

// Validações
if (empty($nome) || empty($telefone) || empty($cpf) || empty($email) || empty($senha)) {
    echo json_encode(["status" => "erro", "mensagem" => "Todos os campos são obrigatórios."]);
    exit;
}

// Valida CPF
if (!validarCPF($cpf)) {
    echo json_encode(["status" => "erro", "mensagem" => "CPF inválido."]);
    exit;
}

// Valida email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "erro", "mensagem" => "E-mail inválido."]);
    exit;
}

// Valida senha (mínimo 8 caracteres)
if (strlen($senha) < 8) {
    echo json_encode(["status" => "erro", "mensagem" => "A senha deve ter no mínimo 8 caracteres."]);
    exit;
}

// Hash da senha
$senha_hash = password_hash($senha, PASSWORD_BCRYPT, ['cost' => 12]);

try {
    // Verifica se CPF ou e-mail já existem
    $stmtCheck = $conn->prepare(
        "SELECT cpf, email FROM funcionario WHERE cpf = ? OR email = ?"
    );
    $stmtCheck->execute([$cpf, $email]);
    $existente = $stmtCheck->fetch();

    if ($existente) {
        if ($existente['cpf'] === $cpf) {
            echo json_encode(["status" => "erro", "mensagem" => "CPF já cadastrado."]);
        } else {
            echo json_encode(["status" => "erro", "mensagem" => "E-mail já cadastrado."]);
        }
        exit;
    }

    // Insere novo funcionário
    $sql = "INSERT INTO funcionario (nome, telefone, cpf, email, senha) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$nome, $telefone, $cpf, $email, $senha_hash]);

    $idInserido = $conn->lastInsertId();

    // Registra log
    registrarLog($conn, $idInserido, 'cadastro', "Novo funcionário cadastrado");

    echo json_encode([
        "status" => "sucesso", 
        "mensagem" => "Cadastro realizado com sucesso! Você já pode fazer login."
    ]);

} catch (PDOException $e) {
    error_log("Erro ao cadastrar: " . $e->getMessage());
    
    // Verifica se é erro de duplicação
    if ($e->getCode() == 23000) {
        echo json_encode(["status" => "erro", "mensagem" => "CPF ou e-mail já cadastrado."]);
    } else {
        echo json_encode(["status" => "erro", "mensagem" => "Erro ao cadastrar. Tente novamente."]);
    }
}

/**
 * Valida CPF
 */
function validarCPF($cpf) {
    $cpf = preg_replace('/\D/', '', $cpf);
    
    if (strlen($cpf) != 11) return false;
    
    // Verifica se todos os dígitos são iguais
    if (preg_match('/(\d)\1{10}/', $cpf)) return false;
    
    // Calcula primeiro dígito verificador
    $soma = 0;
    for ($i = 0; $i < 9; $i++) {
        $soma += $cpf[$i] * (10 - $i);
    }
    $resto = $soma % 11;
    $digito1 = $resto < 2 ? 0 : 11 - $resto;
    
    if ($cpf[9] != $digito1) return false;
    
    // Calcula segundo dígito verificador
    $soma = 0;
    for ($i = 0; $i < 10; $i++) {
        $soma += $cpf[$i] * (11 - $i);
    }
    $resto = $soma % 11;
    $digito2 = $resto < 2 ? 0 : 11 - $resto;
    
    return $cpf[10] == $digito2;
}