<?php

require_once './models/Encuesta.php';
require_once './interfaces/IApiUsable.php';

class EncuestaController extends Encuesta implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();      
        $puntaje_mesa = $parametros['puntaje_mesa'];
        $puntaje_restaurante = $parametros['puntaje_restaurante'];
        $puntaje_mozo = $parametros['puntaje_mozo'];
        $puntaje_cocinero = $parametros['puntaje_cocinero'];       
        $com_codigo = $parametros['com_codigo'];    
        $mes_codigo = $parametros['mes_codigo'];    
        $opinion = $parametros['opinion'];        

        $encuesta = new Encuesta();
        $encuesta->puntaje_mesa = $puntaje_mesa;
        $encuesta->puntaje_restaurante = $puntaje_restaurante;
        $encuesta->puntaje_mozo = $puntaje_mozo;
        $encuesta->puntaje_cocinero = $puntaje_cocinero;
        $encuesta->com_codigo = $com_codigo;
        $encuesta->mes_codigo = $mes_codigo;
        $encuesta->opinion = $opinion;
        $encuesta->crearEncuesta();

        $payload = json_encode(array("mensaje" => "Encuesta creada con exito"));
        
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerPeoresMesas($request, $response, $args)
    {
        $lista = Encuesta::obtenerPeoresMesas();

        if(!empty($lista))
        {

          $payload = json_encode(array("PeoresMesas" => $lista));
         
        }
        else
        {
          $payload = json_encode(array("mensaje" => "no se encotraron datos para esa busqueda"));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerMejoresMesas($request, $response, $args)
    {
        $lista =  Encuesta::obtenerMejoresMesas();
        if(!empty($lista))
        {
          
          $payload = json_encode(array("MejoresMesas" => $lista));
          
        }
        else
        {
          $payload = json_encode(array("mensaje" => "no se encotraron datos para esa busqueda"));
        }
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerMejoresNotas($request, $response, $args)
    {
        $lista =  Encuesta::obtenerMejoresNotas();
        
        if(!empty($lista))
        {          
          $payload = json_encode(array("Mejores notas" => $lista));         
        }
        else
        {
          $payload = json_encode(array("mensaje" => "no se encotraron datos para esa busqueda"));
        }
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }



}


?>