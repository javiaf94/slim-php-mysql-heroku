<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';

require_once './controllers/PersonalController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/ComandaController.php';
require_once './controllers/EncuestaController.php';

require_once './middlewares/AutenticadorJWT.php';


// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();
date_default_timezone_set('America/Argentina/Buenos_Aires');

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes
$app->get('/', function (Request $request, Response $response, $args) {
  $response->getBody()->write("Bienvenido a la comanda");
  return $response;
});

// peticiones
$app->group('/personal', function (RouteCollectorProxy $group) {
  $group->get('[/]', \PersonalController::class . ':TraerTodos')->add(\AutentificadorJWT::class . '::verificacionTokenSocio');  
  $group->get('/{legajo}', \PersonalController::class . ':TraerUno');
  $group->get('/perfil/{perfil}', \PersonalController::class . ':TraerPorPerfil');
  $group->get('/logs/{legajo}', \PersonalController::class . ':TraerLogs')->add(\AutentificadorJWT::class . '::verificacionTokenSocio');  
  $group->post('[/]', \PersonalController::class . ':CargarUno')->add(\AutentificadorJWT::class . '::verificacionTokenSocio');  
  $group->post('/login[/]', \PersonalController::class . ':Login');
  $group->put('[/]', \PersonalController::class . ':ModificarUno')->add(\AutentificadorJWT::class . '::verificacionTokenSocio');
});

$app->group('/producto', function (RouteCollectorProxy $group) {
  $group->get('[/]', \ProductoController::class . ':TraerTodos');
  $group->get('/{tipo}', \ProductoController::class . ':TraerPorTipo');
  $group->post('[/]', \ProductoController::class . ':CargarUno');
  $group->get('/csv/export', \ProductoController::class . ':ExportarCSV')->add(\AutentificadorJWT::class . '::verificacionTokenSocio');
  $group->post('/csv/import', \ProductoController::class . ':ImportarCSV')->add(\AutentificadorJWT::class . '::verificacionTokenSocio');
});

$app->group('/mesa', function (RouteCollectorProxy $group) {
  $group->get('[/]', \MesaController::class . ':TraerTodos')->add(\AutentificadorJWT::class . '::verificacionTokenSocio');
  $group->get('/{codigo}', \MesaController::class . ':TraerUno');
  $group->get('/estado/{estado}', \MesaController::class . ':TraerPorEstado')->add(\AutentificadorJWT::class . '::verificacionTokenMozoSocio');
  $group->get('/estadisticas/masusada', \MesaController::class . ':TraerMasUsada')->add(\AutentificadorJWT::class . '::verificacionTokenSocio');
  $group->get('/estadisticas/menosusada', \MesaController::class . ':TraerMenosUsada')->add(\AutentificadorJWT::class . '::verificacionTokenSocio');
  $group->get('/estadisticas/masfacturada', \MesaController::class . ':TraerMasFacturada')->add(\AutentificadorJWT::class . '::verificacionTokenSocio');
  $group->get('/estadisticas/menosfacturada', \MesaController::class . ':TraerMenosFacturada')->add(\AutentificadorJWT::class . '::verificacionTokenSocio');
  $group->get('/estadisticas/facturamasgrande', \MesaController::class . ':TraerFacturaMasGrande')->add(\AutentificadorJWT::class . '::verificacionTokenSocio');
  $group->get('/estadisticas/facturamaschica', \MesaController::class . ':TraerFacturaMasChica')->add(\AutentificadorJWT::class . '::verificacionTokenSocio');
  $group->post('[/]', \MesaController::class . ':CargarUno')->add(\AutentificadorJWT::class . '::verificacionTokenSocio');
  $group->put('[/]', \MesaController::class . ':ModificarUno')->add(\AutentificadorJWT::class . '::verificacionTokenMesa');


});

$app->group('/comanda', function (RouteCollectorProxy $group) {
  $group->get('[/]', \ComandaController::class . ':TraerTodos')->add(\AutentificadorJWT::class . '::verificacionTokenMozoSocio');
  $group->post('[/]', \ComandaController::class . ':CargarUno')->add(\AutentificadorJWT::class . '::verificacionTokenMozoSocio');
  $group->post('/foto[/]', \ComandaController::class . ':SubirFoto')->add(\AutentificadorJWT::class . '::verificacionTokenMozoSocio');
  $group->put('[/]', \ComandaController::class . ':CobrarComanda')->add(\AutentificadorJWT::class . '::verificacionTokenMozoSocio');
});

$app->group('/pedido', function (RouteCollectorProxy $group) {
  $group->get('[/]', \PedidoController::class . ':TraerTodos')->add(\AutentificadorJWT::class . '::verificacionTokenSocio');    
  $group->get('/{prd_tipo}', \PedidoController::class . ':TraerPendientesPorTipo')->add(\AutentificadorJWT::class . '::verificacionTokenPedidos');  
  $group->get('/{com_codigo}/{mesa_codigo}', \PedidoController::class . ':TraerPorComandaMesa');    
  $group->get('/estadisticas/traer/masvendidos', \PedidoController::class . ':TraerMasVendidos')->add(\AutentificadorJWT::class . '::verificacionTokenSocio'); 
  $group->get('/estadisticas/traer/menosvendidos', \PedidoController::class . ':TraerMenosVendidos')->add(\AutentificadorJWT::class . '::verificacionTokenSocio');
  $group->get('/estadisticas/traer/demorados', \PedidoController::class . ':TraerPedidosDemorados')->add(\AutentificadorJWT::class . '::verificacionTokenSocio');
  $group->get('/estadisticas/traer/cancelados', \PedidoController::class . ':TraerPedidosCancelados')->add(\AutentificadorJWT::class . '::verificacionTokenSocio');
  $group->get('/exportar/traer/pedidos', \PedidoController::class . ':TraerTodosPDF')->add(\AutentificadorJWT::class . '::verificacionTokenSocio');
  $group->post('[/]', \PedidoController::class . ':CargarUno')->add(\AutentificadorJWT::class . '::verificacionTokenMozoSocio');
  $group->put('/{prd_tipo}[/]', \PedidoController::class . ':ModificarUnEstadoyTiempo')->add(\AutentificadorJWT::class . '::verificacionTokenPedidos');  
  $group->put('/estado/{prd_tipo}[/]', \PedidoController::class . ':ModificarUnEstado')->add(\AutentificadorJWT::class . '::verificacionTokenPedidos');  

});

$app->group('/encuesta', function (RouteCollectorProxy $group) {
  $group->get('/mesa/mejores[/]', \EncuestaController::class . ':TraerMejoresMesas')->add(\AutentificadorJWT::class . '::verificacionTokenSocio');
  $group->get('/mesa/peores[/]', \EncuestaController::class . ':TraerPeoresMesas')->add(\AutentificadorJWT::class . '::verificacionTokenSocio');
  $group->get('/mejores[/]', \EncuestaController::class . ':TraerMejoresNotas')->add(\AutentificadorJWT::class . '::verificacionTokenSocio');
});

$app->run();
