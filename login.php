<?php
session_start();
require_once 'conexao.php';
require_once 'csrf_token.php';


$mostrarFormulario = false;
$mensagemErro = "";
$tokenValidado = null;

// 1. VERIFICA TOKEN DO QR CODE
if (isset($_GET['token'])) {
    $tokenRecebido = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_SPECIAL_CHARS);
    
    if (!$tokenRecebido) {
        $mensagemErro = "Token inválido.";
    } else {
        // Busca token no banco
        $stmt = $conn->prepare(
            "SELECT * FROM qrcode 
             WHERE token = :token 
             AND status = 'ativo' 
             AND data_expiracao > NOW()"
        );
        $stmt->bindValue(':token', $tokenRecebido, PDO::PARAM_STR);
        $stmt->execute();
        $dadosQr = $stmt->fetch();
        
        if ($dadosQr) {
            $mostrarFormulario = true;
            $tokenValidado = $tokenRecebido;
            $_SESSION['qr_token'] = $tokenRecebido;
        } else {
            // Verifica se expirou ou não existe
            $stmtCheck = $conn->prepare(
                "SELECT status, data_expiracao FROM qrcode WHERE token = ?"
            );
            $stmtCheck->execute([$tokenRecebido]);
            $qrCheck = $stmtCheck->fetch();
            
            if ($qrCheck) {
                $mensagemErro = "Este QR Code já expirou ou foi utilizado. Solicite um novo.";
            } else {
                $mensagemErro = "QR Code inválido.";
            }
        }
    }
}

// 2. PROCESSA LOGIN
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificarCSRF(); // Valida token CSRF
    
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'] ?? '';
    $tokenQR = $_POST['qr_token'] ?? '';
    
    // Verifica rate limit
    if (verificarRateLimit($conn, $email)) {
        $mensagemErro = "Muitas tentativas de login. Aguarde 15 minutos e tente novamente.";
        $mostrarFormulario = true;
        $tokenValidado = $tokenQR;
    } else {
        // Busca funcionário
        $stmtFunc = $conn->prepare(
            "SELECT id_funcionario, nome, email, senha, ativo, adm 
             FROM funcionario 
             WHERE email = ? 
             LIMIT 1"
        );
        $stmtFunc->execute([$email]);
        $funcionario = $stmtFunc->fetch();
        
        // Verifica credenciais
        if ($funcionario && password_verify($senha, $funcionario['senha'])) {
            
            // Verifica se está ativo
            if ($funcionario['ativo'] != 1) {
                $mensagemErro = "Sua conta está inativa. Contate o RH.";
                registrarTentativaLogin($conn, $email, false);
                $mostrarFormulario = true;
                $tokenValidado = $tokenQR;
            } else {
                // Login bem-sucedido
                registrarTentativaLogin($conn, $email, true);
                
                // Regenera ID da sessão (segurança)
                session_regenerate_id(true);
                
                // Define sessão
                $_SESSION['id_funcionario'] = $funcionario['id_funcionario'];
                $_SESSION['nome'] = $funcionario['nome'];
                $_SESSION['email'] = $funcionario['email'];
                $_SESSION['adm'] = $funcionario['adm'];
                $_SESSION['ultimo_acesso'] = time();
                
                // Registra log
                registrarLog(
                    $conn, 
                    $funcionario['id_funcionario'], 
                    'login', 
                    'Login via QR Code'
                );
                
                // Invalida o QR Code
                if ($tokenQR) {
                    $stmtInvalida = $conn->prepare(
                        "UPDATE qrcode SET status = 'utilizado' WHERE token = ?"
                    );
                    $stmtInvalida->execute([$tokenQR]);
                }
                
                // Redireciona
                if ($funcionario['adm'] == 1) {
                    header("Location: pag_adm.php");
                } else {
                    header("Location: inicio.php");
                }
                exit;
            }
            
        } else {
            // Credenciais inválidas
            registrarTentativaLogin($conn, $email, false);
            $mensagemErro = "E-mail ou senha incorretos.";
            $mostrarFormulario = true;
            $tokenValidado = $tokenQR;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Ponto Eletrônico</title>
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
        .card {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 420px;
            animation: slideUp 0.5s ease;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo i {
            font-size: 64px;
            color: #667eea;
            margin-bottom: 10px;
        }
        h1 {
            font-size: 28px;
            color: #1c1e21;
            margin-bottom: 8px;
            text-align: center;
        }
        .subtitle {
            color: #606770;
            font-size: 15px;
            margin-bottom: 30px;
            text-align: center;
            line-height: 1.5;
        }
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            animation: shake 0.5s;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }
        .alert-error {
            background-color: #fee;
            border: 1px solid #fcc;
            color: #c00;
        }
        .alert-success {
            background-color: #dfd;
            border: 1px solid #bfb;
            color: #060;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            color: #444;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }
        input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e1e4e8;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
            font-family: inherit;
        }
        input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        button {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            padding: 14px;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 10px;
        }
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        button:active {
            transform: translateY(0);
        }
        .loading-icon {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #fff;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .qr-status {
            text-align: center;
            padding: 60px 20px;
        }
        .qr-icon {
            font-size: 80px;
            color: #ccc;
            margin-bottom: 20px;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <div class="card">
        <div class="logo">
            <i class="fas fa-clock"></i>
            <h1>Ponto Eletrônico</h1>
        </div>

        <?php if ($mensagemErro): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($mensagemErro) ?>
            </div>
        <?php endif; ?>

        <?php if ($mostrarFormulario): ?>
            
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> QR Code validado com sucesso!
            </div>
            <p class="subtitle">Insira suas credenciais para registrar o ponto.</p>

            <form method="POST" id="loginForm">
                <?= campoTokenCSRF() ?>
                <input type="hidden" name="qr_token" value="<?= htmlspecialchars($tokenValidado) ?>">
                
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" name="email" id="email" placeholder="seu@email.com" required autofocus>
                </div>
                
                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" name="senha" id="senha" placeholder="••••••••" required>
                </div>
                
                <button type="submit" id="btnSubmit">
                    <i class="fas fa-sign-in-alt"></i> Entrar
                </button>
            </form>

        <?php elseif (empty($mensagemErro)): ?>
            
            <div class="qr-status">
                <div class="qr-icon">
                    <i class="fas fa-qrcode"></i>
                </div>
                <p class="subtitle">
                    Escaneie o QR Code exibido na recepção<br>para acessar o sistema de ponto.
                </p>
            </div>

        <?php endif; ?>
    </div>

    <script>
        document.getElementById('loginForm')?.addEventListener('submit', function() {
            const btn = document.getElementById('btnSubmit');
            btn.innerHTML = '<span class="loading-icon"></span> Entrando...';
            btn.disabled = true;
        });
    </script>
</body>
</html>