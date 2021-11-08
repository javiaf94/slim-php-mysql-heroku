<?php
require_once './models/Comanda.php';
require_once './interfaces/IApiUsable.php';
require_once './models/Pedido.php';
require_once './controllers/PedidoController.php';

class ComandaController extends Comanda implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();      
        //parseo parametros                                   
        $codigo = $parametros['codigo'];
        $prs_mozo_legajo = $parametros['prs_mozo_legajo'];
        $mes_codigo = $parametros['mes_codigo'];
        $nombre_cliente = $parametros['nombre_cliente'];;        
        //creo la Comanda
        $comanda = new Comanda();
        $comanda->codigo = $codigo;
        $comanda->prs_mozo_legajo = $prs_mozo_legajo;
        $comanda->mes_codigo = $mes_codigo;
        $comanda->nombre_cliente = $nombre_cliente;
        $comanda->crearComanda();

        $payload = json_encode(array("mensaje" => "Comanda creada con exito"));
        
        //Instancio un pedido controller y cargo uno
        $pedido = new PedidoController();
        $pedido->CargarUno($request, $response, $args);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Comanda::obtenerTodos();
        $payload = json_encode(array("listaComandas" => $lista));

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
    // public function TraerPorEstado($request, $response, $args)
    // {
    //     //Buscamos mesa por estado
    //     $estado = $args['estado'];
    //     $mesa = Mesa::obtenerPorEstado($estado);
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