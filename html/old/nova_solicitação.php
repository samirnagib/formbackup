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
      <label class="form-label" id="dica-tipobkp" style="margin-top: 10px; font-family: monospace; font-size: 12px">Informar o nome completo do responsável, quem será responsável pelas ações e custo desse backup</label>
    </div>

    <!-- EmailRequisitante -->
    <div class="mb-3">
      <label class="form-label">E-mail</label>
      <input type="email" name="EmailRequisitante" class="form-control" required data-bs-toggle="tooltip" title="Informar o email responsável, pode ser o email da equipe.">
      <label class="form-label" id="dica-tipobkp" style="margin-top: 10px; font-family: monospace; font-size: 12px">
        Informar o email do responsável pelo backup (pode ser o email do grupo)
      </label>

    </div>

    <!-- CentroCusto -->
    <div class="mb-3">
      <label class="form-label">Centro de Custo</label>
      <input type="text" name="CentroCusto" class="form-control" required data-bs-toggle="tooltip" title="Informe o centro de custo responsável, para controle financeiro.">
      <label class="form-label" id="dica-tipobkp" style="margin-top: 10px; font-family: monospace; font-size: 12px">
        Informar o centro de custo para esse backup, é para efeitos de cobrança (Chargeback).
      </label>

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
      <label class="form-label" id="dica-tipobkp" style="margin-top: 10px; font-family: monospace; font-size: 12px">
        Selecionar qual site está o servidor, banco de dados, maquina virtual, ou fileserver a ser protegido
      </label>

    </div>

    <!-- Projeto -->
    <div class="mb-3">
      <label class="form-label">Projeto</label>
      <input type="text" name="Projeto" class="form-control" required data-bs-toggle="tooltip" title="Informe o nome do projeto/compartment/subscription relacionado ao backup.">
      <label class="form-label" id="dica-tipobkp" style="margin-top: 10px; font-family: monospace; font-size: 12px">
        Caso o site seja On Premisses, o campo é opcional, mas nas clouds se faz necessario informar:<br> 
        -> Subscription [Azure]<br>
        -> ID projeto [GCP e AWS]<br>
        -> Compartment [OCI]<br>
      </label>
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
      <label class="form-label" id="dica-tipobkp" style="margin-top: 10px; font-family: monospace; font-size: 12px">
        Selecionar qual ambiente está servidor, banco de dados, maquina virtual, ou fileserver a ser protegido.
      </label>
    </div>

    <!-- TipoBackup -->
    <div class="mb-3">
      <label class="form-label">Tipo de Backup</label>
      <select id="TipoBackup" name="TipoBackup" class="form-select" required onfocus="this.title='Escolha o tipo de backup desejado.'">
        <option value="">Selecione</option>
        <option value="Arquivos">Arquivos</option>
        <option value="BancoDadosOnline">Banco Dados Online</option>
        <option value="MaquinaVirtual">Maquina Virtual</option>
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
        <b>Comum</b>: Backup Semanal Full, Incremental Diario (Banco de dados, Fileservers)<br>
        <b>Completa</b>: Backup Mensal Full, Semanal Full e Incremental diário (Indicado para Banco de Dados, Fileservers,em ambiente de produção)"
      </label>
    </div>

    <!-- Armazenamento -->
    <div class="mb-3">
      <label class="form-label">Armazenamento</label>
      <select name="Armazenamento" class="form-select" required data-bs-toggle="tooltip" title="Escolha a camada de armazenamento.">
        <option value="">Selecione</option>
        <option>Base</option>
        <option>Quente</option>
        <option>Morna</option>
        <option>Fria</option>
        <option>Arquivamento</option>
      </select>
      <div class="container-fluid">
        <label for="descricao-tabela">Detalhes do Armazenamento</label>
        <table class="table table-bordered table-sm" id="descricao-tabela" style="margin-top: 10px; font-family: monospace; font-size: 11px; width: 50%;">
          <colgroup>
            <col style="width: 10%;">
            <col style="width: 15%;">
            <col style="width: 15%;">
            <col style="width: 15%;">
            <col style="width: 10%;">
          </colgroup>
          <thead class="table-light">
            <tr>
              <th>Tipo</th>
              <th>Localização</th>
              <th>Retenção Mínima</th>
              <th>Retenção Máxima</th>
              <th>Custo</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Base</td>
              <td>OnPremisses</td>
              <td>7 dias</td>
              <td>30 dias</td>
              <td><b><i>$$$$$</i></b></td>
            </tr>
            <tr>
              <td>Quente</td>
              <td>Cloud</td>
              <td>7 dias</td>
              <td>30 dias</td>
              <td><b><i>$$$$</i></b></td>
            </tr>
            <tr>
              <td>Morna</td>
              <td>Cloud</td>
              <td>30/90 dias</td>
              <td>Não Há</td>
              <td><b><i>$$$</i></b></td>
            </tr>
            <tr>
              <td>Fria</td>
              <td>Cloud</td>
              <td>90/180 dias</td>
              <td>Não Há</td>
              <td><b><i>$$</i></b></td>
            </tr>
            <tr>
              <td>Arquivamento</td>
              <td>Cloud</td>
              <td>365 dias</td>
              <td>365 dias</td>
              <td><b><i>$</i></b></td>
            </tr>
          </tbody>
        </table>  
      </div>
    </div>
    <!-- ObjetoProtegido -->
    <div class="mb-3">
      <label class="form-label">Qual servidor, Conta de Armazenamento, Banco de Dados a ser protegido?</label>
      <textarea name="ObjetoProtegido" class="form-control" rows="3" required data-bs-toggle="tooltip" title="Descreva o objeto que será protegido pelo backup."></textarea>
        <label class="form-label" style="margin-top: 10px; font-family: monospace; font-size: 11px">
          <b>Backup do tipo Arquivos</b>: Informar o hostname do servidor, ou conta de armazenamento (<i>cloud</i>);<br>
          <b>Backup do tipo Máquina Virtual</b>: hypervisor (<i>VMWare, Hyper-V, XCP-ng</i>);<br>
          <b>Backup do tipo Bando de Dados</b>: Qual SGBD (<i>Oracle, Oracle RAC, SQL Server</i>)<br>
      </label>

    </div>

    <!-- VcenterCluster -->
    <div class="mb-3">
      <label class="form-label">vCenter/Cluster</label>
      <input type="text" name="VcenterCluster" class="form-control"  data-bs-toggle="tooltip" title="Informe o nome do vCenter ou cluster, se aplicável.">
        <label class="form-label" style="margin-top: 10px; font-family: monospace; font-size: 11px">
          Informar o vCenter ou Cluster, se for backup de Máquina Virtual.<br> Caso contrário, deixar em branco.<br>
      </label>

    </div>

    <!-- CaminhoArquivos -->
    <div class="mb-3">
      <label class="form-label">Caminho dos Arquivos</label>
      <textarea name="CaminhoArquivos" class="form-control" rows="2" data-bs-toggle="tooltip" title="Informe o caminho completo dos arquivos a serem protegidos.'"></textarea>
        <label class="form-label" style="margin-top: 10px; font-family: monospace; font-size: 11px">
          Em caso de backup do tipo Arquivos, informar o caminho completo dos arquivos a serem protegidos, ou caso for uma<br>
          cloud storage, informar o container/bucket.<br>
          Caso contrário, deixar em branco.<br>
      </label>

    </div>

    <!-- ServidorBD -->
    <div class="mb-3">
      <label class="form-label">Servidor de Banco de Dados</label>
      <input type="text" name="ServidorBD" class="form-control" data-bs-toggle="tooltip" title="Informe o nome do servidor de banco de dados.">
      <label class="form-label" style="margin-top: 10px; font-family: monospace; font-size: 11px">
          Informar os Ips/hostnames dos servidores envolvidos no backup, caso seja um backup de dados, Oracle ou Microsoft SQL Server.<br>
      </label>
    </div>

    <!-- InstanciaBD -->
    <div class="mb-3">
      <label class="form-label">Instância do BD</label>
      <input type="text" name="InstanciaBD" class="form-control" data-bs-toggle="tooltip" title=Informe a instância do banco de dados.">
      <label class="form-label" style="margin-top: 10px; font-family: monospace; font-size: 11px">
          Informar os Ips/hostnames das instancias (listeners) de banco de dados, caso seja um backup de dados Oracle ou Microsoft SQL Server.<br>
      </label>

    </div>

    <!-- TipoInstanciaBD -->
    <div class="mb-3">
      <label class="form-label">Tipo de Instância BD</label>
      <select name="TipoInstanciaBD" class="form-select" data-bs-toggle="tooltip" title="Escolha o tipo de instância do banco de dados.">
        <option value="">Selecione</option>
        <option value="SingleInstance">Single Instance</option>
        <option value="Oracle RAC">Oracle RAC</option>
        <option value="Cluster">Cluster</option>
        <option value="AlwaysON">SQL AlwaysON</option>
      </select>
        <label class="form-label" style="margin-top: 10px; font-family: monospace; font-size: 11px">
          <b>Single Instance</b>: Instancia de banco de dados única.<br>
          <b>Oracle RAC</b>: Instância Oracle RAC.<br>
          <b>Cluster</b>: Instância Microsoft SQL Server em  Cluster.<br>
          <b>SQL AlwaysON</b>: Instância SQL AlwaysON.<br> (<i>cloud</i>);<br>
          <b>Backup do tipo Máquina Virtual</b>: hypervisor (<i>VMWare, Hyper-V, XCP-ng</i>);<br>
          <b>Backup do tipo Bando de Dados</b>: Qual SGBD (<i>Oracle, Oracle RAC, SQL Server</i>)<br>
      </label>
    </div>

    <!-- ListenerBD -->
    <div class="mb-3">
      <label class="form-label">Listener BD</label>
      <input type="text" name="ListenerBD" class="form-control" data-bs-toggle="tooltip" title="Informe o listener do banco de dados, se houver.">
      <label class="form-label" id="dica-tipobkp" style="margin-top: 10px; font-family: monospace; font-size: 12px">
        Informar o IP/Hostname/DNS do Listener da instancia. (Endereço por onde o backup será realizado)
      </label>
    </div>

    <!-- InfoComplementar -->
    <div class="mb-3">
      <label class="form-label">Informações Complementares</label>
      <textarea name="InfoComplementar" class="form-control" rows="3" data-bs-toggle="tooltip" title="Adicione qualquer informação adicional relevante."></textarea>
      <label class="form-label" id="dica-tipobkp" style="margin-top: 10px; font-family: monospace; font-size: 12px">
        Colocar nesse campo qualquer informação útil que não tenha sido tradada/informada nos campos anteriores.
      </label>
    </div>

    <!-- Status -->
    <select class="form-select" disabled>
      <option value="Aberto" selected>Aberto</option>
      <option value="EmAndamento">Em Andamento</option>
      <option value="Concluido">Concluido</option>
      <option value="Cancelado">Cancelado</option>
    </select>
    <input type="hidden" name="Status" value="Aberto">
    <div class="mb-3">
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
<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
