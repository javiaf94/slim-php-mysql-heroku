<?php

use GuzzleHttp\Psr7\Response;
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

    public static function verificacionPerfil($request, $handler)
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
      };      
      $payload = AutentificadorJWT::ObtenerData($token);
      
      $response = $handler->handle($request);

      //$response->getBody()->write(json_encode($payload));    
      return $response;
    }
}