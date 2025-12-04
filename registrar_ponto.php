<?php
/**
 * API PARA REGISTRO DE PONTO
 * Registra entrada intervalo retorno e saida
 */

session_start();
require_once 'conexao.php';
require_once 'csrf_token.php';

header('Content-Type: application/json; charset=UTF-8');

// Verifica se está autenticado
if (!isset($_SESSION['id_funcionario'])) {
    http_response_code(401);
    echo json_encode([
        'status' => 'erro',
        'mensagem' => 'Não autenticado. Faça login novamente.'
    ]);
    exit;
}

// Verifica se é requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'erro',
        'mensagem' => 'Método não permitido.'
    ]);
    exit;
}

// Recebe dados JSON
$dados = json_decode(file_get_contents('php://input'), true);

if (!$dados) {
    http_response_code(400);
    echo json_encode([
        'status' => 'erro',
        'mensagem' => 'Dados inválidos.'
    ]);
    exit;
}

// Valida CSRF se vier do formulário
if (isset($_POST['csrf_token'])) {
    verificarCSRF();
}

$tipo = $dados['tipo'] ?? '';
$idFuncionario = $_SESSION['id_funcionario'];
$ipRegistro = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

// Valida tipo de registro
$tiposValidos = ['entrada', 'intervalo', 'retorno', 'saida'];
if (!in_array($tipo, $tiposValidos)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'erro',
        'mensagem' => 'Tipo de registro inválido.'
    ]);
    exit;
}

try {
    // Verifica registros do dia
    $hoje = date('Y-m-d');
    $stmtVerifica = $conn->prepare(
        "SELECT tipo_registro, data_hora 
         FROM ponto 
         WHERE id_funcionario = ? 
         AND DATE(data_hora) = ? 
         ORDER BY data_hora DESC"
    );
    $stmtVerifica->execute([$idFuncionario, $hoje]);
    $registrosHoje = $stmtVerifica->fetchAll();

    // Valida sequência lógica
    $ultimoTipo = $registrosHoje[0]['tipo_registro'] ?? null;

    // Regras de validação
    $regras = [
        'entrada' => [
            'permitido' => $ultimoTipo === null,
            'mensagem' => 'Entrada já registrada hoje.'
        ],
        'intervalo' => [
            'permitido' => $ultimoTipo === 'entrada',
            'mensagem' => 'Você precisa registrar entrada antes do intervalo.'
        ],
        'retorno' => [
            'permitido' => $ultimoTipo === 'intervalo',
            'mensagem' => 'Você precisa registrar intervalo antes do retorno.'
        ],
        'saida' => [
            'permitido' => in_array($ultimoTipo, ['entrada', 'retorno']),
            'mensagem' => 'Você precisa registrar entrada ou retorno antes da saída.'
        ]
    ];

    if (!$regras[$tipo]['permitido']) {
        http_response_code(400);
        echo json_encode([
            'status' => 'erro',
            'mensagem' => $regras[$tipo]['mensagem']
        ]);
        exit;
    }

    // Insere registro
    $stmtInsert = $conn->prepare(
        "INSERT INTO ponto (id_funcionario, tipo_registro, data_hora, ip_registro) 
         VALUES (?, ?, NOW(), ?)"
    );
    $stmtInsert->execute([$idFuncionario, $tipo, $ipRegistro]);

    // Registra log
    registrarLog(
        $conn, 
        $idFuncionario, 
        'registro_ponto', 
        "Tipo: $tipo"
    );

    // Calcula estatísticas do dia
    $stats = calcularEstatisticasDia($conn, $idFuncionario, $hoje);

    echo json_encode([
        'status' => 'sucesso',
        'mensagem' => ucfirst($tipo) . ' registrada com sucesso!',
        'horario' => date('H:i'),
        'tipo' => $tipo,
        'estatisticas' => $stats
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'erro',
        'mensagem' => 'Erro ao registrar ponto. Tente novamente.'
    ]);
    error_log("Erro ao registrar ponto: " . $e->getMessage());
}

/**
 * Calcula estatísticas do dia
 */
function calcularEstatisticasDia($conn, $idFuncionario, $data) {
    $stmt = $conn->prepare(
        "SELECT tipo_registro, data_hora 
         FROM ponto 
         WHERE id_funcionario = ? 
         AND DATE(data_hora) = ? 
         ORDER BY data_hora ASC"
    );
    $stmt->execute([$idFuncionario, $data]);
    $registros = $stmt->fetchAll();

    $stats = [
        'entrada' => null,
        'intervalo' => null,
        'retorno' => null,
        'saida' => null,
        'horas_trabalhadas' => null,
        'tempo_intervalo' => null
    ];

    foreach ($registros as $reg) {
        $stats[$reg['tipo_registro']] = date('H:i', strtotime($reg['data_hora']));
    }

    // Calcula horas trabalhadas
    if ($stats['entrada'] && $stats['saida']) {
        $entrada = strtotime($stats['entrada']);
        $saida = strtotime($stats['saida']);
        
        $intervalo = 0;
        if ($stats['intervalo'] && $stats['retorno']) {
            $intervalo = strtotime($stats['retorno']) - strtotime($stats['intervalo']);
            $stats['tempo_intervalo'] = gmdate('H:i', $intervalo);
        }

        $totalSegundos = ($saida - $entrada) - $intervalo;
        $stats['horas_trabalhadas'] = gmdate('H:i', $totalSegundos);
    }

    return $stats;
}