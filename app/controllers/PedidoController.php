<?php
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';
require_once './fpdf/fpdf.php';

class PedidoController extends Pedido implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();      
        $com_codigo = $parametros['codigo'];
        $prd_id = $parametros['prd_id'];
        $prd_tipo = $parametros['prd_tipo'];
        $cantidad = $parametros['cantidad'];
        $pedido = new Pedido();
        $pedido->com_codigo = $com_codigo;
        $pedido->prd_id = $prd_id;
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
        if(!empty($lista))
        {          
            $payload = json_encode(array("listaPedidos" => $lista));            
        }
        else
        {
          $payload = json_encode(array("mensaje" => "no se encotraron datos para esa busqueda"));
        }
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }


    public function TraerPendientesPorTipo($request, $response, $args)
    {
       
        $prd_tipo = $args['prd_tipo'];
        $pedidos = Pedido::obtenerPendientesPorTipo($prd_tipo);
        if(!empty($pedidos))
        {          
            $payload = json_encode($pedidos);            
        }
        else
        {
          $payload = json_encode(array("mensaje" => "no se encotraron datos para esa busqueda"));
        }
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function TraerEnPreparacionPorTipo($request, $response, $args)
    {
       
        $prd_tipo = $args['prd_tipo'];
        $pedidos = Pedido::obtenerEnPreparacionPorTipo($prd_tipo);
        if(!empty($pedidos))
        {          
            $payload = json_encode($pedidos);            
        }
        else
        {
          $payload = json_encode(array("mensaje" => "no se encotraron datos para esa busqueda"));
        }
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function TraerListos($request, $response, $args)
    {
       
        $pedidos = Pedido::obtenerListos();
        if(!empty($pedidos))
        {          
            $payload = json_encode($pedidos);            
        }
        else
        {
          $payload = json_encode(array("mensaje" => "no se encotraron datos para esa busqueda"));
        }
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }
    
    public function TraerPorComandaMesa($request, $response, $args)
    {
        $com_codigo = $args['com_codigo'];
        $mesa_codigo = $args['mesa_codigo'];

        $pedidos = Pedido::obtenerPorComandaMesa($com_codigo, $mesa_codigo);
        if(!empty($pedidos))
        {          
            $payload = json_encode($pedidos);            
        }
        else
        {
          $payload = json_encode(array("mensaje" => "no se encotraron datos para esa busqueda"));
        }
        
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUnEstadoyTiempo($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        
        $tiempo_preparacion = $parametros['tiempo_preparacion'];
        $estado = $parametros['estado'];        
        $prs_legajo = $parametros['prs_legajo'];
        $id = $parametros['id'];

        $filas= Pedido::actualizarEstadoTiempo($id, $estado, $tiempo_preparacion, $prs_legajo);

        if($filas>0)
        {
            $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));
        }
        else
        {
        $payload = json_encode(array("mensaje" => "No se pudo modificar Pedido"));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUnEstado($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        
        $estado = $parametros['estado'];        
        $id = $parametros['id'];

        $filas= Pedido::actualizarEstado($id, $estado);

        if($filas>0)
        {
            $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));
        }
        else
        {
        $payload = json_encode(array("mensaje" => "No se pudo modificar Pedido"));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerMasVendidos($request, $response, $args)
    {
        $lista = Pedido::obtenerMasVendidos();
        if(!empty($lista))
        {          
            $payload = json_encode(array("PedidosMasVendidos" => $lista));                        
        }
        else
        {
          $payload = json_encode(array("mensaje" => "no se encotraron datos para esa busqueda"));
        }
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }


    public function TraerMenosVendidos($request, $response, $args)
    {
        $lista = Pedido::obtenerMenosVendidos();
        if(!empty($lista))
        {          
            $payload = json_encode(array("PedidosMenosVendidos" => $lista));                        
        }
        else
        {
          $payload = json_encode(array("mensaje" => "no se encotraron datos para esa busqueda"));
        }
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerPedidosDemorados($request, $response, $args)
    {
        $lista = Pedido::obtenerDemorados();
        if(!empty($lista))
        {          
    
            $payload = json_encode(array("PedidosDemorados" => $lista));            
        }
        else
        {
          $payload = json_encode(array("mensaje" => "no se encotraron datos para esa busqueda"));
        }
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerPedidosCancelados($request, $response, $args)
    {
        $lista = Pedido::obtenerCancelados();
      
        if(!empty($lista))
        {          
            $payload = json_encode(array("PedidosCancelados" => $lista));            
        }
        else
        {
          $payload = json_encode(array("mensaje" => "no se encotraron datos para esa busqueda"));
        }
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodosPDF($request, $response, $args)
    {
        $lista = Pedido::obtenerTodos();

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',15);
        //header
        $pdf->Cell(170,10, 'Pedidos realizados' ,0,1, 'C');
        $pdf->SetFont('Arial','B',10);
        //body
        foreach($lista as $pedido)
        {
            $linea1 = "id: " . $pedido->id . " | " . "com_codigo: " . $pedido->com_codigo .  " | " . 
            "prd_id: " . $pedido->prd_id . " | " . "prd_tipo: " . $pedido->prd_tipo. " | " . "cantidad: " . $pedido->cantidad . "prs_legajo: " 
            . $pedido->prs_legajo. " | " . "estado: " . $pedido->estado ;

            $linea2 =   "tiempo_preparacion: " . $pedido->tiempo_preparacion . " | " . "timestamp_inicio: " 
            . $pedido->timestamp_inicio . " | " . "timestamp_fin: " . $pedido->timestamp_fin ;
            $pdf->Cell(40,10, $linea1 ,0,1);
            $pdf->Cell(40,10, $linea2 ,0,1);
        } 

        
        $pdf->Output('F', './pdf/Pedidos ' . date("d-m-Y", time()). '.pdf', 'I');
        
        $response->getBody()->write(json_encode(array("mensaje" => "pdf creado con exito")));
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
        
    
}



?>