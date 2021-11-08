<?php

class Comanda
{

    public $codigo;
    public $prs_mozo_legajo;
    public $mes_codigo;
    public $nombre_cliente;
    public $estado;
    
    public function crearComanda()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO comanda_com (codigo, prs_mozo_legajo, mes_codigo, nombre_cliente) VALUES (:codigo, :prs_mozo_legajo, :mes_codigo, :nombre_cliente)");
        
        $consulta->bindValue(':codigo', $this->codigo, PDO::PARAM_STR);
        $consulta->bindValue(':prs_mozo_legajo', $this->prs_mozo_legajo, PDO::PARAM_STR);
        $consulta->bindValue(':mes_codigo', $this->mes_codigo, PDO::PARAM_INT);
        $consulta->bindValue(':nombre_cliente', $this->nombre_cliente, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT codigo, prs_mozo_legajo, mes_codigo, nombre_cliente, estado FROM comanda_com");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Comanda');
    }

    // public static function obtenerPorCodigo($codigo)
    // {
    //     $objAccesoDatos = AccesoDatos::obtenerInstancia();
    //     $consulta = $objAccesoDatos->prepararConsulta("SELECT codigo, estado FROM mesa_mes WHERE codigo = :codigo");
    //     $consulta->bindValue(':codigo', $codigo, PDO::PARAM_INT);
    //     $consulta->execute();

    //     return $consulta->fetchObject('Mesa');
    // }

    // public static function obtenerPorEstado($estado)
    // {
    //     $objAccesoDatos = AccesoDatos::obtenerInstancia();
    //     $consulta = $objAccesoDatos->prepararConsulta("SELECT codigo, estado FROM mesa_mes WHERE estado = :estado");
    //     $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
    //     $consulta->execute();

    //     return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    // }

}


?>