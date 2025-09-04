<?php
require_once '/var/secure/config.php';
require '/var/www/html/formbkp/vendor/autoload.php';
require '/var/secure/funcao.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function redir($msg) {
    header("Location: publico.php?msg={$msg}");
    exit;
}

$camposObrigatorios = [
    'NomeRequisitante', 'EmailRequisitante', 'CentroCusto', 'Site',
    'Projeto', 'Ambiente', 'TipoBackup', 'Recorrencia',
    'Armazenamento', 'ObjetoProtegido'
];

foreach ($camposObrigatorios as $campo) {
    if (empty($_POST[$campo])) {
        redir('campos');
    }
}

$dados = [];
foreach ($_POST as $k => $v) {
    $dados[$k] = trim($v);
}

try {
    // Inserir no banco
    $sql = "INSERT INTO solicitacoes (
                NomeRequisitante, EmailRequisitante, CentroCusto, Site, Projeto,
                Ambiente, TipoBackup, Recorrencia, Armazenamento, ObjetoProtegido,
                VcenterCluster, CaminhoArquivos, ServidorBD, InstanciaBD, TipoInstanciaBD,
                ListenerBD, InfoComplementar, Status, DataSolicitacao
            ) VALUES (
                :NomeRequisitante, :EmailRequisitante, :CentroCusto, :Site, :Projeto,
                :Ambiente, :TipoBackup, :Recorrencia, :Armazenamento, :ObjetoProtegido,
                :VcenterCluster, :CaminhoArquivos, :ServidorBD, :InstanciaBD, :TipoInstanciaBD,
                :ListenerBD, :InfoComplementar, 'Aberto', NOW()
            )";
    $stmt = $conn->prepare($sql);
    foreach ($dados as $k => $v) {
        $stmt->bindValue(":{$k}", $v);
    }
    $stmt->execute();

    $idSolicitacao = $conn->lastInsertId();
  
    // E-mail para requisitante
    $msgRequisitante = "Olá {$dados['NomeRequisitante']},\n\n".
                       "Sua solicitação de backup (#{$idSolicitacao}) foi registrada com sucesso e está com status Aberto.\n".
                       "Em breve nossa equipe entrará em contato.\n\n".
                       "Atenciosamente,\n Equipe de Backup";
    enviarEmail($dados['EmailRequisitante'], $dados['NomeRequisitante'], "Confirmação de Solicitação de Backup", $msgRequisitante);

    // E-mail para equipe interna
    $msgInterno = "Nova solicitação de backup registrada:\n\n".
                  "ID: {$idSolicitacao}\n".
                  "Requisitante: {$dados['NomeRequisitante']} ({$dados['EmailRequisitante']})\n".
                  "Projeto: {$dados['Projeto']}\n".
                  "Ambiente: {$dados['Ambiente']}\n".
                  "Tipo de Backup: {$dados['TipoBackup']}\n".
                  "Recorrência: {$dados['Recorrencia']}\n".
                  "Armazenamento: {$dados['Armazenamento']}\n".
                  "Objeto Protegido: {$dados['ObjetoProtegido']}\n\n".
                  "Acesse o painel para mais detalhes.";
    enviarEmail('samir.nagib@gmail.com', 'Equipe de Backup', "Nova Solicitação de Backup #{$idSolicitacao}", $msgInterno);

    // Registrar log de auditoria

    registra_log($conn, $idSolicitacao, 'Nova solicitação registrada. Submetida por ' . $dados['NomeRequisitante']);


    redir('sucesso');

} catch (Exception $e) {
    error_log("Erro ao processar solicitação: " . $e->getMessage());
    redir('erro');
}
?>