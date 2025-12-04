<?php
require 'conexao.php';
header('Content-Type: application/json');

try {
    //  Invalida QR Codes anteriores
    $stmtUpdate = $pdo->prepare("UPDATE qrcode SET status = 'expirado' WHERE status = 'ativo'");
    $stmtUpdate->execute();

    //  Calcula datas
    $agora = new DateTime();
    $expiracao = clone $agora;
    $expiracao->modify('+20 minutes');

    //  Cria novo registro na tabela 'qrcode'
    // id_qrcode é gerado automaticamente pelo banco
    $stmtInsert = $pdo->prepare("INSERT INTO qrcode (data_geracao, data_expiracao, status) VALUES (?, ?, 'ativo')");
    $stmtInsert->execute([
        $agora->format('Y-m-d H:i:s'),
        $expiracao->format('Y-m-d H:i:s')
    ]);

    // Pega o ID gerado para colocar na URL
    $idGerado = $pdo->lastInsertId();

    // URL para onde o QR Code aponta
    $urlLogin = "http://seusite.com/login.php?qrid=" . $idGerado;

    echo json_encode([
        'success' => true,
        'url' => $urlLogin,
        'id' => $idGerado,
        'expira_em' => $expiracao->getTimestamp()
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>