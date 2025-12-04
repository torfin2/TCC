<?php 
session_start();
require "logica.php";
require 'conexao.php';

$id_funcionario = filter_input(INPUT_GET, "id_funcionario", FILTER_SANITIZE_NUMBER_INT);


echo "<p><b>ID:</b> $id_funcionario</p>";


/** 
* DELETE FROM `usuarios` WHERE 0
*/
$sql = "DELETE FROM funcionario WHERE id_funcionario = ?";

$stmt = $conn ->prepare($sql);
$result = $stmt->execute([$id_funcionario]);

$count = $stmt -> rowCount();

if($result == true && $count >= 1){
//deu certo o insert
?>
<div class="alert alert-secondary" role="alert">
  <h4>Registro excluido com sucesso.</h4>
</div>
<?php
}elseif($count == 0){
    ?>
<div class="alert alert-danger" role="alert">
<h4>Falha ao efetuar exclusão.</h4>
<p>Não foi encontrado nenhum registro com o ID = <?= $id_funcionario ?>.</p>
</div>
<?php
} else {
//não deu certo, erro
 $errorArray = $stmt->errorInfo();
?>
<div class="alert alert-danger" role="alert">
<h4>Falha ao efetuar gravação.</h4>
<p><?= $errorArray[2]; ?></p>
</div>
<?php
}
?>