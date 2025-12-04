<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
     rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
     <script src="https://cdn.tailwindcss.com"></script>

</head>
<body>
    <style>
/*
 * Globals
 */


/* Custom default button */


.cover-container {
  max-width: 42em;
}


/*
 * Header
 */

.nav-masthead .nav-link {
  color: rgba(255, 255, 255, .5);
  border-bottom: .25rem solid transparent;
}

.nav-masthead .nav-link:hover,
.nav-masthead .nav-link:focus {
  border-bottom-color: rgba(255, 255, 255, .25);
}

.nav-masthead .nav-link + .nav-link {
  margin-left: 1rem;
}

.nav-masthead .active {
  color: #fff;
  border-bottom-color: #fff;
}
</style>
    <link
      rel="canonical"
      href="https://getbootstrap.com/docs/5.3/examples/cover/"
    />
    <script src="../assets/js/color-modes.js"></script>
    <link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet" />
    <meta name="theme-color" content="#712cf9" />
    <link href="cover.css" rel="stylesheet" />
    <style>
      
      
      /* From Uiverse.io by satyamchaudharydev */ 
/* === removing default button style ===*/
.button {
  margin: 0;
  height: auto;
  background: transparent;
  padding: 0;
  border: none;
  cursor: pointer;
}

/* button styling */
.button {
  --border-right: 6px;
  --text-stroke-color: rgba(255,255,255,0.6);
  --animation-color: #37FF8B;
  --fs-size: 2em;
  letter-spacing: 3px;
  text-decoration: none;
  font-size: var(--fs-size);
  font-family: "Arial";
  position: relative;
  text-transform: uppercase;
  color: transparent;
  -webkit-text-stroke: 1px var(--text-stroke-color);
}
/* this is the text, when you hover on button */
.hover-text {
  position: absolute;
  box-sizing: border-box;
  content: attr(data-text);
  color: #3182ce;
  width: 0%;
  inset: 0;
  border-right: var(--border-right) solid #3182ce;
  overflow: hidden;
  transition: 0.5s;
  -webkit-text-stroke: 1px #3182ce;
}
/* hover */
.button:hover .hover-text {
  width: 100%;
  filter: drop-shadow(0 0 23px #3182ce)
}
      button{
        margin-top:5vh;
      }
      
    /* Fundo e alinhamento */
    body {
      margin: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background-color: #fff;
      overflow: hidden;
    }
    header{
      width: 240vh;
      margin-left: -70vh;
      margin-top: -15px;
    }
.c1{
  margin-top: 10vh;
}
.c3{
  margin-left: -30vh;
  width: 140vh;
  display: flex;
  justify-content: space-between;
  margin-top: 5vh;
}
.c2{
  border: 2px solid #3182ce;
  border-radius: 20px;
  width: 250px;
  height: 350px;
  padding-top:3vh;
}
h2{
  font-size:2vh ;
  font-weight: bold;
  color: #3182ce;
  margin-top: 3vh;
  margin-left: 2vh;
  margin-right: 2vh;
}
.icone{
  font-size: 50px;
}
.p1{
  margin-top: 3vh;
  font-size: small;
}
.p2{
  font-size:1vh;
  font-weight: bold;
}
/* From Uiverse.io by yaasiinaxmed */ 
button {
  --color: #0077ff;
  font-family: inherit;
  display: inline-block;
  width: 6em;
  height: 2.6em;
  line-height: 2.5em;
  overflow: hidden;
  cursor: pointer;
  margin: 20px;
  font-size: 17px;
  z-index: 1;
  color: var(--color);
  border: 2px solid var(--color);
  border-radius: 6px;
  position: relative;
}

button::before {
  position: absolute;
  content: "";
  background: var(--color);
  width: 150px;
  height: 200px;
  z-index: -1;
  border-radius: 50%;
}

button:hover {
  color: white;
}

button:before {
  top: 100%;
  left: 100%;
  transition: 0.3s all;
}

button:hover::before {
  top: -30px;
  left: -30px;
}


    </style>
  </head>
  <body class="d-flex h-100 text-center">
   
   
    <div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
    <header class="bg-blue-600 text-white shadow-lg">
            <div class="container mx-auto px-4 py-4 flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-clock text-2xl"></i>
                    <h1 class="text-2xl font-bold">Ponto Eletrônico</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                      
                    <a href="pag_apresent.php" class=" me-2 fw-bold">
                        <span data-feather="user-plus"></span>
                        Home
                    </a>
                    <a href="" class=" me-2 fw-bold">
                        <span data-feather="user-plus"></span>
                        Features
                    </a>
                    <a href="" class=" me-2 fw-bold">
                        <span data-feather="user-plus"></span>
                        Contact
                    </a>
                        <p class="font-medium"></p>
                        <p class="text-sm text-blue-100"></p>
                    </div>
                </div>
            </div>
        </header>
        
       <main class="px-3 ">
      <div class="c1">
     
        <!-- From Uiverse.io by satyamchaudharydev --> 
<button class="button" data-text="Awesome">
    <span class="actual-text">&nbsp;CLOCKIN&nbsp;</span>
    <span aria-hidden="true" class="hover-text">&nbsp;CLOCKIN&nbsp;</span>
</button>
        
        <p class="p2 lead">
        Com o ClockIn, sua empresa tem total controle sobre a marcação de ponto dos colaboradores, de forma simples, 
        segura e 100% digital. Esqueça planilhas manuais ou relógios antigos — nossa plataforma oferece registros precisos, 
        relatórios em tempo real e integração com folha de pagamento.
        </p>
<div class="c3">
        <div class="c2">
          <i class="icone fa-solid fa-clock" style="color: #3182ce"></i>
          <h2>Registro automatizado de Jornada</h2>
          <p class="p1">Marcação precisa de entrada, saída, pausas e retornos. Elimina o uso de planilhas ou registros manuais</p>
        </div>

        <div class="c2">
          <i class="icone fa-solid fa-lock" style="color: #3182ce"></i>
          <h2>Acesso Seguro</h2>
          <p class="p1">Login com credênciais únicas(usuário/senha).</p>
        </div>
        
        <div class="c2">
          <i class="icone fa-solid fa-mobile-screen-button" style="color: #3182ce"></i>
          <h2>Interface Responsiva</h2>
          <p class="p1">Funciona em computadores, tablets e smartphones.Permite marcação de ponto remoto para home office (se autorizado)</p>
        </div>
        
        <div class="c2">
          <i class="icone fa-solid fa-scale-balanced" style="color: #3182ce"></i>
          <h2>Confrmidade com a Legislação</h2>
          <p class="p1">Adequado à portaria 671/2021 do Ministério de Trabalho. Garante validade jurídica dos registros</p>
        </div>        
</div>

        <p class="lead">
         <!-- From Uiverse.io by SmookyDev --> 
      
        

<!-- From Uiverse.io by yaasiinaxmed --> 
<a href="registro.php"><button>Cadastrar</button></a>
<a href="login.php"><button>Entrar</button></a>


        </p>
      </main>
    </div>
      <footer class="mt-auto text-white-50">
        <p>
          Cover template for
          <a href="https://getbootstrap.com/" class="text-white">Bootstrap</a>,
          by <a href="https://x.com/mdo" class="text-white">@mdo</a>.
        </p>
      </footer>
    </div>
    <script
      src="../assets/dist/js/bootstrap.bundle.min.js"
      class="astro-vvvwv3sm"
    ></script>
</head>
<br>
  
 


    
</body>
</html>