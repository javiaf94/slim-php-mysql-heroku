<?php

class Encuesta
{

    public $id;
    public $puntaje_mesa;
    public $puntaje_restaurante;
    public $puntaje_mozo;
    public $puntaje_cocinero;
    public $opinion;
    public $com_codigo;
    public $mes_codigo;
    
    public function crearEncuesta()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO encuesta_enc 
        (puntaje_mesa, puntaje_restaurante, puntaje_mozo, puntaje_cocinero, opinion, com_codigo, mes_codigo ) 
        VALUES (:puntaje_mesa, :puntaje_restaurante, :puntaje_mozo, :puntaje_cocinero, :opinion , :com_codigo , :mes_codigo)");
        
        $consulta->bindValue(':puntaje_mesa', $this->puntaje_mesa, PDO::PARAM_INT);
        $consulta->bindValue(':puntaje_restaurante', $this->puntaje_restaurante, PDO::PARAM_INT);
        $consulta->bindValue(':puntaje_mozo', $this->puntaje_mozo, PDO::PARAM_INT);
        $consulta->bindValue(':puntaje_cocinero', $this->puntaje_cocinero, PDO::PARAM_INT);
        $consulta->bindValue(':opinion', $this->opinion, PDO::PARAM_STR);
        $consulta->bindValue(':com_codigo', $this->com_codigo, PDO::PARAM_STR);
        $consulta->bindValue(':mes_codigo', $this->mes_codigo, PDO::PARAM_INT);

        $consulta->execute();
        

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerPeoresMesas()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT mes_codigo, puntaje_mesa
                                                        FROM encuesta_enc
                                                        ORDER BY puntaje_mesa ASC
                                                        LIMIT 5");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerMejoresMesas()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT mes_codigo, puntaje_mesa
                                                        FROM encuesta_enc
                                                        ORDER BY puntaje_mesa DESC
                                                        LIMIT 5");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerMejoresNotas()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM encuesta_enc
                                                        ORDER BY (puntaje_mesa + puntaje_restaurante + puntaje_mozo + puntaje_cocinero) DESC
                                                        LIMIT 5");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Encuesta');
    }

}