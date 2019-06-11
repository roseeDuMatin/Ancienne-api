<?php

namespace App\Controllers;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Utils\DatabaseManager;
// require_once __DIR__ . '/../../utils/DatabaseManager.php';

class PagesController extends Controller{

    protected $container;

    public function __construct($container){

        $this->container = $container;
    }

    public function home(RequestInterface $request, ResponseInterface $response){
        // $response->getBody()->write('Salut les gens moi même !');
        $flash = isset($_SESSION['flash']) ? $_SESSION['flash'] : [];
        $_SESSION['flash'] = [];
        $this->render($response, 'pages/home.twig', ['flash' => $flash]);
    }

    public function postHome(RequestInterface $request, ResponseInterface $response){
        if(true)
            $this->flash('Votre message a bien été envoyé');
        else{
            $this->flash('Certains champs n\'ont pas été remplic correctement', 'error');
        }   
        return $this->redirect($response, 'home');

        
    }

    public function create(RequestInterface $request, ResponseInterface $response, $args){
        header("Content-Type: application/json");
        $json = $request->getBody(); 
        $data = json_decode($json, true);

        $db = DatabaseManager::getManager();

        $model = $this->getModel($args['table']);
        if($model == NULL){
            return $response->withStatus(400);
        }


        $sql = 'INSERT INTO ' . $args['table'] . ' (';
        $prepare = '(';

        $length = count($model);

        $values = [];
        array_push($values, NULL);
        $data = array_values($data);
        foreach($data as $value)
            array_push($values, $value);

        for($i = 0; $i < $length; $i++){
            $sql .= $model[$i];
            $prepare .= '?';
            if($i != $length - 1){
                $sql .= ', ';
                $prepare .= ', ';
            }else{
                $sql .= ')';   
                $prepare .= ')'; 
            }
        }

        $sql = $sql . ' VALUES ' . $prepare;

        $result = $db->exec($sql, $values);
        if($result > 0){
            $values[0] = $db->LastInsertedId();
            echo json_encode($values);
            return $response->withStatus(201);
        }
        echo NULL;
        return $response->withStatus(400);
    }

    public function update(RequestInterface $request, ResponseInterface $response, $args){
        header("Content-Type: application/json");
        $json = $request->getBody(); 
        $data = json_decode($json, true);

        $db = DatabaseManager::getManager();

        $model = $this->getModel($args['table']);
        if($model == NULL){
            return $response->withStatus(400);
        }

        $sql = 'UPDATE '. $args['table'] . ' SET ';


        $values = [];
        $columns = [];
        $array = array_values($data);

        $length = count($model);
        for($i = 0; $i < $length; $i++){
            if($i != 0){
                array_push($values, $array[$i]);
                array_push($columns, $model[$i]);
            }
        }       
        array_push($values, $array[0]);

        for($i = 0; $i < $length - 1; $i += 1){
            $sql .= $columns[$i] . ' = ?';
            if($i != $length - 2) 
                $sql .= ' ,';
        }
        $sql .= ' WHERE ID = ?';

        $result = $db->exec($sql, $values);
        if($result > 0){
            echo json_encode($data);
            return $response->withStatus(201);
        }
        return $response->withStatus(400);
    }

    public function getById(RequestInterface $request, ResponseInterface $response, $args){
        header("Content-Type: application/json");
        $json = $request->getBody(); 
        $data = json_decode($json, true);

        $db = DatabaseManager::getManager();
        $sql = 'SELECT * FROM '. $args['table'].' WHERE ID = ?';

        $id = array(intval($args['id']));
        $result = $db->getOne($sql, $id);
        if($result > 0){
            echo json_encode($result);
            return $response->withStatus(201);
        }
        return $response->withStatus(400);
    }

    public function getAll(RequestInterface $request, ResponseInterface $response, $args){
        header("Content-Type: application/json");

        $db = DatabaseManager::getManager();

        $model = $this->getModel($args['table']);

        if($model == NULL){
            return $response->withStatus(400);
        }
        $sql = 'SELECT * FROM '. $args['table'];
        $result = $db->getAll($sql);
        if($result > 0){
            echo json_encode($result);
            return $response->withStatus(201);
        }
        echo NULL;
        return $response->withStatus(400);
    }

    public function delete(RequestInterface $request, ResponseInterface $response, $args){
        header("Content-Type: application/json");
        $json = $request->getBody(); 
        $data = json_decode($json, true);

        $db = DatabaseManager::getManager();

        $model = $this->getModel($args['table']);
        if($model == NULL){
            return $response->withStatus(400);
        }

        $sql = 'DELETE FROM '. $args['table'].' WHERE id = ?';
        $result = $db->exec($sql, [$data['id']]);
        if($result > 0){
            echo json_encode($result);
            return $response->withStatus(201);
        }
        return $response->withStatus(400);
    }

}

?>