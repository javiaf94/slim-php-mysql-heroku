<?php

class Personal
{
    public $id;
    public $legajo;
    public $perfil;
    public $nombre;
    public $estado;
    public $clave;

    public function crearPersonal()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO personal_prs (legajo, clave, perfil, nombre, estado) VALUES (:legajo, :clave, :perfil, :nombre, :estado)");
        
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
        $consulta->bindValue(':legajo', $this->legajo, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $claveHash);
        $consulta->bindValue(':perfil', $this->perfil, PDO::PARAM_STR);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, legajo, perfil, nombre, estado FROM personal_prs");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Personal');
    }

    public static function obtenerPorLegajo($legajo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, legajo, clave, perfil, nombre, estado FROM personal_prs WHERE legajo = :legajo");
        $consulta->bindValue(':legajo', $legajo, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Personal');
    }

    public static function obtenerPorPerfil($perfil)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, legajo, perfil, nombre, estado FROM personal_prs WHERE perfil = :perfil");
        $consulta->bindValue(':perfil', $perfil, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Personal');
    }



    public static function modificarPersonal($legajo, $estado)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE personal_prs SET estado = :estado WHERE legajo = :legajo");
        $consulta->bindValue(':legajo', $legajo, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->rowCount();
    }
}


?>