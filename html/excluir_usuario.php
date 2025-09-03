<?php
require_once '/var/secure/auth.php';
require_once '/var/secure/config.php';

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
}

header('Location: listar_usuarios.php');
exit;