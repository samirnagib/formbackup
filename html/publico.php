<?php
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Nova Solicitação de Backup</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
    <h2 class="mb-4">📝 Nova Solicitação de Backup</h2>

    <?php if ($msg === 'sucesso'): ?>
        <div class="alert alert-success">Solicitação enviada com sucesso!</div>
    <?php elseif ($msg === 'erro'): ?>
        <div class="alert alert-danger">Ocorreu um erro ao enviar sua solicitação. Tente novamente.</div>
    <?php elseif ($msg === 'campos'): ?>
        <div class="alert alert-warning">Preencha todos os campos obrigatórios.</div>
    <?php endif; ?>

    <form action="processa_solicitacao.php" method="post" class="row g-3">

        <div class="col-md-6">
            <label class="form-label">Nome do Requisitante *</label>
            <input type="text" name="NomeRequisitante" maxlength="150" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">E-mail do Requisitante *</label>
            <input type="email" name="EmailRequisitante" maxlength="150" class="form-control" required>
        </div>

        <div class="col-md-4">
            <label class="form-label">Centro de Custo *</label>
            <input type="text" name="CentroCusto" maxlength="50" class="form-control" required>
        </div>

        <div class="col-md-4">
            <label class="form-label">Site *</label>
            <select name="Site" class="form-select" required>
                <option value="">Selecione...</option>
                <option>OnPremisses</option>
                <option>AWS</option>
                <option>Azure</option>
                <option>GCP</option>
                <option>OCI</option>
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Projeto *</label>
            <input type="text" name="Projeto" maxlength="100" class="form-control" required>
        </div>

        <div class="col-md-4">
            <label class="form-label">Ambiente *</label>
            <select name="Ambiente" class="form-select" required>
                <option value="">Selecione...</option>
                <option>Producao</option>
                <option>Homologacao</option>
                <option>Desenvolvimento</option>
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Tipo de Backup *</label>
            <select name="TipoBackup" class="form-select" required>
                <option value="">Selecione...</option>
                <option>Arquivos</option>
                <option>BancoDadosOnline</option>
                <option>MaquinaVirtual</option>
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Recorrência *</label>
            <select name="Recorrencia" class="form-select" required>
                <option value="">Selecione...</option>
                <option>Simples</option>
                <option>Comum</option>
                <option>Completa</option>
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Armazenamento *</label>
            <select name="Armazenamento" class="form-select" required>
                <option value="">Selecione...</option>
                <option>Base</option>
                <option>Quente</option>
                <option>Morna</option>
                <option>Fria</option>
                <option>Arquivamento</option>
            </select>
        </div>

        <div class="col-md-8">
            <label class="form-label">Objeto Protegido *</label>
            <textarea name="ObjetoProtegido" class="form-control" rows="2" required></textarea>
        </div>

        <div class="col-md-6">
            <label class="form-label">Vcenter / Cluster</label>
            <input type="text" name="VcenterCluster" maxlength="100" class="form-control">
        </div>

        <div class="col-md-6">
            <label class="form-label">Caminho dos Arquivos</label>
            <textarea name="CaminhoArquivos" class="form-control" rows="2"></textarea>
        </div>

        <div class="col-md-4">
            <label class="form-label">Servidor BD</label>
            <input type="text" name="ServidorBD" maxlength="100" class="form-control">
        </div>

        <div class="col-md-4">
            <label class="form-label">Instância BD</label>
            <input type="text" name="InstanciaBD" maxlength="100" class="form-control">
        </div>

        <div class="col-md-4">
            <label class="form-label">Tipo Instância BD</label>
            <select name="TipoInstanciaBD" class="form-select">
                <option value="NAO_SE_APLICA">Selecione...</option>
                <option value="SingleInstance">Single Instance (MS SQL Server ou Oracle)</option>
                <option value="Oracle RAC">Oracle RAC</option>
                <option value="Cluster">Cluster Microsoft</option>
                <option value="AlwaysON">MS SQL Server AlwaysON</option>
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">Listener BD</label>
            <input type="text" name="ListenerBD" maxlength="100" class="form-control">
        </div>

        <div class="col-md-6">
            <label class="form-label">Informações Complementares</label>
            <textarea name="InfoComplementar" class="form-control" rows="2"></textarea>
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-success btn-lg">Enviar Solicitação</button>
            <a href="home.php" class="btn btn-secondary btn-lg">Voltar</a>
        </div>
    </form>
</div>

<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>