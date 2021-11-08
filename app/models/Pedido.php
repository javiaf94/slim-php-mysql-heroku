<?php

class Pedido
{
    public $com_codigo;
    public $prd_nombre;
    public $prd_tipo;
    public $cantidad;
    public $prs_legajo;
    public $estado;
    public $tiempo_preparacion;

    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedido_ped (com_codigo, prd_nombre, prd_tipo, cantidad ) VALUES (:com_codigo,  :prd_nombre, :prd_tipo, :cantidad)");
        
        $consulta->bindValue(':com_codigo', $this->com_codigo, PDO::PARAM_STR);
        $consulta->bindValue(':prd_nombre', $this->prd_nombre, PDO::PARAM_STR);
        $consulta->bindValue(':prd_tipo', $this->prd_tipo, PDO::PARAM_STR);  
        $consulta->bindValue(':cantidad', $this->cantidad, PDO::PARAM_INT);       
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT com_codigo, prd_nombre, prd_tipo, cantidad, prs_legajo, estado, tiempo_preparacion FROM pedido_ped");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerPorTipo($prd_tipo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT com_codigo, prd_nombre, prd_tipo, cantidad, prs_legajo, estado, tiempo_preparacion  FROM pedido_ped WHERE prd_tipo = :prd_tipo");
        $consulta->bindValue(':prd_tipo', $prd_tipo, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    
}


?>