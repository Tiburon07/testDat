<?php
header("Access-Control-Allow-Origin: *");

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


require '../vendor/autoload.php';
require '../controller/UploadController.php';
require_once('../model/Logger.php');

$logger = new Logger('index');

/*modifico l'handler degli errori in modo tale che mi cosideri i php warning come
ErrorException per riuscire a catturarli nel try-catch e poterni vedere nel file di log*/
set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context){
    throw new ErrorException( $err_msg, 0, $err_severity, $err_file, $err_line );
    },
E_WARNING);

//error_reporting(3);

try{
    //// instanzio l'oggetto App
  $app = new \Slim\App(['settings' => [
    'displayErrorDetails' => true,
    'debug'               => true]
  ]);

  $container = $app->getContainer();

  $container['logger'] = function($c) {
    $logger = new Logger();
    return $logger;
  };

  // Dico al container dove sono le view di Twig
  $container['view'] = function ($c) {
      $view = new \Slim\Views\Twig('../view/', [
        'cache' => false]);
      $view['assets'] = $c['request']->getUri()->getBaseURL();
      return $view;
  };

  //Mostra la vista per caricare i file gtfs in formato zip
  $app->get('/', function (Request $request, Response $response) {
      $uploadController = new UploadController($this);
      $uploadController->upload($request, $response);
  });

  //Salva i file dat
  $app->post('/uploaddat', function (Request $request, Response $response) {
      $uploadController = new UploadController($this);
      $uploadController->uploadDAT($request, $response);
  });

  $app->get('/tester/{run}', function (Request $request, Response $response, array $args) {
      while($args['run'] == true){
          $this->logger->debug("tester writing...");
      }
  });

  $app->run();

} catch(ErrorException $e){
  $logger->error($e->getMessage());
}
