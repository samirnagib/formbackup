<?php
require_once '/var/secure/auth.php';
require_once '/var/secure/config.php';
require '/var/secure/funcao.php';

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM solicitacoes WHERE id = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
}
registra_log($conn, $id, "Solicitação {$id} excluída pelo {$_SESSION['usuario']}");

header('Location: listar_solicitacoes.php');
exit;