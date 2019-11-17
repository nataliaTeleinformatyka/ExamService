<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 23.10.2019
 * Time: 18:19
 */

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

require_once 'C:\xampp\htdocs\examServiceProject\vendor\autoload.php';
class UserManager
{
    protected $db;
    protected $database;
    protected $dbname = 'User';

    public function  __construct()
    {
        $serviceAccount = ServiceAccount::fromJsonFile('C:\xampp\htdocs\examServiceProject\secret\examservicedatabase-88ff116bf2b0.json');
        //   $firebase = (new Factory)->withServiceAccount($serviceAccount)->create();

        // $this->database = $firebase->getDatabase();
        $factory = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->withDatabaseUri('https://examservicedatabase.firebaseio.com/');

        $this->database = $factory->createDatabase();
    }

   public function get(int $userId) {
        if(empty($userId) /*|| isset($userId)*/) { return false; }

        if($this->database->getReference($this->dbname)->getSnapshot()->hasChild($userId)) {
            return $this->database->getReference($this->dbname)->getChild($userId)->getValue();
        } else {
            return false;
        }
   }

   public function insert(array $data) {
       if(empty($data) /*|| isset($data)*/) { return false; }

       foreach($data as $key => $value) {
           $this->database->getReference()->getChild($this->dbname)->getChild($key)->set($value);
       }
       return true;
   }

    public function delete(int $userId) {
        if(empty($userId) /*|| isset($userId)*/) { return false; }

        if($this->database->getReference($this->dbname)->getSnapshot()->hasChild($userId)) {
            $this->database->getReference($this->dbname)->getChild($userId)->remove();
            return true;
        } else {
            return false;
        }

    }
}
/*$users = new UserManager();
var_dump($users->insert([
    '1' => 'Test'
]));*/
//var_dump($users->get(1));
//var_dump($users->delete(1));