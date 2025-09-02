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

     <!-- Data da Requisieção -->
    <div class="mb-3">
        <label class="form-label">Data da Solicitação</label>
        <input type="text" name="DataSolicitacao" class="form-control" 
         value="<?php echo date('d/m/Y H:i'); ?>" readonly 
         data-bs-toggle="tooltip" title="Data e hora em que a solicitação está sendo registrada.">
    </div>


    <!-- NomeRequisitante -->
    <div class="mb-3">
      <label class="form-label">Solicitante</label>
      <input type="text" name="NomeRequisitante" class="form-control" required data-bs-toggle="tooltip" title="Informar o nome completo do responsável, quem irar ser responsável pelas ações e custo desse backup">
    </div>

    <!-- EmailRequisitante -->
    <div class="mb-3">
      <label class="form-label">E-mail</label>
      <input type="email" name="EmailRequisitante" class="form-control" required data-bs-toggle="tooltip" title="Informar o email responsável, pode ser o email da equipe.">
    </div>

    <!-- CentroCusto -->
    <div class="mb-3">
      <label class="form-label">Centro de Custo</label>
      <input type="text" name="CentroCusto" class="form-control" required data-bs-toggle="tooltip" title="Informe o centro de custo responsável, para controle financeiro.">
    </div>

    <!-- Site -->
     <div class="mb-3">
      <label class="form-label">Site</label>
      <select name="Site" class="form-select" required data-bs-toggle="tooltip" title="Selecione o ambiente de hospedagem.">
        <option value="">Selecione</option>
        <option value="OnPremisses">Servidor Local (On-Premisses)</option>
        <option value="AWS">Amazon Web Services (AWS)</option>
        <option value="Azure">Microsoft Azure</option>
        <option value="GCP">Google Cloud Platform (GCP)</option>
        <option value="OCI">Oracle Cloud Infrastructure (OCI)</option>
      </select>
    </div>

    <!-- Projeto -->
    <div class="mb-3">
      <label class="form-label">Projeto</label>
      <input type="text" name="Projeto" class="form-control" required data-bs-toggle="tooltip" title="Informe o nome do projeto/compartment/subscription relacionado ao backup.">
    </div>

    <!-- Ambiente -->
    <div class="mb-3">
      <label class="form-label">Ambiente</label>
      <select name="Ambiente" class="form-select" required data-bs-toggle="tooltip" title="Escolha o tipo de ambiente.">
        <option value="">Selecione</option>
        <option value="Producao">Produ&ccedil;&atilde;o</option>
        <option value="Homologacao">Homologa&ccedil;&atilde;o</option>
        <option value="Desenvolvimento">Desenvolvimento</option>
      </select>
    </div>

    <!-- TipoBackup -->
    <div class="mb-3">
      <label class="form-label">Tipo de Backup</label>
      <select id="TipoBackup" name="TipoBackup" class="form-select" required onfocus="this.title='Escolha o tipo de backup desejado.'">
        <option value="">Selecione</option>
        <option>Arquivos</option>
        <option>BancoDadosOnline</option>
        <option>MaquinaVirtual</option>
      </select>
      <label class="form-label" id="dica-tipobkp" style="margin-top: 10px; font-family: monospace; font-size: 12px">
        Arquivos (Fileserver, DBAAS, Logs)<br>
        Banco de Dados Online (Oracle, MS SQL Server)<br>
        Máquina Virtual (VMWare, XCP-ng, Azure VM, e outros )
      </label>
    </div>

    <!-- Recorrencia -->
    <div class="mb-3">
      <label class="form-label">Recorrência</label>
      <select name="Recorrencia" class="form-select" required data-bs-toggle="tooltip" title="Defina a frequência do backup.">
        <option value="">Selecione</option>
        <option>Simples</option>
        <option>Comum</option>
        <option>Completa</option>
      </select>
      <label class="form-label" style="margin-top: 10px; font-family: monospace; font-size: 11px">
        <b>Simples</b>: Backup semanal full (Padrão para Máquinas Virtuais, Fileservers de binários ou arquivos de configuração, Banco de Dados de Desenvolvimento/Homologação)<br>
        Comum: Backup Semanal Full, Incremental Diario (Banco de dados, Fileservers)<br>
        Completa: Backup Mensal Full, Semanal Full e Incremental diário (Indicado para Banco de Dados, Fileservers,em ambiente de produção)"

      </label>
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
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
      new bootstrap.Tooltip(tooltipTriggerEl);
    });
  });
</script>
<script>
  function mostrarDica() {
    const selectElement = document.getElementById('TipoBackup');
    const dicaElement = document.getElementById('dica-tipobkp');
    const selectedValue = selectElement.value;
    let dicaTexto = '';

    switch(selectedValue) {
        case 'Arquivos':
            dicaTexto = 'A banana é uma fruta rica em potássio.';
            break;
        case 'BancoDadosOnline':
            dicaTexto = 'A maçã ajuda na digestão e na saúde bucal.';
            break;
        case 'MaquinaVirtual':
            dicaTexto = 'A uva é uma ótima fonte de antioxidantes.';
            break;
        default:
            dicaTexto = '';
    }

    dicaElement.textContent = dicaTexto;
  }
</script>

<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
