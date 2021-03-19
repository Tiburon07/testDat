<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


require_once('../model/UploadDAT.php');
require_once('../model/Errors.php');
require_once('../model/Logger.php');


class UploadController{

    private $logger;
    private $errors;
    private $container;
    private $model;
    private $iniConf;

    public function __construct($container){
        $this->container = $container;
        $this->model = new UploadDAT();
        $this->errors = new Errors();
        $this->iniConf = parse_ini_file('../lnConfig.ini', true);
        $this->logger = new Logger('UploadController');
    }

    public function uploadDAT($request, $response) {
        $result = $this->model->uploadDat();
        header('Content-Type: application/json');
        echo json_encode($result);
    }

    public function upload($request, $response) {
        return $this->container->view->render($response,'uploadGTFS.twig');
    }

}
