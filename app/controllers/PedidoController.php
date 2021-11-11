<?php
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';

class PedidoController extends Pedido implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();      
        //parseo parametros
        $com_codigo = $parametros['codigo'];
        $prd_nombre = $parametros['prd_nombre'];
        $prd_tipo = $parametros['prd_tipo'];
        $cantidad = $parametros['cantidad'];
        //creo el pedido
        $pedido = new Pedido();
        $pedido->com_codigo = $com_codigo;
        $pedido->prd_nombre = $prd_nombre;
        $pedido->prd_tipo = $prd_tipo;
        $pedido->cantidad = $cantidad;
        $pedido->crearPedido();

        $payload = json_encode(array("mensaje" => "Pedido creado con exito"));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Pedido::obtenerTodos();
        $payload = json_encode(array("listaPedidos" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerPorTipo($request, $response, $args)
    {
        //Buscamos pedido por tipo
        
        $prd_tipo = $args['prd_tipo'];
        $pedidos = Pedido::obtenerPorTipo($prd_tipo);
        $payload = json_encode($pedidos);
        
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }
    
    public function TraerPorComandaMesa($request, $response, $args)
    {
        $com_codigo = $args['com_codigo'];
        $mesa_codigo = $args['mesa_codigo'];

        $pedidos = Pedido::obtenerPorComandaMesa($com_codigo, $mesa_codigo);
        $payload = json_encode($pedidos);
        
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    //me quede acá armando el pedido
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        
        $tiempo_Preparacion = $parametros['tiempo_Preparacion'];
        $estado = $parametros['estado'];        
        $com_codigo = $parametros['com_codigo'];
        $prd_nombre = $parametros['prd_nombre'];

        $filas= Pedido::actualizarEstadoTiempo($com_codigo, $prd_nombre, $estado, $tiempo_Preparacion);

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
    
    
    //en desuso por ahora
    //
    //
    //
    
    
    // public function TraerUno($request, $response, $args)
    // {
    //     //Buscamos mesa por codigo
    //     $codigo = $args['codigo'];
    //     $mesa = Mesa::obtenerPorCodigo($codigo);
    //     $payload = json_encode($mesa);

    //     $response->getBody()->write($payload);
    //     return $response
    //       ->withHeader('Content-Type', 'application/json');
    // }
    
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

?>