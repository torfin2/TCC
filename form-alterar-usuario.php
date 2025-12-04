<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuário</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .form-input {
            transition: all 0.3s ease;
        }
        .form-input:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }
        .cpf-mask, .phone-mask {
            letter-spacing: 1px;
        }
        #message {
            display: none;
        }
    </style>
</head>
<?php 
session_start();
require "logica.php";


$titu="Formulário de alteração";

$id_funcionario = filter_input(INPUT_GET, "id_funcionario", FILTER_SANITIZE_NUMBER_INT);

if(empty($id_funcionario)){
    ?>
    <div class="alert alert-danger" role="alert">
    <h4>Falha ao abrir formulário para edição.</h4>
    <p> ID do produto está vazio.</p>
    </div>
    <?php
    exit;
}

require 'conexao.php';

$sql = "SELECT `nome`, `telefone`, `email` FROM `funcionario` WHERE id_funcionario = ?";

$stmt = $conn ->prepare($sql);
$result = $stmt->execute([$id_funcionario]);

$rowUsuarios = $stmt->fetch();


?>

<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Registro Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-blue-600 py-6 px-8 text-center">
                <div class="flex justify-center mb-4">
                    <div class="bg-white p-3 rounded-full">
                        <i class="fas fa-user-plus text-blue-600 text-2xl"></i>
                    </div>
                </div>
                <h1 class="text-2xl font-bold text-white">Crie sua conta</h1>
                <p class="text-blue-100 mt-1">Preencha os campos abaixo para se registrar</p>
            </div>
            
            <!-- Mensagem -->
            <div id="message" class="bg-red-100 text-red-700 p-3 text-center text-sm"></div>

            <!-- Form -->
            <form class="p-8" id="registrationForm" action="alterar-usuarios.php" method="POST">
                <!-- Nome -->
                 <input type="hidden" name="id_funcionario" id="id_funcionario" value="<?=$id_funcionario?>">
                <div class="mb-6">
                    <label for="nome" class="block text-gray-700 font-medium mb-2">Nome Completo</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <input type="text" id="nome" name="nome" value="<?=$rowUsuarios['nome']?>" class="form-input w-full pl-10 pr-3 py-3 rounded-lg border border-gray-300 focus:border-blue-500" placeholder="Digite seu nome completo" required>
                    </div>
                </div>
                
                <!-- Telefone -->
                <div class="mb-6">
                    <label for="telefone" class="block text-gray-700 font-medium mb-2">Telefone</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-phone text-gray-400"></i>
                        </div>
                        <input type="text" id="telefone" name="telefone" value="<?=$rowUsuarios['telefone']?>" class="form-input phone-mask w-full pl-10 pr-3 py-3 rounded-lg border border-gray-300 focus:border-blue-500" placeholder="(00) 00000-0000" maxlength="15" required>
                    </div>
                </div>
                
                <!-- Email -->
                <div class="mb-6">
                    <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" id="email" name="email" value="<?=$rowUsuarios['email']?>" class="form-input w-full pl-10 pr-3 py-3 rounded-lg border border-gray-300 focus:border-blue-500" placeholder="exemplo@dominio.com" required>
                    </div>
                </div>

                

                
                <!-- Botão -->
                <button id="submitBtn" type="submit" class="w-40 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                    <i class="fas fa-user-plus mr-2"></i> Gravar
                </button>
                <button id="submitBtn" type="reset" class="w-40 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                    <i class="fas fa-user-plus mr-2"></i> Cancelar
                </button>
                <!-- Login -->
                
            </form>
        </div>
    </div>
        


            </form>
