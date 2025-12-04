<?php
session_start();
require "logica.php";
require 'conexao.php';

// Recupera os dados do formulário
$nome = filter_input(INPUT_POST, "nome", FILTER_SANITIZE_SPECIAL_CHARS);
$email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
$senha = filter_input(INPUT_POST, "senha");

if(!$email){
 // Recupera o ID do usuário da URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id) {
    // Busca o usuário no banco de dados pelo ID
    $sql = "SELECT nome, email, senha FROM funcionario WHERE id_funcionario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if ($row) {
        // Se o usuário foi encontrado, valida a senha
        // Aqui você deveria pedir a senha para o usuário (pode ser via um formulário)
        // Para fins de demonstração, vamos assumir que a senha correta foi passada
        // no código que você precisa corrigir (por exemplo, via POST).
        $id = filter_input(INPUT_GET, 'id'); // Aqui o usuário deve inserir a senha em um formulário.

        if ($id) {
            // Se a senha for válida, armazena os dados na sessão
            $_SESSION["email"] = $row['email'];
            $_SESSION["nome"] = $row['nome'];
            $_SESSION["id_funcionario"] = $row['id_funcionario'];

            // Redireciona para a página "inicio.php"
            header("Location: inicio.php");
            exit;
        } else {
            // Se a senha for inválida
            echo '<div class="alert alert-danger" role="alert">Senha incorreta!</div>';
        }
    } else {
        // Caso o usuário não seja encontrado
        echo '<div class="alert alert-danger" role="alert">Usuário não encontrado!</div>';
    }
} else {
    // Caso o ID não seja fornecido
    echo '<div class="alert alert-danger" role="alert">ID inválido!</div>';
}
}else{

// Verifica se o usuário existe no banco
$sql = "SELECT nome, email, senha FROM funcionario WHERE email= ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$email]);

$row = $stmt->fetch();

if ($row && password_verify($senha, $row['senha'])) {
    // Se a senha for válida, armazena os dados na sessão
    $_SESSION["email"] = $email;
    $_SESSION["nome"] = $row['nome'];

    // Redireciona para a página "inicio.php"
    header("Location: inicio.php");
    exit; // Não se esqueça de usar o exit após o header para garantir que o código não continue executando.
} else {
    // Se a senha não for válida ou o usuário não for encontrado
    unset($_SESSION["email"]);
    unset($_SESSION["nome"]);
    echo '<div class="alert alert-danger" role="alert">Erro ao realizar o login!</div>';
}
}


?>
