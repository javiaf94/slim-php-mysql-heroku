<?php
require_once './models/Personal.php';
require_once './interfaces/IApiUsable.php';
require_once './middlewares/AutenticadorJWT.php';
require_once './models/Login.php';


class PersonalController extends Personal implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();      
        $legajo = $parametros['legajo'];
        $perfil = $parametros['perfil'];
        $nombre = $parametros['nombre'];
        $estado = $parametros['estado'];
        $clave = $parametros['clave'];
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
        if(!empty($lista))
        {          
            $payload = json_encode(array("listaPersonal" => $lista));                        
        }
        else
        {
          $payload = json_encode(array("mensaje" => "no se encotraron datos para esa busqueda"));
        }
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function TraerUno($request, $response, $args)
    {
        $legajo = $args['legajo'];
        $personal = Personal::obtenerPorLegajo($legajo);
        if(!empty($personal))
        {          
            $payload = json_encode($personal);
            
        }
        else
        {
          $payload = json_encode(array("mensaje" => "no se encotraron datos para esa busqueda"));
        }
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerPorPerfil($request, $response, $args)
    {
        $perfil = $args['perfil'];
        $personal = Personal::obtenerPorPerfil($perfil);
        if(!empty($personal))
        {          
            $payload = json_encode($personal);
            
        }
        else
        {
          $payload = json_encode(array("mensaje" => "no se encotraron datos para esa busqueda"));
        }
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

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
          $dataToken = array("perfil"=> $perfil , "legajo"=> $legajo);
          $tokenjson = json_encode(array( "mensaje" => AutentificadorJWT::CrearToken($dataToken)));
          $response->getBody()->write($tokenjson);

          $login = new Login();
          $login->prs_legajo = $legajo;
          $login->crearLogin();
        }
        else
        {        
          $tokenjson = json_encode(array( "mensaje" => "Datos invalidos"));
          $response->getBody()->write($tokenjson);
        }
        
      }
      
      return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerLogs($request, $response, $args)
  {
    $legajo = $args['legajo'];

    $lista = Login::traerLogsPersonal($legajo);
    if(!empty($lista))
    {
      $payload = json_encode(array("listaLogs" => $lista));
    }
    else
    {
      $payload = json_encode(array("mensaje" => "no se encontraron logs para ese legajo"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args)
  {
      $parametros = $request->getParsedBody();

      $legajo = $parametros['legajo'];
      $estado = $parametros['estado'];
      
      $filas= Personal::modificarPersonal($legajo, $estado);

      if($filas>0)
      {

        $payload = json_encode(array("mensaje" => "Personal modificado con exito"));
      }
      else
      {
        $payload = json_encode(array("mensaje" => "No se pudo modificar Personal"));
      }

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
  }
  

}