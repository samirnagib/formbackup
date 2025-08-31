CREATE DATABASE db_form_backup;
USE db_form_backup;

CREATE TABLE SolicitacoesBackup (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    DataSolicitacao DATETIME NOT NULL,
    NomeRequisitante VARCHAR(150) NOT NULL,
    EmailRequisitante VARCHAR(150) NOT NULL,
    CentroCusto VARCHAR(50) NOT NULL,
    Site ENUM('OnPremisses','AWS','Azure','GCP','OCI') NOT NULL,
    Projeto VARCHAR(100) NOT NULL,
    Ambiente ENUM('Producao','Homologacao','Desenvolvimento') NOT NULL,
    TipoBackup ENUM('Arquivos','BancoDadosOnline','MaquinaVirtual') NOT NULL,
    Recorrencia ENUM('Simples','Comum','Completa') NOT NULL,
    Armazenamento ENUM('Base','Quente','Morna','Fria','Arquivamento') NOT NULL,
    ObjetoProtegido TEXT NOT NULL,
    VcenterCluster VARCHAR(100),
    CaminhoArquivos TEXT,
    ServidorBD VARCHAR(100),
    InstanciaBD VARCHAR(100),
    TipoInstanciaBD ENUM('SingleInstance','Oracle RAC','Cluster','AlwaysON'),
    ListenerBD VARCHAR(100),
    InfoComplementar TEXT,
    Status ENUM('Aberto','EmAndamento','Concluido','Cancelado') NOT NULL DEFAULT 'Aberto'
);

CREATE USER 'formbackupuser'@'10.44.0.28' IDENTIFIED BY 'w7F+ssNx7-Tp~NcD';
GRANT ALL PRIVILEGES ON db_form_backup.* TO 'formbackupuser'@'10.44.0.28';
FLUSH PRIVILEGES;

