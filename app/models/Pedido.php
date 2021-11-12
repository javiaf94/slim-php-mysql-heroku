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

    public static function obtenerPendientesPorTipo($prd_tipo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT com_codigo, prd_nombre, prd_tipo, cantidad, prs_legajo, estado, tiempo_preparacion  FROM pedido_ped WHERE prd_tipo = :prd_tipo and estado = pendiente");
        $consulta->bindValue(':prd_tipo', $prd_tipo, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerPorComandaMesa($com_codigo, $mesa_codigo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT prd_nombre as nombre_producto, 
                                                          cantidad, ped.estado, tiempo_preparacion
                                                        FROM pedido_ped ped
                                                        INNER JOIN comanda_com com
                                                        on com.codigo = ped.com_codigo
                                                        WHERE ped.com_codigo = :com_codigo
                                                        AND com.mes_codigo = :mes_codigo");
        $consulta->bindValue(':com_codigo', $com_codigo, PDO::PARAM_STR);
        $consulta->bindValue(':mes_codigo', $mesa_codigo, PDO::PARAM_INT);

        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function actualizarEstadoTiempo($com_codigo, $prd_nombre, $estado, $tiempo_preparacion, $prs_legajo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedido_ped SET estado=:estado,    
                                                      tiempo_preparacion=:tiempo_preparacion,
                                                      prs_legajo=:prs_legajo
                                                      WHERE com_codigo=:com_codigo AND prd_nombre=:prd_nombre");
        $consulta->bindValue(':com_codigo', $com_codigo, PDO::PARAM_STR);
        $consulta->bindValue(':prd_nombre', $prd_nombre, PDO::PARAM_STR);
        $consulta->bindValue(':prs_legajo', $prs_legajo, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':tiempo_preparacion', $tiempo_preparacion, PDO::PARAM_INT);

        $consulta->execute();
        return $consulta->rowCount();
    }

}


?>