<?php

class Personal
{

    public $legajo;
    public $perfil;
    public $nombre;
    public $estado;

    public function crearPersonal()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO personal_prs (legajo, perfil, nombre, estado) VALUES (:legajo, :perfil, :nombre, :estado)");
        
        $consulta->bindValue(':legajo', $this->legajo, PDO::PARAM_STR);
        $consulta->bindValue(':perfil', $this->perfil, PDO::PARAM_STR);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT legajo, perfil, nombre, estado FROM personal_prs");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Personal');
    }

    public static function obtenerPorLegajo($legajo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT legajo, perfil, nombre, estado FROM personal_prs WHERE legajo = :legajo");
        $consulta->bindValue(':legajo', $legajo, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Personal');
    }

    public static function obtenerPorPerfil($perfil)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT legajo, perfil, nombre, estado FROM personal_prs WHERE perfil = :perfil");
        $consulta->bindValue(':perfil', $perfil, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Personal');
    }

    // public static function modificarPersonal($legajo)
    // {
    //     $objAccesoDato = AccesoDatos::obtenerInstancia();
    //     $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET usuario = :usuario, clave = :clave WHERE id = :id");
    //     $claveHash = password_hash($usuario->clave, PASSWORD_DEFAULT);
    //     $consulta->bindValue(':usuario', $usuario->usuario, PDO::PARAM_STR);
    //     $consulta->bindValue(':clave', $claveHash, PDO::PARAM_STR);
    //     $consulta->bindValue(':id', $usuario->id, PDO::PARAM_INT);
    //     $consulta->execute();
    //     return $consulta->rowCount();
    // }

    // public static function borrarUsuario($usuario)
    // {
    //     $objAccesoDato = AccesoDatos::obtenerInstancia();
    //     $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET fechaBaja = :fechaBaja WHERE id = :id");
    //     $fecha = new DateTime(date("d-m-Y"));
    //     $consulta->bindValue(':id', $usuario, PDO::PARAM_INT);
    //     $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
    //     $consulta->execute();
    // }

}


?>