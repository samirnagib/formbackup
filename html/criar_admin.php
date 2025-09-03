<?php
require_once '/var/secure/config.php';

$usuario = 'admin';
$nome    = 'Administrador';
$email   = 'admin@seudominio.com';
$senha   = password_hash('senha123', PASSWORD_DEFAULT);

$sql = "INSERT INTO usuarios (usuario, nome, email, senha_hash)
        VALUES (:usuario, :nome, :email, :senha)";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':usuario', $usuario);
$stmt->bindValue(':nome', $nome);
$stmt->bindValue(':email', $email);
$stmt->bindValue(':senha', $senha);
$stmt->execute();

echo "Usuário admin criado com sucesso!";
header('Location: login.php');
exit;