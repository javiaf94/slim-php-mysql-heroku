<?php
require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';

class MesaController extends Mesa implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();      
        $codigo = $parametros['codigo'];
        $estado = $parametros['estado'];
        $mesa = new Mesa();
        $mesa->codigo = $codigo;
        $mesa->estado = $estado;
        $mesa->crearMesa();

        $payload = json_encode(array("mensaje" => "Mesa creada con exito"));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Mesa::obtenerTodos();
        if(!empty($lista))
        {          
            $payload = json_encode(array("listaMesas" => $lista));                 
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
        $codigo = $args['codigo'];
        $mesa = Mesa::obtenerPorCodigo($codigo);
        
        if(!empty($mesa))
        {          
            $payload = json_encode($mesa);
        }
        else
        {
          $payload = json_encode(array("mensaje" => "no se encotraron datos para esa busqueda"));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerPorEstado($request, $response, $args)
    {
        $estado = $args['estado'];
        $mesa = Mesa::obtenerPorEstado($estado);

        if(!empty($mesa))
        {          
            $payload = json_encode($mesa);
        }
        else
        {
          $payload = json_encode(array("mensaje" => "no se encotraron datos para esa busqueda"));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerMasUsada($request, $response, $args)
    {
        $mesa = Mesa::obtenerMasUsada();

        if(!empty($mesa))
        {          
            $payload = json_encode(array("MasUsada" => $mesa));
        }
        else
        {
          $payload = json_encode(array("mensaje" => "no se encotraron datos para esa busqueda"));
        }
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerMenosUsada($request, $response, $args)
    {
        $mesa = Mesa::obtenerMenosUsada();

        if(!empty($mesa))
        {          
            $payload = json_encode(array("MenosUsada" => $mesa));            
        }
        else
        {
          $payload = json_encode(array("mensaje" => "no se encotraron datos para esa busqueda"));
        }
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerMasFacturada($request, $response, $args)
    {
        $mesa = Mesa::obtenerMasFacturada();
        if(!empty($mesa))
        {          
            $payload = json_encode(array("MesaMasFacturada" => $mesa));                  
        }
        else
        {
          $payload = json_encode(array("mensaje" => "no se encotraron datos para esa busqueda"));
        }
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerMenosFacturada($request, $response, $args)
    {
        $mesa = Mesa::obtenerMenosFacturada();
        if(!empty($mesa))
        {          
            $payload = json_encode(array("MesaMenosFacturada" => $mesa));                            
        }
        else
        {
          $payload = json_encode(array("mensaje" => "no se encotraron datos para esa busqueda"));
        }
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerFacturaMasGrande($request, $response, $args)
    {
        $mesa = Mesa::obtenerFacturaMasGrande();
        if(!empty($mesa))
        {          
            $payload = json_encode(array("MesaConFacturaMasgrande" => $mesa));                       
        }
        else
        {
          $payload = json_encode(array("mensaje" => "no se encotraron datos para esa busqueda"));
        }
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerFacturaMasChica($request, $response, $args)
    {
        $mesa = Mesa::obtenerFacturaMasChica();
        if(!empty($mesa))
        {          
            $payload = json_encode(array("MesaConFacturaMasChica" => $mesa));                        
        }
        else
        {
          $payload = json_encode(array("mensaje" => "no se encotraron datos para esa busqueda"));
        }
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        
        $codigo = $parametros['codigo'];
        $estado = $parametros['estado'];              
        
        $filas= Mesa::cerrarMesa($codigo,$estado);

        if($filas>0)
        {
            $payload = json_encode(array("mensaje" => "Mesa modificada con exito"));
        }
        else
        {
        $payload = json_encode(array("mensaje" => "No se pudo modificar mesa"));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }



}

?>