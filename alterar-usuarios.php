<?php 
session_start();
require "logica.php";

require 'conexao.php';

$id_funcionario = filter_input(INPUT_POST, "id_funcionario", FILTER_SANITIZE_NUMBER_INT);
$nome = filter_input(INPUT_POST, "nome", FILTER_SANITIZE_SPECIAL_CHARS);
$telefone = filter_input(INPUT_POST, "telefone", FILTER_SANITIZE_SPECIAL_CHARS);
$email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
$senha = filter_input(INPUT_POST, "senha", FILTER_SANITIZE_SPECIAL_CHARS);

echo "<p><b>ID:</b> $id_funcionario</p>";
echo "<p><b>Nome:</b> $nome</p>";
echo "<p><b>Email:</b> $email</p>";


/** 
* UPDATE `usuarios` SET `id`='[value-1]',`nome`='[value-2]',`email`='[value-3]',`senha`='[value-4]' WHERE 1
*/
$sql = "UPDATE funcionario SET nome = ?, telefone = ?, email = ?, senha = ?
         WHERE id_funcionario= ?";

$stmt = $conn ->prepare($sql);
$result = $stmt->execute([$nome, $telefone, $email, $senha, $id_funcionario]);
$count = $stmt -> rowCount();

if($result == true && $count >= 1){
//deu certo o insert
?>
<div class="alert alert-sucess" role="alert">
  <h4>Dados alterados com sucesso.</h4>
</div>
<?php
}elseif($result == true && $count == 0){
    ?>
    <div class="alert alert-secondary" role="alert">
      <h4>Nenhum dado foi alterado.</h4>
    </div>
    <?php
}else{

    //não deu certo, erro
    $errorArray = $stmt->errorInfo();

    ?>
    <div class="alert alert-danger" role="alert">
  <h4>Falha ao efetuar gravação.</h4>
  <p><?= $stmt->error; ?></p>
</div>
    <?php
}


?>