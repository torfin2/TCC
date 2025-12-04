<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClockIn - Controle de Ponto</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
     rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            background: #f8fafc;
            font-family: 'Segoe UI', sans-serif;
        }

        header {
            background: white;
            border-bottom: 2px solid #e2e8f0;
        }

        /* Botões principais */
        .btn-main {
            border: 2px solid #3182ce;
            color: #3182ce;
            padding: 10px 25px;
            border-radius: 8px;
            transition: 0.3s;
            font-weight: bold;
        }

        .btn-main:hover {
            background: #3182ce;
            color: white;
            transform: translateY(-3px);
        }

        /* Cards */
        .feature-card {
            background: white;
            border-radius: 18px;
            padding: 25px;
            border: 2px solid #3182ce33;
            transition: 0.4s;
            text-align: center;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            border-color: #3182ce;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .icon-feature {
            font-size: 55px;
            color: #3182ce;
        }

        .feature-title {
            font-size: 1.4rem;
            font-weight: bold;
            color: #3182ce;
            margin-top: 12px;
        }
    </style>
</head>

<body>

<!-- ===================== HEADER ======================= -->
<header>
    <div class="container mx-auto px-6 py-4 flex justify-between items-center">
        <div class="flex items-center space-x-3">
            <i class="fas fa-clock text-5xl text-blue-600"></i>
            <h1 class="text-5xl font-bold text-blue-600">ClockIn</h1>
        </div>

        <nav class="flex items-center space-x-6 text-xl font-semibold">
            <a href="pag_apresent.php" class="text-blue-600 hover:underline">Home</a>
            <a href="#" class="hover:text-blue-600">Features</a>
            <a href="#" class="hover:text-blue-600">Contact</a>

            <a href="registro.php"><button class="btn-main">Cadastrar</button></a>
            <a href="login.php"><button class="btn-main">Entrar</button></a>
        </nav>
    </div>
</header>

<!-- ===================== HERO SECTION ======================= -->
<section class="mt-10 container mx-auto px-10 flex flex-wrap items-center justify-between">
    <div class="max-w-xl">
        <h2 class="text-5xl font-extrabold text-blue-800 leading-snug">
            Com o ClockIn, sua empresa tem total controle sobre a marcação de ponto dos colaboradores
        </h2>

        <p class="text-2xl mt-8 text-gray-700">
            Um sistema moderno, seguro e extremamente confiável para controlar a jornada de trabalho,
            reduzir erros e tornar seu processo mais eficiente.
        </p>
    </div>

    <img class="w-72 lg:w-96 drop-shadow-xl" 
         src="https://upload.wikimedia.org/wikipedia/commons/f/fa/Link_pra_pagina_principal_da_Wikipedia-PT_em_codigo_QR_b.svg">
</section>

<!-- ===================== FEATURES ======================= -->
<section class="mt-20 container mx-auto px-10">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">

        <div class="feature-card">
            <i class="fa-solid fa-clock icon-feature"></i>
            <h3 class="feature-title">Registro Automatizado</h3>
            <p class="mt-4 text-gray-700">
                Marcação precisa de entrada, saída, pausas e retornos. 
                Elimina o uso de planilhas ou registros manuais.
            </p>
        </div>

        <div class="feature-card">
            <i class="fa-solid fa-lock icon-feature"></i>
            <h3 class="feature-title">Acesso Seguro</h3>
            <p class="mt-4 text-gray-700">
                Login com credenciais únicas, garantindo segurança e confiabilidade.
            </p>
        </div>

        <div class="feature-card">
            <i class="fa-solid fa-mobile-screen-button icon-feature"></i>
            <h3 class="feature-title">Interface Responsiva</h3>
            <p class="mt-4 text-gray-700">
                Compatível com computadores, tablets e smartphones. 
                Suporte a home office (se autorizado).
            </p>
        </div>

        <div class="feature-card">
            <i class="fa-solid fa-scale-balanced icon-feature"></i>
            <h3 class="feature-title">Conformidade Legal</h3>
            <p class="mt-4 text-gray-700">
                Adequado à Portaria 671/2021 do Ministério do Trabalho. 
                Garante validade jurídica dos registros.
            </p>
        </div>

    </div>
</section>

</body>
</html>
