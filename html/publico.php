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
            <label class="form-label" style="margin-top: 5px; font-family: monospace; font-size: 11px">
        Informar o nome completo do responsável, quem será responsável pelas ações e custo desse backup
            </label>
        </div>

        <div class="col-md-6">
            <label class="form-label">E-mail do Requisitante *</label>
            <input type="email" name="EmailRequisitante" maxlength="150" class="form-control" required>
            <label class="form-label" style="margin-top: 5px; font-family: monospace; font-size: 11px">
        Informar o email do responsável pelo backup (pode ser o email do grupo)
          </label>
        </div>

        <div class="col-md-4">
            <label class="form-label">Centro de Custo *</label>
            <input type="text" name="CentroCusto" maxlength="50" class="form-control" required>
            <label class="form-label" style="margin-top: 5px; font-family: monospace; font-size: 11px">
        Informar o centro de custo para esse backup, é para efeitos de cobrança (Chargeback).
          </label>
        </div>

        <div class="col-md-4">
            <label class="form-label">Site *</label>
            <select name="Site" class="form-select" required>
                <option value="">Selecione...</option>
                <option value="OnPremisses">Servidor Local (On-Premisses)</option>
                <option value="AWS">Amazon Web Services (AWS)</option>
                <option value="Azure">Microsoft Azure</option>
                <option value="GCP">Google Cloud Platform (GCP)</option>
                <option value="OCI">Oracle Cloud Infrastructure (OCI)</option>
            </select>
        <label class="form-label" style="margin-top: 5px; font-family: monospace; font-size: 11px">
        Selecionar qual site está o servidor, banco de dados, máquina virtual, ou fileserver a ser protegido
        </label>
        </div>

        <div class="col-md-4">
            <label class="form-label">Projeto *</label>
            <input type="text" name="Projeto" maxlength="100" class="form-control" required>
            <label class="form-label" style="margin-top: 5px; font-family: monospace; font-size: 11px">
        Caso o site seja On Premisses, o campo é opcional, mas nas clouds se faz necessário informar:<br>
        -> Subscription [Azure]<br>
        -> ID projeto [GCP e AWS]<br>
        -> Compartment [OCI]<br>
            </label>
        </div>

        <div class="col-md-4">
            <label class="form-label">Ambiente *</label>
            <select name="Ambiente" class="form-select" required>
                <option value="">Selecione...</option>
                <option value="Producao">Produção</option>
                <option value="Homologacao">Homologação</option>
                <option value="Desenvolvimento">Desenvolvimento</option>
            </select>
            <label class="form-label" style="margin-top: 5px; font-family: monospace; font-size: 11px">
                Selecionar qual ambiente está servidor, banco de dados, máquina virtual, ou fileserver a ser protegido.
            </label>
        </div>

        <div class="col-md-4">
            <label class="form-label">Tipo de Backup *</label>
            <select name="TipoBackup" class="form-select" required>
                <option value="">Selecione...</option>
                <option value="Arquivos">Arquivos</option>
                <option value="BancoDadosOnline">Banco Dados Online</option>
                <option value="MaquinaVirtual">Máquina Virtual</option>
            </select>
            <label class="form-label" style="margin-top: 5px; font-family: monospace; font-size: 11px">
                Arquivos (Fileserver, DBAAS, Logs)<br>
                Banco de Dados Online (Oracle, MS SQL Server)<br>
                Máquina Virtual (VMWare, XCP-ng, Azure VM, e outros)
            </label>
        </div>

        <div class="col-md-4">
            <label class="form-label">Recorrência *</label>
            <select name="Recorrencia" class="form-select" required>
                <option value="">Selecione...</option>
                <option>Simples</option>
                <option>Comum</option>
                <option>Completa</option>
            </select>
            <label class="form-label" style="margin-top: 5px; font-family: monospace; font-size: 11px">
                <b>Simples:</b> Backup semanal full (Padrão para Máquinas Virtuais, Fileservers de binários ou arquivos de configuração, Banco de Dados de Desenvolvimento/Homologação)<br>
                <b>Comum:</b> Backup Semanal Full, Incremental Diário (Banco de dados, Fileservers)<br>
                <b>Completa:</b> Backup Mensal Full, Semanal Full e Incremental diário (Indicado para Banco de Dados, Fileservers, em ambiente de produção)
            </label>
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
        <div class="col-md-4">
            <div class="container-fluid">
                <label class="form-label" style="margin-top: 5px;">Detalhes do Armazenamento</label>
                <table class="table table-bordered table-sm" style="margin-top: 5px; font-family: monospace; font-size: 11px; width: 100%;">
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
                    <td><b><i>$$$$</i></b></td>
                    </tr>
                    <tr>
                    <td>Quente</td>
                    <td>Cloud</td>
                    <td>7 dias</td>
                    <td>30 dias</td>
                    <td><b><i>$$$$$</i></b></td>
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

        <div class="col-md-8">
            <label class="form-label">Qual servidor, Conta de Armazenamento, Banco de Dados a ser protegido? *</label>
            <textarea name="ObjetoProtegido" class="form-control" rows="2" required></textarea>
            <label class="form-label" style="margin-top: 5px; font-family: monospace; font-size: 11px">
        <b>Backup do tipo Arquivos:</b> Informar o hostname do servidor, ou conta de armazenamento (cloud).<br>
        <b>Backup do tipo Máquina Virtual:</b> Informar o hypervisor (VMWare, Hyper-V, XCP-ng).<br>
        <b>Backup do tipo Banco de Dados:</b> Informar o SGBD (Oracle, Oracle RAC, SQL Server).
            </label>
        </div>

        <div class="col-md-6">
            <label class="form-label">Vcenter / Cluster</label>
            <input type="text" name="VcenterCluster" maxlength="100" class="form-control">
            <label class="form-label" style="margin-top: 5px; font-family: monospace; font-size: 11px">
        Informar o vCenter ou Cluster, se for backup de Máquina Virtual. Caso contrário, deixar em branco.
            </label>
        </div>

        <div class="col-md-6">
            <label class="form-label">Caminho dos Arquivos</label>
            <textarea name="CaminhoArquivos" class="form-control" rows="2"></textarea>
            <label class="form-label" style="margin-top: 5px; font-family: monospace; font-size: 11px">
                Em caso de backup do tipo Arquivos, informar o caminho completo dos arquivos a serem protegidos, ou, se for cloud storage, informar o container/bucket. Caso contrário, deixar em branco.
            </label>
        </div>

        <div class="col-md-4">
            <label class="form-label">Servidor de Banco de Dados</label>
            <input type="text" name="ServidorBD" maxlength="100" class="form-control">
            <label class="form-label" style="margin-top: 5px; font-family: monospace; font-size: 11px">
                Informar os IPs/hostnames dos servidores envolvidos no backup, caso seja um backup de dados (Oracle ou Microsoft SQL Server).
            </label>
        </div>

        <div class="col-md-4">
            <label class="form-label">Instância Bando de Dados</label>
            <input type="text" name="InstanciaBD" maxlength="100" class="form-control">
            <label class="form-label" style="margin-top: 5px; font-family: monospace; font-size: 11px">
            Informar os IPs/hostnames das instâncias (listeners) de banco de dados, caso seja um backup de dados Oracle ou Microsoft SQL Server.
            </label>
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
            <label class="form-label" style="margin-top: 5px; font-family: monospace; font-size: 11px">
        <b>Single Instance:</b> Instância de banco de dados única.<br>
        <b>Oracle RAC:</b> Instância Oracle RAC.<br>
        <b>Cluster:</b> Instância Microsoft SQL Server em Cluster.<br>
        <b>SQL AlwaysON:</b> Instância SQL AlwaysON.
            </label>
        </div>

        <div class="col-md-6">
            <label class="form-label">Listener BD</label>
            <input type="text" name="ListenerBD" maxlength="100" class="form-control">
            <label class="form-label" style="margin-top: 5px; font-family: monospace; font-size: 11px">
        Informar o IP/Hostname/DNS do Listener da instância (endereço por onde o backup será realizado).
          </label>
        </div>

        <div class="col-md-6">
            <label class="form-label">Informações Complementares</label>
            <textarea name="InfoComplementar" class="form-control" rows="2"></textarea>
            <label class="form-label" style="margin-top: 5px; font-family: monospace; font-size: 11px">
        Colocar nesse campo qualquer informação útil que não tenha sido tratada/informada nos campos anteriores.
          </label>
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