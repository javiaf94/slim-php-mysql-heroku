<?php
require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';
require_once './controllers/CSVController.php';


class ProductoController extends Producto implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();      
        $nombre = $parametros['nombre'];
        $tipo = $parametros['tipo'];
        $precio = $parametros['precio'];
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
        if(!empty($lista))
        {          
            $payload = json_encode(array("listaProductos" => $lista));                        
        }
        else
        {
          $payload = json_encode(array("mensaje" => "no se encotraron datos para esa busqueda"));
        }
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function TraerPorTipo($request, $response, $args)
    {
        $tipo = $args['tipo'];
        $producto = Producto::obtenerPorTipo($tipo);
        if(!empty($producto))
        {          
            $payload = json_encode($producto);                        
        }
        else
        {
          $payload = json_encode(array("mensaje" => "no se encotraron datos para esa busqueda"));
        }
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function ExportarCSV($request, $response, $args)
    {
        $lista = Producto::obtenerTodos();

        $dir_subida = "./app/CSVExportados";
        if (!file_exists($dir_subida)) 
        {
            mkdir($dir_subida);     
        }
        $ruta = $dir_subida . "/productos.csv";

        echo var_dump($ruta);

        foreach($lista as $prod)
        {
            $texto = $prod->id . "," . $prod->nombre . "," . $prod->tipo . ",". $prod->precio . "," ;
            CSVController::AgregarCSV($ruta, $texto);
        } 

        $response->getBody()->write(json_encode(array("mensaje" => "CSV creado con exito")));
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function ImportarCSV($request, $response, $args)
    {
        $file = $request->getUploadedFiles()['file'];
        
        $dir_subida = "./CSVImportados";
        if (!file_exists($dir_subida)) 
        {
            mkdir($dir_subida);     
        }

        $ruta = $dir_subida . "/productos.csv";
        $file->moveTo($ruta);

        $productosCSV = CSVController::LeerCSV($ruta);
        
        foreach($productosCSV as $prod)
        {
            $parametros = explode(',',$prod);
            $producto = new Producto();
            $producto->nombre = $parametros[1];
            $producto->tipo = $parametros[2];
            $producto->precio = $parametros[3];
            
            $producto->crearProducto();
        }
        
        $response->getBody()->write(json_encode(array("mensaje" => "CSV importado con exito")));
        return $response
          ->withHeader('Content-Type', 'application/json');        

    }

    
}

?>