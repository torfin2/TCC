<?php
// Configurações do banco de dados
$host = 'localhost';
$dbname = 'tcc_peletronico';
$user = 'root';
$pass = 'mydba';

try {
    // Conecta ao banco usando PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Define o ano desejado
    $ano = 2025;

    // URL da API BrasilAPI para feriados nacionais
    $url = "https://brasilapi.com.br/api/feriados/v1/$ano";

    // Inicializa o cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Executa a requisição
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception('Erro no cURL: ' . curl_error($ch));
    }

    curl_close($ch);

    // Decodifica o JSON retornado
    $feriados = json_decode($response, true);

    if (!$feriados) {
        throw new Exception('Erro ao decodificar JSON');
    }

    // Prepara o insert (ignora se já existir)
    $stmt = $pdo->prepare("
        INSERT IGNORE INTO feriados (nome, data, tipo) VALUES (:nome, :data, :tipo)
    ");

    // Insere cada feriado no banco
    foreach ($feriados as $feriado) {
        $stmt->execute([
            ':nome' => $feriado['name'],
            ':data' => $feriado['date'],
            ':tipo' => $feriado['type']
        ]);
    }

   

} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
// $soma=0;
// foreach ($feriados as $feriado) {
//    $feriado['name'];
//  $soma=$soma+1;
// }
// foreach ($feriados as $feriado) {
//   echo $feriado['name'] . "<br>";
// }

// echo $feriados[1]['name'];

