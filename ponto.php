<?php
require_once 'conexao.php';

// Se for requisição AJAX para gerar novo token
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');

    try {
        // 1. Invalidar QR Codes anteriores
        $stmtUpdate = $conn->prepare("UPDATE qrcode SET status = 'expirado' WHERE status = 'ativo'");
        $stmtUpdate->execute();

        // 2. Gerar token seguro (64 caracteres aleatórios)
        $token = bin2hex(random_bytes(32));

        // 3. Calcular datas
        $agora = new DateTime();
        $expiracao = clone $agora;
        $expiracao->modify('+20 minutes');

        // 4. Obter IP de origem
        $ipOrigem = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        // 5. Inserir novo QR Code
        $stmtInsert = $conn->prepare(
            "INSERT INTO qrcode (token, data_geracao, data_expiracao, status, ip_origem) 
             VALUES (?, ?, ?, 'ativo', ?)"
        );
        $stmtInsert->execute([
            $token,
            $agora->format('Y-m-d H:i:s'),
            $expiracao->format('Y-m-d H:i:s'),
            $ipOrigem
        ]);

        // 6. URL para login
        $urlDestino = "http://" . $_SERVER['HTTP_HOST'] . "/login.php?token=" . $token;

        echo json_encode([
            'success' => true,
            'url' => $urlDestino,
            'token' => substr($token, 0, 8) . '...', // Mostra apenas início
            'expira_em' => $expiracao->getTimestamp() * 1000 // JS usa milissegundos
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false, 
            'erro' => 'Erro ao gerar QR Code. Tente novamente.'
        ]);
        error_log("Erro ao gerar QR Code: " . $e->getMessage());
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code - Ponto Eletrônico</title>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 50px;
            border-radius: 24px;
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.3);
            text-align: center;
            max-width: 500px;
            width: 100%;
            animation: fadeIn 0.6s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        .logo {
            margin-bottom: 20px;
        }
        .logo i {
            font-size: 72px;
            color: #667eea;
        }
        h1 {
            font-size: 32px;
            color: #1c1e21;
            margin-bottom: 8px;
        }
        .subtitle {
            color: #606770;
            font-size: 16px;
            margin-bottom: 40px;
        }
        #qrcode {
            margin: 30px auto;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 16px;
            display: inline-block;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .status {
            font-size: 15px;
            color: #666;
            margin: 20px 0;
            min-height: 24px;
        }
        .timer {
            font-size: 48px;
            font-weight: bold;
            color: #667eea;
            margin: 20px 0;
            font-variant-numeric: tabular-nums;
        }
        .info {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin-top: 30px;
            padding: 15px;
            background: #f0f4ff;
            border-radius: 10px;
        }
        .info-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .info-label {
            font-size: 12px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-value {
            font-size: 14px;
            font-weight: 600;
            color: #333;
        }
        .loading {
            display: inline-block;
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .pulse {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="logo">
            <i class="fas fa-clock"></i>
        </div>
        <h1>Ponto Eletrônico</h1>
        <p class="subtitle">Escaneie o QR Code para fazer login</p>
        
        <div id="qrcode"></div>
        
        <div class="status" id="status">
            <span class="loading"></span>
        </div>
        
        <div class="timer pulse" id="timer">--:--</div>
        
        <div class="info">
            <div class="info-item">
                <span class="info-label">Token ID</span>
                <span class="info-value" id="tokenId">-</span>
            </div>
            <div class="info-item">
                <span class="info-label">Status</span>
                <span class="info-value" style="color: #28a745;">● Ativo</span>
            </div>
        </div>
    </div>

    <script>
        let intervaloRelogio;
        const TEMPO_ATUALIZACAO = 20 * 60; // 20 minutos em segundos

        function atualizarQRCode() {
            fetch('?ajax=1')
                .then(response => {
                    if (!response.ok) throw new Error('Erro na requisição');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Limpa QR Code anterior
                        document.getElementById("qrcode").innerHTML = "";
                        
                        // Gera novo QR Code
                        new QRCode(document.getElementById("qrcode"), {
                            text: data.url,
                            width: 280,
                            height: 280,
                            colorDark: "#1c1e21",
                            colorLight: "#ffffff",
                            correctLevel: QRCode.CorrectLevel.H
                        });

                        // Atualiza informações
                        document.getElementById("status").innerHTML = 
                            '<i class="fas fa-check-circle" style="color: #28a745;"></i> QR Code gerado com sucesso';
                        document.getElementById("tokenId").textContent = data.token;

                        // Reinicia contador
                        iniciarContagemRegressiva(TEMPO_ATUALIZACAO);
                    } else {
                        mostrarErro(data.erro || 'Erro desconhecido');
                    }
                })
                .catch(err => {
                    console.error("Erro:", err);
                    mostrarErro('Falha ao conectar com o servidor');
                });
        }

        function iniciarContagemRegressiva(segundos) {
            if (intervaloRelogio) clearInterval(intervaloRelogio);
            
            let restantes = segundos;
            const display = document.getElementById("timer");

            intervaloRelogio = setInterval(() => {
                let minutos = Math.floor(restantes / 60);
                let segs = restantes % 60;
                
                display.textContent = 
                    `${minutos.toString().padStart(2, '0')}:${segs.toString().padStart(2, '0')}`;

                // Alerta quando faltam 2 minutos
                if (restantes === 120) {
                    display.style.color = '#ff9800';
                }

                // Alerta quando falta 1 minuto
                if (restantes === 60) {
                    display.style.color = '#f44336';
                }

                if (--restantes < 0) {
                    clearInterval(intervaloRelogio);
                    display.textContent = "EXPIRANDO";
                    display.style.color = '#667eea';
                    setTimeout(atualizarQRCode, 1000);
                }
            }, 1000);
        }

        function mostrarErro(mensagem) {
            document.getElementById("status").innerHTML = 
                `<i class="fas fa-exclamation-triangle" style="color: #f44336;"></i> ${mensagem}`;
            document.getElementById("timer").textContent = "ERRO";
            document.getElementById("timer").style.color = '#f44336';
        }

        // Inicia quando a página carrega
        window.addEventListener('load', atualizarQRCode);

        // Atualiza automaticamente a cada 20 minutos
        setInterval(atualizarQRCode, TEMPO_ATUALIZACAO * 1000);
    </script>
</body>
</html>