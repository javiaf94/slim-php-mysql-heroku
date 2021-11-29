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
        $codigo = $parametros['codigo'];
        $prs_mozo_legajo = $parametros['prs_mozo_legajo'];
        $mes_codigo = $parametros['mes_codigo'];
        $nombre_cliente = $parametros['nombre_cliente'];;        
        $comanda = new Comanda();
        $comanda->codigo = $codigo;
        $comanda->prs_mozo_legajo = $prs_mozo_legajo;
        $comanda->mes_codigo = $mes_codigo;
        $comanda->nombre_cliente = $nombre_cliente;
        $comanda->crearComanda();

        $payload = json_encode(array("mensaje" => "Comanda creada con exito"));
        
        $pedido = new PedidoController();
        $pedido->CargarUno($request, $response, $args);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Comanda::obtenerTodos();
        if(!empty($lista))
        {

          $payload = json_encode(array("listaComandas" => $lista));
        }
        else
        {
          $payload = json_encode(array("mensaje" => "no se encotraron datos para esa busqueda"));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

     public function SubirFoto($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $codigo = $parametros['codigo'];
        

        $file = $request->getUploadedFiles()['file'];
        
        $dir_subida = "./fotosPedidos";
        $fecha = new DateTime();

        $ruta = $dir_subida . "/" . $codigo . " " . date_format($fecha, 'd-m-y') . ".jpg";
        $filas = Comanda::cargarFoto($ruta, $codigo);
        
        if($filas>0)
        {
            
            if (!file_exists($dir_subida)) 
            {
                mkdir($dir_subida);     
            }
            
            $file->moveTo($ruta);
            $payload = json_encode(array("mensaje" => "Foto subida con exito"));
        }
        else
        {
          $payload = json_encode(array("mensaje" => "No se pudo subir foto"));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
        
    public function CobrarComanda($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $codigo = $parametros['codigo'];
        $filas = Comanda::realizarCobro($codigo);
        
        if($filas>0)
        {            
            $payload = json_encode(array("mensaje" => "Comanda Cobrada con exito"));
        }
        else
        {
          $payload = json_encode(array("mensaje" => "No se pudo Cobrar comanda"));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    
  }

?>