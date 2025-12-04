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
    <?php
    require 'logica.php';
    require 'calendario.php';
    ?>
</head>
<body class="bg-gray-50">
    <?php
session_start();

?>

<?php
// Recuperar email e senha passados via URL
$email = filter_input(INPUT_GET, 'email', FILTER_SANITIZE_EMAIL);
$senha = filter_input(INPUT_GET, 'senha', FILTER_SANITIZE_STRING);


// Aqui você pode validar essas informações para garantir que o usuário está autorizado
// Isso pode ser feito verificando no banco de dados ou com algum outro sistema de autenticação.

require 'conexao.php';
$sql = "SELECT * FROM funcionario WHERE email = ? AND senha = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$email, $senha]);
$user = $stmt->fetch();
?>
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-blue-600 text-white shadow-lg">
            <div class="container mx-auto px-4 py-4 flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-clock text-2xl"></i>
                    <h1 class="text-2xl font-bold">ClockIn</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="font-medium"></p>
                        <p class="text-sm text-blue-100"></p>
                    </div>
                    <div class="flex items-center gap-2 bg-white/20 px-4 py-2 rounded-lg shadow">
    <i class="fas fa-user text-white"></i>
    <span class="font-semibold text-white"><?= $_SESSION["nome"] ?></span>
</div>
                </div>
            </div>
        </header>
       


        <!--Conteúdo principal -->
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

                    <!-- Cartões de controle de tempo -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Relógio no cartão -->
                        <div class="time-card bg-white rounded-xl shadow-md p-6 text-center border-l-4 border-green-500">
                            <div class="text-gray-500 mb-2">
                                <i class="fas fa-sign-in-alt text-2xl"></i>
                                <h3 class="tipo1 text-lg font-medium mt-2">Entrada</h3>
                            </div>
                            <p class="text-3xl font-bold text-gray-800">--:--</p>
                            <p class="text-sm text-gray-500 mt-2">Aguardando registro</p>
                        </div>

                        <!-- cartão de saída de relógio -->
                        <div class="time-card bg-white rounded-xl shadow-md p-6 text-center border-l-4 border-red-500">
                            <div class="text-gray-500 mb-2">
                                <i class="fas fa-sign-out-alt text-2xl"></i>
                                <h3 class="tipo2 text-lg font-medium mt-2">Saída</h3>
                            </div>
                            <p class="text-3xl font-bold text-gray-800">--:--</p>
                            <p class="text-sm text-gray-500 mt-2">Aguardando registro</p>
                        </div>

                        <!-- Cartão de Horas Trabalhadas -->
                        <div class="time-card bg-white rounded-xl shadow-md p-6 text-center border-l-4 border-blue-500">
                            <div class="text-gray-500 mb-2">
                                <i class="fas fa-business-time text-2xl"></i>
                                <h3 class="text-lg font-medium mt-2">Horas Trabalhadas</h3>
                            </div>
                            <p class="trab text-3xl font-bold text-gray-800"></p>
                            <p class="text-sm text-gray-500 mt-2"></p>
                        </div>

                        <!-- Cartão de Resumo Mensal -->
                        <div class="time-card bg-white rounded-xl shadow-md p-6 text-center border-l-4 border-purple-500">
                            <div class="text-gray-500 mb-2">
                                <i class="fas fa-calendar-alt text-2xl"></i>
                                <h3 class="text-lg font-medium mt-2">Resumo Mensal</h3>
                            </div>
                            <p class="text-3xl font-bold text-gray-800"></p>
                            <button id="ResumoBtn"><p class="text-sm text-gray-500 mt-2">Aqui</p></button>
                        </div>
                    </div>

                    <!-- Botão de entrada/saída -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                       
                        <button id="clockBtn" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-4 px-6 rounded-lg flex items-center justify-center space-x-3 transition-colors text-lg">
                            <i class="fas fa-fingerprint text-xl"></i>
                            <span>Bater Ponto</span>
                        </button>
                    </div>
                </div>

                <!-- Coluna Direita - History -->
                <div class="space-y-6">
                    <!-- Historico recente  -->
                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        <div class="bg-blue-600 text-white px-6 py-4">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-bold">Histórico Recente</h3>
                                <button class="text-blue-100 hover:text-white">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                        </div>
                        <div class="divide-y divide-gray-200">
                            <!-- itens do historico -->
                        </div>
                         <button id="btnHist" class="">
                        <div class="px-6 py-3 bg-gray-50 text-center">
                            <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Ver histórico completo</a>
                        </div>
                    </button>
                    </div>

                    <!-- Notficação -->
                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        <div class="bg-blue-600 text-white px-6 py-4">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-bold">Notificações</h3>
                            </div>
                        </div>
                        <div class="divide-y divide-gray-200">
                            <div id="not" class="px-6 py-4">
                                <!-- <div class="flex items-start space-x-3">
                                    <div class="bg-blue-100 text-blue-800 p-2 rounded-full">
                                        <i class="fas fa-info-circle"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium">Aprovação Pendente</p>
                                        <p class="text-sm text-gray-500">Seu ponto de ontem precisa de ajuste</p>
                                    </div>
                                </div>
                            </div>
                            <div class="px-6 py-4">
                                <div class="flex items-start space-x-3">
                                    <div class="bg-green-100 text-green-800 p-2 rounded-full">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium">Ponto Aprovado</p>
                                        <p class="text-sm text-gray-500">Seu ponto da semana passada foi aprovado</p>
                                    </div> -->
                                </div>
                            </div>
                            <!-- <?php 
for ($i = 0; $i < count($feriados); $i++) {
    $feriado = $feriados[$i]; // pega o feriado correspondente
?>
    <div class="px-6 py-4">
        <div class="flex items-start space-x-3">
            <div class="bg-yellow-100 text-yellow-800 p-2 rounded-full">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div>
                <p class="font-medium">Feriado Próximo</p>
                <p class="text-sm text-gray-500">
                    Dia <?= date("d/m", strtotime($feriado['date'])) ?> - <?= $feriado['name'] ?>
                </p>
            </div>
        </div>
    </div>
<?php 
} 
?> -->

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
                                <?php 
                                if(adm()){
                                ?>
                                <a href="pag_adm.php"><button id="logoutBtn" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span>Voltar</span>
                                </button></a>
                                <?php
                                }else{
                                ?>
                                <button id="logoutBtn" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span>Sair</span>
                                </button>
                                <?php
                                 }
                                ?>
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

    <!--  Modal de saida -->
    <div id="logoutModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <i class="fas fa-exclamation text-red-600"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-3">Confirmar Saída</h3>
                <div class="mt-2">
                    <p class="text-sm text-gray-500">Tem certeza que deseja sair do sistema?</p>
                </div>
                <div class="mt-4 flex justify-center space-x-4">
                    <button id="cancelLogout" type="button" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-gray-800">
                        Cancelar
                    </button>
                    <a href="pag2.php"><button id="confirmLogout" type="button" class="px-4 py-2 bg-red-500 hover:bg-red-600 rounded-lg text-white">
                        Sair
                    </button></a>
                </div>
            </div>
        </div>
    </div>

    <div>
        <div id="opcao" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <i class="fas fa-exclamation text-red-600"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-3">escolha uma opção:</h3>
                <div class="mt-2">
                    <p class="text-sm text-gray-500">Tem certeza que deseja sair do sistema?</p>
                </div>
             <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <button id="entrada" type="button" value="Entrada">
     <div class="time-card bg-white rounded-xl shadow-md p-6 text-center border-l-4 border-green-500">
                            <div class="text-gray-500 mb-2">
                                <i class="fas fa-sign-in-alt text-2xl"></i>
                                <h3 class="text-lg font-medium mt-2">Entrada</h3>
</div></div></button>
                        <!-- cartão de saída de relógio -->
                         <button id="pausa" type="button">
                        <div class="time-card bg-white rounded-xl shadow-md p-6 text-center border-l-4 border-red-500">
                            <div class="text-gray-500 mb-2">
                                <i class="fas fa-sign-out-alt text-2xl"></i>
                                <h3 class="text-lg font-medium mt-2">Intervalo</h3>
</div></div></button>
<button id="volta" type="button">
                        <div class="time-card bg-white rounded-xl shadow-md p-6 text-center border-l-4 border-blue-500">
                            <div class="text-gray-500 mb-2">
                                <i class="fas fa-business-time text-2xl"></i>
                                <h3 class="text-lg font-medium mt-2">Retorno</h3>
                            </div>
      
                        </div></button>

                        <!-- Cartão de Resumo Mensal -->
                         <button id="saida" type="button">
                        <div class="time-card bg-white rounded-xl shadow-md p-6 text-center border-l-4 border-purple-500">
                            <div class="text-gray-500 mb-2">
                                <i class="fas fa-calendar-alt text-2xl"></i>
                                <h3 class="text-lg font-medium mt-2">Saída</h3>
                            </div>
                        </div></button>
                    </div>
                    <button id="cancel" type="button" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-gray-800">
                        Cancelar
                    </button>
</div>

            </div>
        </div>
    </div>


    </div>

    <!-- Resumo Mensal -->
   <div>
  <!-- 🔹 Modal principal -->
  <div id="resumoMes" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-3xl overflow-y-auto max-h-[90vh]">

      <!-- Cabeçalho -->
      <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Resumo Mensal</h2>
        <p class="text-gray-500 text-sm">Escolha um mês para visualizar o resumo</p>
      </div>

      <!-- 🔸 Grade de meses -->
      <div id="mesesGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
        <!-- Gerado dinamicamente pelo JS -->
      </div>

      <!-- 🔸 Container do resumo (inicialmente oculto) -->
      <div id="resumoContainer" class="hidden mt-6">
        <h3 id="tituloResumo" class="text-xl font-bold text-gray-800 text-center mb-4"></h3>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 text-center">
          <div class="bg-green-50 p-4 rounded-xl border-l-4 border-green-500">
            <p class="text-gray-600 text-sm">Horas Previstas</p>
            <h3 id="horasPrevistas" class="text-xl font-bold text-gray-800">--</h3>
          </div>
          <div class="bg-blue-50 p-4 rounded-xl border-l-4 border-blue-500">
            <p class="text-gray-600 text-sm">Horas Trabalhadas</p>
            <h3 id="horasTrabalhadas" class="text-xl font-bold text-gray-800">--</h3>
          </div>
          <div class="bg-yellow-50 p-4 rounded-xl border-l-4 border-yellow-500">
            <p class="text-gray-600 text-sm">Horas Extras</p>
            <h3 id="horasExtras" class="text-xl font-bold text-gray-800">--</h3>
          </div>
          <div class="bg-red-50 p-4 rounded-xl border-l-4 border-red-500">
            <p class="text-gray-600 text-sm">Faltas</p>
            <h3 id="faltas" class="text-xl font-bold text-gray-800">--</h3>
          </div>
        </div>

      <!-- Tabela diária -->
      <div class="overflow-x-auto mb-6">
        <table id="tabelaDias" class="min-w-full border border-gray-200 text-sm text-gray-700">
          <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
            <tr>
              <th class="px-3 py-2 border">Data</th>
              <th class="px-3 py-2 border">Entrada</th>
              <th class="px-3 py-2 border">Saída</th>
              <th class="px-3 py-2 border">Intervalo</th>
              <th class="px-3 py-2 border">Total</th>
              <th class="px-3 py-2 border">Ocorrência</th>
            </tr>
          </thead>
          <tbody>
            <!-- <tr>
              <td id="diadomes" class="px-3 py-2 border text-center"></td>
              <td class="px-3 py-2 border text-center">08:00</td>
              <td class="px-3 py-2 border text-center">17:00</td>
              <td class="px-3 py-2 border text-center">1h</td>
              <td class="px-3 py-2 border text-center">8h</td>
              <td class="px-3 py-2 border text-center text-green-600">OK</td>
            </tr>
            <tr>
              <td class="px-3 py-2 border text-center">02/11</td>
              <td class="px-3 py-2 border text-center">–</td>
              <td class="px-3 py-2 border text-center">–</td>
              <td class="px-3 py-2 border text-center">–</td>
              <td class="px-3 py-2 border text-center">0h</td>
              <td class="px-3 py-2 border text-center text-blue-600">Feriado</td>
            </tr>
            <tr>
              <td class="px-3 py-2 border text-center">03/11</td>
              <td class="px-3 py-2 border text-center">08:15</td>
              <td class="px-3 py-2 border text-center">17:00</td>
              <td class="px-3 py-2 border text-center">1h</td>
              <td class="px-3 py-2 border text-center">7h45</td>
              <td class="px-3 py-2 border text-center text-yellow-600">Atraso</td>
            </tr> -->
          </tbody>
        </table>
      </div>
        <div class="bg-gray-50 p-4 rounded-xl mb-6">
          <h4 class="font-semibold text-gray-800 mb-2">Observações</h4>
          <p id="observacoes" class="text-sm text-gray-600">Selecione um mês para visualizar os detalhes.</p>
        </div>

        <!-- Botão para voltar aos meses -->
        <div class="text-center">
          <button id="voltarMeses" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i> Voltar
          </button>
        </div>
      </div>

      <!-- Rodapé -->
      <div class="text-center mt-8">
        <button id="cancelResumo" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-gray-800">
          Fechar
        </button>
      </div>
    </div>
  </div>
</div>





    <div>
<div id="historico" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <i class="fas fa-exclamation text-red-600"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-3">escolha uma opção:</h3>

                <!-- Historico recente  -->
                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        <div class="bg-blue-600 text-white px-6 py-4">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-bold">Histórico Completo</h3>
                                <button class="text-blue-100 hover:text-white">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                        </div>
                        <div id="CompHist" class="divide-y divide-gray-200 max-h-80 overflow-y-auto">
                            <!-- itens do historico -->
                        </div>
                    </div>

                <div class="mt-2">
                    <p class="text-sm text-gray-500">Tem certeza que deseja sair do sistema?</p>
                </div>
             
                    <button id="cancel1" type="button" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-gray-800">
                        Cancelar
                    </button>
</div>

    </div>

    <script>
        // saida 
        document.getElementById('logoutBtn').addEventListener('click', function() {
            document.getElementById('logoutModal').classList.remove('hidden');
        });

        document.getElementById('cancelLogout').addEventListener('click', function() {
            document.getElementById('logoutModal').classList.add('hidden');
        });

        document.getElementById('confirmLogout').addEventListener('click', function() {
            // Here you would typically redirect to logout endpoint
            alert('Você foi desconectado com sucesso!');
            // window.location.href = '/logout';
            document.getElementById('logoutModal').classList.add('hidden');
           
        });
        
// Horas previstas por mes

        function calcularHorasPrevistasPorMes(ano) {
  const horasPorDia = 8;
  const meses = [
    "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
    "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"
  ];

  const resultado = {};

  for (let mes = 0; mes < 12; mes++) {
    let diasUteis = 0;

    // Número de dias no mês
    const diasNoMes = new Date(ano, mes + 1, 0).getDate();

    for (let dia = 1; dia <= diasNoMes; dia++) {
      const data = new Date(ano, mes, dia);
      const diaSemana = data.getDay(); // 0 = domingo, 6 = sábado

      // Conta apenas de segunda (1) a sexta (5)
      if (diaSemana >= 1 && diaSemana <= 5) {
        diasUteis++;
      }
    }

    const horasMes = diasUteis * horasPorDia;
    resultado[meses[mes]] = {
      diasUteis,
      horasPrevistas: horasMes
    };
  }

  return resultado;
}

// Exemplo de uso:
const horas2025 = calcularHorasPrevistasPorMes(2025);
console.table(horas2025);


//historico
        function adicionarHistorico(tipo, hora, cor = "text-gray-800") {
            const historicoContainer = document.querySelector('.divide-y.divide-gray-200');
   
    
    const novoItem = document.createElement('div');
    novoItem.classList.add('history-item', 'px-6', 'py-4');
    novoItem.innerHTML = `
        <div class="flex justify-between">
            <div>
                <p class="font-medium">${tipo}</p>
                <p class="text-sm text-gray-500">${new Date().toLocaleDateString('pt-BR')}</p>
            </div>
            <div class="text-right">
                <p class="font-medium ${cor}">${hora}</p>
                <p class="text-sm text-gray-500">Registrado</p>
            </div>/
        </div>
    `;
      // adiciona o novo item no topo (mais recente primeiro)
    historicoContainer.prepend(novoItem);

    // Mantém no máximo 5 históricos
    const itens = historicoContainer.querySelectorAll('.history-item');
    if (itens.length > 5) {
        itens[itens.length - 1].remove(); // remove o mais antigo (último)
    }
}

//historico-completo
function adicionarHistoricoCompleto(tipo, hora, cor = "text-gray-800") {
     const historicoContainer = document.getElementById('CompHist');
    
    const novoItem = document.createElement('div');
    novoItem.classList.add('history-item', 'px-6', 'py-4');
    novoItem.innerHTML = `
        <div class="flex justify-between">
            <div>
                <p class="font-medium">${tipo}</p>
                <p class="text-sm text-gray-500">${new Date().toLocaleDateString('pt-BR')}</p>
            </div>
            <div class="text-right">
                <p class="font-medium ${cor}">${hora}</p>
                <p class="text-sm text-gray-500">Registrado</p>
            </div>
        </div>
    `;
      // adiciona o novo item no topo (mais recente primeiro)
    historicoContainer.prepend(novoItem);

}


//opções
// const hoje = new Date();
// const dia = hoje.getDate().toString().padStart(2, '0');      // dia do mês (01–31)
// const mes = (hoje.getMonth() + 1).toString().padStart(2, '0'); // mês (0–11 → +1)
// const ano = hoje.getFullYear();                               // ano (ex: 2025)

// const dataFormatada = `${dia}/${mes}/${ano}`;
// console.log(dataFormatada); // exemplo → "27/10/2025"





// window.addEventListener('load', () => {
//   verificarDiasRestantes();
// });

// function verificarDiasRestantes() {
//   const hoje = new Date();
//   const dataAlvo = new Date('2025-11-03');
//   hoje.setHours(0, 0, 0, 0);
//   dataAlvo.setHours(0, 0, 0, 0);
//   const diffMs = dataAlvo - hoje;
//   const diffDias = Math.ceil(diffMs / (1000 * 60 * 60 * 24));

//   if (diffDias < 7.3 && diffDias > 6) {
//     showToast("⏰ Falta exatamente 1 semana!");
//   }
// }

let clockedIn = false;
let resetFeitoHoje = false;

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

  // 🔹 Detecta virada de dia (meia-noite exata)
  if (horas === 0 && minutos === 0 && segundos === 0 && !resetFeitoHoje) {
    clockedIn = false;
    resetFeitoHoje = true;

    // Limpa textos de horário
    document.querySelector('.border-l-4.border-green-500 .text-3xl').textContent = '--:--';
    document.querySelector('.border-l-4.border-red-500 .text-3xl').textContent = '--:--';
    document.querySelector('.border-l-4.border-green-500 .text-sm').textContent = 'Aguardando registro';
    document.querySelector('.border-l-4.border-red-500 .text-sm').textContent = 'Aguardando registro';

  

    showToast('🌅 Novo dia! Sistema de ponto resetado.', 'bg-blue-500', 'fa-sun');
    
  }

  // 🔹 Libera o reset quando passa da meia-noite
  if (horas > 0 && resetFeitoHoje) {
    resetFeitoHoje = false;
  }
}

// Atualiza automaticamente
setInterval(atualizarDataHora, 1000);
atualizarDataHora();


let clockedOut = true;

let clockedBreak = true;
        const clockBtn = document.getElementById('clockBtn');
        const clockInTime = document.querySelector('.border-l-4.border-green-500 .text-3xl');
        const clockOutTime = document.querySelector('.border-l-4.border-red-500 .text-3xl');
        // Start with true because we have an entry time already

        document.getElementById('clockBtn').addEventListener('click', function() {
        document.getElementById('opcao').classList.remove('hidden');
        });
        document.getElementById('cancel').addEventListener('click', function() {
            document.getElementById('opcao').classList.add('hidden');
        });

// Array para armazenar as horas trabalhadas por dia
let horasTrabalhadasDia = [];

// Função para calcular o total de horas trabalhadas no mês
function calcularHorasTotaisMes() {
    let totalMinutos = 0;

    // Percorrendo todas as horas trabalhadas no mês
    horasTrabalhadasDia.forEach(dia => {
        // Extrai horas e minutos
        const [horas, minutos] = dia.horas.split(' ').map((valor, index) => {
            if (index === 0) return parseInt(valor); // Horas
            return parseInt(valor.replace('min', '').trim()); // Minutos
        });

        // Converte tudo para minutos
        totalMinutos += horas * 60 + minutos;
    });

    // Converte os minutos totais de volta para horas e minutos
    const horasTotais = Math.floor(totalMinutos / 60);
    const minutosTotais = totalMinutos % 60;

    return `${horasTotais}h ${minutosTotais}min`;
}

         document.getElementById('entrada').addEventListener('click', function() {
            document.getElementById('opcao').classList.add('hidden');

        if(clockedIn){
            showToast(`⚠️ Erro: entrada já registrada hoje.`, "bg-red-500", "fa-sign-in-alt");   
            return;
             }else{ 
        
            const now = new Date();
            const hours = now.getHours().toString().padStart(2, '0');
            const minutes = now.getMinutes().toString().padStart(2, '0');
           
            
            clockedIn= true;
            clockedOut = false;
            clockedBreak = false;
            clockInTime.textContent = `${hours}:${minutes}`;
            hora_ent= `${hours}:${minutes}`;
            
               document.querySelector('.border-l-4.border-green-500 .text-sm').textContent = 'Registrado hoje';
               document.querySelector('.tipo1').textContent = 'Entrada';
 
                 adicionarHistorico('Entrada', `${hours}:${minutes}`, 'text-green-600');
                 adicionarHistoricoCompleto('Entrada', `${hours}:${minutes}`, 'text-green-600');
                 showToast(`Entrada registrada às ${hours}:${minutes}`, "bg-green-500", "fa-sign-in-alt");
             }
        });

        
        
         document.getElementById('pausa').addEventListener('click', function() {
            document.getElementById('opcao').classList.add('hidden');

            if(!clockedIn){
                showToast(`⚠️ Erro: ainda não há entrada registrada hoje.`, "bg-red-500", "fa-sign-in-alt");
            }if(clockedBreak && clockedIn && !clockedOut){
                showToast(`⚠️ Erro: Você ja esta no período de intervalo.`, "bg-red-500", "fa-sign-in-alt");
            }if(clockedOut && !clockedBreak){
                showToast(`⚠️ Erro: O intervalo não pode ser registrado após o registro de saída`, "bg-red-500", "fa-sign-in-alt");
            }if(!clockedBreak && !clockedOut){

            
            const now = new Date();
            const hours = now.getHours().toString().padStart(2, '0');
            const minutes = now.getMinutes().toString().padStart(2, '0');
            clockedBreak = true;
            clockOutTime.textContent = `${hours}:${minutes}`;
            hora_inter= `${hours}:${minutes}`;
            // registrarPonto('Intervalo');
            document.querySelector('.tipo2').textContent = 'Intervalo';
            adicionarHistorico('Intervalo', `${hours}:${minutes}`, 'text-yellow-600');
            adicionarHistoricoCompleto('Intervalo', `${hours}:${minutes}`, 'text-yellow-600');
            showToast(`Intervalo iniciado às ${hours}:${minutes}`, "bg-yellow-500", "fa-coffee");
            }
         });
          document.getElementById('volta').addEventListener('click', function() {
            document.getElementById('opcao').classList.add('hidden');

            if(!clockedIn){
                showToast(`⚠️ Erro: ainda não há entrada registrada hoje.`, "bg-red-500", "fa-sign-in-alt");
            }
            if(!clockedBreak){
                showToast(`⚠️ Erro: ainda não há intervalo registrado hoje.`, "bg-red-500", "fa-sign-in-alt");
            }
            if(clockedBreak && clockedIn){

            
             const now = new Date();
            const hours = now.getHours().toString().padStart(2, '0');
            const minutes = now.getMinutes().toString().padStart(2, '0');
            clockedBreak = false;
            clockInTime.textContent = `${hours}:${minutes}`;
            hora_ret= `${hours}:${minutes}`;
            const [hInter, mInter] = hora_inter.split(':').map(Number);
const [hRet, mRet] = hora_ret.split(':').map(Number);

// converte tudo para minutos
const minutosInter = hInter * 60 + mInter;
const minutosRet = hRet * 60 + mRet;

// diferença em minutos
let diffMinutos = minutosRet - minutosInter;

// Se quiser em horas e minutos novamente:
const lhoras = Math.floor(diffMinutos / 60);
const lminutos = diffMinutos % 60;

horas_pausa = `${lhoras}h ${lminutos}min`;


            // registrarPonto('Retorno');
            document.querySelector('.tipo1').textContent = 'Retorno';
            adicionarHistorico('Retorno', `${hours}:${minutes}`, 'text-blue-600');
            adicionarHistoricoCompleto('Retorno', `${hours}:${minutes}`, 'text-blue-600');
            showToast(`Retorno às ${hours}:${minutes}`, "bg-blue-500", "fa-business-time");
            showToast(`Intervalo total ${horas_pausa}`, "bg-blue-500", "fa-business-time");
            }
          });    
        
          document.getElementById('saida').addEventListener('click', function() {    
            document.getElementById('opcao').classList.add('hidden');

        if(!clockedIn){
       
            showToast(`⚠️ Erro: ainda não há entrada registrada hoje.`, "bg-red-500", "fa-sign-in-alt");
            }
            
            if(clockedOut && clockedIn){
            showToast(`⚠️ Erro: saída já registrada hoje.`, "bg-red-500", "fa-sign-in-alt");//problema 
            return;
            }
            if(!clockedOut){

            const now = new Date();
            const hours = now.getHours().toString().padStart(2, '0');
            const minutes = now.getMinutes().toString().padStart(2, '0');
            clockedOut = true;
             // Clock out
                clockOutTime.textContent = `${hours}:${minutes}`;
                hora_saida= `${hours}:${minutes}`;
                document.querySelector('.border-l-4.border-red-500 .text-sm').textContent = ' ';
// ===== ENTRADA E SAÍDA =====
const [hEnt, mEnt] = hora_ent.split(':').map(Number);
const [hSai, mSai] = hora_saida.split(':').map(Number);

const minutosEnt = hEnt * 60 + mEnt;
const minutosSai = hSai * 60 + mSai;

// ===== INTERVALO (PAUSA E RETORNO) =====
const [hInter, mInter] = hora_inter.split(':').map(Number);
const [hRet, mRet] = hora_ret.split(':').map(Number);

const minutosInter = hInter * 60 + mInter;
const minutosRet = hRet * 60 + mRet;

// ===== CÁLCULOS =====

// Tempo total entre entrada e saída
const minutosTotaisBrutos = minutosSai - minutosEnt;

// Tempo total de intervalo
const minutosIntervalo = minutosRet - minutosInter;

// Tempo realmente trabalhado no dia
const minutosTrabalhados = minutosTotaisBrutos - minutosIntervalo;

// Converte minutos → horas e minutos
const horas = Math.floor(minutosTrabalhados / 60);
const minutos = minutosTrabalhados % 60;

// Resultado final
const horas_trab = `${horas}h ${minutos}min`;


// Função para adicionar uma nova linha na tabela
function adicionarLinhaTabela() {
    // Acessa o corpo da tabela (tbody)
    const tabela = document.getElementById('tabelaDias').getElementsByTagName('tbody')[0];
    
    // Cria uma nova linha (tr)
    const linha = document.createElement('tr');
    
    // Cria as células (td)
    const celulaData = document.createElement('td');
    const celulaEntrada = document.createElement('td');
    const celulaSaida = document.createElement('td');
    const celulaIntervalo = document.createElement('td');
    const celulaHorasTrabalhadas = document.createElement('td');
    const celulaObservacoes = document.createElement('td');
    
    // Adiciona o conteúdo às células
    celulaData.textContent = `${mes}`;  // Data
    celulaEntrada.textContent = `${hora_ent}`;   // Entrada
    celulaSaida.textContent = `${hora_saida}`;     // Saída
    celulaIntervalo.textContent = `${horas_pausa}`; // Intervalo
    celulaHorasTrabalhadas.textContent = `${horas_trab}`; // Horas trabalhadas
    celulaObservacoes.textContent = 'Feriado'; // Observações

    // Adiciona as classes de estilo (caso queira usar Tailwind ou outro framework)
    celulaData.classList.add('px-3', 'py-2', 'border', 'text-center');
    celulaEntrada.classList.add('px-3', 'py-2', 'border', 'text-center');
    celulaSaida.classList.add('px-3', 'py-2', 'border', 'text-center');
    celulaIntervalo.classList.add('px-3', 'py-2', 'border', 'text-center');
    celulaHorasTrabalhadas.classList.add('px-3', 'py-2', 'border', 'text-center');
    celulaObservacoes.classList.add('px-3', 'py-2', 'border', 'text-center', 'text-blue-600');
    
    // Adiciona as células à linha
    linha.appendChild(celulaData);
    linha.appendChild(celulaEntrada);
    linha.appendChild(celulaSaida);
    linha.appendChild(celulaIntervalo);
    linha.appendChild(celulaHorasTrabalhadas);
    linha.appendChild(celulaObservacoes);
    
    // Adiciona a linha ao corpo da tabela (tbody)
    tabela.appendChild(linha);
}

// Converter horas previstas (ex: "168h") para minutos
const horasPrev = parseInt(horasPrevistas) * 60;

// Converter horas trabalhadas (ex: "7h 35min") para minutos
const [hTrab, mTrab] = horas_trab
    .replace("min", "")
    .split("h")
    .map(x => x.trim())
    .filter(x => x !== "")
    .map(Number);

const minutosTrab = hTrab * 60 + mTrab;

// Comparação correta
if (minutosTrab === horasPrev) {
    document.getElementById('horasExtras').textContent = "0h";
} else if (minutosTrab > horasPrev) {
    let horaExtra = minutosTrab - horasPrev;
    document.getElementById('horasExtras').textContent = formatarHoras(horaExtra);
} else {
    let horaNegativa = horasPrev - minutosTrab;
    document.getElementById('horasExtras').textContent = "-" + formatarHoras(horaNegativa);
}

// Adiciona o evento de clique no botão para adicionar a linha
document.getElementById('saida').addEventListener('click', adicionarLinhaTabela);


                document.querySelector('.tipo2').textContent = 'Saída';
                document.querySelector('.trab').textContent = `${horas_trab}`;
                adicionarHistorico('Saída', `${hours}:${minutes}`, 'text-red-600');
                adicionarHistoricoCompleto('Saída', `${hours}:${minutes}`, 'text-red-600');
                showToast(`Saída registrada às ${hours}:${minutes}`, "bg-red-500", "fa-sign-out-alt");
                showToast(`Tempo trabalhado: ${horas_trab}`, "bg-blue-500", "fa-clock");

                // Armazenando o tempo trabalhado no array com a data atual
        const dataHoje = new Date().toISOString().split('T')[0]; // Formato: "YYYY-MM-DD"
        horasTrabalhadasDia.push({ data: dataHoje, horas: hora_trab });
            }
        });
        
          document.getElementById('btnHist').addEventListener('click', function() {
        document.getElementById('historico').classList.remove('hidden');
        });
          document.getElementById('cancel1').addEventListener('click', function() {
        document.getElementById('historico').classList.add('hidden');
        });

        function showToast(mensagem, cor = 'bg-green-500', icone = 'fa-info-circle') {
  const container = document.getElementById('not');

  // Elemento visual (permanente na área de notificações)
  const item = document.createElement('div');
  item.className = "flex items-start space-x-3 px-4 py-3 border-b";
  item.innerHTML = `
    <div class="${cor.replace('bg-', 'text-')} p-2 rounded-full bg-opacity-20">
      <i class="fas ${icone}"></i>
    </div>
    <div>
      <p class="font-medium">${mensagem}</p>
      <p class="text-sm text-gray-500">${new Date().toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'})}</p>
    </div>
  `;

  container.prepend(item);

  // Mantém só as 5 mais recentes
  const itens = container.querySelectorAll('.flex.items-start');
  if (itens.length > 5) itens[itens.length - 1].remove();

  // Toast flutuante temporário
  const toast = document.createElement('div');
  toast.textContent = mensagem;
  toast.className = `${cor} text-white px-4 py-2 rounded-md shadow-md fixed bottom-4 right-4 animate-fade-in`;
  document.body.appendChild(toast);
  setTimeout(() => toast.remove(), 4000);
}

function calcularDiasUteis(ano, mes) {
    // O mês em JavaScript começa do 0 (janeiro), então ajustamos o mês para 0-indexado
    const primeiroDia = new Date(ano, mes - 1, 1);  // Primeiro dia do mês
    const ultimoDia = new Date(ano, mes, 0);  // Último dia do mês

    let diasUteis = 0;

    // Iterando pelos dias do mês
    for (let dia = primeiroDia; dia <= ultimoDia; dia.setDate(dia.getDate() + 1)) {
        const diaSemana = dia.getDay();  // getDay() retorna 0 (domingo) a 6 (sábado)
        if (diaSemana >= 1 && diaSemana <= 5) {  // Verifica se é de segunda (1) a sexta-feira (5)
            diasUteis++;
        }
    }

    return diasUteis;
}

function calcularHorasPrevistas(ano, mes) {
    const diasUteis = calcularDiasUteis(ano, mes);
    const horasPrevistas = diasUteis * 8;  // 8 horas de trabalho por dia útil
    return horasPrevistas;
}

// Exemplo de uso
const ano = 2025;
const mes = 11; // Novembro

const horasPrevistas = calcularHorasPrevistas(ano, mes);

document.getElementById('ResumoBtn').addEventListener('click', function() {
            document.getElementById('resumoMes').classList.remove('hidden');
        });
 // 🔹 Dados para cada mês
  const dadosMensais = {
    Janeiro: { horasPrevistas: "", horasTrabalhadas: "", horasExtras: "", faltas: "1", obs: "Atraso em 05/01 justificado." },
    Fevereiro: { horasPrevistas: "", horasTrabalhadas: "", horasExtras: "", faltas: "0", obs: "Sem ocorrências." },
    Março: { horasPrevistas: "", horasTrabalhadas: "", horasExtras: "", faltas: "0", obs: "Excelente assiduidade." },
    Abril: { horasPrevistas: "", horasTrabalhadas: "", horasExtras: "", faltas: "0", obs: "Sem faltas nem atrasos." },
    Maio: { horasPrevistas: "", horasTrabalhadas: "", horasExtras: "", faltas: "2", obs: "02/05 e 15/05 faltas injustificadas." },
    Junho: { horasPrevistas: "", horasTrabalhadas: "", horasExtras: "", faltas: "0", obs: "Mês perfeito!" },
    Julho: { horasPrevistas: "", horasTrabalhadas: "", horasExtras: "", faltas: "0", obs: "01/07 atraso leve." },
    Agosto: { horasPrevistas: "", horasTrabalhadas: "", horasExtras: "", faltas: "0", obs: "Boa produtividade." },
    Setembro: { horasPrevistas: "", horasTrabalhadas: "", horasExtras: "", faltas: "1", obs: "Falta em 22/09 justificada." },
    Outubro: { horasPrevistas: "", horasTrabalhadas: "", horasExtras: "", faltas: "0", obs: "Sem ocorrências." },
    Novembro: { horasPrevistas: "", horasTrabalhadas: "", horasExtras: "", faltas: "1", obs: "Atraso em 03/11." },
    Dezembro: { horasPrevistas: "", horasTrabalhadas: "", horasExtras: "", faltas: "1", obs: "Falta em 12/12." },
  };

  // Atualizar o objeto 'dadosMensais' com as horas previstas
const anoAtual = new Date().getFullYear(); // Obtém o ano atual (pode ser ajustado conforme necessário)
Object.keys(dadosMensais).forEach((mes, index) => {
    // Calcula o número do mês (de 1 a 12, janeiro = 1, fevereiro = 2, etc.)
    const mesNumero = index + 1;
    const horasPrevistas = calcularHorasPrevistas(anoAtual, mesNumero);
    dadosMensais[mes].horasPrevistas = `${horasPrevistas}h`; // Atualiza as horas previstas
});

  
  // 🔸 Gera os cards de meses dinamicamente
  const meses = Object.keys(dadosMensais);
  const grid = document.getElementById("mesesGrid");
  meses.forEach((mes, index) => {
    const card = document.createElement("button");
    card.className = `time-card bg-white rounded-xl shadow-md p-6 text-center border-l-4 border-purple-500 hover:bg-purple-50 transition`;
    card.innerHTML = `
      <div class="text-gray-600 mb-2">
        <i class="fas fa-calendar-alt text-2xl"></i>
        <h3 class="text-lg font-medium mt-2">${mes}</h3>
      </div>
    `;
    card.addEventListener("click", () => mostrarResumo(mes));
    grid.appendChild(card);
  });

  // 🔹 Mostra o resumo do mês selecionado
  function mostrarResumo(mes) {
    const info = dadosMensais[mes];
    document.getElementById("tituloResumo").textContent = `Resumo de ${mes}`;
    document.getElementById("horasPrevistas").textContent = info.horasPrevistas;
    document.getElementById("horasTrabalhadas").textContent = info.horasTrabalhadas;
    document.getElementById("horasExtras").textContent = info.horasExtras;
    document.getElementById("faltas").textContent = info.faltas;
    document.getElementById("observacoes").textContent = info.obs;

    document.getElementById("mesesGrid").classList.add("hidden");
    document.getElementById("resumoContainer").classList.remove("hidden");
  }

  // 🔹 Voltar para seleção de meses
  document.getElementById("voltarMeses").addEventListener("click", () => {
    document.getElementById("resumoContainer").classList.add("hidden");
    document.getElementById("mesesGrid").classList.remove("hidden");
  });

  // 🔹 Fechar modal
  document.getElementById("cancelResumo").addEventListener("click", () => {
    document.getElementById("resumoMes").classList.add("hidden");
    document.getElementById("resumoContainer").classList.add("hidden");
    document.getElementById("mesesGrid").classList.remove("hidden");
  });
        


// function registrarPonto(tipo) {
//   const agora = new Date();
//   const horario = agora.toISOString().slice(0, 19).replace('T', ' '); // formato YYYY-MM-DD HH:MM:SS

//   fetch('ponto.php', {
//     method: 'POST',
//     headers: { 'Content-Type': 'application/json' },
//     body: JSON.stringify({ tipo, horario })
//   })
//   .then(res => res.json())
//   .then(data => {
//     if (data.sucesso) {
//       showToast(`${tipo} registrada às ${agora.toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'})}`, 'bg-green-500');
//     } else {
//       showToast('Erro ao registrar ponto!', 'bg-red-500');
//     }
//   })
//   .catch(() => showToast('Falha na conexão com o servidor!', 'bg-red-500'));
// }

            // Clock in/out functionality
        
        //clockBtn.addEventListener('click', function() {
          //  const now = new Date();
            //const hours = now.getHours().toString().padStart(2, '0');
            //const minutes = now.getMinutes().toString().padStart(2, '0');
        //}
        
           // if (clockedIn) {
                // Clock out
                clockOutTime.textContent = `${hours}:${minutes}`;
                document.querySelector('.border-l-4.border-red-500 .text-sm').textContent = ' ';
               // clockBtn.innerHTML = '<i class="fas fa-fingerprint text-xl"></i><span>Bater Ponto (Entrada)</span>';
                alert(`Saída registrada às ${hours}:${minutes}`);
          //  } else {
                // Clock in
            clockInTime.textContent = `${hours}:${minutes}`;
               document.querySelector('.border-l-4.border-green-500 .text-sm').textContent = 'Registrado hoje';
                clockBtn.innerHTML = '<i class="fas fa-fingerprint text-xl"></i><span>Bater Ponto (Saída)</span>';
                 alert(`Entrada registrada às ${hours}:${minutes}`);
  //          }
            
    //        clockedIn = !clockedIn;
            
            // Update worked hours (simplified example)
      //      if (!clockedIn) {
         //       const workedHoursEl = document.querySelector('.border-l-4.border-blue-500 .text-3xl');
        //        workedHoursEl.textContent = '';
          //  }
       // });
    </script>
</body>
</html>