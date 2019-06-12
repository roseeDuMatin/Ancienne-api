<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface; 
use App\Utils\DatabaseManager;

// require_once __DIR__ . '/../utils/DatabaseManager.php';
// require_once __DIR__ . '/../utils/DatabaseManager.php';


class Controller{

    protected $container;

    public function __construct($container){
        $this->container = $container;
    }

    public function render(ResponseInterface $response, $file, $params = []){
        $this->container->view->render($response, $file, $params); 
    }

    public function flash($message, $type = 'success'){
        if(!isset($_SESSION['flash'])){
            $_SESSION['flash'] = [];
        }
        return $_SESSION['flash'][$type] = $message;        
    }

    public function redirect(ResponseInterface $response, $name){
        $router = $this->container->router->pathFor($name);
        return $response->withStatus(302)->withHeader('Location', $router);
    }

    public function __get($name){
        return $this->container->get($name);
    }
}
?>