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

    protected function getModel($table){
        $col = [];
        switch($table){
            case 'Adhesion':
                $col = ['ID', 'UsrID', 'DateAdhesion', 'Cb', 'Code'];
                break;
            case 'Affectation':
                $col = ['ServiceID', 'UsrID'];
                break;
            case 'Ask' : 
                $col = ['ID', 'UsrMakeID', 'UsrAnwserID', 'AskTypeID', 'Subject', 'DateStart', 'DateEnd'];
                break;
            case 'AskType' : 
                $col = ['ID', 'Name'];
                break;
            case 'Competence' : 
                $col = ['ID', 'Name'];
                break;
            case 'Delivery' : 
                $col = ['ID', 'TruckID', 'UsrID', 'DeliveryTypeID', 'DateStart', 'DateEnd'];
                break;
            case 'DeliveryType' :
                $col = ['ID', 'Name'];
                break;
            case 'Depositery':
                $col = ['ID', 'SiteID', 'Numero', 'Rue', 'Postcode', 'Area', 'Capacity'];
                break;
            case 'Ingredient':
                $col = ['ID', 'Name'];
                break;
            case 'InStock':
                $col = ['IngredientID', 'ProductID'];
                break;
            case 'Justificatif':
                $col = ['ID', 'Link', 'CompetenceID', 'UsrID'];
                break;
            case 'Mission':
                $col = ['ID', 'UsrID', 'ServiceID', 'DateStart', 'DateEnd'];
                break;
            case 'Product':
                $col = ['ID', 'Name', 'Barcode', 'ValidDate', 'DepositeryID', 'UsrID_Donated', 'UsrID_Received', 'StatutID'];
                break;
            case 'Quantity':
                $col = ['ID', 'Quantity', 'RecipeeID', 'IngredientID'];
                break;
            case 'Recipee':
                $col = ['ID', 'Name', 'Content', 'Type'];
                break;
            case 'Service':
                $col = ['ID', 'Name'];
                break;
            case 'Site':
                $col = ['ID', 'Name', 'Numero', 'Rue', 'Postcode', 'Area', 'Capacity'];
                break;
            case 'Statut':
                $col = ['ID', 'Name'];
                break;
            case 'Stop':
                $col = ['ID', 'DateHour', 'DeliveryID', 'UsrID'];
                break;
            case 'Stop_Product':
                $col = ['StopID','ProductID'];
                break;
            case 'Truck':
                $col = ['ID', 'SiteID', 'Plate', 'Name', 'Capacity'];
                break;
            case 'Usr':
                $col = ['ID', 'SiteID', 'Email', 'Name', 'Surname', 'Password', 'Numero', 'Rue', 'Postcode', 'Area', 'Eligibility', 'Siret', 'Salary', 'Discriminator'];
                break;
        }
        return $col;
    }
}
?>