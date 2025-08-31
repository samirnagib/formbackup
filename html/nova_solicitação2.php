<?php require_once '/var/secure/config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Nova Solicitação</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
  <h2>📝 Nova Solicitação de Backup</h2>
  <form action="processa_solicitacao.php" method="POST" class="mt-3">

    <!-- NomeRequisitante -->
    <div class="mb-3">
      <label class="form-label">Solicitante</label>
      <input type="text" name="NomeRequisitante" class="form-control" required onfocus="this.title='Informe o nome completo do requisitante.'">
    </div>

    <!-- EmailRequisitante -->
    <div class="mb-3">
      <label class="form-label">E-mail</label>
      <input type="email" name="EmailRequisitante" class="form-control" required onfocus="this.title='Digite o e-mail corporativo do requisitante.'">
    </div>

    <!-- CentroCusto -->
    <div class="mb-3">
      <label class="form-label">Centro de Custo</label>
      <input type="text" name="CentroCusto" class="form-control" required onfocus="this.title='Informe o centro de custo responsável.'">
    </div>

    <!-- Site -->
    <div class="mb-3">
      <label class="form-label">Site</label>
      <select name="Site" class="form-select" required onfocus="this.title='Selecione o ambiente onde o backup será realizado.'">
        <option value="">Selecione</option>
        <option>OnPremisses</option>
        <option>AWS</option>
        <option>Azure</option>
        <option>GCP</option>
        <option>OCI</option>
      </select>
    </div>

    <!-- Projeto -->
    <div class="mb-3">
      <label class="form-label">Projeto</label>
      <input type="text" name="Projeto" class="form-control" required onfocus="this.title='Informe o nome do projeto relacionado ao backup.'">
    </div>

    <!-- Ambiente -->
    <div class="mb-3">
      <label class="form-label">Ambiente</label>
      <select name="Ambiente" class="form-select" required onfocus="this.title='Escolha o tipo de ambiente.'">
        <option value="">Selecione</option>
        <option>Producao</option>
        <option>Homologacao</option>
        <option>Desenvolvimento</option>
      </select>
    </div>

    <!-- TipoBackup -->
    <div class="mb-3">
      <label class="form-label">Tipo de Backup</label>
      <select name="TipoBackup" class="form-select" required onfocus="this.title='Escolha o tipo de backup desejado.'">
        <option value="">Selecione</option>
        <option>Arquivos</option>
        <option>BancoDadosOnline</option>
        <option>MaquinaVirtual</option>
      </select>
    </div>

    <!-- Recorrencia -->
    <div class="mb-3">
      <label class="form-label">Recorrência</label>
      <select name="Recorrencia" class="form-select" required onfocus="this.title='Defina a frequência do backup.'">
        <option value="">Selecione</option>
        <option>Simples</option>
        <option>Comum</option>
        <option>Completa</option>
      </select>
    </div>

    <!-- Armazenamento -->
    <div class="mb-3">
      <label class="form-label">Armazenamento</label>
      <select name="Armazenamento" class="form-select" required onfocus="this.title='Escolha a camada de armazenamento.'">
        <option value="">Selecione</option>
        <option>Base</option>
        <option>Quente</option>
        <option>Morna</option>
        <option>Fria</option>
        <option>Arquivamento</option>
      </select>
    </div>

    <!-- ObjetoProtegido -->
    <div class="mb-3">
      <label class="form-label">Objeto Protegido</label>
      <textarea name="ObjetoProtegido" class="form-control" rows="3" required onfocus="this.title='Descreva o objeto que será protegido pelo backup.'"></textarea>
    </div>

    <!-- VcenterCluster -->
    <div class="mb-3">
      <label class="form-label">vCenter/Cluster</label>
      <input type="text" name="VcenterCluster" class="form-control" onfocus="this.title='Informe o nome do vCenter ou cluster, se aplicável.'">
    </div>

    <!-- CaminhoArquivos -->
    <div class="mb-3">
      <label class="form-label">Caminho dos Arquivos</label>
      <textarea name="CaminhoArquivos" class="form-control" rows="2" onfocus="this.title='Informe o caminho completo dos arquivos a serem protegidos.'"></textarea>
    </div>

    <!-- ServidorBD -->
    <div class="mb-3">
      <label class="form-label">Servidor de Banco de Dados</label>
      <input type="text" name="ServidorBD" class="form-control" onfocus="this.title='Informe o nome do servidor de banco de dados.'">
    </div>

    <!-- InstanciaBD -->
    <div class="mb-3">
      <label class="form-label">Instância do BD</label>
      <input type="text" name="InstanciaBD" class="form-control" onfocus="this.title='Informe a instância do banco de dados.'">
    </div>

    <!-- TipoInstanciaBD -->
    <div class="mb-3">
      <label class="form-label">Tipo de Instância BD</label>
      <select name="TipoInstanciaBD" class="form-select" onfocus="this.title='Escolha o tipo de instância do banco de dados.'">
        <option value="">Selecione</option>
        <option>SingleInstance</option>
        <option>Oracle RAC</option>
        <option>Cluster</option>
        <option>AlwaysON</option>
      </select>
    </div>

    <!-- ListenerBD -->
    <div class="mb-3">
      <label class="form-label">Listener BD</label>
      <input type="text" name="ListenerBD" class="form-control" onfocus="this.title='Informe o listener do banco de dados, se houver.'">
    </div>

    <!-- InfoComplementar -->
    <div class="mb-3">
      <label class="form-label">Informações Complementares</label>
      <textarea name="InfoComplementar" class="form-control" rows="3" onfocus="this.title='Adicione qualquer informação adicional relevante.'"></textarea>
    </div>

    <!-- Status -->
    <div class="mb-3">
      <label class="form-label">Status</label>
      <select name="Status" class="form-select" required onfocus="this.title='Defina o status inicial da solicitação.'">
        <option value="Aberto" selected>Aberto</option>
        <option>EmAndamento</option>
        <option>Concluido</option>
        <option>Cancelado</option>
      </select>
    </div>

    <button type="submit" class="btn btn-success">Enviar Solicitação</button>
    <a href="index.php" class="btn btn-secondary">Voltar</a>
  </form>
</div>

<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
