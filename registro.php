<?php
require_once 'csrf_token.php';
session_start();
$csrf_token = gerarTokenCSRF();
?>
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
            <form class="p-8" id="registrationForm" action="dest_registro.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                
                <!-- Nome -->
                <div class="mb-6">
                    <label for="nome" class="block text-gray-700 font-medium mb-2">Nome Completo</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <input type="text" id="nome" name="nome" class="form-input w-full pl-10 pr-3 py-3 rounded-lg border border-gray-300 focus:border-blue-500" placeholder="Digite seu nome completo" required>
                    </div>
                </div>
                
                <!-- CPF -->
                <div class="mb-6">
                    <label for="cpf" class="block text-gray-700 font-medium mb-2">CPF</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-id-card text-gray-400"></i>
                        </div>
                        <input type="text" id="cpf" name="cpf" class="form-input cpf-mask w-full pl-10 pr-3 py-3 rounded-lg border border-gray-300 focus:border-blue-500" placeholder="000.000.000-00" maxlength="14" required>
                    </div>
                </div>
                
                <!-- Telefone -->
                <div class="mb-6">
                    <label for="telefone" class="block text-gray-700 font-medium mb-2">Telefone</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-phone text-gray-400"></i>
                        </div>
                        <input type="text" id="telefone" name="telefone" class="form-input phone-mask w-full pl-10 pr-3 py-3 rounded-lg border border-gray-300 focus:border-blue-500" placeholder="(00) 00000-0000" maxlength="15" required>
                    </div>
                </div>
                
                <!-- Email -->
                <div class="mb-6">
                    <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" id="email" name="email" class="form-input w-full pl-10 pr-3 py-3 rounded-lg border border-gray-300 focus:border-blue-500" placeholder="exemplo@dominio.com" required>
                    </div>
                </div>

                <!-- Senha -->
                <div class="mb-6">
                    <label for="senha" class="block text-gray-700 font-medium mb-2">Senha</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" id="senha" name="senha" class="form-input w-full pl-10 pr-10 py-3 rounded-lg border border-gray-300 focus:border-blue-500" placeholder="Crie uma senha segura" required>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <button type="button" id="togglePassword" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Mínimo de 8 caracteres</p>
                </div>

                <!-- Confirmar Senha -->
                <div class="mb-8">
                    <label for="confirmPassword" class="block text-gray-700 font-medium mb-2">Confirmar Senha</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" id="confirmPassword" name="confirmPassword" class="form-input w-full pl-10 pr-3 py-3 rounded-lg border border-gray-300 focus:border-blue-500" placeholder="Confirme sua senha" required>
                    </div>
                </div>
                
                <!-- Botão -->
                <button id="submitBtn" type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                    <i class="fas fa-user-plus mr-2"></i> Cadastrar
                </button>

                <!-- Login -->
                <div class="mt-6 text-center">
                    <p class="text-gray-600">Já tem uma conta? 
                        <a href="login.php" class="text-blue-600 font-medium hover:text-blue-800">Faça login</a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <script>
const cpfInput = document.getElementById('cpf');
const telInput = document.getElementById('telefone');
const togglePassword = document.getElementById('togglePassword');
const passwordInput = document.getElementById('senha');
const form = document.getElementById('registrationForm');
const message = document.getElementById('message');
const submitBtn = document.getElementById('submitBtn');

// Máscara CPF
cpfInput.addEventListener('input', e => {
    let v = e.target.value.replace(/\D/g, '');
    v = v.replace(/(\d{3})(\d)/, '$1.$2');
    v = v.replace(/(\d{3})(\d)/, '$1.$2');
    v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    e.target.value = v;
});

// Máscara telefone
telInput.addEventListener('input', e => {
    let v = e.target.value.replace(/\D/g, '');
    v = v.replace(/^(\d{2})(\d)/g, '($1) $2');
    v = v.replace(/(\d{5})(\d)/, '$1-$2');
    e.target.value = v.substring(0, 15);
});

// Mostrar/ocultar senha
togglePassword.addEventListener('click', () => {
    const icon = togglePassword.querySelector('i');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
});

// Validação CPF
function validarCPF(cpf) {
    cpf = cpf.replace(/[^\d]+/g, '');
    if (cpf.length !== 11 || /^(\d)\1+$/.test(cpf)) return false;
    let soma = 0;
    for (let i = 0; i < 9; i++) soma += parseInt(cpf[i]) * (10 - i);
    let resto = (soma * 10) % 11;
    if (resto === 10 || resto === 11) resto = 0;
    if (resto !== parseInt(cpf[9])) return false;
    soma = 0;
    for (let i = 0; i < 10; i++) soma += parseInt(cpf[i]) * (11 - i);
    resto = (soma * 10) % 11;
    if (resto === 10 || resto === 11) resto = 0;
    return resto === parseInt(cpf[10]);
}

// Mostrar mensagem
function showMessage(text, type = 'erro') {
    message.textContent = text;
    message.style.display = 'block';
    message.className = type === 'erro'
        ? 'bg-red-100 text-red-700 p-3 text-center text-sm rounded'
        : 'bg-green-100 text-green-700 p-3 text-center text-sm rounded';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function hideMessage() {
    message.style.display = 'none';
}

// Envio do formulário
form.addEventListener('submit', e => {
    e.preventDefault();
    hideMessage();

    const password = passwordInput.value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const cpfValue = cpfInput.value;

    if (!validarCPF(cpfValue)) return showMessage('CPF inválido. Verifique e tente novamente.');
    if (password !== confirmPassword) return showMessage('As senhas não coincidem!');
    if (password.length < 8) return showMessage('A senha deve ter pelo menos 8 caracteres!');

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Enviando...';

    fetch(form.action, {
        method: 'POST',
        body: new FormData(form)
    })
    .then(res => res.json())
    .then(response => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-user-plus mr-2"></i> Cadastrar';

        if (response.status === 'erro') {
            showMessage('✗ ' + response.mensagem);
        } else {
            showMessage('✓ ' + response.mensagem, 'sucesso');
            form.reset();
            setTimeout(() => {
                window.location.href = 'login.php';
            }, 2000);
        }
    })
    .catch(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-user-plus mr-2"></i> Cadastrar';
        showMessage('✗ Falha na conexão com o servidor. Tente novamente.');
    });
});
    </script>
</body>
</html>