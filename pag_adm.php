<?php
require 'conexao.php'; // Conexão com o banco de dados
require 'logica.php';
require 'calendario.php';

// Consulta para pegar todos os funcionários
$sql = "SELECT id_funcionario, nome, telefone, cpf, email FROM funcionario ORDER BY nome";
$stmt = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Ponto Eletrônico</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .time-card {
            transition: all 0.3s ease;
        }
        .time-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        .history-item:nth-child(odd) {
            background-color: #f8fafc;
        }
        .pulse {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(59, 130, 246, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(59, 130, 246, 0);
            }
        }
    </style>
</head>
<body class="bg-gray-50">
     <?php 
     $sql = "SELECT id_funcionario, nome, telefone, cpf, email, senha FROM funcionario ORDER BY nome";
     $stmt = $conn->query($sql);
     
     ?>
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-blue-600 text-white shadow-lg">
            <div class="container mx-auto px-4 py-4 flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-clock text-2xl"></i>
                    <h1 class="text-2xl font-bold">Ponto Eletrônico</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                       <a href="registro.php"
   class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg shadow transition">
    Cadastrar
</a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Conteúdo principal -->
        <main class="flex-grow container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Coluna da esquerda - Cartões de ponto -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Cartão de boas-vindas -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <h2 class="text-xl font-bold text-gray-800">Bem-vindo</h2>
                                <p class="text-gray-600">Hoje é <span id="dia" class="font-medium">-</span></p>
                            </div>
                            <div class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                <i class="fas fa-circle-notch mr-1"></i> Ativo
                            </div>
                        </div>
                    </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Coluna da esquerda - Listagem de funcionários -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Cartão de listagem de funcionários -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-800">Usuários Cadastrados</h2>
                                <?php
                                // Exibindo os funcionários
                                while($row = $stmt->fetch()){
                                   ?>
                                   <div class="time-card bg-white rounded-xl shadow-md p-6 text-center border-l-4 border-blue-500">
                            <div class="text-gray-500 mb-2">
                                <i class="fas fa-user-circle text-2xl"></i>
                                <p class="text-lg font-medium mt-2"><?= $row['nome']?> - <?= $row['telefone']?> - <?= $row['email']?></p>
                                <div class="flex justify-center gap-3 mt-4">
                                <a href="dest_login.php?id=<?= urlencode($row['id_funcionario']) ?>"
       class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition">
        Acessar
    </a>
                              <a href="form-alterar-usuario.php?id_funcionario=<?=$row["id_funcionario"]?>"
       class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg shadow transition flex items-center gap-2">
        <i class="fas fa-edit"></i> Editar
    </a>
</a>
<a href="excluir-usuario.php?id_funcionario=<?=$row["id_funcionario"]?>"
       onclick="return confirm('Tem certeza que deseja excluir?')"
       class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg shadow transition flex items-center gap-2">
        <i class="fas fa-trash-alt"></i> Excluir
    </a>

    

</div>

                            </div>
                            <p class="trab text-3xl font-bold text-gray-800"></p>
                            <p class="text-sm text-gray-500 mt-2"></p>
                        </div>
                        <?php
                                }
                                ?>
                        
                    </div>
                </div>
            </div>
            <!-- Sair -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        <div class="px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-gray-100 text-gray-800 p-3 rounded-full">
                                        <i class="fas fa-user-circle text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium"></p>
                                        <p class="text-sm text-gray-500"></p>
                                    </div>
                                </div>
                                <button id="logoutBtn" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span>Sair</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-gray-100 border-t border-gray-200 py-4">
            <div class="container mx-auto px-4 text-center text-gray-600 text-sm">
                <p>Sistema de Ponto Eletrônico v2.0 &copy; 2023 - Todos os direitos reservados</p>
            </div>
        </footer>
    </div>

    <script>
    
function atualizarDataHora() {
  const hoje = new Date();

  // 🔹 Arrays de dias e meses por extenso
  const diasSemana = [
    'Domingo', 'Segunda-feira', 'Terça-feira',
    'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'
  ];

  const meses = [
    'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
    'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
  ];

  // 🔹 Pega data e hora atuais
  const diaSemana = diasSemana[hoje.getDay()];
  const dia = hoje.getDate().toString().padStart(2, '0');
  const mes = meses[hoje.getMonth()];
  const ano = hoje.getFullYear();

  const horas = hoje.getHours();
  const minutos = hoje.getMinutes();
  const segundos = hoje.getSeconds();

  const horarioCompleto = `${horas.toString().padStart(2, '0')}:${minutos
    .toString()
    .padStart(2, '0')}:${segundos.toString().padStart(2, '0')}`;

  const dataFormatada = `${diaSemana}, ${dia} de ${mes} de ${ano}`;

  // 🔹 Atualiza o texto na tela
  document.getElementById('dia').textContent = `${dataFormatada} — ${horarioCompleto}`;
}
// Atualiza automaticamente
setInterval(atualizarDataHora, 1000);
atualizarDataHora();


    </script>
</body>
</html>
