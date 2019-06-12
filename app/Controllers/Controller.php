<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface; 
use App\Utils\DatabaseManager;

class Controller{

    protected $container;

    public function __construct($container){
        $this->container = $container;
    }

}
?>