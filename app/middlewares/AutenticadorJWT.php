<?php

use GuzzleHttp\Psr7\Response;
use \Slim\Routing\RouteContext;
use Firebase\JWT\JWT;


class AutentificadorJWT
{
    private static $claveSecreta = 'T3sT$JWT';
    private static $tipoEncriptacion = ['HS256'];

    public static function CrearToken($datos)
    {
        $ahora = time();
        $payload = array(
            'iat' => $ahora,
            'exp' => $ahora + (60000000000000000000),
            'aud' => self::Aud(),
            'data' => $datos,
            'app' => "Test JWT"
        );
        return JWT::encode($payload, self::$claveSecreta);
    }

    public static function VerificarToken($token)
    {
        if (empty($token)) {
            throw new Exception("El token esta vacio.");
        }
        try {
            $decodificado = JWT::decode(
                $token,
                self::$claveSecreta,
                self::$tipoEncriptacion
            );
        } catch (Exception $e) {
            throw $e;
        }
        if ($decodificado->aud !== self::Aud()) {
            throw new Exception("No es el usuario valido");
        }
        return true;
    }


    public static function ObtenerPayLoad($token)
    {
        if (empty($token)) {
            throw new Exception("El token esta vacio.");
        }
        return JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion
        );
    }

    public static function ObtenerData($token)
    {
        return JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion
        )->data;
    }

    private static function Aud()
    {
        $aud = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }

        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();

        return sha1($aud);
    }

    //

    public static function verificacionTokenPedidos($request, $handler)
    {
      //parseo el header y tomo el string
      $auth = $request->getHeaders()['Authorization'][0];
      //le saco el bearer
      $token = explode(" ", $auth)[1];
      $response = new Response();
      //pruebo si el token esta bien
      try
      {
        AutentificadorJWT::VerificarToken($token);

      }catch(Exception $e)
      {
        $response->getBody()->write(json_encode(array( "token" => "Datos invalidos")));    
        return $response;
      }     

      $perfilToken = AutentificadorJWT::ObtenerData($token);
      
      //traigo el tipo de pedido desde el argumento
      $args = RouteContext::fromRequest($request)->getRoute()->getArguments();
      
      if($perfilToken =='socio')
      {
        echo "autorizado!\n";
        $response = $handler->handle($request);
      }
      else
      {
          //comparo el perfil del token con el tipo de pedido.
          switch($args['prd_tipo'])
          {
            case 'cocina':
                if($perfilToken=='cocinero')
                {
                    echo "autorizado!\n";
                    $response = $handler->handle($request);
                }
                else
                {
                    echo "no autorizado!\n";
                }            
                break;
            
            case 'cerveza':
                if($perfilToken=='cervecero')
                {
                    $response = $handler->handle($request);
                }
                else
                {
                    echo "no autorizado!\n";
                }    
                break;            
            
            case 'trago':
                if($perfilToken=='bartender')
                {
                    $response = $handler->handle($request);
                }
                else
                {
                    echo "no autorizado!\n";
                }    
                break;
    
            case 'postre':
                if($perfilToken =='socio')
                {
                    $response = $handler->handle($request);
                }
                else
                {
                    echo "no autorizado!\n";
                }    
                break;    
            default:
                break;
          }
      }
      return $response;     

    }

    public static function verificacionTokenMesa($request, $handler)
    {
        $parametros = $request->getParsedBody();
        $nuevoEstadoMesa = $parametros['estado'];        
        
        $auth = $request->getHeaders()['Authorization'];
        $response = new Response();
        
        if(!empty($auth))
        {
            $token = explode(" ", $auth[0])[1];    
            try
            {
              AutentificadorJWT::VerificarToken($token);
      
            }catch(Exception $e)
            {
              $response->getBody()->write(json_encode(array( "token" => "Datos invalidos")));    
              return $response;
            }     
            
            $perfilToken = AutentificadorJWT::ObtenerData($token);

            echo var_dump($perfilToken);
            if($perfilToken == "socio")
            {
              $response = $handler->handle($request);
              return $response;
            }
            else if($perfilToken == "mozo" && $nuevoEstadoMesa != "cerrada")
            {
                $response = $handler->handle($request);
                return $response;
            }
            else if($nuevoEstadoMesa == "cerrada")
            {
              $response->getBody()->write(json_encode(array( "error" => "La mesa solo puede ser cerrada por un socio")));    
              return $response;
            }
            else
            {
              $response->getBody()->write(json_encode(array( "error" => "El estado de la mesa solo puede ser modificado por un socio o mozo")));    
              return $response;
            }
        }
        else
        {
          $response->getBody()->write(json_encode(array( "error" => "Se requiere ingresar un token para esta accion")));    
          return $response;
        }
  
    }
    

    public static function verificacionTokenSocio($request, $handler)
    {
      $response = new Response();
      $auth = $request->getHeaders()['Authorization'];

      if(!empty($auth))
      {
          $token = explode(" ", $auth[0])[1];    
          try
          {
            AutentificadorJWT::VerificarToken($token);
    
          }catch(Exception $e)
          {
            $response->getBody()->write(json_encode(array( "token" => "Datos invalidos")));    
            return $response;
          }     
          
          $perfilToken = AutentificadorJWT::ObtenerData($token);
          echo var_dump($perfilToken->perfil);

        //   $perfilTokenJson = json_encode($perfilToken);
        //   echo var_dump("\n". $perfilTokenJson);
          
          if($perfilToken == "socio")
          {
            $response = $handler->handle($request);
            return $response;
          }
          else
          {
            $response->getBody()->write(json_encode(array( "error" => "Esta tarea solo puede ser realizada por socios")));    
            return $response;
          }
      }
      else
      {
        $response->getBody()->write(json_encode(array( "error" => "Se requiere ingresar un token para esta accion")));    
        return $response;
      }

    }

      

}