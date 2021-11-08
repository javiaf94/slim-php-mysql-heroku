<?php
require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';

class ProductoController extends Producto implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();      
        //parseo parametros
        $nombre = $parametros['nombre'];
        $tipo = $parametros['tipo'];
        $precio = $parametros['precio'];
        //creo el producto
        $prd = new Producto();
        $prd->nombre = $nombre;
        $prd->tipo = $tipo;
        $prd->precio = $precio;
        $prd->crearProducto();

        $payload = json_encode(array("mensaje" => "Producto creado con exito"));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Producto::obtenerTodos();
        $payload = json_encode(array("listaProductos" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function TraerPorTipo($request, $response, $args)
    {
        //Buscamos personal por perfil
        $tipo = $args['tipo'];
        $producto = Producto::obtenerPorTipo($tipo);
        $payload = json_encode($producto);

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
    //     //Buscamos personal por legajo
    //     $legajo = $args['legajo'];
    //     $personal = Personal::obtenerPorLegajo($legajo);
    //     $payload = json_encode($personal);

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