<?php

class login
{

    public $id;
    public $prs_legajo;
    public $timestamp;


    public function crearLogin()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO login_log (prs_legajo) VALUES (:prs_legajo)");
        
        $consulta->bindValue(':prs_legajo', $this->prs_legajo, PDO::PARAM_STR);        
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function traerLogsPersonal($prs_legajo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT l.* , p.nombre FROM login_log l
                                                       LEFT JOIN personal_prs p
                                                       ON l.prs_legajo = p.legajo
                                                       WHERE l.prs_legajo = :prs_legajo");
        
        $consulta->bindValue(':prs_legajo', $prs_legajo, PDO::PARAM_STR);        
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

}


?>