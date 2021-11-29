<?php

class Comanda
{

    public $id;
    public $codigo;
    public $prs_mozo_legajo;
    public $mes_codigo;
    public $nombre_cliente;
    public $estado;
    public $precio_total;
    public $timestamp_cobro;
    public $foto;
    
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
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo, prs_mozo_legajo, mes_codigo, nombre_cliente, precio_total, timestamp_cobro, estado FROM comanda_com");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Comanda');
    }

    public static function cargarFoto($foto, $codigo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE comanda_com SET foto=:foto WHERE codigo=:codigo");
        $consulta->bindValue(':foto', $foto, PDO::PARAM_STR);
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->rowCount();
    }

    public static function realizarCobro($codigo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE comanda_com com
                                                       SET estado=:estado,
                                                       timestamp_cobro = :timestamp_cobro,
                                                       precio_total= (SELECT  SUM(prd.precio * ped.cantidad) FROM pedido_ped ped
                                                                    JOIN producto_prd prd 
                                                                    ON prd.id = ped.prd_id
                                                                    WHERE ped.com_codigo=:com_codigo)
                                                       WHERE codigo=:codigo ");
        $consulta->bindValue(':estado', 'cobrado', PDO::PARAM_STR);
        $consulta->bindValue(':com_codigo', $codigo, PDO::PARAM_STR);
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $timestamp = new DateTime();
        $consulta->bindValue(':timestamp_cobro', date_format($timestamp, 'y-m-d h:m:s'), PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->rowCount();
    }

}


?>