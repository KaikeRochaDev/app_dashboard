<?php

class Dashboard {
    public $data_inicio;
    public $data_fim;
    public $numeroVendas;
    public $totalVendas;

    public function __get($atr){
        return $this->$atr;
    }

    public function __set($atr, $valor){
        $this->$atr = $valor;
        return $this;
    }
}

class Conexao {
    private $host = 'localhost';
    private $dbname = 'dashboard';
    private $user = 'root';
    private $pass = '';

    public function conectar() {
        try {

            $conexao = new PDO(
                "mysql:host=$this->host;dbname=$this->dbname",
                "$this->user",
                "$this->pass"
            );

            $conexao->exec('set charset utf8');

            return $conexao;
        }catch(PDOException $e) {
            echo '<p>'. $e->getMessage().'</p>';
        }
    }
}

class Bd{
    private $conexao;
    private $dashboard;

    public function __construct(Conexao $conexao, Dashboard $dashboard){
        $this->conexao = $conexao->conectar();
        $this->dashboard = $dashboard;
    }

    public function getNumeroVendas() { 
        $query = "
            select
                COUNT(*) as numeroVendas
            from
                tb_vendas
            where
                data_venda between :data_inicio and :data_fim
        ";

        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':data_inicio', $this->dashboard->data_inicio);
        $stmt->bindValue(':data_fim', $this->dashboard->data_fim);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->numeroVendas;

    }

    public function getTotalVendas(){
        $query = '
        select 
            SUM(total) as total_vendas 
        from 
            tb_vendas 
        where 
            data_venda 
        between 
            :data_inicio 
        and 
            :data_fim';
        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
        $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->total_vendas;
    }
    public function getClientes($status) {
        $query = "
            select
                COUNT(*) as clientes
            from
                tb_clientes
            where
                cliente_ativo = $status
        ";

        $stmt = $this->conexao->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->clientes;
    }

    public function getTotalContato($tipo_contato) {
        $query = "
            select
                COUNT(*) as totalContato
            from
                tb_contatos
            where
                tipo_contato = $tipo_contato
        ";

        $stmt = $this->conexao->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->totalContato;
    }

    public function getTotalDespesas() {
        $query = "
            select
                SUM(total) as totalDespesas
            from
                tb_despesas 
            where
                data_despesa
            between
                :data_inicio
            and
                :data_fim
        ";

        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':data_inicio', $this->dashboard->data_inicio);
        $stmt->bindValue(':data_fim', $this->dashboard->data_fim);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->totalDespesas;
    }

}

$competencia = explode('-', $_GET['competencia']);
$mes = $competencia[1];
$ano = $competencia[0];
$dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

$dashboard = new Dashboard;
$dashboard->__set('data_inicio', "$ano-$mes-01")
          ->__set('data_fim', "$ano-$mes-$dias_mes");

$conexao = new Conexao;    

$bd = new Bd($conexao, $dashboard);

$dashboard->__set('numeroVendas', $bd->getNumeroVendas());
$dashboard->__set('totalVendas', $bd->getTotalVendas());
$dashboard->__set('clientesAtivos', $bd->getClientes('true'));
$dashboard->__set('clientesInativos', $bd->getClientes('false'));
$dashboard->__set('totalReclamacoes', $bd->getTotalContato(1));
$dashboard->__set('totalElogios', $bd->getTotalContato(2));
$dashboard->__set('totalSugestoes', $bd->getTotalContato(3));
$dashboard->__set('totalDespesas', $bd->getTotalDespesas());

echo json_encode($dashboard);

?>