<?php

namespace App\Utils;
use PDO;
use  PDOStatement;

require_once __DIR__ . '/.conf.php';

class DatabaseManager {
	private $pdo;

	 // 1.
	private static $manager;

	 // 2.
	private function __construct(){
		$conn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME;
		$this->pdo = new PDO($conn, DB_USER, DB_PWD);
	}

	// 3.
	public static function getManager(): DatabaseManager{ 
		// DatabaseManager::getManager();
		if(!isset(self::$manager)){
			self::$manager = new DatabaseManager();
		}
		return self::$manager;
	}

	private function internalExec(string $sql, array $params = []): ?PDOStatement {
		$stmt = $this->pdo->prepare($sql);
		if($stmt !== false){
			$success = $stmt->execute($params);
			if($success !== false){
				return $stmt;
			}
			if(DB_LOG){
				print_r($stmt->errorInfo());
			}
		}
		return NULL;
	}

	public function exec(string $sql, array $params = []): int{
		$stmt = $this->internalExec($sql, $params);
		return $stmt !== NULL ? $stmt->rowCount() : 0;
	}

	public function getAll(string $sql, array $params = []): array{
		$stmt = $this->internalExec($sql, $params);
		return $stmt !== NULL ? $stmt->FetchAll(PDO::FETCH_ASSOC) : [];
	}

	public function getOne(string $sql, array $params = []): array {
		$stmt = $this->internalExec($sql, $params);
		return $stmt !== NULL ? $stmt->fetch(PDO::FETCH_ASSOC) : array('success' => 'false');;
	}

	public function lastInsertedId(): int{
		return intval($this->pdo->lastInsertId());
	}


	public function getSQLAll(string $table) : string{
        $model = $this->getModel($table);
        if($model == NULL){
            return $response->withStatus(400);
        }
        
		$sql = 'SELECT * FROM '. $table;
		return $sql;
	}

	public function getSQLCreate(string $table, array $data): string{
        $model = $this->getModel($table);
        if($model == NULL){
            return 'error';
		}

        $sql = 'INSERT INTO ' . $table . ' (';
        $prepare = '(';

		$length = count($model);
		
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
		return $sql;
	}

	public function getSQLUpdate(string $table, array $data): array{
		// Retourne un tableau [0] = sql, [1] = values à rentrer
		$model = $this->getModel($table);
        if($model == NULL){
            return 'error';
		}

        $sql = 'UPDATE '. $table . ' SET ';

		$length = count($model);
		
		$values = [];
        $columns = [];
		$array = array_values($data);
		
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
		$result = [];

		array_push($result, $sql);
		array_push($result, $values);

		return $result;
	}

    private function getModel($table){
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