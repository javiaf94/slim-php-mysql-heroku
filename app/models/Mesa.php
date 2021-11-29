<?php

class Mesa
{

    public $id;
    public $codigo;
    public $estado;
    
    public function crearMesa()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesa_mes (codigo, estado) VALUES (:codigo, :estado)");
        
        $consulta->bindValue(':codigo', $this->codigo, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo, estado FROM mesa_mes");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public static function obtenerPorCodigo($codigo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo, estado FROM mesa_mes WHERE codigo = :codigo");
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Mesa');
    }

    public static function obtenerPorEstado($estado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo, estado FROM mesa_mes WHERE estado = :estado");
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public static function obtenerMasUsada()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT mes.id, mes.codigo, COUNT(com.mes_codigo) as veces_usada
                                                        FROM comanda_com com
                                                        JOIN mesa_mes mes  ON com.mes_codigo = mes.codigo
                                                        GROUP BY com.mes_codigo
                                                        ORDER BY veces_usada DESC

                                                        LIMIT 1");
        
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerMenosUsada()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT mes.id, mes.codigo, COUNT(com.mes_codigo) as veces_usada
                                                        FROM comanda_com com
                                                        JOIN mesa_mes mes  ON com.mes_codigo = mes.codigo
                                                        GROUP BY com.mes_codigo
                                                        ORDER BY veces_usada ASC
                                                        LIMIT 1");
        
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }


    public static function obtenerMasFacturada()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT mes.id, mes.codigo, sum(precio_total) as total_facturado
                                                        FROM mesa_mes mes
                                                        INNER JOIN comanda_com com 
                                                        ON mes.codigo = com.mes_codigo
                                                        GROUP BY mes_codigo
                                                        ORDER BY total_facturado DESC
                                                        LIMIT 1");
        
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);

    }

    public static function obtenerMenosFacturada()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT mes.id, mes.codigo, sum(precio_total) as total_facturado
                                                        FROM mesa_mes mes
                                                        INNER JOIN comanda_com com 
                                                        ON mes.codigo = com.mes_codigo
                                                        GROUP BY mes_codigo
                                                        ORDER BY total_facturado ASC
                                                        LIMIT 1");
        
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerFacturaMasGrande()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT mes.id, mes.codigo, precio_total
                                                        FROM mesa_mes mes
                                                        INNER JOIN comanda_com com 
                                                        ON mes.codigo = com.mes_codigo
                                                        ORDER BY precio_total DESC
                                                        LIMIT 1");
        
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerFacturaMasChica()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT mes.id, mes.codigo, precio_total
                                                        FROM mesa_mes mes
                                                        INNER JOIN comanda_com com 
                                                        ON mes.codigo = com.mes_codigo
                                                        ORDER BY precio_total ASC
                                                        LIMIT 1");
        
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }



    public static function cerrarMesa($codigo, $estado)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE mesa_mes SET estado= :estado WHERE codigo = :codigo");        
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_INT);                
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);  
        $consulta->execute();
        return $consulta->rowCount();
    }
}


?>