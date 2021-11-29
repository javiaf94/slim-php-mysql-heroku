<?php

class Pedido
{
    public $id;
    public $com_codigo;
    public $prd_id;
    public $prd_tipo;
    public $cantidad;
    public $prs_legajo;
    public $estado;
    public $tiempo_preparacion;
    public $timestamp_inicio;
    public $timestamp_fin;

    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedido_ped (com_codigo, prd_id, prd_tipo, cantidad ) VALUES (:com_codigo,  :prd_id, :prd_tipo, :cantidad)");
        
        $consulta->bindValue(':com_codigo', $this->com_codigo, PDO::PARAM_STR);
        $consulta->bindValue(':prd_id', $this->prd_id, PDO::PARAM_INT);
        $consulta->bindValue(':prd_tipo', $this->prd_tipo, PDO::PARAM_STR);  
        $consulta->bindValue(':cantidad', $this->cantidad, PDO::PARAM_INT);       
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, com_codigo, prd_id, prd_tipo, cantidad, prs_legajo, estado, tiempo_preparacion FROM pedido_ped");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerPendientesPorTipo($prd_tipo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, com_codigo, prd_id, prd_tipo, cantidad, prs_legajo, estado, tiempo_preparacion  FROM pedido_ped WHERE prd_tipo = :prd_tipo and estado = :estado");
        $consulta->bindValue(':prd_tipo', $prd_tipo, PDO::PARAM_STR);
        $consulta->bindValue(':estado', "pendiente", PDO::PARAM_STR);

        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerPorComandaMesa($com_codigo, $mesa_codigo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT ped.id, prd.nombre AS nombre_producto, 
                                                              cantidad, ped.estado, tiempo_preparacion
                                                        FROM pedido_ped ped
                                                        INNER JOIN comanda_com com
                                                        ON com.codigo = ped.com_codigo
                                                        INNER JOIN producto_prd prd
                                                        ON ped.prd_id = prd.id
                                                        WHERE ped.com_codigo = :com_codigo
                                                        AND com.mes_codigo = :mes_codigo");
        $consulta->bindValue(':com_codigo', $com_codigo, PDO::PARAM_STR);
        $consulta->bindValue(':mes_codigo', $mesa_codigo, PDO::PARAM_INT);

        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function actualizarEstadoTiempo($id, $estado, $tiempo_preparacion, $prs_legajo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedido_ped SET estado=:estado,    
                                                      tiempo_preparacion=:tiempo_preparacion,
                                                      prs_legajo=:prs_legajo,
                                                      timestamp_inicio=:timestamp_inicio
                                                      WHERE id=:id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':prs_legajo', $prs_legajo, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $timestamp = new DateTime();
        $consulta->bindValue(':tiempo_preparacion', $tiempo_preparacion, PDO::PARAM_INT);
        $consulta->bindValue(':timestamp_inicio', date_format($timestamp, 'y-m-d h:m:s'), PDO::PARAM_STR);
   
        $consulta->execute();
        return $consulta->rowCount();
    }

    public static function actualizarEstado($id, $estado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedido_ped SET estado=:estado,                                                  
                                                      timestamp_fin=:timestamp_fin
                                                      WHERE id=:id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $timestamp = new DateTime();
        $consulta->bindValue(':timestamp_fin', date_format($timestamp, 'y-m-d h:m:s'), PDO::PARAM_STR);
   
        $consulta->execute();
        return $consulta->rowCount();
    }


    public static function obtenerMasVendidos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT COUNT(ped.prd_id) as cantidad_vendidos, ped.prd_id, prd.nombre
                                                        FROM pedido_ped ped
                                                        INNER JOIN producto_prd prd
                                                        ON prd.id=ped.prd_id
                                                        GROUP BY ped.prd_id
                                                        ORDER BY ped.prd_id ASC
                                                        LIMIT 5");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }



    public static function obtenerMenosVendidos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT COUNT(ped.prd_id) as cantidad_vendidos, ped.prd_id, prd.nombre
                                                        FROM pedido_ped ped
                                                        INNER JOIN producto_prd prd
                                                        ON prd.id=ped.prd_id
                                                        GROUP BY ped.prd_id
                                                        ORDER BY ped.prd_id DESC
                                                        LIMIT 5");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerDemorados()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, prd_id, com_codigo, TIMESTAMPDIFF(MINUTE, timestamp_inicio, timestamp_fin) as tiempo_total, tiempo_preparacion as tiempo_estimado FROM pedido_ped
        WHERE TIMESTAMPDIFF(MINUTE, timestamp_inicio, timestamp_fin) > tiempo_preparacion");

        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerCancelados()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedido_ped WHERE estado=:estado");        
        $consulta->bindValue(':estado', "cancelado", PDO::PARAM_STR);

        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }
}


?>