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
            SELECT
                COUNT(*) as numeroVendas
            FROM
                tb_vendas
            WHERE
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
            tb_vendas where data_venda between :data_inicio and :data_fim';
        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
        $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->total_vendas;
    }
}

$dashboard = new Dashboard();

$conexao = new Conexao();

$dashboard->__set('data_inicio', '2023-10-01');
$dashboard->__set('data_fim', '2023-10-31');

$bd = new Bd($conexao, $dashboard);

$dashboard->__set('numeroVendas', $bd->getNumeroVendas());
print_r($dashboard);

print_r($bd->getTotalVendas());

?>