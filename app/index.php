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
// require_once './middlewares/Logger.php';

require_once './controllers/PersonalController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/ComandaController.php';

require_once './middlewares/AutenticadorJWT.php';


// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

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
  $group->post('[/]', \PersonalController::class . ':CargarUno')->add(\AutentificadorJWT::class . '::verificacionTokenSocio');  
  $group->post('/login[/]', \PersonalController::class . ':Login');
});

$app->group('/producto', function (RouteCollectorProxy $group) {
$group->get('[/]', \ProductoController::class . ':TraerTodos');
$group->get('/{tipo}', \ProductoController::class . ':TraerPorTipo');
$group->post('[/]', \ProductoController::class . ':CargarUno');
});

$app->group('/mesa', function (RouteCollectorProxy $group) {
$group->get('[/]', \MesaController::class . ':TraerTodos');
$group->get('/{codigo}', \MesaController::class . ':TraerUno');
$group->get('/estado/{estado}', \MesaController::class . ':TraerPorEstado');
$group->post('[/]', \MesaController::class . ':CargarUno');
$group->put('[/]', \MesaController::class . ':ModificarUno')->add(\AutentificadorJWT::class . '::verificacionTokenMesa');

});

$app->group('/comanda', function (RouteCollectorProxy $group) {
$group->get('[/]', \ComandaController::class . ':TraerTodos');
$group->post('[/]', \ComandaController::class . ':CargarUno');
});

$app->group('/pedido', function (RouteCollectorProxy $group) {
$group->get('[/]', \PedidoController::class . ':TraerTodos')->add(\AutentificadorJWT::class . '::verificacionTokenSocio');    
$group->get('/{prd_tipo}', \PedidoController::class . ':TraerPorTipo')->add(\AutentificadorJWT::class . '::verificacionTokenPedidos');  
$group->get('/{com_codigo}/{mesa_codigo}', \PedidoController::class . ':TraerPorComandaMesa');    
$group->post('[/]', \PedidoController::class . ':CargarUno')->add(\AutentificadorJWT::class . '::verificacionTokenAltaPedidos');
$group->put('[/]', \PedidoController::class . ':ModificarUno');//->add(\AutentificadorJWT::class . '::verificacionTokenPedidos');  

});

$app->run();
