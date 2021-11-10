<?php
require_once './models/Personal.php';
require_once './interfaces/IApiUsable.php';
require_once './middlewares/AutenticadorJWT.php';


class PersonalController extends Personal implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();      
        //parseo parametros
        $legajo = $parametros['legajo'];
        $perfil = $parametros['perfil'];
        $nombre = $parametros['nombre'];
        $estado = $parametros['estado'];
        $clave = $parametros['clave'];
        //creo el usuario
        $prs = new Personal();
        $prs->legajo = $legajo;
        $prs->perfil = $perfil;
        $prs->nombre = $nombre;
        $prs->estado = $estado;
        $prs->clave = $clave;

        $prs->crearPersonal();

        $payload = json_encode(array("mensaje" => "Usuario creado con exito"));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Personal::obtenerTodos();
        $payload = json_encode(array("listaPersonal" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function TraerUno($request, $response, $args)
    {
        //Buscamos personal por legajo
        $legajo = $args['legajo'];
        $personal = Personal::obtenerPorLegajo($legajo);
        $payload = json_encode($personal);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerPorPerfil($request, $response, $args)
    {
        //Buscamos personal por perfil
        $perfil = $args['perfil'];
        $personal = Personal::obtenerPorPerfil($perfil);
        $payload = json_encode($personal);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    ///token

    public function Login($request, $response, $args)
    {
      $parametros = $request->getParsedBody();
      $legajo = $parametros['legajo'];
      $perfil = $parametros['perfil'];
      $clave = $parametros['clave'];
      
      $prs = Personal::obtenerPorLegajo($legajo);
      if($prs)
      {
        if(password_verify($clave, $prs->clave) && $perfil == $prs->perfil)
        {
          $tokenjson = json_encode(array( "token" => AutentificadorJWT::CrearToken($perfil)));
          $response->getBody()->write($tokenjson);
        }
        else
        {        
          $tokenjson = json_encode(array( "token" => "Datos invalidos"));
          $response->getBody()->write($tokenjson);
        }
        
      }
      
      return $response
      ->withHeader('Content-Type', 'application/json');
  }




    //en desuso por ahora
    //
    //
    //

    
    
    // public function ModificarUno($request, $response, $args)
    // {
    //     $parametros = $request->getParsedBody();

    //     $usuario = $parametros['usuario'];
    //     $clave = $parametros['clave'];
    //     $id = $parametros['id'];

    //     $usr = new Usuario();
    //     $usr->usuario = $usuario;
    //     $usr->clave = $clave;
    //     $usr->id = $id;
        
    //     $filas= Usuario::modificarUsuario($usr);

    //     if($filas>0)
    //     {

    //       $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));
    //     }
    //     else
    //     {
    //       $payload = json_encode(array("mensaje" => "No se pudo modificar usuario"));
    //     }

    //     $response->getBody()->write($payload);
    //     return $response
    //       ->withHeader('Content-Type', 'application/json');
    // }

    // public function BorrarUno($request, $response, $args)
    // {
        
    //     $parametros = $request->getParsedBody();

    //     $usuarioId = $parametros['usuarioId'];
        
    //     Usuario::borrarUsuario($usuarioId);

    //     $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));

    //     $response->getBody()->write($payload);
    //     return $response
    //       ->withHeader('Content-Type', 'application/json');
    // }
}